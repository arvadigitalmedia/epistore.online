<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col md:flex-row gap-8">
                        <!-- Product Image -->
                        <div class="w-full md:w-1/2">
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-auto rounded-lg shadow-sm">
                            @else
                                <div class="w-full h-96 bg-gray-200 flex items-center justify-center text-gray-400 rounded-lg">
                                    {{ __('No Image') }}
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="w-full md:w-1/2 flex flex-col">
                            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product->name }}</h1>
                            <div class="text-sm text-gray-500 mb-4">{{ $product->brand->name }} | SKU: {{ $product->sku }}</div>
                            
                            <div class="text-3xl font-bold text-indigo-600 mb-6">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>

                            <div class="prose prose-sm text-gray-500 mb-8">
                                <p>{{ $product->description }}</p>
                            </div>

                            <div class="mt-auto pt-6 border-t border-gray-100">
                                <form action="{{ route('cart.add') }}" method="POST" class="flex gap-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    
                                    <div class="w-24">
                                        <label for="quantity" class="sr-only">{{ __('Quantity') }}</label>
                                        <input type="number" name="quantity" id="quantity" value="1" min="1" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                    </div>
                                    
                                    <button type="submit" class="flex-1 bg-indigo-600 border border-transparent rounded-md py-3 px-8 flex items-center justify-center text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ __('Add to Cart') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
