<x-storefront-layout>
    <!-- Hero Section -->
    <div class="relative bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">Welcome to</span>
                            <span class="block text-blue-600 xl:inline">{{ $current_distributor->name ?? 'EPI Store' }}</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            Discover our exclusive collection of premium products. Quality you can trust, delivered to your doorstep.
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            <div class="rounded-md shadow">
                                <a href="{{ route('storefront.products.index') }}" class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 md:py-4 md:text-lg md:px-10">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2 bg-gray-50">
             <!-- Placeholder for Hero Image -->
             <div class="h-56 w-full bg-blue-100 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center text-blue-300">
                <svg class="h-20 w-20" fill="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
             </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Featured Products</h2>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 mx-auto">Hand-picked selections just for you.</p>
            </div>

            <div class="mt-12 grid gap-5 max-w-lg mx-auto lg:grid-cols-4 lg:max-w-none">
                @foreach($featuredProducts as $product)
                    <div class="flex flex-col rounded-lg shadow-lg overflow-hidden bg-white hover:shadow-xl transition-shadow duration-300">
                        <div class="flex-shrink-0 relative">
                            @if($product->image)
                                <img class="h-48 w-full object-cover" src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="h-48 w-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 bg-white p-6 flex flex-col justify-between">
                            <div class="flex-1">
                                <a href="{{ route('storefront.products.show', $product->slug) }}" class="block mt-2">
                                    <p class="text-xl font-semibold text-gray-900">{{ $product->name }}</p>
                                    <p class="mt-3 text-base text-gray-500">{{ Str::limit($product->description, 50) }}</p>
                                </a>
                            </div>
                            <div class="mt-6 flex items-center justify-between">
                                <p class="text-lg font-bold text-blue-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <form action="{{ route('storefront.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none transition">
                                        Add
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-10 text-center">
                <a href="{{ route('storefront.products.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">View all products &rarr;</a>
            </div>
        </div>
    </div>
</x-storefront-layout>
