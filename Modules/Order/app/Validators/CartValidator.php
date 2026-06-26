<?php

namespace Modules\Order\Validators;

use App\DataTransferObjects\Catalog\ProductData;
use DomainException;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class CartValidator
{
    /**
     * @param array<array{product_id: int, quantity: int}> $cartItems
     * @param array<array-key, mixed> $dtoIndex
     */
    public function validate(array $cartItems, array $dtoIndex, string $customerEmail): void
    {
        foreach ($cartItems as $item) {
            /** @var ProductData|null $dto */
            $dto = $dtoIndex[$item['product_id']] ?? null;

            if ($dto === null) {
                Log::channel('products')->error('Product not found during order placement', [
                    'product_id' => $item['product_id'],
                    'customer_email' => $customerEmail,
                ]);
                throw new InvalidArgumentException("Product {$item['product_id']} not found.");
            }

            if (! $dto->inStock) {
                Log::channel('products')->error('Attempted to order out-of-stock product', [
                    'product_id' => $dto->id,
                    'product_name' => $dto->name,
                    'customer_email' => $customerEmail,
                ]);
                throw new DomainException("Product \"{$dto->name}\" is out of stock.");
            }
        }
    }
}
