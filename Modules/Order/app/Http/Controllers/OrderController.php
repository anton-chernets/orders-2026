<?php

namespace Modules\Order\Http\Controllers;

use Illuminate\View\View;
use Modules\Order\Models\Order;

class OrderController extends Controller
{
    public function create(): View
    {
        return view('order::orders.create');
    }

    public function show(Order $order): View
    {
        $order->load('items');

        return view('order::orders.show', compact('order'));
    }
}
