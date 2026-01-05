<x-storefront-layout>
    <div class="bg-white">
        <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Shopping Cart</h1>

            @if(session('success'))
                <div class="mt-4 bg-green-50 border-l-4 border-green-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <!-- Heroicon name: solid/check-circle -->
                            <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form class="mt-12 lg:grid lg:grid-cols-12 lg:gap-x-12 lg:items-start lg:col-span-7">
                <section aria-labelledby="cart-heading" class="lg:col-span-7">
                    <h2 id="cart-heading" class="sr-only">Items in your shopping cart</h2>

                    <ul role="list" class="border-t border-b border-gray-200 divide-y divide-gray-200">
                        @if($cart && $cart->items->count() > 0)
                            @foreach($cart->items as $item)
                                <li class="flex py-6 sm:py-10">
                                    <div class="flex-shrink-0">
                                        @if($item->product->image)
                                            <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-24 h-24 rounded-md object-center object-cover sm:w-48 sm:h-48">
                                        @else
                                            <div class="w-24 h-24 rounded-md bg-gray-200 flex items-center justify-center text-gray-400 sm:w-48 sm:h-48">No Image</div>
                                        @endif
                                    </div>

                                    <div class="ml-4 flex-1 flex flex-col justify-between sm:ml-6">
                                        <div class="relative pr-9 sm:grid sm:grid-cols-2 sm:gap-x-6 sm:pr-0">
                                            <div>
                                                <div class="flex justify-between">
                                                    <h3 class="text-sm">
                                                        <a href="{{ route('storefront.products.show', $item->product->slug) }}" class="font-medium text-gray-700 hover:text-gray-800">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    </h3>
                                                </div>
                                                <p class="mt-1 text-sm font-medium text-gray-900">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                            </div>

                                            <div class="mt-4 sm:mt-0 sm:pr-9">
                                                <label for="quantity-{{ $item->id }}" class="sr-only">Quantity, {{ $item->product->name }}</label>
                                                
                                                <div class="flex items-center">
                                                     <button type="button" 
                                                        onclick="updateQuantity('{{ $item->id }}', {{ $item->quantity - 1 }})"
                                                        class="p-1 text-gray-500 hover:text-gray-700">
                                                        -
                                                    </button>
                                                    <span class="mx-2 text-gray-700">{{ $item->quantity }}</span>
                                                    <button type="button" 
                                                        onclick="updateQuantity('{{ $item->id }}', {{ $item->quantity + 1 }})"
                                                        class="p-1 text-gray-500 hover:text-gray-700">
                                                        +
                                                    </button>
                                                </div>

                                                <div class="absolute top-0 right-0">
                                                    <button type="button" onclick="removeItem('{{ $item->id }}')" class="-m-2 p-2 inline-flex text-gray-400 hover:text-gray-500">
                                                        <span class="sr-only">Remove</span>
                                                        <!-- Heroicon name: solid/x -->
                                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        @else
                            <li class="py-6 text-center text-gray-500">
                                Your cart is empty.
                            </li>
                        @endif
                    </ul>
                </section>

                <!-- Order summary -->
                @if($cart && $cart->items->count() > 0)
                    <section aria-labelledby="summary-heading" class="lg:col-span-5 mt-16 bg-gray-50 rounded-lg px-4 py-6 sm:p-6 lg:p-8 lg:mt-0 lg:ml-8">
                        <h2 id="summary-heading" class="text-lg font-medium text-gray-900">Order summary</h2>

                        <dl class="mt-6 space-y-4">
                            <div class="flex items-center justify-between">
                                <dt class="text-sm text-gray-600">Subtotal</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    @php
                                        $subtotal = $cart->items->sum(function($item) {
                                            return $item->product->price * $item->quantity;
                                        });
                                    @endphp
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </dd>
                            </div>
                            <div class="border-t border-gray-200 pt-4 flex items-center justify-between">
                                <dt class="text-base font-medium text-gray-900">Order total</dt>
                                <dd class="text-base font-medium text-gray-900">Rp {{ number_format($subtotal, 0, ',', '.') }}</dd>
                            </div>
                        </dl>

                        <div class="mt-6">
                            <a href="{{ route('storefront.checkout.index') }}" class="w-full bg-blue-600 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-50 focus:ring-blue-500 block text-center">Checkout</a>
                        </div>
                    </section>
                @endif
            </form>
        </div>
    </div>

    <!-- Hidden forms for JS actions -->
    <form id="update-cart-form" method="POST" class="hidden">
        @csrf
        @method('PATCH')
        <input type="hidden" name="quantity" id="update-quantity-input">
    </form>

    <form id="remove-cart-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function updateQuantity(itemId, newQuantity) {
            if (newQuantity < 1) return;
            const form = document.getElementById('update-cart-form');
            form.action = "{{ route('storefront.cart.update', ':id') }}".replace(':id', itemId);
            document.getElementById('update-quantity-input').value = newQuantity;
            form.submit();
        }

        function removeItem(itemId) {
            if (!confirm('Are you sure?')) return;
            const form = document.getElementById('remove-cart-form');
            form.action = "{{ route('storefront.cart.remove', ':id') }}".replace(':id', itemId);
            form.submit();
        }
    </script>
</x-storefront-layout>
