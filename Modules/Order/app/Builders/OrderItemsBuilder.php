<?php

namespace Modules\Order\Builders;

use App\DataTransferObjects\Catalog\ProductData;
use Modules\Order\DataTransferObjects\OrderPayload;

class OrderItemsBuilder
{
    /**
     * @param array<array{product_id: int, quantity: int}> $cartItems
     * @param array<array-key, mixed> $dtoIndex
     */
    public function build(array $cartItems, array $dtoIndex): OrderPayload
    {
        $total = 0.0;
        $items = [];

        foreach ($cartItems as $item) {
            /** @var ProductData $dto */
            $dto = $dtoIndex[$item['product_id']];
            $subtotal = round($dto->price * $item['quantity'], 2);
            $total += $subtotal;

            $items[] = [
                'product_id' => $dto->id,
                'product_name' => $dto->name,
                'product_price' => $dto->price,
                'quantity' => $item['quantity'],
                'subtotal' => $subtotal,
            ];
        }

        return new OrderPayload(round($total, 2), $items);
    }
}
