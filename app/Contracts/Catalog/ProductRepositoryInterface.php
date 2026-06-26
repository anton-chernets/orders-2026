<?php

namespace App\Contracts\Catalog;

use App\DataTransferObjects\Catalog\ProductData;

interface ProductRepositoryInterface
{
    public function findById(int $id): ProductData;

    /** @return ProductData[] */
    public function findMany(array $ids): array;

    /** @return ProductData[] */
    public function listAvailable(): array;

    /** @return ProductData[] */
    public function listAll(): array;
}
