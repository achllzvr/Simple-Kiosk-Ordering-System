<?php

namespace Tests\Feature;

use App\Contracts\PaymentGateway;
use App\Models\Cart;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\User;
use App\Services\OrderService;
use App\Services\PayMongoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderAndPayMongoFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_gateway_is_bound_to_paymongo_service(): void
    {
        $this->assertInstanceOf(PayMongoService::class, app(PaymentGateway::class));
    }

    public function test_guest_can_place_cash_order_end_to_end(): void
    {
        $item = MenuItem::query()->create([
            'name' => 'Classic Bucket',
            'description' => 'Test item',
            'category' => 'Chicken',
            'price' => 199.00,
            'image' => 'assets/images/KFC_logo_full_icon.png',
            'is_active' => true,
        ]);

        // Establish session before cart writes
        $this->get(route('ordering.selection'))->assertOk();

        $this->post(route('add-to-cart'), [
            'item_id' => $item->id,
            'quantity' => 2,
            'mode' => 'dine-in',
        ])->assertRedirect();

        $this->assertSame(1, Cart::query()->count());
        $this->assertSame(1, Cart::query()->first()->items()->count());

        $response = $this->post(route('place-order'), [
            'payment_method' => 'cash_payment',
            'agreement' => '1',
            'mode' => 'dine-in',
            'guest_name' => 'Test Guest',
            'guest_phone' => '09123456789',
            'guest_email' => 'guest@example.com',
            'address' => 'Table 4',
            'seating_option' => 'available',
        ]);

        $order = Order::query()->first();
        $this->assertNotNull($order);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame('placed', $order->status);
        $this->assertSame(1, $order->items()->count());

        $response->assertRedirect(route('order.success', [
            'order_id' => $order->id,
            'token' => $order->tracking_token,
        ]));
    }

    public function test_fulfill_online_payment_is_idempotent(): void
    {
        $order = Order::query()->create([
            'guest_name' => 'Guest',
            'guest_phone' => '09111111111',
            'tracking_token' => 'tokentokentokentokentokentoken12',
            'status' => 'placed',
            'payment_status' => 'unpaid',
            'payment_method' => 'credit_card',
            'total_price' => 100,
            'order_mode' => 'take-out',
        ]);

        $service = app(OrderService::class);
        $first = $service->fulfillOnlinePayment($order->id, 'pay_abc', 'cs_abc', 'pay_abc');
        $second = $service->fulfillOnlinePayment($order->id, 'pay_abc', 'cs_abc', 'pay_abc');

        $this->assertTrue($first['success']);
        $this->assertTrue($second['success']);
        $this->assertSame('Already paid.', $second['message']);
        $this->assertSame('paid', $order->fresh()->payment_status);
    }

    public function test_webhook_rejects_invalid_signature_when_secret_configured(): void
    {
        config([
            'paymongo.webhook_secret' => 'whsec_test_secret',
            'paymongo.enabled' => true,
            'paymongo.secret_key' => 'sk_test_x',
        ]);

        $this->postJson(route('paymongo.webhook'), [
            'data' => ['id' => 'evt_1', 'attributes' => ['type' => 'checkout_session.payment.paid']],
        ], [
            'Paymongo-Signature' => 't=1,te=invalid',
        ])->assertUnauthorized();
    }

    public function test_admin_can_access_dashboard_and_orders(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin@kiosk.test',
            'password' => 'password',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('admin.orders'))
            ->assertOk();
    }

    public function test_non_admin_cannot_login_to_admin_area(): void
    {
        User::factory()->create([
            'email' => 'customer@example.com',
            'password' => 'password',
            'role' => 'customer',
        ]);

        $this->post(route('login.submit'), [
            'email' => 'customer@example.com',
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
