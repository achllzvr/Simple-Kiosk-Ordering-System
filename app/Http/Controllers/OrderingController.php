<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderingController extends Controller
{
    private function getDummyDataPath(string $fileName): string
    {
        return base_path('dummy data/' . $fileName);
    }

    private function readJsonFile(string $fileName, array $fallback = []): array
    {
        $path = $this->getDummyDataPath($fileName);

        if (!file_exists($path)) {
            file_put_contents($path, json_encode($fallback, JSON_PRETTY_PRINT));
            return $fallback;
        }

        $content = file_get_contents($path);
        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : $fallback;
    }

    private function writeJsonFile(string $fileName, array $data): void
    {
        $path = $this->getDummyDataPath($fileName);
        file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT));
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
        $mode = $request->query('mode', 'dine-in');
        $search = $request->query('search', '');
        $items = $this->getMenuItems();
        $hasCart = count($this->getCartItemsFromFile()) > 0;
        $cartSubtotal = $this->calculateCartSubtotal();

        if ($search) {
            $items = array_filter($items, function ($item) use ($search) {
                return stripos($item['name'], $search) !== false;
            });
        }

        return view('ordering.menu', [
            'items' => array_values($items),
            'mode' => $mode,
            'search' => $search,
            'hasCart' => $hasCart,
            'cartSubtotal' => $cartSubtotal
        ]);
    }

    public function cart(Request $request)
    {
        $mode = $request->query('mode', 'dine-in');
        $cart = $this->getCartItemsFromFile();
        $items = $this->getMenuItems();

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
                    'cartIndex' => $cartIndex
                ]);
            }
        }

        $serviceFee = 0;
        $tax = floor($subtotal * 0.1); // 10% tax
        $total = $subtotal + $serviceFee + $tax;
        $cartCount = count($cartItems);

        return view('ordering.cart', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'serviceFee' => $serviceFee,
            'total' => $total,
            'mode' => $mode,
            'cartSubtotal' => $subtotal,
            'cartCount' => $cartCount
        ]);
    }

    public function updateCartQuantity(Request $request)
    {
        $request->validate([
            'cart_index' => 'required|integer|min:0',
            'action' => 'required|in:increase,decrease,set,remove',
            'quantity' => 'nullable|integer|min:1|max:99',
            'mode' => 'nullable|in:dine-in,take-out'
        ]);

        $cartIndex = (int)$request->input('cart_index');
        $action = $request->input('action');
        $mode = $request->input('mode', 'dine-in');
        $cart = $this->getCartItemsFromFile();

        if (!isset($cart[$cartIndex])) {
            return redirect()->route('ordering.cart', ['mode' => $mode])->with('error', 'Cart item not found.');
        }

        if ($action === 'remove') {
            unset($cart[$cartIndex]);
            $this->saveCartItemsToFile(array_values($cart));
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
        $this->saveCartItemsToFile($cart);

        return redirect()->route('ordering.cart', ['mode' => $mode])->with('success', 'Quantity updated.');
    }

    public function checkout(Request $request)
    {
        $mode = $request->query('mode', 'dine-in');
        return view('ordering.checkout', ['mode' => $mode]);
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'agreement' => 'accepted',
            'mode' => 'required|in:dine-in,take-out',
        ]);

        $paymentMethod = $request->input('payment_method');
        $mode = $request->input('mode', 'dine-in');
        $cart = $this->getCartItemsFromFile();

        $seatingOption = $request->input('seating_option', 'available');
        $address = $request->input('address');

        if ($mode === 'dine-in') {
            if ($seatingOption === 'unavailable') {
                $address = 'unavailable';
            } else {
                $request->validate([
                    'address' => 'required|string|max:255',
                ]);
            }
        } else {
            $address = $address ?: 'Take-Out at KFC Main Branch';
        }

        if (empty($cart)) {
            return redirect()->route('order.failure', ['mode' => $mode, 'error' => 'Your cart is empty. Please add items before checkout.']);
        }

        // Randomly decide success or failure for demo
        $success = rand(1, 2) === 1;

        if (!$success) {
            return redirect()->route('order.failure', ['mode' => $mode, 'error' => 'Payment processing failed. Please try again.']);
        }

        $orderId = 'ORD' . rand(100000, 999999);
        $orders = $this->readJsonFile('orders.json', []);
        $orders[] = [
            'order_id' => $orderId,
            'mode' => $mode,
            'payment_method' => $paymentMethod,
            'address' => $address,
            'items' => $cart,
            'created_at' => now()->toDateTimeString(),
            'status' => 'placed'
        ];
        $this->writeJsonFile('orders.json', $orders);

        $this->saveCartItemsToFile([]);
        return redirect()->route('order.success', ['order_id' => $orderId]);
    }

    public function success(Request $request)
    {
        $orderId = $request->query('order_id', 'N/A');
        return view('ordering.success', ['orderId' => $orderId]);
    }

    public function failure(Request $request)
    {
        $errorMessage = $request->query('error', 'An error occurred while processing your order.');
        $mode = $request->query('mode', 'dine-in');
        return view('ordering.failure', [
            'errorMessage' => $errorMessage,
            'mode' => $mode
        ]);
    }

    public function addToCart(Request $request)
    {
        $itemId = $request->input('item_id');
        $variation = $request->input('variation');
        $quantity = $request->input('quantity', 1);
        $addons = $request->input('addons', []);

        $items = $this->getMenuItems();
        $item = array_values(array_filter($items, fn($i) => $i['id'] == $itemId))[0] ?? null;

        if (!$item) {
            return redirect()->back()->with('error', 'Item not found');
        }

        // Calculate price
        $price = $item['price'];
        
        // Add variation price
        if ($variation) {
            $variationData = array_values(array_filter($item['variations'], fn($v) => $v['name'] === $variation))[0] ?? null;
            if ($variationData) {
                $price += $variationData['price'];
            }
        }

        // Add addons price
        $addonNames = [];
        if (is_array($addons)) {
            foreach ($addons as $addon) {
                $addonData = array_values(array_filter($item['addons'], fn($a) => $a['name'] === $addon))[0] ?? null;
                if ($addonData) {
                    $price += $addonData['price'];
                    $addonNames[] = $addon;
                }
            }
        }

        $cart = $this->getCartItemsFromFile();
        $cart[] = [
            'itemId' => $itemId,
            'variation' => $variation,
            'addons' => $addonNames,
            'quantity' => (int)$quantity,
            'price' => $price
        ];
        $this->saveCartItemsToFile($cart);

        $mode = $request->input('mode', 'dine-in');
        return redirect()->route('ordering.menu', ['mode' => $mode])->with('success', 'Item added to cart!');
    }
}
