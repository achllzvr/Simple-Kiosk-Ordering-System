<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\MenuService;
use App\Services\OrderService;
use App\Services\PayMongoService;
use App\Services\RestaurantService;
use Illuminate\Http\Request;

class OrderingController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private MenuService $menuService,
        private OrderService $orderService,
        private PayMongoService $payMongoService,
        private RestaurantService $restaurantService,
    ) {}

    public function selection()
    {
        return view('ordering.selection');
    }

    public function location(Request $request)
    {
        $mode = $request->query('mode', 'delivery');
        if ($mode !== 'delivery') {
            return redirect()->route('ordering.menu', ['mode' => $mode]);
        }

        $stores = $this->restaurantService->listActive()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'address' => $s->address,
            'lat' => (float) $s->lat,
            'lng' => (float) $s->lng,
        ])->values();

        return view('ordering.location', [
            'mode' => 'delivery',
            'stores' => $stores,
        ]);
    }

    public function saveLocation(Request $request)
    {
        $data = $request->validate([
            'mode' => 'required|in:delivery',
            'restaurant_id' => 'required|exists:restaurants,id',
            'customer_lat' => 'required|numeric|between:-90,90',
            'customer_lng' => 'required|numeric|between:-180,180',
        ]);

        $restaurant = $this->restaurantService->listActive()->firstWhere('id', (int) $data['restaurant_id']);
        if (! $restaurant || ! $restaurant->is_active) {
            return back()->with('error', 'Please select an active store.');
        }

        session([
            'order_mode' => 'delivery',
            'restaurant_id' => (int) $data['restaurant_id'],
            'customer_lat' => (float) $data['customer_lat'],
            'customer_lng' => (float) $data['customer_lng'],
        ]);

        return redirect()->route('ordering.menu', ['mode' => 'delivery']);
    }

    public function nearbyStores(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
        ]);

        $stores = $this->restaurantService->activeNearby((float) $data['lat'], (float) $data['lng']);

        return response()->json([
            'status' => 'success',
            'data' => $stores->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'address' => $s->address,
                'lat' => (float) $s->lat,
                'lng' => (float) $s->lng,
                'distance_km' => $s->distance_km ?? null,
            ])->values(),
        ]);
    }

    public function menu(Request $request)
    {
        $mode = $request->query('mode', 'dine-in');
        if ($mode === 'delivery' && ! session('restaurant_id')) {
            return redirect()->route('ordering.location', ['mode' => 'delivery']);
        }

        $search = $request->query('search', '');
        $cart = $this->cartService->getOrCreateCart();
        $subtotal = $this->cartService->subtotal($cart);

        return view('ordering.menu', [
            'items' => $this->menuService->listActive($search ?: null)->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'category' => $item->category,
                'price' => (float) $item->price,
                'image' => $item->image,
                'variations' => [],
                'addons' => [],
            ])->values()->all(),
            'mode' => $mode,
            'search' => $search,
            'hasCart' => ! $this->cartService->isEmpty($cart),
            'cartSubtotal' => $subtotal,
        ]);
    }

    public function cart(Request $request)
    {
        $mode = $request->query('mode', session('order_mode', 'dine-in'));
        $cart = $this->cartService->getOrCreateCart();
        $cartItems = $this->cartService->getItems($cart);
        $subtotal = $this->cartService->subtotal($cart);
        $tax = $this->cartService->tax($subtotal);
        $total = $subtotal + $tax;

        $normalized = [];
        foreach ($cartItems as $index => $item) {
            $normalized[] = array_merge($item, [
                'cartIndex' => $item['id'],
                'itemId' => $item['menu_item_id'],
            ]);
        }

        return view('ordering.cart', [
            'cartItems' => $normalized,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'serviceFee' => 0,
            'total' => $total,
            'mode' => $mode,
            'cartSubtotal' => $subtotal,
            'cartCount' => count($normalized),
        ]);
    }

    public function updateCartQuantity(Request $request)
    {
        $data = $request->validate([
            'cart_index' => 'required|integer|min:1',
            'action' => 'required|in:increase,decrease,set,remove',
            'quantity' => 'nullable|integer|min:1|max:99',
            'mode' => 'nullable|in:dine-in,take-out,delivery',
        ]);

        $mode = $data['mode'] ?? 'dine-in';
        $cart = $this->cartService->getOrCreateCart();

        try {
            $this->cartService->updateQuantity(
                $cart,
                (int) $data['cart_index'],
                $data['action'],
                isset($data['quantity']) ? (int) $data['quantity'] : null
            );
        } catch (\Throwable $e) {
            return redirect()->route('ordering.cart', ['mode' => $mode])->with('error', 'Cart item not found.');
        }

        $message = $data['action'] === 'remove' ? 'Item removed from cart.' : 'Quantity updated.';

        return redirect()->route('ordering.cart', ['mode' => $mode])->with('success', $message);
    }

    public function checkout(Request $request)
    {
        $mode = $request->query('mode', session('order_mode', 'dine-in'));
        if ($mode === 'delivery' && ! session('restaurant_id')) {
            return redirect()->route('ordering.location', ['mode' => 'delivery']);
        }

        $cart = $this->cartService->getOrCreateCart();
        if ($this->cartService->isEmpty($cart)) {
            return redirect()->route('ordering.menu', ['mode' => $mode])->with('error', 'Your cart is empty.');
        }

        $restaurant = null;
        if ($mode === 'delivery' && session('restaurant_id')) {
            $restaurant = $this->restaurantService->listAll()->firstWhere('id', (int) session('restaurant_id'));
        }

        return view('ordering.checkout', [
            'mode' => $mode,
            'restaurant' => $restaurant,
            'paymongoEnabled' => $this->payMongoService->isEnabled(),
            'subtotal' => $this->cartService->subtotal($cart),
            'tax' => $this->cartService->tax($this->cartService->subtotal($cart)),
            'total' => $this->cartService->total($cart),
        ]);
    }

    public function placeOrder(Request $request)
    {
        $data = $request->validate([
            'payment_method' => 'required|string',
            'agreement' => 'accepted',
            'mode' => 'required|in:dine-in,take-out,delivery',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'required|string|max:40',
            'guest_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:255',
            'seating_option' => 'nullable|string',
        ]);

        $mode = $data['mode'];
        if ($mode === 'delivery' && ! session('restaurant_id')) {
            return redirect()->route('ordering.location', ['mode' => 'delivery'])
                ->with('error', 'Please select a store for delivery.');
        }

        $cart = $this->cartService->getOrCreateCart();

        try {
            $order = $this->orderService->placeUnpaidOrder($cart, [
                'guest_name' => $data['guest_name'],
                'guest_phone' => $data['guest_phone'],
                'guest_email' => $data['guest_email'] ?? null,
                'mode' => $mode,
                'payment_method' => $data['payment_method'],
                'restaurant_id' => $mode === 'delivery' ? (int) session('restaurant_id') : null,
                'customer_lat' => $mode === 'delivery' ? session('customer_lat') : null,
                'customer_lng' => $mode === 'delivery' ? session('customer_lng') : null,
                'notes' => $this->buildNotes($data),
            ]);
        } catch (\RuntimeException $e) {
            return redirect()->route('order.failure', ['mode' => $mode, 'error' => $e->getMessage()]);
        }

        session()->forget(['restaurant_id', 'customer_lat', 'customer_lng', 'order_mode']);

        if ($this->payMongoService->isPayMongoMethod($data['payment_method'])) {
            $checkout = $this->payMongoService->createCheckoutForOrder(
                $order,
                route('paymongo.return', ['token' => $order->tracking_token, 'status' => 'success']),
                route('paymongo.return', ['token' => $order->tracking_token, 'status' => 'cancel']),
            );

            if (! ($checkout['success'] ?? false)) {
                return redirect()->route('order.failure', [
                    'mode' => $mode,
                    'error' => $checkout['error'] ?? 'PayMongo checkout failed.',
                ]);
            }

            $this->orderService->savePaymongoCheckoutSession($order, $checkout['session_id']);

            return redirect()->away($checkout['checkout_url']);
        }

        $this->orderService->markCashAwaiting($order);

        return redirect()->route('order.success', [
            'order_id' => $order->id,
            'token' => $order->tracking_token,
        ]);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id');
        $token = $request->query('token');
        $order = null;

        if ($token) {
            $order = $this->orderService->findByTrackingToken($token);
        } elseif ($orderId) {
            $order = \App\Models\Order::with(['items.menuItem', 'restaurant'])->find($orderId);
        }

        return view('ordering.success', [
            'orderId' => $order?->id ?? $orderId ?? 'N/A',
            'order' => $order,
            'token' => $order?->tracking_token ?? $token,
        ]);
    }

    public function failure(Request $request)
    {
        return view('ordering.failure', [
            'errorMessage' => $request->query('error', 'An error occurred while processing your order.'),
            'mode' => $request->query('mode', 'dine-in'),
        ]);
    }

    public function addToCart(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|integer|exists:menu_items,id',
            'quantity' => 'nullable|integer|min:1|max:99',
            'mode' => 'nullable|in:dine-in,take-out,delivery',
            'variation' => 'nullable|string',
            'addons' => 'nullable|array',
        ]);

        $mode = $data['mode'] ?? 'dine-in';
        $cart = $this->cartService->getOrCreateCart();

        try {
            $this->cartService->addItem(
                $cart,
                (int) $data['item_id'],
                (int) ($data['quantity'] ?? 1),
                $data['variation'] ?? null,
                is_array($data['addons'] ?? null) ? $data['addons'] : []
            );
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Item not found');
        }

        return redirect()->route('ordering.menu', ['mode' => $mode])->with('success', 'Item added to cart!');
    }

    public function track(Request $request)
    {
        $token = $request->query('token', '');
        $order = $token !== '' ? $this->orderService->findByTrackingToken($token) : null;

        return view('ordering.track', [
            'token' => $token,
            'order' => $order,
        ]);
    }

    private function buildNotes(array $data): ?string
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
}
