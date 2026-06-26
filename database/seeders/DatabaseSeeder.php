<?php

namespace Database\Seeders;

use App\Models\SeedVersion;
use Illuminate\Database\Seeder;
use Modules\Catalog\Database\Seeders\CatalogDatabaseSeeder;
use Modules\Order\Database\Seeders\OrderDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->applyVersioned('catalog_v1', CatalogDatabaseSeeder::class);
        $this->applyVersioned('order_v1', OrderDatabaseSeeder::class);
    }

    private function applyVersioned(string $version, string $seederClass): void
    {
        if (SeedVersion::hasRun($version)) {
            $this->command?->line("  Skipping {$version} (already applied)");

            return;
        }

        $this->call($seederClass);
        SeedVersion::markAsRun($version, $seederClass);
        $this->command?->info("  Applied {$version}");
    }
}
