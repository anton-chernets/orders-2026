<?php

namespace Modules\Catalog\Observers;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use Modules\Catalog\Models\Category;

class CategoryObserver
{
    public function created(Category $category): void
    {
        AuditLog::record('category', $category->id, AuditAction::Created, $this->initialAttributes($category));
    }

    public function updated(Category $category): void
    {
        AuditLog::record('category', $category->id, AuditAction::Updated, $this->changedAttributes($category));
    }

    public function deleted(Category $category): void
    {
        AuditLog::record('category', $category->id, AuditAction::Deleted);
    }

    private function initialAttributes(Category $category): array
    {
        return collect($category->getAttributes())
            ->except(['id', 'created_at', 'updated_at'])
            ->toArray();
    }

    private function changedAttributes(Category $category): array
    {
        return collect($category->getChanges())
            ->except(['updated_at'])
            ->toArray();
    }
}
