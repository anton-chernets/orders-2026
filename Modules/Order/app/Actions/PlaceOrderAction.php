<?php

namespace Modules\Order\Actions;

use App\Contracts\Catalog\ProductRepositoryInterface;
use App\DataTransferObjects\Catalog\ProductData;
use App\Enums\OrderStatus;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Models\Order;

class PlaceOrderAction
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {
    }

    /**
     * @param array<array{product_id: int, quantity: int}> $cartItems
     * @throws \Throwable
     */
    public function execute(string $customerName, string $customerEmail, array $cartItems): Order
    {
        $productIds = array_column($cartItems, 'product_id');
        $dtos = $this->products->findMany($productIds);
        $dtoIndex = array_column($dtos, null, 'id');

        foreach ($cartItems as $item) {
            $dto = $dtoIndex[$item['product_id']] ?? null;

            if ($dto === null) {
                Log::error('Product not found during order placement', [
                    'product_id' => $item['product_id'],
                    'customer_email' => $customerEmail,
                ]);
                throw new InvalidArgumentException("Product {$item['product_id']} not found.");
            }

            if (! $dto->inStock) {
                Log::error('Attempted to order out-of-stock product', [
                    'product_id' => $dto->id,
                    'product_name' => $dto->name,
                    'customer_email' => $customerEmail,
                ]);
                throw new DomainException("Product \"{$dto->name}\" is out of stock.");
            }
        }

        return DB::transaction(function () use ($customerName, $customerEmail, $cartItems, $dtoIndex): Order {
            $total = 0.0;
            $itemsPayload = [];

            foreach ($cartItems as $item) {
                /** @var ProductData $dto */
                $dto = $dtoIndex[$item['product_id']];
                $subtotal = round($dto->price * $item['quantity'], 2);
                $total += $subtotal;

                $itemsPayload[] = [
                    'product_id' => $dto->id,
                    'product_name' => $dto->name,
                    'product_price' => $dto->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::create([
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'total_amount' => round($total, 2),
                'status' => OrderStatus::Pending,
            ]);

            $order->items()->createMany($itemsPayload);

            OrderPlaced::dispatch(
                $order->id,
                array_map(fn ($i) => ['product_id' => $i['product_id'], 'quantity' => $i['quantity']], $itemsPayload),
            );

            return $order;
        });
    }
}
