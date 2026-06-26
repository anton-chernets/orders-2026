<?php

namespace App\Models;

use App\Enums\AuditAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $fillable = [
        'entity',
        'entity_id',
        'action',
        'changes',
        'user_id',
    ];

    public static function record(string $entity, int $entityId, AuditAction $action, array $changes = []): void
    {
        self::create([
            'entity' => $entity,
            'entity_id' => $entityId,
            'action' => $action,
            'changes' => $changes ?: null,
            'user_id' => auth()->id(),
        ]);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'changes' => 'array',
        ];
    }
}
