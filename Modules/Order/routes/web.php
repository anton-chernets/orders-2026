<?php

use Illuminate\Support\Facades\Route;
use Modules\Order\Http\Controllers\OrderController;

Route::middleware('web')->prefix('orders')->name('order.')->group(function () {
    Route::get('create', [OrderController::class, 'create'])->name('create');
    Route::get('{order}', [OrderController::class, 'show'])->name('show');
});
