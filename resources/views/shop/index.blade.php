<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Shop') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Store Header -->
            @if(isset($distributor) && $distributor)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 border border-gray-100">
                <div class="p-6 sm:p-8 flex flex-col sm:flex-row items-center sm:items-start gap-8">
                    <!-- Logo Section (1:1 Ratio, Consistent Size) -->
                    <div class="flex-shrink-0">
                        <div class="w-32 h-32 sm:w-40 sm:h-40 rounded-xl border border-gray-200 overflow-hidden shadow-sm flex items-center justify-center bg-gray-50">
                            @if($distributor->logo)
                                <img src="{{ Storage::url($distributor->logo) }}" alt="{{ $distributor->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-300">
                                    <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                    <span class="text-sm font-medium uppercase tracking-wider">{{ __('Store Logo') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Info Section -->
                    <div class="text-center sm:text-left flex-1 pt-2">
                        <div class="flex items-center justify-center sm:justify-start gap-3 mb-2">
                            <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">{{ $distributor->name }}</h1>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                {{ __('Official Distributor') }}
                            </span>
                        </div>
                        
                        <div class="space-y-3 text-gray-600 mt-4">
                            @if($distributor->address)
                                <p class="flex items-center justify-center sm:justify-start gap-2.5">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="text-base">{{ $distributor->address }}</span>
                                </p>
                            @endif
                            @if($distributor->phone)
                                <p class="flex items-center justify-center sm:justify-start gap-2.5">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                    <span class="text-base font-medium">{{ $distributor->phone }}</span>
                                </p>
                            @endif
                            @if($distributor->email)
                                <p class="flex items-center justify-center sm:justify-start gap-2.5">
                                    <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                    <span class="text-base">{{ $distributor->email }}</span>
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Filters -->
            <div class="mb-6 bg-white p-4 rounded-lg shadow-sm">
                <form action="{{ route('shop.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search products...') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    </div>
                    <div class="w-full md:w-1/4">
                        <select name="brand" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <option value="">{{ __('All Brands') }}</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->slug }}" {{ request('brand') == $brand->slug ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-full hover:shadow-md transition-shadow duration-200">
                            <div class="aspect-w-1 aspect-h-1 w-full overflow-hidden bg-gray-200 xl:aspect-w-7 xl:aspect-h-8">
                                @if($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="h-48 w-full object-cover object-center group-hover:opacity-75">
                                @else
                                    <div class="h-48 w-full bg-gray-200 flex items-center justify-center text-gray-400">
                                        No Image
                                    </div>
                                @endif
                            </div>
                            <div class="p-4 flex flex-col flex-grow">
                                <h3 class="text-lg font-medium text-gray-900 truncate" title="{{ $product->name }}">
                                    <a href="{{ route('shop.show', $product) }}">
                                        {{ $product->name }}
                                    </a>
                                </h3>
                                <p class="mt-1 text-sm text-gray-500">{{ $product->brand->name }}</p>
                                <div class="mt-4 flex items-end justify-between flex-grow">
                                    <p class="text-lg font-bold text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="mt-4" x-data="{ loading: false }">
                                    <button 
                                        type="button"
                                        @click="loading = true; $dispatch('add-to-cart', { productId: {{ $product->id }}, quantity: 1 }); setTimeout(() => loading = false, 500)"
                                        :disabled="loading"
                                        :class="{ 'opacity-75 cursor-wait': loading, 'hover:-translate-y-0.5 hover:shadow-lg': !loading }"
                                        class="w-full bg-blue-600 border border-transparent rounded-lg py-2.5 px-4 flex items-center justify-center gap-2 text-sm font-bold text-white shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        
                                        <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        
                                        <svg x-show="loading" style="display: none;" class="animate-spin w-5 h-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>

                                        <span x-text="loading ? '{{ __('Adding...') }}' : '{{ __('Add to Cart') }}'">{{ __('Add to Cart') }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    {{ __('No products found.') }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
