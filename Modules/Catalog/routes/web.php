<?php

use Illuminate\Support\Facades\Route;
use Modules\Catalog\Http\Controllers\CatalogController;

Route::middleware('web')->prefix('products')->name('catalog.products.')->group(function () {
    Route::get('/', [CatalogController::class, 'index'])->name('index');
    Route::get('{product}', [CatalogController::class, 'show'])->name('show');
});
