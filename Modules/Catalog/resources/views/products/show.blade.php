@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

    <a href="{{ route('catalog.products.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-slate-500 hover:text-slate-900 transition-colors mb-8">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
        </svg>
        All products
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8">
                @if ($product->category)
                    <span class="inline-block bg-indigo-50 text-indigo-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                        {{ $product->category->name }}
                    </span>
                @endif
                <h1 class="text-3xl font-bold text-slate-900 mb-4">{{ $product->name }}</h1>
                @if ($product->description)
                    <p class="text-slate-600 leading-relaxed">{{ $product->description }}</p>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sticky top-24">
                <div class="text-4xl font-bold text-slate-900 mb-1">${{ number_format($product->price, 2) }}</div>
                <div class="mb-6">
                    @if ($product->isInStock())
                        <span class="inline-flex items-center gap-1.5 text-sm text-emerald-700 font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            In stock &mdash; {{ $product->stock_quantity }} units
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 text-sm text-red-600 font-medium">
                            <span class="w-2 h-2 rounded-full bg-red-400"></span>
                            Out of stock
                        </span>
                    @endif
                </div>

                @if ($product->isInStock())
                    <a href="{{ route('order.create') }}"
                       class="block w-full text-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition-colors shadow-sm">
                        Place an Order
                    </a>
                @else
                    <button disabled
                            class="block w-full text-center px-6 py-3 bg-slate-100 text-slate-400 font-semibold rounded-xl cursor-not-allowed">
                        Currently Unavailable
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
