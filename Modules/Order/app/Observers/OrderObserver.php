<?php

namespace Modules\Order\Observers;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use Modules\Order\Models\Order;

class OrderObserver
{
    public function updated(Order $order): void
    {
        AuditLog::record('order', $order->id, AuditAction::Updated, $this->changedAttributes($order));
    }

    private function changedAttributes(Order $order): array
    {
        return collect($order->getChanges())
            ->except(['updated_at'])
            ->toArray();
    }
}
