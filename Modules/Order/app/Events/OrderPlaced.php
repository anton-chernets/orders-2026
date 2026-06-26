<?php

namespace Modules\Order\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderPlaced
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param int $orderId
     * @param array<array{product_id: int, quantity: int}> $items
     */
    public function __construct(
        public readonly int $orderId,
        public readonly array $items,
    ) {
    }
}
