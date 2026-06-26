<?php

namespace Modules\Catalog\Http\Controllers;

use App\Contracts\Catalog\ProductRepositoryInterface;
use Illuminate\View\View;
use Modules\Catalog\Models\Product;

class CatalogController extends Controller
{
    public function __construct(
        private readonly ProductRepositoryInterface $products,
    ) {
    }

    public function index(): View
    {
        $products = $this->products->listAll();

        return view('catalog::products.index', compact('products'));
    }

    public function show(Product $product): View
    {
        return view('catalog::products.show', compact('product'));
    }
}
