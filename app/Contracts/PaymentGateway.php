<?php

namespace App\Contracts;

use App\Models\Order;

/**
 * Adapter contract for hosted checkout providers (PayMongo today).
 */
interface PaymentGateway
{
    public function isEnabled(): bool;

    public function isPayMongoMethod(string $paymentMethod): bool;

    /**
     * @return array{success: bool, session_id?: string, checkout_url?: string, error?: string}
     */
    public function createCheckoutForOrder(Order $order, string $successUrl, string $cancelUrl): array;

    /**
     * @return array{success: bool, body?: array, error?: string, http_code?: int}
     */
    public function getCheckoutSession(string $sessionId): array;

    public function getPaymentStatusFromSession(array $sessionBody): string;

    /**
     * @return array{payment_id: string, session_id: string}|null
     */
    public function extractPaymentFromSession(array $sessionBody, string $fallbackSessionId = ''): ?array;

    public function verifyWebhookSignature(string $rawBody, ?string $signatureHeader): bool;

    /**
     * @return array{order_id: string, session_id: string, payment_id: string}|null
     */
    public function parsePaidWebhook(array $event): ?array;
}
