<?php

namespace App\Http\Controllers;

use App\Models\Collaboration;
use App\Models\Coupon;
use App\Models\CreatorProfile;
use App\Models\PlatformSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollaborationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user->role === 'brand') {
            $items = $user->collaborationsAsBrand()->with(['creator', 'creator.creatorProfile', 'package', 'payoutRequests'])->latest()->get();
        } elseif ($user->role === 'creator') {
            $items = $user->collaborationsAsCreator()->with(['brand', 'brand.brandProfile', 'package', 'payoutRequests'])->latest()->get();
        } else {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
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
        $collaboration->update(['status' => 'rejected', 'rejected_at' => now()]);
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

        try {
            $file = $request->file('deliverable_file');
            // We use the folder name explicitly so it matches the FileAccessController filter
            $path = \Illuminate\Support\Facades\Storage::disk('gcs')->putFile('project_deliverables', $file);
            
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
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['message' => 'Failed to upload to cloud storage: ' . $e->getMessage()], 500);
        }

        $collaboration->update([
            'status' => 'delivered',
            'deliverable_type' => 'file',
            'deliverable_content' => $path,
            'delivered_at' => now(),
        ]);
        
        \Log::info('Collaboration record updated', ['collaboration_id' => $collaboration->id, 'status' => 'delivered']);

        return response()->json($collaboration);
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

        if (! $collaboration->deliverable_content) {
            return response()->json(['message' => 'No deliverable found'], 404);
        }

        $targetPath = $collaboration->deliverable_content;
        $basename = basename($targetPath);
        $candidates = array_values(array_unique(array_filter([
            $targetPath,
            $basename,
            str_starts_with($targetPath, 'project_deliverables/') ? Str::after($targetPath, 'project_deliverables/') : null,
            'project_deliverables/' . $basename,
        ])));

        $finalPath = null;
        foreach ($candidates as $candidate) {
            if (Storage::disk('gcs')->exists($candidate)) {
                $finalPath = $candidate;
                break;
            }
        }

        if (! $finalPath) {
            return response()->json(['message' => 'Deliverable not found in cloud storage'], 404);
        }

        return response()->json([
            'url' => Storage::disk('gcs')->temporaryUrl($finalPath, now()->addMinutes(10)),
        ]);
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
}
