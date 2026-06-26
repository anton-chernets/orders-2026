<?php

namespace Modules\Order\Models;

use App\Enums\OrderStatus;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Modules\Order\Database\Factories\OrderFactory;

/**
 * @property int $id
 * @property string $customer_name
 * @property string $customer_email
 * @property float $total_amount
 * @property OrderStatus $status
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_name',
        'customer_email',
        'total_amount',
        'status',
    ];

    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transitionTo(OrderStatus $newStatus): void
    {
        if (! $this->status->canTransitionTo($newStatus)) {
            Log::error('Invalid order status transition attempted', [
                'order_id' => $this->id,
                'from' => $this->status->value,
                'to' => $newStatus->value,
            ]);
            throw new DomainException(
                "Cannot transition from {$this->status->value} to {$newStatus->value}"
            );
        }

        $this->update(['status' => $newStatus]);
    }

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'total_amount' => 'decimal:2',
        ];
    }
}
