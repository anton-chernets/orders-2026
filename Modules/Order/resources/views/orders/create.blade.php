@extends('layouts.app')

@section('title', 'Place an Order')

@push('head')
    @livewireStyles
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-slate-900">Place an Order</h1>
        <p class="text-slate-500 mt-1">Add products to your cart and fill in your details.</p>
    </div>
    @livewire('order::place-order-form')
</div>
@endsection

@push('scripts')
    @livewireScripts
@endpush
