<?php

namespace Modules\Catalog\Repositories;

use App\Contracts\Catalog\ProductRepositoryInterface;
use App\DataTransferObjects\Catalog\ProductData;
use Modules\Catalog\Models\Product;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function findById(int $id): ProductData
    {
        return $this->toDto(Product::findOrFail($id));
    }

    public function findMany(array $ids): array
    {
        return Product::whereIn('id', $ids)
            ->get()
            ->map(fn (Product $p) => $this->toDto($p))
            ->all();
    }

    public function listAvailable(): array
    {
        return Product::where('stock_quantity', '>', 0)
            ->with('category')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => $this->toDto($p))
            ->all();
    }

    public function listAll(): array
    {
        return Product::with('category')
            ->orderBy('name')
            ->get()
            ->map(fn (Product $p) => $this->toDto($p))
            ->all();
    }

    private function toDto(Product $product): ProductData
    {
        return new ProductData(
            id: $product->id,
            name: $product->name,
            price: $product->price,
            stockQuantity: $product->stock_quantity,
            inStock: $product->stock_quantity > 0,
        );
    }
}
