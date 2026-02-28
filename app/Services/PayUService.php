<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;

class PayUService
{
    public function __construct(
        private string $key,
        private string $salt,
        private string $baseUrl,
        private string $surl,
        private string $furl
    ) {
    }

    public static function fromConfig(): self
    {
        $config = config('payu');
        return new self(
            $config['merchant_key'],
            $config['salt'],
            $config['base_url'],
            $config['surl'],
            $config['furl']
        );
    }

    /**
     * Query PayU for transaction status using merchant postservice.
     * Returns decoded JSON or ['error'=>...] on failure.
     */
    public function getTransactionStatus(string $txnid): array
    {
        $statusUrl = config('payu.status_url');
        $payload = [
            'key' => $this->key,
            'command' => 'verify_payment',
            'var1' => $txnid,
        ];

        // Try common hash orders — PayU expects a sha512 hash but order varies by integration.
        $hashes = [
            strtolower(hash('sha512', $this->salt . '|' . 'verify_payment' . '|' . $txnid . '|' . $this->key)),
            strtolower(hash('sha512', $this->key . '|' . 'verify_payment' . '|' . $txnid . '|' . $this->salt)),
        ];

        foreach ($hashes as $h) {
            $payload['hash'] = $h;
            try {
                if (config('app.debug')) {
                    \Illuminate\Support\Facades\Log::debug('PayU status request', ['url' => $statusUrl, 'payload' => $payload]);
                }
                $resp = Http::asForm()->post($statusUrl, $payload);
                if (! $resp->successful()) {
                    // try next hash variant
                    continue;
                }
                $json = $resp->json();
                // If API returns a message about hash, let caller decide; return raw json/array
                return is_array($json) ? $json : ['raw' => $resp->body()];
            } catch (\Exception $e) {
                return ['error' => 'exception', 'message' => $e->getMessage()];
            }
        }

        return ['error' => 'http_error_or_invalid_hash'];
    }

    /**
     * Generate hash for payment request.
     * Sequence: key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||salt
     */
    public function generateRequestHash(string $txnid, string $amount, string $productinfo, string $firstname, string $email, string $udf1 = '', string $udf2 = '', string $udf3 = '', string $udf4 = '', string $udf5 = ''): string
    {
        $hashString = $this->key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|'
            . $firstname . '|' . $email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $this->salt;
        return strtolower(hash('sha512', $hashString));
    }

    /**
     * Verify PayU response hash (reverse hash).
     * Regular: sha512(SALT|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
     */
    public function verifyResponseHash(array $params): bool
    {
        $receivedHash = $params['hash'] ?? '';
        $salt = $this->salt;
        $status = $params['status'] ?? '';
        $udf5 = $params['udf5'] ?? '';
        $udf4 = $params['udf4'] ?? '';
        $udf3 = $params['udf3'] ?? '';
        $udf2 = $params['udf2'] ?? '';
        $udf1 = $params['udf1'] ?? '';
        $email = $params['email'] ?? '';
        $firstname = $params['firstname'] ?? '';
        $productinfo = $params['productinfo'] ?? '';
        $amount = $params['amount'] ?? '';
        $txnid = $params['txnid'] ?? '';
        $key = $this->key;

        $hashString = $salt . '|' . $status . '||||||' . $udf5 . '|' . $udf4 . '|' . $udf3 . '|' . $udf2 . '|' . $udf1 . '|' . $email . '|' . $firstname . '|' . $productinfo . '|' . $amount . '|' . $txnid . '|' . $key;
        $expectedHash = strtolower(hash('sha512', $hashString));
        return hash_equals($expectedHash, $receivedHash);
    }

    public function buildPaymentParams(User $user, string $txnid, float $amount, string $productinfo, string $udf1 = ''): array
    {
        $amountFormatted = number_format((float) $amount, 2, '.', '');
        $firstname = str_replace('|', ' ', substr($user->name ?? 'Customer', 0, 100));
        $email = $user->email ?? '';

        $hash = $this->generateRequestHash($txnid, $amountFormatted, $productinfo, $firstname, $email, $udf1);

        return [
            'key' => $this->key,
            'txnid' => $txnid,
            'amount' => $amountFormatted,
            'productinfo' => $productinfo,
            'firstname' => $firstname,
            'email' => $email,
            'phone' => $user->phone ?? '9999999999',
            'surl' => $this->surl,
            'furl' => $this->furl,
            'hash' => $hash,
            'udf1' => $udf1,
        ];
    }

    public function getPaymentUrl(): string
    {
        return $this->baseUrl;
    }
}
