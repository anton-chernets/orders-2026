<?php

namespace Modules\Order\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Order';

    public function boot(): void
    {
        $this->routes(function () {
            Route::middleware('web')
                ->namespace($this->namespace ?? '')
                ->group(module_path($this->name, '/routes/web.php'));
        });
    }
}
