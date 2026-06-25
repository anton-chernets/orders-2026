<?php

use Modules\Catalog\Models\Product;
use Modules\Order\Models\Order;

// Order creation workflow and cross-module functionality

test('customer can place an order with products', function () {
    // TODO: implement
})->todo();

test('order is created with pending status', function () {
    // TODO: implement
})->todo();

test('order total is calculated correctly from products', function () {
    // TODO: implement
})->todo();

test('order cannot be placed with out of stock product', function () {
    // TODO: implement
})->todo();

test('placing order decrements product inventory', function () {
    // TODO: cross-module via event: OrderPlaced -> DecrementStock
})->todo();

test('order creation fires OrderPlaced event', function () {
    // TODO: implement
})->todo();
