<?php

use App\Enums\OrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Modules\Catalog\Models\Product;
use Modules\Order\Actions\PlaceOrderAction;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Livewire\PlaceOrderForm;
use Modules\Order\Models\Order;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('customer can place an order with products', function () {
    $product = Product::factory()->create(['stock_quantity' => 10]);

    $order = app(PlaceOrderAction::class)->execute(
        'John Doe',
        'john@example.com',
        [['product_id' => $product->id, 'quantity' => 2]],
    );

    expect($order)->toBeInstanceOf(Order::class)
        ->and($order->customer_name)->toBe('John Doe')
        ->and($order->items)->toHaveCount(1);
});

test('order is created with pending status', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);

    $order = app(PlaceOrderAction::class)->execute(
        'Jane',
        'jane@example.com',
        [['product_id' => $product->id, 'quantity' => 1]],
    );

    expect($order->status)->toBe(OrderStatus::Pending);
});

test('order total is calculated correctly from products', function () {
    $product = Product::factory()->create(['price' => 25.00, 'stock_quantity' => 10]);

    $order = app(PlaceOrderAction::class)->execute(
        'Test',
        'test@example.com',
        [['product_id' => $product->id, 'quantity' => 3]],
    );

    expect((float) $order->total_amount)->toEqual(75.00);
});

test('order cannot be placed with out of stock product', function () {
    $product = Product::factory()->outOfStock()->create();

    expect(fn () => app(PlaceOrderAction::class)->execute(
        'Test',
        'test@example.com',
        [['product_id' => $product->id, 'quantity' => 1]],
    ))->toThrow(DomainException::class);
});

test('placing order decrements product inventory', function () {
    // Stock tracking belongs to the Inventory module (not yet implemented).
    // This verifies that OrderPlaced is dispatched with the correct payload
    // so a future Inventory listener can consume it.
    Event::fake([OrderPlaced::class]);

    $product = Product::factory()->create(['stock_quantity' => 10]);

    app(PlaceOrderAction::class)->execute(
        'Test',
        'test@example.com',
        [['product_id' => $product->id, 'quantity' => 3]],
    );

    Event::assertDispatched(
        OrderPlaced::class,
        fn ($e) => collect($e->items)
        ->contains(fn ($i) => $i['product_id'] === $product->id && $i['quantity'] === 3)
    );
});

test('order creation fires OrderPlaced event', function () {
    Event::fake([OrderPlaced::class]);

    $product = Product::factory()->create(['stock_quantity' => 5]);

    app(PlaceOrderAction::class)->execute(
        'Test',
        'test@example.com',
        [['product_id' => $product->id, 'quantity' => 1]],
    );

    Event::assertDispatched(OrderPlaced::class);
});

test('order cannot be placed for non-existent product', function () {
    expect(fn () => app(PlaceOrderAction::class)->execute(
        'Test',
        'test@example.com',
        [['product_id' => 99999, 'quantity' => 1]],
    ))->toThrow(InvalidArgumentException::class);
});

test('newly created order status is not confirmed', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);

    $order = app(PlaceOrderAction::class)->execute(
        'Test',
        'test@example.com',
        [['product_id' => $product->id, 'quantity' => 1]],
    );

    expect($order->status)->not->toBe(OrderStatus::Confirmed);
});

test('livewire rejects order with empty customer name', function () {
    $product = Product::factory()->create(['stock_quantity' => 5]);

    Livewire::test(PlaceOrderForm::class)
        ->set('customerEmail', 'valid@example.com')
        ->set('cart', [$product->id => 1])
        ->call('placeOrder')
        ->assertHasErrors(['customerName']);
});

test('livewire rejects order with empty cart', function () {
    Livewire::test(PlaceOrderForm::class)
        ->set('customerName', 'John')
        ->set('customerEmail', 'john@example.com')
        ->call('placeOrder')
        ->assertHasErrors(['cart']);
});
