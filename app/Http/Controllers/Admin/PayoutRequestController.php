<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PayoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PayoutRequestController extends Controller
{
    public function index()
    {
        return response()->json(
            PayoutRequest::with([
                'user' => function ($q) {
                    $q->with(['creatorProfile', 'brandProfile'])
                        ->withCount([
                            'collaborationsAsBrand as brand_collabs_count',
                            'collaborationsAsBrand as brand_rejected_count' => fn ($q) => $q->where('status', 'rejected'),
                            'collaborationsAsCreator as creator_collabs_count',
                            'collaborationsAsCreator as creator_completed_count' => fn ($q) => $q->where('status', 'completed'),
                            'collaborationsAsCreator as creator_rejected_count' => fn ($q) => $q->where('status', 'rejected'),
                        ])->withSum('collaborationsAsCreator as creator_total_revisions', 'revision_count');
                },
                'collaboration',
                'bankAccount',
            ])
                ->orderByDesc('created_at')
                ->get()
                ->each(function (PayoutRequest $p): void {
                    $p->user?->append('avatar_url');
                })
        );
    }

    public function process(Request $request, PayoutRequest $payoutRequest)
    {
        $request->validate([
            'status' => 'required|in:paid,rejected,processing',
            'admin_notes' => 'nullable|string',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120'
        ]);

        $data = [
            'status' => $request->status,
            'admin_notes' => $request->admin_notes,
        ];

        if ($request->hasFile('receipt_file')) {
            if ($payoutRequest->receipt_url) {
                Storage::delete($payoutRequest->receipt_url);
            }
            $data['receipt_url'] = $request->file('receipt_file')->store('receipts');
        }

        if ($request->status === 'paid' && !$payoutRequest->processed_at) {
            $data['processed_at'] = now();
        }

        $payoutRequest->update($data);

        return response()->json([
            'message' => 'Payout request updated successfully.',
            'payoutRequest' => $payoutRequest->load(['user', 'collaboration', 'bankAccount'])
        ]);
    }
}
