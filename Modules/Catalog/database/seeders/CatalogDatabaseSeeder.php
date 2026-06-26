<?php

namespace Modules\Catalog\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

class CatalogDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::factory(5)->create();

        $categories->each(function (Category $category) {
            Product::factory(8)->create(['category_id' => $category->id]);
        });
    }
}
