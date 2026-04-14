<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderingController extends Controller
{
    private function getCustomerDataPath(int $userId, string $fileName): string
    {
        return base_path('customer_data/' . $userId . '/' . $fileName);
    }

    private function getCustomerDataDir(int $userId): string
    {
        return base_path('customer_data/' . $userId);
    }

    private function ensureCustomerDataDir(int $userId): void
    {
        $dir = $this->getCustomerDataDir($userId);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }

    private function readJsonFile(int $userId, string $fileName, array $fallback = []): array
    {
        $this->ensureCustomerDataDir($userId);
        $path = $this->getCustomerDataPath($userId, $fileName);

        if (!file_exists($path)) {
            file_put_contents($path, json_encode($fallback, JSON_PRETTY_PRINT));
            return $fallback;
        }

        $content = file_get_contents($path);
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : $fallback;
    }

    private function writeJsonFile(int $userId, string $fileName, array $data): void
    {
        $this->ensureCustomerDataDir($userId);
        $path = $this->getCustomerDataPath($userId, $fileName);
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
    }

    private function syncMenuFromDatabase(int $userId): void
    {
        $items = MenuItem::where('is_active', true)->get();
        $menuData = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'description' => $item->description,
                'category' => $item->category,
                'price' => (float)$item->price,
                'image' => $item->image,
                'variations' => [],
                'addons' => [],
            ];
        })->toArray();

        $this->writeJsonFile($userId, 'menu.json', $menuData);
    }

    private function getMenuItems()
    {
        return $this->readJsonFile('menu.json', []);
    }

    private function getCartItemsFromFile(): array
    {
        return $this->readJsonFile('cart.json', []);
    }

    private function saveCartItemsToFile(array $cartItems): void
    {
        $this->writeJsonFile('cart.json', array_values($cartItems));
    }

    private function calculateCartSubtotal(): int|float
    {
        $cart = $this->getCartItemsFromFile();
        $items = $this->getMenuItems();
        $subtotal = 0;

        foreach ($cart as $cartItem) {
            $subtotal += $cartItem['price'] * $cartItem['quantity'];
        }

        return $subtotal;
    }

    public function selection()
    {
        return view('ordering.selection');
    }

    public function menu(Request $request)
    {
        $userId = Auth::id();
        $mode = $request->query('mode', 'dine-in');
        $search = $request->query('search', '');

        // Sync menu from DB
        $this->syncMenuFromDatabase($userId);

        $items = $this->readJsonFile($userId, 'menu.json', []);
        $cart = $this->readJsonFile($userId, 'cart.json', []);

        // Filter by search
        if ($search) {
            $items = array_filter($items, function ($item) use ($search) {
                return stripos($item['name'], $search) !== false || stripos($item['description'] ?? '', $search) !== false;
            });
        }

        // Calculate cart totals
        $subtotal = 0;
        foreach ($cart as $cartItem) {
            $subtotal += $cartItem['price'] * $cartItem['quantity'];
        }

        return view('ordering.menu', [
            'items' => array_values($items),
            'mode' => $mode,
            'search' => $search,
            'hasCart' => count($cart) > 0,
            'cartSubtotal' => $subtotal,
        ]);
    }

    public function cart(Request $request)
    {
        $userId = Auth::id();
        $mode = $request->query('mode', 'dine-in');
        $cart = $this->readJsonFile($userId, 'cart.json', []);
        $items = $this->readJsonFile($userId, 'menu.json', []);

        // Calculate totals
        $subtotal = 0;
        $cartItems = [];
        foreach ($cart as $cartIndex => $cartItem) {
            $item = array_values(array_filter($items, fn($i) => (int)$i['id'] === (int)$cartItem['itemId']))[0] ?? null;
            if ($item) {
                $itemTotal = $cartItem['price'] * $cartItem['quantity'];
                $subtotal += $itemTotal;
                $cartItems[] = array_merge($cartItem, [
                    'total' => $itemTotal,
                    'itemName' => $item['name'],
                    'cartIndex' => $cartIndex,
                ]);
            }
        }

        $tax = floor($subtotal * 0.1); // 10% tax
        $total = $subtotal + $tax;
        $cartCount = count($cartItems);

        return view('ordering.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'serviceFee' => 0,
            'total' => $total,
            'mode' => $mode,
            'cartSubtotal' => $subtotal,
            'cartCount' => $cartCount,
        ]);
    }

    public function updateCartQuantity(Request $request)
    {
        $userId = Auth::id();
        $request->validate([
            'cart_index' => 'required|integer|min:0',
            'action' => 'required|in:increase,decrease,set,remove',
            'quantity' => 'nullable|integer|min:1|max:99',
            'mode' => 'nullable|in:dine-in,take-out',
        ]);

        $cartIndex = (int)$request->input('cart_index');
        $action = $request->input('action');
        $mode = $request->input('mode', 'dine-in');
        $cart = $this->readJsonFile($userId, 'cart.json', []);

        if (!isset($cart[$cartIndex])) {
            return redirect()->route('ordering.cart', ['mode' => $mode])->with('error', 'Cart item not found.');
        }

        if ($action === 'remove') {
            unset($cart[$cartIndex]);
            $this->writeJsonFile($userId, 'cart.json', array_values($cart));
            return redirect()->route('ordering.cart', ['mode' => $mode])->with('success', 'Item removed from cart.');
        }

        $currentQuantity = (int)($cart[$cartIndex]['quantity'] ?? 1);

        if ($action === 'increase') {
            $newQuantity = $currentQuantity + 1;
        } elseif ($action === 'decrease') {
            $newQuantity = max(1, $currentQuantity - 1);
        } else {
            $newQuantity = (int)$request->input('quantity', $currentQuantity);
            $newQuantity = max(1, min(99, $newQuantity));
        }

        $cart[$cartIndex]['quantity'] = $newQuantity;
        $this->writeJsonFile($userId, 'cart.json', $cart);

        return redirect()->route('ordering.cart', ['mode' => $mode])->with('success', 'Quantity updated.');
    }

    public function checkout(Request $request)
    {
        $mode = $request->query('mode', 'dine-in');
        return view('ordering.checkout', ['mode' => $mode]);
    }

    public function placeOrder(Request $request)
    {
        $userId = Auth::id();
        $request->validate([
            'payment_method' => 'required|string',
            'agreement' => 'accepted',
            'mode' => 'required|in:dine-in,take-out',
        ]);

        $paymentMethod = $request->input('payment_method');
        $mode = $request->input('mode', 'dine-in');
        $cart = $this->readJsonFile($userId, 'cart.json', []);

        if (empty($cart)) {
            return redirect()->route('order.failure', ['mode' => $mode, 'error' => 'Your cart is empty. Please add items before checkout.']);
        }

        // Calculate total
        $total = 0;
        foreach ($cart as $cartItem) {
            $total += $cartItem['price'] * $cartItem['quantity'];
        }
        $tax = floor($total * 0.1);
        $finalTotal = $total + $tax;

        // Create order in database
        $order = Order::create([
            'user_id' => $userId,
            'status' => 'placed',
            'total_price' => $finalTotal,
            'order_mode' => $mode,
            'notes' => 'Payment: ' . $paymentMethod,
        ]);

        // Create order items
        $items = $this->readJsonFile($userId, 'menu.json', []);
        foreach ($cart as $cartItem) {
            $item = array_values(array_filter($items, fn($i) => (int)$i['id'] === (int)$cartItem['itemId']))[0] ?? null;
            if ($item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $cartItem['itemId'],
                    'quantity' => $cartItem['quantity'],
                    'price_at_purchase' => $cartItem['price'],
                ]);
            }
        }

        // Clear cart
        $this->writeJsonFile($userId, 'cart.json', []);

        return redirect()->route('order.success', ['order_id' => $order->id]);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id', 'N/A');
        $order = Order::find($orderId);

        return view('ordering.success', ['orderId' => $orderId, 'order' => $order]);
    }

    public function failure(Request $request)
    {
        $errorMessage = $request->query('error', 'An error occurred while processing your order.');
        $mode = $request->query('mode', 'dine-in');
        return view('ordering.failure', [
            'errorMessage' => $errorMessage,
            'mode' => $mode,
        ]);
    }

    public function addToCart(Request $request)
    {
        $userId = Auth::id();
        $itemId = $request->input('item_id');
        $quantity = $request->input('quantity', 1);
        $mode = $request->input('mode', 'dine-in');
        $variation = $request->input('variation', null);
        $addons = $request->input('addons', []);

        $items = $this->readJsonFile($userId, 'menu.json', []);
        $item = array_values(array_filter($items, fn($i) => (int)$i['id'] === (int)$itemId))[0] ?? null;

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found');
        }

        $cart = $this->readJsonFile($userId, 'cart.json', []);
        $cart[] = [
            'itemId' => $itemId,
            'quantity' => (int)$quantity,
            'price' => (float)$item['price'],
            'variation' => $variation,
            'addons' => is_array($addons) ? $addons : [],
        ];
        $this->writeJsonFile($userId, 'cart.json', $cart);

        return redirect()->route('ordering.menu', ['mode' => $mode])->with('success', 'Item added to cart!');
    }

    public function orderHistory()
    {
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)->with('items.menuItem')->orderBy('created_at', 'desc')->get();

        return view('ordering.order-history', [
            'orders' => $orders,
        ]);
    }
}
