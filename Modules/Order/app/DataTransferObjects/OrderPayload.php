<?php

namespace Modules\Order\DataTransferObjects;

readonly class OrderPayload
{
    /**
     * @param array<int, array{product_id: int, product_name: string, product_price: float|string, quantity: int, subtotal: float}> $items
     */
    public function __construct(
        public float $total,
        public array $items,
    ) {
    }

    /**
     * @return array<int, array{product_id: int, quantity: int}>
     */
    public function productQuantities(): array
    {
        return array_map(
            fn (array $item) => ['product_id' => $item['product_id'], 'quantity' => $item['quantity']],
            $this->items,
        );
    }
}
