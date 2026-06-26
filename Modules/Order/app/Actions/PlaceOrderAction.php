<?php

namespace Modules\Order\Actions;

use App\Contracts\Catalog\ProductRepositoryInterface;
use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Modules\Order\Builders\OrderItemsBuilder;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Models\Order;
use Modules\Order\Validators\CartValidator;

readonly class PlaceOrderAction
{
    public function __construct(
        private ProductRepositoryInterface $products,
        private CartValidator $cartValidator,
        private OrderItemsBuilder $orderItemsBuilder,
    ) {
    }

    /**
     * @param array<array{product_id: int, quantity: int}> $cartItems
     * @throws \Throwable
     */
    public function execute(string $customerName, string $customerEmail, array $cartItems): Order
    {
        $dtoIndex = $this->resolveProducts($cartItems);

        $this->cartValidator->validate($cartItems, $dtoIndex, $customerEmail);

        $payload = $this->orderItemsBuilder->build($cartItems, $dtoIndex);

        return DB::transaction(function () use ($customerName, $customerEmail, $payload): Order {
            $order = Order::create([
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'total_amount' => $payload->total,
                'status' => OrderStatus::Pending,
            ]);

            $order->items()->createMany($payload->items);

            OrderPlaced::dispatch($order->id, $payload->productQuantities());

            return $order;
        });
    }

    /**
     * @param array<array{product_id: int, quantity: int}> $cartItems
     * @return array<array-key, mixed>
     */
    private function resolveProducts(array $cartItems): array
    {
        $productIds = array_column($cartItems, 'product_id');
        $dtos = $this->products->findMany($productIds);

        return array_column($dtos, null, 'id');
    }
}
