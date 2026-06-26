<?php

namespace Modules\Catalog\Observers;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use Modules\Catalog\Models\Product;

class ProductObserver
{
    public function created(Product $product): void
    {
        AuditLog::record('product', $product->id, AuditAction::Created, $this->initialAttributes($product));
    }

    public function updated(Product $product): void
    {
        AuditLog::record('product', $product->id, AuditAction::Updated, $this->changedAttributes($product));
    }

    public function deleted(Product $product): void
    {
        AuditLog::record('product', $product->id, AuditAction::Deleted);
    }

    private function initialAttributes(Product $product): array
    {
        return collect($product->getAttributes())
            ->except(['id', 'created_at', 'updated_at'])
            ->toArray();
    }

    private function changedAttributes(Product $product): array
    {
        return collect($product->getChanges())
            ->except(['updated_at'])
            ->toArray();
    }
}
