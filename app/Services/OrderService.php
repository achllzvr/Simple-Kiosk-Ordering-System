<?php

namespace App\Services;

use App\Contracts\PaymentGateway;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymongoWebhookLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function __construct(
        private CartService $cartService,
        private PaymentGateway $payMongoService,
    ) {}

    /**
     * Build guest checkout notes from form fields.
     *
     * @param  array{address?: string, seating_option?: string}  $data
     */
    public function buildGuestNotes(array $data): ?string
    {
        $parts = [];
        if (! empty($data['address'])) {
            $parts[] = 'Address/Seat: '.$data['address'];
        }
        if (! empty($data['seating_option'])) {
            $parts[] = 'Seating: '.$data['seating_option'];
        }

        return $parts === [] ? null : implode(' | ', $parts);
    }

    /**
     * @return array<string, Collection<int, Order>>
     */
    public function kanbanColumns(): array
    {
        $statuses = ['placed', 'preparing', 'ready', 'completed', 'cancelled'];
        $columns = [];

        foreach ($statuses as $status) {
            $columns[$status] = Order::query()
                ->where('status', $status)
                ->with(['user', 'restaurant', 'items.menuItem'])
                ->latest()
                ->get();
        }

        return $columns;
    }

    public function findOrFail(int $orderId): Order
    {
        return Order::query()->findOrFail($orderId);
    }

    public function findWithDetails(?int $orderId): ?Order
    {
        if (! $orderId) {
            return null;
        }

        return Order::query()
            ->with(['items.menuItem', 'restaurant'])
            ->find($orderId);
    }

    /**
     * @param  array{
     *   guest_name: string,
     *   guest_phone: string,
     *   guest_email?: ?string,
     *   mode: string,
     *   payment_method: string,
     *   restaurant_id?: ?int,
     *   customer_lat?: ?float,
     *   customer_lng?: ?float,
     *   notes?: ?string
     * }  $data
     */
    public function placeUnpaidOrder(Cart $cart, array $data): Order
    {
        if ($this->cartService->isEmpty($cart)) {
            throw new \RuntimeException('Your cart is empty. Please add items before checkout.');
        }

        $items = $this->cartService->getItems($cart);
        $total = $this->cartService->total($cart);

        return DB::transaction(function () use ($cart, $data, $items, $total) {
            $order = Order::create([
                'user_id' => null,
                'guest_name' => $data['guest_name'],
                'guest_phone' => $data['guest_phone'],
                'guest_email' => $data['guest_email'] ?? null,
                'tracking_token' => Str::lower(Str::random(32)),
                'restaurant_id' => $data['restaurant_id'] ?? null,
                'status' => 'placed',
                'payment_status' => 'unpaid',
                'payment_method' => $data['payment_method'],
                'total_price' => $total,
                'order_mode' => $data['mode'],
                'customer_lat' => $data['customer_lat'] ?? null,
                'customer_lng' => $data['customer_lng'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['menu_item_id'],
                    'quantity' => $item['quantity'],
                    'price_at_purchase' => $item['price'],
                ]);
            }

            $this->cartService->clear($cart);

            return $order->load(['items.menuItem', 'restaurant']);
        });
    }

    public function savePaymongoCheckoutSession(Order $order, string $sessionId): void
    {
        $order->update(['paymongo_checkout_session_id' => $sessionId]);
    }

    public function markCashAwaiting(Order $order): void
    {
        $order->update([
            'payment_status' => 'unpaid',
            'payment_method' => $order->payment_method ?: 'cash_payment',
        ]);
    }

    /**
     * Idempotent paid fulfillment (webhook / reconcile).
     */
    public function fulfillOnlinePayment(
        int|string $orderId,
        ?string $externalRef = null,
        ?string $sessionId = null,
        ?string $paymentId = null,
    ): array {
        $order = Order::query()->find($orderId);
        if (! $order) {
            return ['success' => false, 'error' => 'Order not found.'];
        }

        if ($order->payment_status === 'paid') {
            return ['success' => true, 'message' => 'Already paid.', 'order' => $order];
        }

        $order->update([
            'payment_status' => 'paid',
            'external_payment_ref' => $externalRef ?: $order->external_payment_ref,
            'paymongo_checkout_session_id' => $sessionId ?: $order->paymongo_checkout_session_id,
            'paymongo_payment_id' => $paymentId ?: $order->paymongo_payment_id,
        ]);

        return ['success' => true, 'message' => 'Order marked paid.', 'order' => $order->fresh()];
    }

    public function reconcilePaymongoOrder(Order $order): array
    {
        if ($order->payment_status === 'paid') {
            return ['success' => true, 'message' => 'Already paid.'];
        }

        $sessionId = $order->paymongo_checkout_session_id;
        if (! $sessionId) {
            return ['success' => false, 'error' => 'No PayMongo session on this order.'];
        }

        $response = $this->payMongoService->getCheckoutSession($sessionId);
        if (! ($response['success'] ?? false)) {
            return $response;
        }

        $status = $this->payMongoService->getPaymentStatusFromSession($response['body']);
        if ($status !== 'paid') {
            return ['success' => false, 'error' => 'PayMongo session is not paid yet (status: '.$status.').'];
        }

        $payment = $this->payMongoService->extractPaymentFromSession($response['body'], $sessionId);

        return $this->fulfillOnlinePayment(
            $order->id,
            $payment['payment_id'] ?? null,
            $payment['session_id'] ?? $sessionId,
            $payment['payment_id'] ?? null,
        );
    }

    public function updateKitchenStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order->fresh();
    }

    public function findByTrackingToken(string $token): ?Order
    {
        return Order::query()
            ->with(['items.menuItem', 'restaurant'])
            ->where('tracking_token', $token)
            ->first();
    }

    public function logWebhookEvent(array $meta): PaymongoWebhookLog
    {
        return PaymongoWebhookLog::create($meta);
    }

    public function webhookEventExists(?string $eventId): bool
    {
        if (! $eventId) {
            return false;
        }

        return PaymongoWebhookLog::query()->where('event_id', $eventId)->exists();
    }
}
