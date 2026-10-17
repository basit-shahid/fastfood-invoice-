<?php

namespace Tests\Feature;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function item(array $attributes = []): MenuItem
    {
        return MenuItem::create(array_merge([
            'name' => 'Classic Burger', 'category' => 'Burgers', 'price' => 250,
            'is_available' => true, 'preparation_time' => 5,
        ], $attributes));
    }

    private function checkout(array $items, array $payment = [])
    {
        return $this->postJson(route('orders.store'), array_merge([
            'items' => $items, 'payment_method' => 'cash', 'cash_received' => 2000,
        ], $payment));
    }

    public function test_repeated_products_and_identical_orders_are_accepted(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $item = $this->item();
        $items = [
            ['id' => $item->id, 'quantity' => 1, 'instructions' => 'No onions'],
            ['id' => $item->id, 'quantity' => 2, 'instructions' => 'Extra spicy'],
        ];
        $first = $this->checkout($items)->assertOk()->assertJsonPath('success', true);
        $second = $this->checkout($items)->assertOk()->assertJsonPath('success', true);
        $this->assertNotSame($first->json('order.invoice_number'), $second->json('order.invoice_number'));
        $this->assertDatabaseCount('orders', 2);
        $this->assertDatabaseCount('order_items', 4);
        foreach (Order::all() as $order) {
            $this->assertSame('750.00', $order->total);
            $this->assertSame('1250.00', $order->change_amount);
            $this->assertNotNull($order->completed_at);
            $this->assertSame(['No onions', 'Extra spicy'], $order->items()->orderBy('id')->pluck('special_instructions')->all());
        }
    }

    public function test_receipt_generation_does_not_reuse_the_next_number_before_an_order_is_saved(): void
    {
        $this->assertNotSame(Order::generateInvoiceNumber(), Order::generateInvoiceNumber());
    }

    public function test_multiple_lines_discount_tax_and_decimal_cash_use_database_prices(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $one = $this->item(['price' => '100.10']);
        $two = $this->item(['name' => 'Drink', 'price' => '20.20']);
        $response = $this->checkout([
            ['id' => $one->id, 'quantity' => 3, 'price' => 1],
            ['id' => $two->id, 'quantity' => 2],
        ], ['discount' => '10.05', 'tax' => '5.25', 'cash_received' => '400.00'])->assertOk();
        $this->assertSame('340.70', $response->json('order.subtotal'));
        $this->assertSame('335.90', $response->json('order.total'));
        $this->assertSame('64.10', $response->json('order.change_amount'));
    }

    public function test_card_and_online_orders_need_no_cash_amount(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $item = $this->item();
        foreach (['card', 'online'] as $method) {
            $this->checkout([['id' => $item->id, 'quantity' => 1]], [
                'payment_method' => $method, 'cash_received' => null,
            ])->assertOk()->assertJsonPath('order.cash_received', null)->assertJsonPath('order.change_amount', null);
        }
        $this->assertDatabaseCount('orders', 2);
    }

    public function test_invalid_payments_and_empty_orders_do_not_write_partial_orders(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $item = $this->item();
        $items = [['id' => $item->id, 'quantity' => 1]];
        $this->checkout([])->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->checkout($items, ['cash_received' => 100])->assertUnprocessable()->assertJsonValidationErrors('cash_received');
        $this->checkout($items, ['discount' => -1])->assertUnprocessable()->assertJsonValidationErrors('discount');
        $this->checkout($items, ['discount' => 251])->assertUnprocessable()->assertJsonValidationErrors('discount');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_unavailable_product_rolls_back_the_entire_order(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $available = $this->item();
        $unavailable = $this->item(['name' => 'Sold out', 'is_available' => false]);
        $this->checkout([
            ['id' => $available->id, 'quantity' => 1],
            ['id' => $unavailable->id, 'quantity' => 1],
        ])->assertUnprocessable()->assertJsonValidationErrors('items.1.id');
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_a_failed_line_insert_rolls_back_and_explicitly_allows_a_safe_retry(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $item = $this->item();
        $attempts = 0;
        Event::listen('eloquent.creating: '.OrderItem::class, function () use (&$attempts) {
            if (++$attempts === 2) {
                throw new \RuntimeException('Simulated line write failure');
            }
        });
        try {
            $items = [['id' => $item->id, 'quantity' => 1], ['id' => $item->id, 'quantity' => 1]];
            $this->checkout($items)->assertStatus(500)->assertJsonPath('order_saved', false);
            $this->assertDatabaseCount('orders', 0);
            $this->assertDatabaseCount('order_items', 0);
        } finally {
            Event::forget('eloquent.creating: '.OrderItem::class);
        }
        $this->checkout($items)->assertOk();
        $this->assertDatabaseCount('orders', 1);
        $this->assertDatabaseCount('order_items', 2);
    }

    public function test_browser_form_checkout_redirects_to_history_and_displays_the_saved_order(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $item = $this->item();
        $response = $this->post(route('orders.store'), [
            'items' => [['id' => $item->id, 'quantity' => 2]],
            'payment_method' => 'cash', 'cash_received' => '600.00', 'discount' => 0,
        ]);
        $order = Order::sole();
        $response->assertRedirect(route('orders.history', ['order' => $order->id]));
        $this->get(route('orders.history', ['order' => $order->id]))
            ->assertOk()->assertSee($order->invoice_number)->assertSee('Just saved')->assertSee($item->name);
        $this->assertSame('500.00', $order->total);
        $this->assertSame('100.00', $order->change_amount);
    }

    public function test_json_checkout_returns_a_saved_order_that_is_visible_in_history(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'cashier']));
        $item = $this->item();
        $response = $this->checkout([['id' => $item->id, 'quantity' => 1]])->assertOk();
        $this->get(route('orders.history', ['order' => $response->json('order.id')]))
            ->assertOk()->assertSee($response->json('order.invoice_number'))->assertSee('Just saved');
    }
}
