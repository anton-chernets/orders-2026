<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none; }</style>
    @stack('head')
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col">

    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('catalog.products.index') }}" class="flex items-center gap-2 font-bold text-slate-900 text-lg hover:text-indigo-600 transition-colors">
                <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                </svg>
                {{ config('app.name') }}
            </a>
            <nav class="flex items-center gap-1">
                <a href="{{ route('catalog.products.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors {{ request()->routeIs('catalog.products.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                    Products
                </a>
                <a href="{{ route('order.create') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors {{ request()->routeIs('order.*') ? 'text-indigo-600 bg-indigo-50' : '' }}">
                    Place Order
                </a>
                <a href="/admin"
                   class="ml-2 px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white hover:bg-indigo-700 transition-colors">
                    Admin
                </a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="border-t border-slate-200 bg-white mt-16">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex items-center justify-between text-sm text-slate-400">
            <span>{{ config('app.name') }} &copy; {{ date('Y') }}</span>
            <a href="/admin" class="hover:text-slate-600 transition-colors">Admin panel</a>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
