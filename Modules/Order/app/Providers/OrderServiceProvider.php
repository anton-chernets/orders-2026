<?php

namespace Modules\Order\Providers;

use Livewire\Livewire;
use Modules\Order\Livewire\PlaceOrderForm;
use Modules\Order\Models\Order;
use Modules\Order\Observers\OrderObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class OrderServiceProvider extends ModuleServiceProvider
{
    protected string $name = 'Order';
    protected string $nameLower = 'order';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Livewire::component('order::place-order-form', PlaceOrderForm::class);
        Order::observe(OrderObserver::class);
    }
}
