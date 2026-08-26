<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PayMongo Hosted Checkout (API v2 checkout_sessions) — TAPUS-aligned.
 */
class PayMongoService
{
    public function isEnabled(): bool
    {
        return (bool) config('paymongo.enabled') && config('paymongo.secret_key') !== '';
    }

    public function isPayMongoMethod(string $paymentMethod): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $method = strtolower($paymentMethod);

        if (in_array($method, ['cash_payment', 'cash'], true)) {
            return false;
        }

        return in_array($method, ['paymongo', 'online_payment', 'credit_card', 'digital_wallet', 'online_banking'], true)
            || str_contains($method, 'paymongo');
    }

    /**
     * @return array{success: bool, session_id?: string, checkout_url?: string, error?: string}
     */
    public function createCheckoutForOrder(Order $order, string $successUrl, string $cancelUrl): array
    {
        $order->loadMissing('items.menuItem');

        $lineItems = [];
        foreach ($order->items as $item) {
            $name = $item->menuItem?->name ?? 'Menu item';
            $lineItems[] = [
                'name' => mb_substr($name, 0, 120),
                'amount' => $this->toCentavos((float) $item->price_at_purchase),
                'currency' => 'PHP',
                'quantity' => max(1, (int) $item->quantity),
            ];
        }

        if ($lineItems === []) {
            return ['success' => false, 'error' => 'No billable items for checkout.'];
        }

        $payload = [
            'data' => [
                'attributes' => [
                    'billing' => [
                        'name' => $order->guest_name ?: 'Kiosk Guest',
                        'email' => $order->guest_email ?: 'orders@example.com',
                        'phone' => $order->guest_phone ?: null,
                    ],
                    'line_items' => $lineItems,
                    'payment_method_types' => config('paymongo.payment_method_types'),
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'reference_number' => (string) $order->id,
                    'description' => 'Kiosk Order #'.$order->id,
                    'send_email_receipt' => false,
                ],
            ],
        ];

        $response = $this->request('POST', '/v2/checkout_sessions', $payload);
        if (! ($response['success'] ?? false)) {
            return $response;
        }

        $body = $response['body'];
        $sessionId = $body['data']['id'] ?? null;
        $checkoutUrl = $body['data']['attributes']['checkout_url'] ?? null;

        if (! $sessionId || ! $checkoutUrl) {
            Log::error('PayMongo checkout missing session id or checkout_url', ['body' => $body]);

            return ['success' => false, 'error' => 'Invalid PayMongo checkout response.'];
        }

        return [
            'success' => true,
            'session_id' => $sessionId,
            'checkout_url' => $checkoutUrl,
        ];
    }

    public function getCheckoutSession(string $sessionId): array
    {
        if ($sessionId === '') {
            return ['success' => false, 'error' => 'Session ID is required.'];
        }

        return $this->request('GET', '/v2/checkout_sessions/'.rawurlencode($sessionId));
    }

    public function getPaymentStatusFromSession(array $sessionBody): string
    {
        $attrs = $sessionBody['data']['attributes'] ?? [];
        $payments = $attrs['payments'] ?? [];

        foreach ($payments as $payment) {
            $paymentStatus = strtolower((string) ($payment['attributes']['status'] ?? ''));
            if ($paymentStatus === 'paid') {
                return 'paid';
            }
        }

        $sessionStatus = strtolower((string) ($attrs['status'] ?? ''));
        if (in_array($sessionStatus, ['paid', 'completed', 'succeeded'], true)) {
            return 'paid';
        }
        if (in_array($sessionStatus, ['expired', 'cancelled', 'canceled'], true)) {
            return 'expired';
        }

        return $sessionStatus !== '' ? $sessionStatus : 'unknown';
    }

    /**
     * @return array{payment_id: string, session_id: string}|null
     */
    public function extractPaymentFromSession(array $sessionBody, string $fallbackSessionId = ''): ?array
    {
        $data = $sessionBody['data'] ?? null;
        if (! is_array($data)) {
            return null;
        }

        $attrs = $data['attributes'] ?? [];
        $sessionId = (string) ($data['id'] ?? $fallbackSessionId);
        $paymentId = '';

        if (! empty($attrs['payments'][0]['id'])) {
            $paymentId = (string) $attrs['payments'][0]['id'];
        } elseif (! empty($attrs['payment_intent']['id'])) {
            $paymentId = (string) $attrs['payment_intent']['id'];
        }

        if ($sessionId === '') {
            return null;
        }

        return [
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
        ];
    }

    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool
    {
        $webhookSecret = (string) config('paymongo.webhook_secret');
        if ($webhookSecret === '') {
            Log::warning('PayMongo webhook: PAYMONGO_WEBHOOK_SECRET not set — skipping verification (dev only).');

            return true;
        }

        if ($signatureHeader === null || $signatureHeader === '') {
            return false;
        }

        $parts = [];
        foreach (explode(',', $signatureHeader) as $segment) {
            $segment = trim($segment);
            if (! str_contains($segment, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $segment, 2);
            $parts[$key] = $value;
        }

        $timestamp = $parts['t'] ?? null;
        if ($timestamp === null || $timestamp === '') {
            return false;
        }

        $signedPayload = $timestamp.'.'.$rawBody;
        $expected = hash_hmac('sha256', $signedPayload, $webhookSecret);

        foreach (['li', 'te', 'v1'] as $key) {
            $provided = $parts[$key] ?? '';
            if ($provided !== '' && hash_equals($expected, $provided)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{order_id: string, session_id: string, payment_id: string}|null
     */
    public function parsePaidWebhook(array $event): ?array
    {
        $attrs = $event['data']['attributes'] ?? $event['attributes'] ?? [];
        $type = (string) ($attrs['type'] ?? $event['data']['type'] ?? '');

        $session = $attrs['data'] ?? null;
        if ($type !== 'checkout_session.payment.paid') {
            return null;
        }

        if (! is_array($session)) {
            return null;
        }

        $sessionAttrs = $session['attributes'] ?? [];
        $orderId = (string) ($sessionAttrs['reference_number'] ?? '');
        $sessionId = (string) ($session['id'] ?? '');

        $paymentId = '';
        if (! empty($sessionAttrs['payments'][0]['id'])) {
            $paymentId = (string) $sessionAttrs['payments'][0]['id'];
        } elseif (! empty($sessionAttrs['payment_intent']['id'])) {
            $paymentId = (string) $sessionAttrs['payment_intent']['id'];
        }

        if ($orderId === '' || $sessionId === '') {
            return null;
        }

        return [
            'order_id' => $orderId,
            'session_id' => $sessionId,
            'payment_id' => $paymentId,
        ];
    }

    private function toCentavos(float $pesos): int
    {
        return (int) round($pesos * 100);
    }

    /**
     * @return array{success: bool, body?: array, error?: string, http_code?: int}
     */
    private function request(string $method, string $path, ?array $payload = null): array
    {
        $secretKey = (string) config('paymongo.secret_key');
        if ($secretKey === '') {
            return ['success' => false, 'error' => 'PayMongo secret key is not configured.'];
        }

        $url = config('paymongo.api_base').$path;

        try {
            $pending = Http::withBasicAuth($secretKey, '')
                ->acceptJson()
                ->timeout(30);

            $response = $method === 'GET'
                ? $pending->get($url)
                : $pending->send($method, $url, ['json' => $payload]);
        } catch (\Throwable $e) {
            Log::error('PayMongo request failed: '.$e->getMessage());

            return ['success' => false, 'error' => 'Could not reach PayMongo.'];
        }

        $body = $response->json();
        $httpCode = $response->status();

        if (! is_array($body)) {
            return ['success' => false, 'error' => 'Invalid PayMongo response.', 'http_code' => $httpCode];
        }

        if (! $response->successful()) {
            $message = $body['errors'][0]['detail'] ?? $body['errors'][0]['code'] ?? 'PayMongo request failed';
            Log::error('PayMongo API error', ['code' => $httpCode, 'body' => $body]);

            return ['success' => false, 'error' => $message, 'http_code' => $httpCode];
        }

        return ['success' => true, 'body' => $body, 'http_code' => $httpCode];
    }
}
