<?php

use App\Enums\OrderStatus;
use Modules\Order\Models\Order;

// Order lifecycle management and admin interface interactions

test('order status transitions from pending to confirmed', function () {
    // TODO: implement using OrderStatus::canTransitionTo()
})->todo();

test('order status transitions from confirmed to shipped', function () {
    // TODO: implement
})->todo();

test('order status transitions from shipped to delivered', function () {
    // TODO: implement
})->todo();

test('invalid status transition is rejected', function () {
    // TODO: e.g. pending -> delivered should fail
})->todo();

test('admin can confirm an order', function () {
    // TODO: implement via Filament admin interface
})->todo();

test('admin can view all orders', function () {
    // TODO: implement via Filament admin interface
})->todo();

test('admin can filter orders by status', function () {
    // TODO: implement
})->todo();

test('livewire order status component updates on transition', function () {
    // TODO: implement
})->todo();
