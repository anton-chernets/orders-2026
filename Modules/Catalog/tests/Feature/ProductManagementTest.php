<?php

use App\Models\User;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Modules\Catalog\Filament\Resources\ProductResource;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('admin can view product list', function () {
    Product::factory(3)->create();

    $this->get('/admin/products')->assertOk();
});

test('admin can create a product', function () {
    $category = Category::factory()->create();

    Livewire::test(ProductResource\Pages\CreateProduct::class)
        ->fillForm([
            'category_id' => $category->id,
            'name' => 'Brand New Product',
            'price' => 29.99,
            'stock_quantity' => 50,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $this->assertDatabaseHas('products', ['name' => 'Brand New Product']);
});

test('admin can update a product', function () {
    $product = Product::factory()->create(['name' => 'Old Name']);

    Livewire::test(ProductResource\Pages\EditProduct::class, ['record' => $product->id])
        ->fillForm(['name' => 'Updated Name'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($product->fresh()->name)->toBe('Updated Name');
});

test('admin can delete a product', function () {
    $product = Product::factory()->create();

    Livewire::test(ProductResource\Pages\ListProducts::class)
        ->callTableAction(DeleteAction::class, $product);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('product requires name and price', function () {
    $category = Category::factory()->create();

    Livewire::test(ProductResource\Pages\CreateProduct::class)
        ->fillForm(['category_id' => $category->id, 'name' => '', 'price' => ''])
        ->call('create')
        ->assertHasFormErrors(['name', 'price']);
});

test('admin cannot create product with negative price', function () {
    $category = Category::factory()->create();

    Livewire::test(ProductResource\Pages\CreateProduct::class)
        ->fillForm([
            'category_id' => $category->id,
            'name' => 'Valid Name',
            'price' => -5,
            'stock_quantity' => 10,
        ])
        ->call('create')
        ->assertHasFormErrors(['price']);
});

test('admin cannot create product with negative stock quantity', function () {
    $category = Category::factory()->create();

    Livewire::test(ProductResource\Pages\CreateProduct::class)
        ->fillForm([
            'category_id' => $category->id,
            'name' => 'Valid Name',
            'price' => 10.00,
            'stock_quantity' => -3,
        ])
        ->call('create')
        ->assertHasFormErrors(['stock_quantity']);
});

test('unauthenticated user cannot access admin product list', function () {
    auth()->logout();

    $this->get('/admin/products')
        ->assertStatus(302)
        ->assertRedirect('/admin/login');
});
