<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Services\PayUService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcilePayu extends Command
{
    protected $signature = 'payu:reconcile {--dry-run : Do not update DB, just show actions} {--minutes=5 : Only consider pending payments older than this many minutes}';
    protected $description = 'Reconcile pending PayU payments by querying PayU transaction status API';

    public function handle()
    {
        $dry = $this->option('dry-run');
        $minutes = (int) $this->option('minutes');

        $this->info("Finding pending PayU payments older than {$minutes} minutes...");
        $cutoff = now()->subMinutes($minutes);
        $payments = Payment::where('gateway', 'payu')
            ->where('status', Payment::STATUS_PENDING)
            ->where('created_at', '<=', $cutoff)
            ->get();

        if ($payments->isEmpty()) {
            $this->info('No pending payments found.');
            return 0;
        }

        $payu = app(PayUService::class);

        foreach ($payments as $payment) {
            $this->line("Checking payment {$payment->id} txn={$payment->txnid} amount={$payment->amount}");
            if (! $payment->txnid) {
                $this->warn("  - No txnid stored, skipping");
                continue;
            }

            $resp = $payu->getTransactionStatus($payment->txnid);
            Log::info('PayU reconcile response', ['payment_id' => $payment->id, 'resp' => $resp]);

            if (isset($resp['error'])) {
                $this->warn("  - API error: " . ($resp['message'] ?? $resp['error']));
                continue;
            }

            // Response may be array of entries or structured differently. Try common patterns.
            $status = null;
            $gatewayRef = null;
            $amount = null;

            if (isset($resp['status'])) {
                $status = strtolower($resp['status']);
                $gatewayRef = $resp['mihpayid'] ?? $resp['payuMoneyId'] ?? null;
                $amount = $resp['amount'] ?? null;
            } elseif (is_array($resp) && isset($resp[0]) && is_array($resp[0])) {
                $entry = $resp[0];
                $status = isset($entry['status']) ? strtolower($entry['status']) : null;
                $gatewayRef = $entry['mihpayid'] ?? $entry['payuMoneyId'] ?? null;
                $amount = $entry['amount'] ?? null;
            } elseif (isset($resp['transaction_details']) && isset($resp['transaction_details'][$payment->txnid])) {
                $entry = $resp['transaction_details'][$payment->txnid];
                $status = isset($entry['status']) ? strtolower($entry['status']) : null;
                $gatewayRef = $entry['mihpayid'] ?? null;
                $amount = $entry['amount'] ?? null;
            }

            if (! $status) {
                $this->warn('  - Could not determine status from response');
                continue;
            }

            $this->line("  - PayU status={$status} gateway_ref={$gatewayRef} amount={$amount}");

            if ($status === 'success' || $status === 'completed') {
                if ($dry) {
                    $this->info("  - Would mark completed");
                } else {
                    $payment->markCompleted($gatewayRef);
                    // call payable completion logic
                    try {
                        app(\App\Http\Controllers\PayUController::class)->completePayable($payment);
                    } catch (\Throwable $e) {
                        Log::error('Error completing payable after reconcile', ['e' => $e->getMessage(), 'payment' => $payment->id]);
                    }
                    $this->info('  - Marked completed');
                }
            } else {
                if ($dry) {
                    $this->info('  - Would mark failed');
                } else {
                    $payment->markFailed();
                    $this->info('  - Marked failed');
                }
            }
        }

        $this->info('Done');
        return 0;
    }
}
