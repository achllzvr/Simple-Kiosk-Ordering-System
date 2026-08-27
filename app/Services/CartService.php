<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MenuItem;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function getOrCreateCart(?string $sessionId = null): Cart
    {
        $existingId = session('cart_id');
        if ($existingId) {
            $existing = Cart::query()->find($existingId);
            if ($existing) {
                return $existing;
            }
        }

        $sessionId = $sessionId ?: (string) session()->getId();
        $cart = Cart::query()->firstOrCreate(['session_id' => $sessionId]);
        session(['cart_id' => $cart->id]);

        return $cart;
    }

    public function getItems(Cart $cart): array
    {
        $cart->load(['items.menuItem']);

        return $cart->items->map(function (CartItem $item) {
            return [
                'id' => $item->id,
                'menu_item_id' => $item->menu_item_id,
                'itemName' => $item->menuItem?->name ?? 'Item',
                'quantity' => $item->quantity,
                'price' => (float) $item->unit_price,
                'variation' => $item->variation,
                'addons' => $item->addons ?? [],
                'total' => (float) $item->unit_price * $item->quantity,
            ];
        })->values()->all();
    }

    public function subtotal(Cart $cart): float
    {
        return (float) $cart->items()->sum(DB::raw('unit_price * quantity'));
    }

    public function tax(float $subtotal): float
    {
        return floor($subtotal * 0.1);
    }

    public function total(Cart $cart): float
    {
        $subtotal = $this->subtotal($cart);

        return $subtotal + $this->tax($subtotal);
    }

    public function addItem(Cart $cart, int $menuItemId, int $quantity = 1, ?string $variation = null, array $addons = []): void
    {
        $menuItem = MenuItem::query()
            ->where('is_active', true)
            ->findOrFail($menuItemId);

        $cart->items()->create([
            'menu_item_id' => $menuItem->id,
            'quantity' => max(1, min(99, $quantity)),
            'unit_price' => $menuItem->price,
            'variation' => $variation,
            'addons' => $addons,
        ]);
    }

    public function updateQuantity(Cart $cart, int $cartItemId, string $action, ?int $quantity = null): void
    {
        $item = $cart->items()->whereKey($cartItemId)->firstOrFail();

        if ($action === 'remove') {
            $item->delete();

            return;
        }

        $current = (int) $item->quantity;

        $newQuantity = match ($action) {
            'increase' => $current + 1,
            'decrease' => max(1, $current - 1),
            'set' => max(1, min(99, (int) ($quantity ?? $current))),
            default => $current,
        };

        $item->update(['quantity' => $newQuantity]);
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
        session()->forget('cart_id');
    }

    public function isEmpty(Cart $cart): bool
    {
        return $cart->items()->count() === 0;
    }
}
