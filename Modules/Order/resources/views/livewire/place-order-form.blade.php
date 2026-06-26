<div>
    @if (session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl mb-6">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <p class="font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if ($errors->has('cart'))
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl mb-6">
            <svg class="w-5 h-5 mt-0.5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
            </svg>
            <p class="font-medium">{{ $errors->first('cart') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- Product list --}}
        <div class="lg:col-span-3">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-900">Available Products</h2>
                    <p class="text-sm text-slate-400 mt-0.5">{{ count($availableProducts) }} items in stock</p>
                </div>
                <div class="divide-y divide-slate-50">
                    @forelse ($availableProducts as $product)
                        <div class="flex items-center justify-between px-6 py-4 hover:bg-slate-50 transition-colors">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-900 truncate">{{ $product->name }}</p>
                                <p class="text-sm text-slate-400 mt-0.5">{{ $product->stockQuantity }} in stock</p>
                            </div>
                            <div class="flex items-center gap-4 ml-4 shrink-0">
                                <span class="font-semibold text-slate-900">${{ number_format($product->price, 2) }}</span>
                                @if (isset($cart[$product->id]) && $cart[$product->id] > 0)
                                    <div class="flex items-center gap-2">
                                        <button wire:click="removeFromCart({{ $product->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors font-bold text-lg leading-none">
                                            −
                                        </button>
                                        <span class="w-6 text-center font-semibold text-indigo-600">{{ $cart[$product->id] }}</span>
                                        <button wire:click="addToCart({{ $product->id }})"
                                                class="w-8 h-8 flex items-center justify-center rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-colors font-bold text-lg leading-none">
                                            +
                                        </button>
                                    </div>
                                @else
                                    <button wire:click="addToCart({{ $product->id }})"
                                            class="flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Add
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center text-slate-400">
                            <p>No products available at the moment.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Cart + form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm sticky top-24">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-900">Your Order</h2>
                </div>

                @if (count($cart) === 0)
                    <div class="px-6 py-10 text-center text-slate-400">
                        <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                        </svg>
                        <p class="text-sm">Add products to get started</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-50">
                        @foreach ($cart as $productId => $quantity)
                            @php $item = collect($availableProducts)->firstWhere('id', (int) $productId) @endphp
                            <div class="flex items-center justify-between px-6 py-3">
                                <div class="min-w-0 mr-3">
                                    <p class="text-sm font-medium text-slate-900 truncate">
                                        {{ $item?->name ?? 'Product #'.$productId }}
                                    </p>
                                    @if ($item)
                                        <p class="text-xs text-slate-400">${{ number_format($item->price * $quantity, 2) }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <button wire:click="removeFromCart({{ $productId }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors text-base font-bold leading-none">
                                        −
                                    </button>
                                    <span class="w-5 text-center font-semibold text-slate-900 text-sm">{{ $quantity }}</span>
                                    <button wire:click="addToCart({{ $productId }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors text-base font-bold leading-none">
                                        +
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 pt-4 pb-2 border-t border-slate-100">
                        <div class="flex justify-between text-sm font-semibold text-slate-900 mb-4">
                            <span>Total</span>
                            <span>
                                ${{ number_format(collect($cart)->map(function ($qty, $id) use ($availableProducts) {
                                    $p = collect($availableProducts)->firstWhere('id', (int) $id);
                                    return $p ? $p->price * $qty : 0;
                                })->sum(), 2) }}
                            </span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div>
                                <input wire:model="customerName"
                                       type="text"
                                       placeholder="Your name"
                                       class="w-full border rounded-lg px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition {{ $errors->has('customerName') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('customerName')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <input wire:model="customerEmail"
                                       type="email"
                                       placeholder="Your email"
                                       class="w-full border rounded-lg px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition {{ $errors->has('customerEmail') ? 'border-red-400 bg-red-50' : 'border-slate-200' }}">
                                @error('customerEmail')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <button wire:click="placeOrder"
                                wire:loading.attr="disabled"
                                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-60 text-white font-semibold rounded-xl transition-colors">
                            <span wire:loading.remove>Place Order</span>
                            <span wire:loading>Placing…</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
