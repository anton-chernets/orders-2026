<?php

namespace Modules\Catalog\Providers;

use App\Contracts\Catalog\ProductRepositoryInterface;
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
}
