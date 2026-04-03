<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateDeliverablePreview;
use App\Models\Collaboration;
use App\Models\Coupon;
use App\Models\CreatorProfile;
use App\Models\PlatformSetting;
use App\Notifications\CollaborationNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use League\Flysystem\PathPrefixer;
use Spatie\GoogleCloudStorage\GoogleCloudStorageAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CollaborationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $items = Collaboration::where('brand_id', $user->id)
            ->orWhere('creator_id', $user->id)
            ->with(['brand', 'brand.brandProfile', 'creator', 'creator.creatorProfile', 'package', 'payoutRequests'])
            ->latest()
            ->get();
            
        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'creator_id' => ['required', 'exists:users,id'],
            'package_id' => ['nullable', 'exists:packages,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'brand_notes' => ['nullable', 'string', 'max:1000'],
            'coupon_code' => ['nullable', 'string', 'max:64'],
        ]);

        if ($request->user()->role !== 'brand') {
            return response()->json(['message' => 'Only brands can create collaborations'], 403);
        }

        $creator = \App\Models\User::findOrFail($request->creator_id);
        if ($creator->role !== 'creator') {
            return response()->json(['message' => 'Invalid creator'], 422);
        }

        $amount = (float) $request->amount;
        $couponId = null;
        if ($request->filled('coupon_code')) {
            $applied = Coupon::apply($request->coupon_code, $amount, 'collaboration');
            if (isset($applied['error'])) {
                return response()->json(['message' => $applied['error']], 422);
            }
            $amount = $applied['final_amount'];
            $couponId = $applied['coupon_id'] ?? null;
        }

        $feePercent = (float) (PlatformSetting::get('platform_fee_percent', 10));
        $platformFee = round($amount * $feePercent / 100, 2);
        $creatorAmount = $amount - $platformFee;

        $maxRevisions = 0;
        if ($request->package_id) {
            $pkg = \App\Models\Package::find($request->package_id);
            $maxRevisions = $pkg ? (int) $pkg->revisions : 0;
        }

        $collab = Collaboration::create([
            'brand_id' => $request->user()->id,
            'creator_id' => $request->creator_id,
            'package_id' => $request->package_id,
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'creator_amount' => $creatorAmount,
            'status' => 'pending',
            'brand_notes' => $request->brand_notes,
            'coupon_id' => $couponId,
            'max_revisions' => $maxRevisions,
        ]);

        $collab->load(['creator', 'creator.creatorProfile', 'package']);
        return response()->json($collab, 201);
    }

    public function accept(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($collaboration->status !== 'pending') {
            return response()->json(['message' => 'Collaboration cannot be updated'], 422);
        }
        $collaboration->update(['status' => 'accepted']);
        $this->notify($collaboration, 'brand', 'accepted');
        $collaboration->load(['brand', 'package']);
        return response()->json($collaboration);
    }

    public function pay(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->brand_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if (! in_array($collaboration->status, ['pending', 'accepted'], true)) {
            return response()->json(['message' => 'Collaboration cannot be paid'], 422);
        }
        // Stub: mark as paid (replace with Stripe/Connect later)
        $collaboration->update(['status' => 'paid', 'paid_at' => now()]);
        $this->notify($collaboration, 'creator', 'paid');
        $collaboration->load(['creator', 'package']);
        return response()->json($collaboration);
    }

    public function requestRevision(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->brand_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'notes' => ['required', 'string', 'max:1000'],
        ]);

        if ($collaboration->revision_count >= $collaboration->max_revisions) {
            return response()->json(['message' => 'Maximum revisions reached for this collaboration'], 422);
        }

        $collaboration->increment('revision_count');
        $collaboration->update([
            'status' => 'revision_requested',
            'revision_notes' => $request->notes,
        ]);
        $this->notify($collaboration, 'creator', 'revision_requested');

        $collaboration->load(['creator', 'package']);
        return response()->json($collaboration);
    }

    public function reject(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($collaboration->status !== 'pending') {
            return response()->json(['message' => 'Collaboration cannot be rejected'], 422);
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $collaboration->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason,
        ]);

        $this->notify($collaboration, 'brand', 'rejected');
        return response()->json($collaboration);
    }

    public function resend(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->brand_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($collaboration->status !== 'rejected') {
            return response()->json(['message' => 'Only rejected collaborations can be resent'], 422);
        }
        if ($collaboration->resend_count >= 3) {
            return response()->json(['message' => 'Maximum resend limit reached (3 times)'], 422);
        }

        $collaboration->increment('resend_count');
        $collaboration->update([
            'status' => 'pending',
            'rejected_at' => null,
            // Keep the old reason in record or clear it? User said "shown on brand dashboard and brand can also chek the reasona and can resend".
            // If we resend, we probably want to clear it so creator can set a new one if rejected again.
            // But maybe keep it so creator sees why they rejected last time?
            // Usually, we clear it for the NEW attempt.
            'rejection_reason' => null,
        ]);

        $this->notify($collaboration, 'creator', 'pending');
        return response()->json($collaboration);
    }

    public function deliver(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->creator_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'deliverable_file' => ['required', 'file', 'max:102400', 'mimes:mp4,mov,m4v,avi,mkv,zip,rar,pdf,jpg,png,jpeg,doc,docx,xlsx'],
        ]);


        if (!in_array($collaboration->status, ['paid', 'revision_requested', 'delivered'])) {
            return response()->json(['message' => 'Cannot deliver at this stage'], 422);
        }

        \Log::info('Project delivery started', ['collaboration_id' => $collaboration->id, 'file' => $request->file('deliverable_file')->getClientOriginalName()]);

        $file = $request->file('deliverable_file');
        $ext = strtolower((string) $file->getClientOriginalExtension());
        $isVideo = (bool) preg_match('/^(mp4|mov|m4v|avi|mkv)$/', $ext);
        $oldPreviewPath = $collaboration->deliverable_preview_path;

        try {
            // We use the folder name explicitly so it matches the FileAccessController filter
            $path = Storage::disk('gcs')->putFile('project_deliverables', $file);

            if ($path === false) {
                \Log::error('GCS Storage explicitly returned FALSE', [
                    'bucket' => config('filesystems.disks.gcs.bucket'),
                    'project' => config('filesystems.disks.gcs.project_id'),
                ]);

                return response()->json(['message' => 'Upload failed: Cloud storage rejected the file. Please check bucket permissions and ensure it has no "Uniform" access restrictions on public objects.'], 500);
            }

            \Log::info('Project delivery upload successful', ['path' => $path, 'disk' => 'gcs']);
        } catch (\Exception $e) {
            \Log::error('Project delivery upload failed', [
                'message' => $e->getMessage(),
                'collaboration_id' => $collaboration->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['message' => 'Failed to upload to cloud storage: '.$e->getMessage()], 500);
        }

        if ($oldPreviewPath) {
            try {
                Storage::disk('gcs')->delete($oldPreviewPath);
            } catch (\Throwable) {
            }
        }

        $collaboration->update([
            'status' => 'delivered',
            'deliverable_type' => 'file',
            'deliverable_content' => $path,
            'delivered_at' => now(),
            'deliverable_preview_path' => null,
            'deliverable_preview_status' => $isVideo ? 'processing' : 'ready',
        ]);

        if ($isVideo) {
            GenerateDeliverablePreview::dispatch($collaboration->id)->afterCommit();
        }

        \Log::info('Collaboration record updated', ['collaboration_id' => $collaboration->id, 'status' => 'delivered']);
        $this->notify($collaboration, 'brand', 'delivered');

        return response()->json($collaboration->fresh());
    }

    public function previewFile(Request $request, Collaboration $collaboration): JsonResponse
    {
        $user = $request->user();
        $isAdmin = $user && $user->role === 'admin';
        $isBrand = $user && $collaboration->brand_id === $user->id;
        $isCreator = $user && $collaboration->creator_id === $user->id;

        if (! $isAdmin && ! $isBrand && ! $isCreator) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($isBrand
            && $collaboration->status === 'delivered'
            && $this->isDeliverableVideoCollaboration($collaboration)
            && $request->query('intent') !== 'download') {
            if ($collaboration->deliverable_preview_status === 'processing') {
                return response()->json([
                    'ready' => false,
                    'deliverable_preview_status' => 'processing',
                    'message' => 'A watermarked, lower-resolution preview is being prepared (usually 1–3 minutes). Please try again shortly.',
                ]);
            }
            if ($collaboration->deliverable_preview_status === 'failed') {
                return response()->json([
                    'ready' => false,
                    'deliverable_preview_status' => 'failed',
                    'message' => 'We could not generate a protected preview (video processing failed). Install ffmpeg on the server, or contact support.',
                ]);
            }
        }

        $finalPath = $this->resolveDeliverableGcsPath($collaboration);
        if (! $finalPath) {
            return response()->json([
                'message' => $collaboration->deliverable_content
                    ? 'Deliverable not found in cloud storage'
                    : 'No deliverable found',
            ], 404);
        }

        $streamPath = '/api/collaborations/'.$collaboration->id.'/file/stream';

        $payload = [
            'u' => (int) $user->id,
            'c' => (int) $collaboration->id,
            'e' => now()->addMinutes(25)->timestamp,
        ];

        if ($request->query('intent') === 'download') {
            $canDownload = $isAdmin || $isCreator
                || ($isBrand && in_array($collaboration->status, ['completed', 'resolved'], true));
            if (! $canDownload) {
                return response()->json(['message' => 'You are not allowed to download this file yet.'], 403);
            }
            $payload['d'] = 1;
        }

        $previewToken = Crypt::encryptString(json_encode($payload, JSON_THROW_ON_ERROR));

        return response()->json([
            'ready' => true,
            'url' => $streamPath,
            'preview_token' => $previewToken,
            'deliverable_preview_status' => $collaboration->deliverable_preview_status,
        ]);
    }

    /**
     * Stream deliverable bytes through the app so the browser never sees a raw GCS signed URL.
     * Requires an authenticated session plus a short-lived preview_token (except admins).
     */
    public function streamDeliverable(Request $request, Collaboration $collaboration): Response
    {
        $user = $request->user();
        $isAdmin = $user && $user->role === 'admin';
        $isBrand = $user && $collaboration->brand_id === $user->id;
        $isCreator = $user && $collaboration->creator_id === $user->id;

        if (! $isAdmin && ! $isBrand && ! $isCreator) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $wantsDownload = $request->boolean('download');
        if ($wantsDownload && $isBrand && ! in_array($collaboration->status, ['completed', 'resolved'], true)) {
            return response()->json(['message' => 'Download is available after the project is completed.'], 403);
        }

        if (! $isAdmin) {
            $tokenResponse = $this->validateStreamPreviewToken($request, $collaboration, $wantsDownload, (int) $user->id);
            if ($tokenResponse !== null) {
                return $tokenResponse;
            }
        }

        if ($isBrand && ! $wantsDownload && $collaboration->status === 'delivered'
            && $this->isDeliverableVideoCollaboration($collaboration)) {
            if ($collaboration->deliverable_preview_status === 'processing') {
                return response()->json([
                    'message' => 'A watermarked preview is still being generated. Please wait and try again.',
                ], 503);
            }
            if ($collaboration->deliverable_preview_status === 'failed') {
                return response()->json([
                    'message' => 'Protected preview is unavailable. Contact support or ask the creator to re-upload the video.',
                ], 503);
            }
        }

        [$finalPath, $dispositionBasename] = $this->resolveStreamTarget(
            $collaboration,
            $wantsDownload,
            (bool) $isAdmin,
            (bool) $isBrand,
            (bool) $isCreator
        );

        if (! $finalPath) {
            abort(404, $collaboration->deliverable_content
                ? 'Deliverable not found in cloud storage'
                : 'No deliverable found');
        }

        $disk = Storage::disk('gcs');
        if (! $disk instanceof GoogleCloudStorageAdapter) {
            abort(500, 'Cloud storage is not configured for secure streaming.');
        }

        $size = $disk->size($finalPath);
        $mime = $disk->mimeType($finalPath) ?: 'application/octet-stream';

        $gcsConfig = config('filesystems.disks.gcs');
        $root = (string) ($gcsConfig['root'] ?? $gcsConfig['path_prefix'] ?? '');
        $objectKey = (new PathPrefixer($root))->prefixPath($finalPath);
        $storageObject = $disk->getClient()->bucket($gcsConfig['bucket'])->object($objectKey);

        $baseHeaders = [
            'Content-Type' => $mime,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ];

        $dispositionType = $wantsDownload
            ? ResponseHeaderBag::DISPOSITION_ATTACHMENT
            : ResponseHeaderBag::DISPOSITION_INLINE;
        $baseHeaders['Content-Disposition'] = (new Response)->headers->makeDisposition(
            $dispositionType,
            $dispositionBasename,
            preg_replace('/[^A-Za-z0-9_.-]/', '_', $dispositionBasename)
        );

        if ($request->isMethod('HEAD')) {
            return response('', 200, $baseHeaders + [
                'Content-Length' => (string) $size,
            ]);
        }

        $rangeHeader = $request->header('Range');
        $range = $rangeHeader ? $this->parseSingleByteRange($rangeHeader, $size) : null;

        if ($range === 'unsatisfiable') {
            return response('', 416, $baseHeaders + [
                'Content-Range' => 'bytes */'.$size,
            ]);
        }

        if ($range !== null) {
            [$start, $end] = $range;
            $length = $end - $start + 1;
            $rangeSpec = $start.'-'.$end;

            return response()->stream(function () use ($storageObject, $rangeSpec) {
                $stream = $storageObject->downloadAsStream([
                    'restOptions' => [
                        'headers' => [
                            'Range' => 'bytes='.$rangeSpec,
                        ],
                    ],
                ]);
                while (! $stream->eof()) {
                    echo $stream->read(65536);
                }
            }, 206, $baseHeaders + [
                'Content-Length' => (string) $length,
                'Content-Range' => 'bytes '.$start.'-'.$end.'/'.$size,
            ]);
        }

        return response()->stream(function () use ($disk, $finalPath) {
            $stream = $disk->readStream($finalPath);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, $baseHeaders + [
            'Content-Length' => (string) $size,
        ]);
    }

    /**
     * @return string|null Logical path on the gcs disk, or null if missing
     */
    private function resolveDeliverableGcsPath(Collaboration $collaboration): ?string
    {
        return $this->resolveLogicalGcsPath($collaboration->deliverable_content);
    }

    private function resolveLogicalGcsPath(?string $targetPath): ?string
    {
        if (! $targetPath) {
            return null;
        }

        $basename = basename($targetPath);
        $candidates = array_values(array_unique(array_filter([
            $targetPath,
            $basename,
            str_starts_with($targetPath, 'project_deliverables/') ? Str::after($targetPath, 'project_deliverables/') : null,
            'project_deliverables/'.$basename,
        ])));

        foreach ($candidates as $candidate) {
            if (Storage::disk('gcs')->exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    private function isDeliverableVideoCollaboration(Collaboration $collaboration): bool
    {
        $path = $collaboration->deliverable_content ?? '';

        return (bool) preg_match('/\.(mp4|mov|m4v|avi|mkv)$/i', $path);
    }

    /**
     * @return array{0: string|null, 1: string}
     */
    private function resolveStreamTarget(
        Collaboration $collaboration,
        bool $wantsDownload,
        bool $isAdmin,
        bool $isBrand,
        bool $isCreator
    ): array {
        $originalBasename = basename($collaboration->deliverable_content ?? 'deliverable');

        if ($wantsDownload) {
            return [$this->resolveLogicalGcsPath($collaboration->deliverable_content), $originalBasename];
        }

        if ($isAdmin || $isCreator) {
            return [$this->resolveLogicalGcsPath($collaboration->deliverable_content), $originalBasename];
        }

        if ($isBrand) {
            if (in_array($collaboration->status, ['completed', 'resolved'], true)) {
                return [$this->resolveLogicalGcsPath($collaboration->deliverable_content), $originalBasename];
            }

            if ($collaboration->status === 'delivered' && $this->isDeliverableVideoCollaboration($collaboration)) {
                if ($collaboration->deliverable_preview_status === 'ready' && $collaboration->deliverable_preview_path) {
                    $previewLogical = $this->resolveLogicalGcsPath($collaboration->deliverable_preview_path);
                    if ($previewLogical) {
                        return [$previewLogical, basename($previewLogical)];
                    }
                }
            }
        }

        return [$this->resolveLogicalGcsPath($collaboration->deliverable_content), $originalBasename];
    }

    private function validateStreamPreviewToken(Request $request, Collaboration $collaboration, bool $wantsDownload, int $userId): ?JsonResponse
    {
        $payload = $this->decodePreviewToken($request->query('preview_token'));
        if ($payload === null) {
            return response()->json([
                'message' => 'This media URL is not valid on its own. Sign in and open the preview from your StarJD collaboration or support ticket.',
            ], 403);
        }
        if ((int) ($payload['u'] ?? 0) !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ((int) ($payload['c'] ?? 0) !== (int) $collaboration->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ((int) ($payload['e'] ?? 0) < now()->timestamp) {
            return response()->json([
                'message' => 'This preview link has expired. Close it and open the file again from your dashboard.',
            ], 403);
        }
        if ($wantsDownload && empty($payload['d'])) {
            return response()->json([
                'message' => 'Use the Download button on the collaboration page to download this file.',
            ], 403);
        }

        return null;
    }

    private function decodePreviewToken(?string $token): ?array
    {
        if ($token === null || $token === '') {
            return null;
        }
        try {
            $raw = Crypt::decryptString($token);
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);

            return is_array($data) ? $data : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array{0: int, 1: int}|null|'unsatisfiable'
     */
    private function parseSingleByteRange(string $rangeHeader, int $fileSize): array|string|null
    {
        $rangeHeader = trim($rangeHeader);
        if ($rangeHeader === '' || ! preg_match('/^bytes=/i', $rangeHeader)) {
            return null;
        }
        if (! preg_match('/bytes=(.+)$/i', $rangeHeader, $m)) {
            return null;
        }
        $range = trim($m[1]);
        if (str_contains($range, ',')) {
            return null;
        }
        if (! preg_match('/^(\d*)-(\d*)$/', $range, $parts)) {
            return null;
        }

        $startRaw = $parts[1];
        $endRaw = $parts[2];

        if ($startRaw === '' && $endRaw === '') {
            return null;
        }

        if ($startRaw === '') {
            $suffixLen = (int) $endRaw;
            if ($suffixLen <= 0) {
                return null;
            }
            $start = max(0, $fileSize - $suffixLen);
            $end = $fileSize - 1;
        } elseif ($endRaw === '') {
            $start = (int) $startRaw;
            $end = $fileSize - 1;
        } else {
            $start = (int) $startRaw;
            $end = (int) $endRaw;
        }

        if ($fileSize === 0 || $start > $end || $start >= $fileSize) {
            return 'unsatisfiable';
        }

        $end = min($end, $fileSize - 1);

        return [$start, $end];
    }

    public function complete(Request $request, Collaboration $collaboration): JsonResponse

    {
        if ($collaboration->brand_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($collaboration->status !== 'delivered') {
            return response()->json(['message' => 'Collaboration is not in delivered status'], 422);
        }

        $collaboration->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        $this->notify($collaboration, 'creator', 'complete');

        return response()->json($collaboration);
    }

    public function rejectDelivery(Request $request, Collaboration $collaboration): JsonResponse
    {
        if ($collaboration->brand_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if ($collaboration->status !== 'delivered') {
            return response()->json(['message' => 'Collaboration is not in delivered status'], 422);
        }

        $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        $collaboration->update([
            'status' => 'disputed',
            'revision_notes' => "Work rejected by brand. Reason: {$request->reason}. Notes: {$request->notes}",
        ]);
        $this->notify($collaboration, 'creator', 'disputed');

        // Create a professional Support Ticket for mediation
        $ticket = \App\Models\SupportTicket::create([
            'user_id' => $request->user()->id,
            'collaboration_id' => $collaboration->id,
            'ticket_id' => 'DISP-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'subject' => "Collaboration Dispute: #" . $collaboration->id . " - " . $collaboration->creator->name,
            'priority' => 'high',
            'status' => 'open',
        ]);

        \App\Models\SupportMessage::create([
            'support_ticket_id' => $ticket->id,
            'user_id' => $request->user()->id,
            'message' => "Automatic Dispute Raised for Collaboration #{$collaboration->id}.\n\nReason: {$request->reason}\nBrand Feedback: {$request->notes}\n\nAdmin has been notified to supervise this matter. Creator (#{$collaboration->creator_id}) is requested to await admin review.",
            'is_admin' => false,
        ]);

        return response()->json([
            'message' => 'Dispute raised and work rejected. Admin will moderate this ticket.',
            'collaboration' => $collaboration->load(['creator', 'package'])
        ]);
    }

    public function claimSettlement(Request $request, Collaboration $collaboration)
    {
        $user = $request->user();
        if (!in_array($collaboration->status, ['resolved', 'completed'])) {
            return response()->json(['message' => 'Collaboration is not in a claimable state.'], 403);
        }

        $request->validate([
            'type' => 'required|in:brand,creator',
            'bank_account_id' => 'required|exists:bank_accounts,id',
        ]);

        $type = $request->input('type');
        $bankAccountId = $request->input('bank_account_id');
        $bank = \App\Models\BankAccount::where('id', $bankAccountId)->where('user_id', $user->id)->firstOrFail();

        if ($type === 'brand') {
            if ($collaboration->brand_id !== $user->id) abort(403);
            if ($collaboration->brand_claimed) abort(400, 'Already claimed.');
            $amount = $collaboration->status === 'resolved' ? $collaboration->resolved_refund_amount : 0;
            if ($amount <= 0) abort(400, 'No refund amount available.');
            
            $collaboration->update(['brand_claimed' => true]);
            $payoutType = 'brand_refund';
        } else {
            if ($collaboration->creator_id !== $user->id) abort(403);
            if ($collaboration->creator_claimed) abort(400, 'Already claimed.');
            $amount = $collaboration->status === 'resolved' ? $collaboration->resolved_creator_amount : $collaboration->creator_amount;
            if ($amount <= 0) abort(400, 'No payout amount available.');

            $collaboration->update(['creator_claimed' => true]);
            $payoutType = 'creator_payout';
        }

        \App\Models\PayoutRequest::create([
            'user_id' => $user->id,
            'collaboration_id' => $collaboration->id,
            'bank_account_id' => $bank->id,
            'amount' => $amount,
            'type' => $payoutType,
            'status' => 'pending'
        ]);

        return response()->json(['message' => 'Claim request submitted to admin for processing.']);
    }

    private function notify(Collaboration $collaboration, string $recipientType, string $event)
    {
        $recipient = $recipientType === 'brand' ? $collaboration->brand : $collaboration->creator;
        if (!$recipient) return;

        $notification = new CollaborationNotification($collaboration, $event);
        $recipient->notify($notification);
        $notification->sendPush($recipient);
    }
}
