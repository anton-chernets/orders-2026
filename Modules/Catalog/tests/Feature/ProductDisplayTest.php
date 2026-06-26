<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Catalog\Models\Product;
use Modules\Order\Livewire\PlaceOrderForm;

uses(Tests\TestCase::class, RefreshDatabase::class);

test('product list is visible to guests', function () {
    Product::factory(3)->create();

    $this->get(route('catalog.products.index'))
        ->assertOk()
        ->assertViewIs('catalog::products.index');
});

test('product detail page shows correct information', function () {
    $product = Product::factory()->create([
        'name' => 'Test Widget',
        'price' => 49.99,
        'description' => 'A great widget',
    ]);

    $this->get(route('catalog.products.show', $product))
        ->assertOk()
        ->assertSee('Test Widget')
        ->assertSee('49.99')
        ->assertSee('A great widget');
});

test('out of stock products are marked correctly', function () {
    Product::factory()->outOfStock()->create(['name' => 'Sold Out Item']);

    $this->get(route('catalog.products.index'))
        ->assertOk()
        ->assertSee('Out of stock');
});

test('livewire product list component renders', function () {
    $product = Product::factory()->create(['name' => 'Visible Product']);

    Livewire::test(PlaceOrderForm::class)
        ->assertSee('Visible Product');
});

test('livewire product search filters results', function () {
    Product::factory()->create(['name' => 'Available Item']);
    Product::factory()->outOfStock()->create(['name' => 'Sold Out Item']);

    Livewire::test(PlaceOrderForm::class)
        ->assertSee('Available Item')
        ->assertDontSee('Sold Out Item');
});

test('non-existent product returns 404 not 200', function () {
    $this->get(route('catalog.products.show', ['product' => 99999]))
        ->assertNotFound();
});

test('unauthenticated user is redirected from admin panel not shown 200', function () {
    $this->get('/admin/products')
        ->assertStatus(302)
        ->assertRedirect('/admin/login');
});
