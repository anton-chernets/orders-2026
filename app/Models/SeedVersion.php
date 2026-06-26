<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property string $version
 * @property string $seeder_class
 * @property Carbon $applied_at
 */
class SeedVersion extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'version',
        'seeder_class',
        'applied_at',
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];

    public static function hasRun(string $version): bool
    {
        return self::where('version', $version)->exists();
    }

    public static function markAsRun(string $version, string $seederClass): void
    {
        self::create([
            'version' => $version,
            'seeder_class' => $seederClass,
            'applied_at' => now(),
        ]);
    }
}
