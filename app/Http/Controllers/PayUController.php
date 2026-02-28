<?php

namespace App\Http\Controllers;

use App\Models\AccessPayment;
use App\Models\Booking;
use App\Models\Collaboration;
use App\Models\Coupon;
use App\Models\Payment;
use App\Services\BookingService;
use App\Services\PayUService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PayUController extends Controller
{
    public function __construct(
        private PayUService $payu,
        private BookingService $bookingService
    ) {}

    /**
     * Create PayU order and return redirect URL + form params for frontend to POST to PayU.
     */
    public function create(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $type = $request->input('type');
        $amount = (float) $request->input('amount');
        $productinfo = $request->input('productinfo', 'Order');

        if ($amount <= 0) {
            return response()->json(['message' => 'Invalid amount'], 422);
        }

        $payableType = null;
        $payableId = null;

        $couponId = null;
        switch ($type) {
            case 'access':
                $planId = $request->input('plan_id');
                $plans = config('plans.'.$user->role, []);
                $plan = collect($plans)->firstWhere('id', $planId);
                $originalAmount = (float) ($plan['price'] ?? 0);
                if (! $plan) {
                    return response()->json(['message' => 'Invalid plan'], 422);
                }
                if ($request->filled('coupon_code')) {
                    $applied = Coupon::apply($request->coupon_code, $originalAmount, 'access');
                    if (isset($applied['error'])) {
                        return response()->json(['message' => $applied['error']], 422);
                    }
                    $amount = $applied['final_amount'];
                    $couponId = $applied['coupon_id'] ?? null;
                } elseif ((float) $amount !== $originalAmount) {
                    return response()->json(['message' => 'Invalid amount'], 422);
                }
                $accessPayment = AccessPayment::create([
                    'user_id' => $user->id,
                    'type' => $user->role,
                    'amount' => $amount,
                    'status' => 'pending',
                    'gateway_ref' => null,
                    'coupon_id' => $couponId,
                ]);
                $payableType = AccessPayment::class;
                $payableId = $accessPayment->id;
                $productinfo = 'StarJD '.ucfirst($user->role).' Plan - '.($plan['name'] ?? $planId);
                break;

            case 'collaboration':
                $collabId = $request->input('collaboration_id');
                $collab = Collaboration::where('brand_id', $user->id)->findOrFail($collabId);
                if (! in_array($collab->status, ['pending', 'accepted'], true)) {
                    return response()->json(['message' => 'Collaboration cannot be paid'], 422);
                }
                if ((float) $collab->amount != $amount) {
                    return response()->json(['message' => 'Amount mismatch'], 422);
                }
                $payableType = Collaboration::class;
                $payableId = $collab->id;
                $productinfo = 'Collaboration #'.$collab->id.' - StarJD';
                break;

            case 'booking':
                $bookingId = $request->input('booking_id');
                $booking = Booking::where('user_id', $user->id)->findOrFail($bookingId);
                if ($booking->status !== Booking::STATUS_PAYMENT_PENDING || $booking->payment_status !== 'pending') {
                    return response()->json(['message' => 'Booking is not pending payment'], 422);
                }
                if ((float) $booking->amount != $amount) {
                    return response()->json(['message' => 'Amount mismatch'], 422);
                }
                $payableType = Booking::class;
                $payableId = $booking->id;
                $productinfo = 'Studio Booking #'.$booking->id.' - StarJD';
                break;

            default:
                return response()->json(['message' => 'Invalid payment type'], 422);
        }

        $txnid = 'TXN'.strtoupper(Str::random(8)).$payableId.time();
        $payment = Payment::create([
            'user_id' => $user->id,
            'type' => $type,
            'payable_type' => $payableType,
            'payable_id' => $payableId,
            'amount' => $amount,
            'currency' => 'INR',
            'status' => Payment::STATUS_PENDING,
            'gateway' => 'payu',
            'txnid' => $txnid,
            'request_params' => [],
        ]);

        $params = $this->payu->buildPaymentParams($user, $txnid, $amount, $productinfo, (string) $payment->id);
        $payment->update(['request_params' => $params]);

        return response()->json([
            'payment_url' => $this->payu->getPaymentUrl(),
            'params' => $params,
            'payment_id' => $payment->id,
            'txnid' => $txnid,
        ]);
    }

    /**
     * PayU redirects here (success or failure). Verify hash and complete/fail payment.
     * Params are normalized to lowercase keys so production PayU response is read correctly.
     */
    public function callback(Request $request)
    {
        $raw = $request->all();
        $params = array_change_key_case($raw, CASE_LOWER);
        $txnid = $params['txnid'] ?? null;
        $status = $params['status'] ?? '';
        $amount = $params['amount'] ?? '';
        $gatewayRef = $params['mihpayid'] ?? $params['payumoneyid'] ?? $params['txnid'] ?? '';

        $payment = Payment::where('txnid', $txnid)->first();
        if (! $payment) {
            Log::warning('PayU callback received for unknown txn', [
                'method' => $request->method(),
                'content' => $request->getContent(),
                'params' => $params,
                'headers' => $request->headers->all(),
            ]);

            // If no txnid is provided, treat this as a non-final browser redirect
            // and show a pending result rather than marking payment failed.
            return redirect()->to(url('/payment/result?status=pending&reason=no_txn'));
        }

        $payment->update(['request_params' => array_merge($payment->request_params ?? [], $raw)]);

        if (! $this->payu->verifyResponseHash($params)) {
            Log::warning('PayU callback: hash verification failed', [
                'txnid' => $txnid,
                'status' => $status,
                'amount' => $amount,
                'payment_amount' => (string) $payment->amount,
            ]);
            $payment->markFailed();

            return redirect()->to(url('/payment/result?status=failed&reason=hash_mismatch'));
        }

        $statusNorm = trim(strtolower((string) $status));
        $amountNorm = preg_replace('/[^\d.]/', '', (string) $amount);
        $paymentAmount = number_format((float) $payment->amount, 2, '.', '');
        $isSuccess = $statusNorm === 'success' && bccomp($amountNorm, $paymentAmount, 2) === 0;

        if ($statusNorm === 'success' && ! $isSuccess) {
            Log::warning('PayU callback: amount mismatch', [
                'txnid' => $txnid,
                'received_amount' => $amountNorm,
                'expected_amount' => $paymentAmount,
            ]);
        }

        if ($isSuccess) {
            $payment->markCompleted($gatewayRef);
            $this->completePayable($payment);
            Log::info('PayU callback: payment completed', ['txnid' => $txnid, 'type' => $payment->type]);
        } else {
            $payment->markFailed();
        }

        $redirectUrl = $isSuccess
            ? $this->successRedirectFor($payment)
            : url('/payment/result?status=failed');

        return redirect()->to($redirectUrl);
    }

    public function validateCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:64'],
            'amount' => ['required', 'numeric', 'min:0'],
            'applicable_to' => ['nullable', 'string', 'in:access,collaboration,booking'],
        ]);
        $result = Coupon::apply(
            $request->code,
            (float) $request->amount,
            $request->applicable_to
        );
        if (isset($result['error'])) {
            return response()->json(['valid' => false, 'message' => $result['error']], 422);
        }

        return response()->json(['valid' => true, 'data' => $result]);
    }

    private function completePayable(Payment $payment): void
    {
        switch ($payment->type) {
            case Payment::TYPE_ACCESS:
                $access = $payment->payable;
                if ($access && $access->status === 'pending') {
                    $access->update(['status' => 'paid', 'paid_at' => now(), 'gateway_ref' => $payment->gateway_ref]);
                }
                if ($access && $access->coupon_id) {
                    Coupon::where('id', $access->coupon_id)->increment('used_count');
                }
                break;
            case Payment::TYPE_COLLABORATION:
                $collab = $payment->payable;
                if ($collab && in_array($collab->status, ['pending', 'accepted'], true)) {
                    $collab->update(['status' => 'paid', 'paid_at' => now()]);
                }
                if ($collab && $collab->coupon_id) {
                    Coupon::where('id', $collab->coupon_id)->increment('used_count');
                }
                break;
            case Payment::TYPE_BOOKING:
                $booking = $payment->payable;
                if ($booking && $booking->status === Booking::STATUS_PAYMENT_PENDING) {
                    $this->bookingService->confirmBooking($booking, 'payu', $payment->gateway_ref);
                }
                if ($booking && $booking->coupon_id) {
                    Coupon::where('id', $booking->coupon_id)->increment('used_count');
                }
                break;
        }
    }

    private function successRedirectFor(Payment $payment): string
    {
        $base = url('/payment/result?status=success');
        switch ($payment->type) {
            case Payment::TYPE_BOOKING:
                return $base.'&booking=1';
            default:
                return $base;
        }
    }
}
