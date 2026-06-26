@extends('layouts.app')

@section('title', 'Order #'.$order->id)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 py-10">

    <div class="text-center mb-10">
        <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-slate-900">Order #{{ $order->id }}</h1>
        <p class="text-slate-500 mt-1">Thank you, {{ $order->customer_name }}!</p>
        <div class="mt-3">
            @php
                $colors = [
                    'success' => 'bg-emerald-50 text-emerald-700',
                    'info'    => 'bg-blue-50 text-blue-700',
                    'warning' => 'bg-amber-50 text-amber-700',
                    'primary' => 'bg-indigo-50 text-indigo-700',
                ];
                $colorClass = $colors[$order->status->color()] ?? 'bg-slate-100 text-slate-600';
            @endphp
            <span class="inline-block px-4 py-1.5 rounded-full text-sm font-semibold {{ $colorClass }}">
                {{ $order->status->label() }}
            </span>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="font-semibold text-slate-900">Order Items</h2>
            <span class="text-sm text-slate-400">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
        </div>
        <table class="w-full">
            <thead>
                <tr class="text-xs font-semibold text-slate-400 uppercase tracking-wide bg-slate-50">
                    <th class="text-left px-6 py-3">Product</th>
                    <th class="text-right px-6 py-3">Price</th>
                    <th class="text-right px-6 py-3">Qty</th>
                    <th class="text-right px-6 py-3">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @foreach ($order->items as $item)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4 font-medium text-slate-900">{{ $item->product_name }}</td>
                        <td class="px-6 py-4 text-right text-slate-600">${{ number_format($item->product_price, 2) }}</td>
                        <td class="px-6 py-4 text-right text-slate-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-right font-semibold text-slate-900">${{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="border-t-2 border-slate-200">
                    <td colspan="3" class="px-6 py-4 text-right font-semibold text-slate-700">Total</td>
                    <td class="px-6 py-4 text-right text-xl font-bold text-slate-900">${{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-5 mb-8">
        <h2 class="font-semibold text-slate-900 mb-3">Customer Details</h2>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-400 mb-0.5">Name</p>
                <p class="font-medium text-slate-900">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-slate-400 mb-0.5">Email</p>
                <p class="font-medium text-slate-900">{{ $order->customer_email }}</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <a href="{{ route('catalog.products.index') }}"
           class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>
            Back to products
        </a>
        <a href="{{ route('order.create') }}"
           class="px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors">
            Place another order
        </a>
    </div>

</div>
@endsection
