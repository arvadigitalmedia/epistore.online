<x-storefront-layout>
    <div class="bg-white">
        <div class="max-w-2xl mx-auto py-16 px-4 sm:py-24 sm:px-6 lg:max-w-7xl lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-extrabold tracking-tight text-gray-900">Products</h2>
                
                <form action="{{ route('storefront.products.index') }}" method="GET" class="flex">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700">
                        Search
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 gap-y-10 sm:grid-cols-2 gap-x-6 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                @forelse($products as $product)
                    <a href="{{ route('storefront.products.show', $product->slug) }}" class="group">
                        <div class="w-full aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden xl:aspect-w-7 xl:aspect-h-8">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-center object-cover group-hover:opacity-75">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                    No Image
                                </div>
                            @endif
                        </div>
                        <h3 class="mt-4 text-sm text-gray-700">
                            {{ $product->name }}
                        </h3>
                        <p class="mt-1 text-lg font-medium text-gray-900">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </p>
                    </a>
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No products found.
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-storefront-layout>
