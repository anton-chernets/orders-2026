<?php

namespace Modules\Order\Database\Seeders;

use App\Contracts\Catalog\ProductRepositoryInterface;
use App\DataTransferObjects\Catalog\ProductData;
use App\Enums\OrderStatus;
use Illuminate\Database\Seeder;
use Modules\Order\Models\Order;

class OrderDatabaseSeeder extends Seeder
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {
    }

    public function run(): void
    {
        $available = $this->products->listAvailable();

        if (empty($available)) {
            $this->command->warn('No products available. Run CatalogDatabaseSeeder first.');
            return;
        }

        $statuses = [OrderStatus::Pending, OrderStatus::Confirmed, OrderStatus::Shipped, OrderStatus::Delivered];

        for ($i = 0; $i < 20; $i++) {
            $order = Order::factory()->create([
                'status' => $statuses[array_rand($statuses)],
            ]);

            $selected = array_slice($available, 0, rand(1, 3));
            $total = 0.0;

            foreach ($selected as $product) {
                /** @var ProductData $product */
                $qty = rand(1, 5);
                $subtotal = round($product->price * $qty, 2);
                $total += $subtotal;

                $order->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $qty,
                    'subtotal' => $subtotal,
                ]);
            }

            $order->update(['total_amount' => round($total, 2)]);
        }
    }
}
