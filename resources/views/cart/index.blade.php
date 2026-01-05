<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if($cart && $cart->items->count() > 0)
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <!-- Cart Items -->
                    <div class="lg:col-span-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-lg font-bold text-gray-900">Your Items ({{ $cart->items->count() }})</h3>
                            </div>
                            <ul role="list" class="divide-y divide-gray-100">
                                @foreach($cart->items as $item)
                                    <li class="p-6 flex items-center gap-6 hover:bg-gray-50 transition-colors duration-200">
                                        <!-- 1. Product Image (Left, Consistent Size) -->
                                        <div class="flex-shrink-0 w-24 h-24 bg-gray-100 rounded-lg overflow-hidden border border-gray-200 shadow-sm relative group">
                                            @if($item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-center object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- 2. Product Name (Left Aligned, Clear Typography) -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-bold text-gray-900 leading-tight truncate">
                                                <a href="{{ route('shop.show', $item->product) }}" class="hover:text-blue-600 transition-colors">
                                                    {{ $item->product->name }}
                                                </a>
                                            </h3>
                                            <p class="text-sm text-gray-500 mt-1">{{ $item->product->brand->name }}</p>
                                            <div class="mt-1 font-medium text-gray-900">
                                                Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                            </div>
                                        </div>

                                        <!-- 3. Quantity Column (After Name, Arrows, Value Between) -->
                                        <div class="flex-shrink-0">
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center bg-white border border-gray-300 rounded-lg shadow-sm overflow-hidden h-10">
                                                @csrf
                                                @method('PATCH')
                                                
                                                <!-- Decrease Button (Panah Bawah/Kurang) -->
                                                <button type="button" 
                                                        onclick="updateQuantity(this, -1)"
                                                        class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none border-r border-gray-200 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                                </button>

                                                <!-- Quantity Value (Clear, Between Buttons) -->
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       readonly
                                                       class="w-14 h-full text-center border-none focus:ring-0 p-0 text-gray-900 font-bold text-sm bg-transparent appearance-none">

                                                <!-- Increase Button (Panah Atas/Tambah) -->
                                                <button type="button" 
                                                        onclick="updateQuantity(this, 1)"
                                                        class="w-10 h-full flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none border-l border-gray-200 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                </button>
                                            </form>
                                        </div>

                                        <!-- 4. Delete Icon (Far Right, Intuitive) -->
                                        <div class="flex-shrink-0 pl-4">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="group p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                                        title="Remove {{ $item->product->name }} from cart">
                                                    <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                            
                            <script>
                                function updateQuantity(btn, change) {
                                    const form = btn.closest('form');
                                    const input = form.querySelector('input[name="quantity"]');
                                    let newVal = parseInt(input.value) + change;
                                    if (newVal < 1) newVal = 1;
                                    input.value = newVal;
                                    form.submit();
                                }
                            </script>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-4">
                        <div class="bg-white shadow-lg sm:rounded-lg overflow-hidden sticky top-6">
                            <div class="p-6 bg-gray-900 text-white">
                                <h2 class="text-xl font-bold">Order Summary</h2>
                            </div>
                            <div class="p-6">
                                <dl class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <dt class="text-gray-600">Subtotal</dt>
                                        <dd class="font-medium text-gray-900">Rp {{ number_format($cart->total, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <dt class="text-gray-600">Shipping Estimate</dt>
                                        <dd class="font-medium text-gray-900">Calculated at checkout</dd>
                                    </div>
                                    <div class="pt-4 flex items-center justify-between border-t border-gray-200">
                                        <dt class="text-lg font-bold text-gray-900">Order Total</dt>
                                        <dd class="text-2xl font-bold text-indigo-600">Rp {{ number_format($cart->total, 0, ',', '.') }}</dd>
                                    </div>
                                </dl>

                                <div class="mt-8">
                                    <a href="{{ route('checkout.index') }}" class="group w-full bg-blue-600 border border-transparent rounded-lg shadow-md py-4 px-4 flex items-center justify-center text-lg font-bold text-white uppercase tracking-wider hover:bg-blue-700 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300">
                                        <svg class="mr-3 w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        Proceed to Checkout
                                    </a>
                                    <p class="mt-4 text-xs text-center text-gray-500 flex items-center justify-center gap-1">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Secure Checkout powered by EPI-OSS
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Your cart is empty</h3>
                    <p class="mt-1 text-sm text-gray-500">Start adding some products to your cart.</p>
                    <div class="mt-6">
                        <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Start Shopping
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
