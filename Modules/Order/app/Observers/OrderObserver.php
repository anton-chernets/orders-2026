<?php

namespace Modules\Order\Observers;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use Modules\Order\Models\Order;

class OrderObserver
{
    public function created(Order $order): void
    {
        AuditLog::record('order', $order->id, AuditAction::Created, $this->initialAttributes($order));
    }

    public function updated(Order $order): void
    {
        AuditLog::record('order', $order->id, AuditAction::Updated, $this->changedAttributes($order));
    }

    private function initialAttributes(Order $order): array
    {
        return collect($order->getAttributes())
            ->except(['id', 'created_at', 'updated_at'])
            ->toArray();
    }

    private function changedAttributes(Order $order): array
    {
        return collect($order->getChanges())
            ->except(['updated_at'])
            ->toArray();
    }
}
