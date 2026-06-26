@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Products</h1>
            <p class="text-slate-500 mt-1">{{ count($products) }} items available</p>
        </div>
        <a href="{{ route('order.create') }}"
           class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            Place Order
        </a>
    </div>

    @if (count($products) === 0)
        <div class="text-center py-24 text-slate-400">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
            </svg>
            <p class="font-medium">No products available</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($products as $product)
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 flex flex-col">
                    <div class="p-6 flex-1">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <h2 class="font-semibold text-slate-900 leading-snug">{{ $product->name }}</h2>
                            @if ($product->inStock)
                                <span class="shrink-0 inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 text-xs font-medium px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    In stock
                                </span>
                            @else
                                <span class="shrink-0 inline-flex items-center gap-1 bg-red-50 text-red-600 text-xs font-medium px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                    Out of stock
                                </span>
                            @endif
                        </div>
                        @if ($product->inStock)
                            <p class="text-xs text-slate-400">{{ $product->stockQuantity }} units left</p>
                        @endif
                    </div>
                    <div class="px-6 pb-6 flex items-center justify-between">
                        <span class="text-2xl font-bold text-slate-900">${{ number_format($product->price, 2) }}</span>
                        @if ($product->inStock)
                            <a href="{{ route('order.create') }}"
                               class="flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Order
                            </a>
                        @else
                            <span class="px-4 py-2 bg-slate-100 text-slate-400 text-sm font-medium rounded-lg cursor-not-allowed">Unavailable</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
