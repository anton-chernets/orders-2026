<?php

namespace Modules\Catalog\Providers;

use App\Contracts\Catalog\ProductRepositoryInterface;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Observers\CategoryObserver;
use Modules\Catalog\Observers\ProductObserver;
use Modules\Catalog\Repositories\EloquentProductRepository;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CatalogServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Catalog';
    protected string $nameLower = 'catalog';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->bind(
            ProductRepositoryInterface::class,
            EloquentProductRepository::class,
        );
    }

    public function boot(): void
    {
        parent::boot();

        Product::observe(ProductObserver::class);
        Category::observe(CategoryObserver::class);
    }
}
