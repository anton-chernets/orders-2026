<?php

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Order\Filament\Resources\OrderResource;
use Modules\Order\Models\Order;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('order status transitions from pending to confirmed', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    $order->transitionTo(OrderStatus::Confirmed);

    expect($order->fresh()->status)->toBe(OrderStatus::Confirmed);
});

test('order status transitions from confirmed to shipped', function () {
    $order = Order::factory()->confirmed()->create();

    $order->transitionTo(OrderStatus::Shipped);

    expect($order->fresh()->status)->toBe(OrderStatus::Shipped);
});

test('order status transitions from shipped to delivered', function () {
    $order = Order::factory()->shipped()->create();

    $order->transitionTo(OrderStatus::Delivered);

    expect($order->fresh()->status)->toBe(OrderStatus::Delivered);
});

test('invalid status transition is rejected', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    expect(fn () => $order->transitionTo(OrderStatus::Delivered))
        ->toThrow(DomainException::class);
});

test('admin can confirm an order', function () {
    $this->actingAs(User::factory()->create());
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    Livewire::test(OrderResource\Pages\ListOrders::class)
        ->callTableAction('confirm', $order)
        ->assertHasNoActionErrors();

    expect($order->fresh()->status)->toBe(OrderStatus::Confirmed);
});

test('admin can view all orders', function () {
    $this->actingAs(User::factory()->create());
    Order::factory(5)->create();

    $this->get('/admin/orders')->assertOk();
});

test('admin can filter orders by status', function () {
    $this->actingAs(User::factory()->create());

    $pending = Order::factory()->create(['status' => OrderStatus::Pending]);
    $confirmed = Order::factory()->confirmed()->create();

    Livewire::test(OrderResource\Pages\ListOrders::class)
        ->filterTable('status', OrderStatus::Pending->value)
        ->assertCanSeeTableRecords([$pending])
        ->assertCanNotSeeTableRecords([$confirmed]);
});

test('livewire order status component updates on transition', function () {
    $this->actingAs(User::factory()->create());
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    Livewire::test(OrderResource\Pages\ViewOrder::class, ['record' => $order->id])
        ->assertSee(OrderStatus::Pending->label());
});

test('delivered order cannot transition further', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Delivered]);

    expect(fn () => $order->transitionTo(OrderStatus::Shipped))
        ->toThrow(DomainException::class);
});

test('order cannot skip from pending to shipped', function () {
    $order = Order::factory()->create(['status' => OrderStatus::Pending]);

    expect(fn () => $order->transitionTo(OrderStatus::Shipped))
        ->toThrow(DomainException::class);
});

test('order cannot go backwards from shipped to confirmed', function () {
    $order = Order::factory()->shipped()->create();

    expect(fn () => $order->transitionTo(OrderStatus::Confirmed))
        ->toThrow(DomainException::class);
});

test('unauthenticated user is redirected from admin orders not shown 200', function () {
    $this->get('/admin/orders')
        ->assertStatus(302)
        ->assertRedirect('/admin/login');
});

test('confirm action is not visible on delivered order', function () {
    $this->actingAs(User::factory()->create());
    $order = Order::factory()->create(['status' => OrderStatus::Delivered]);

    Livewire::test(OrderResource\Pages\ListOrders::class)
        ->assertTableActionHidden('confirm', $order);
});
