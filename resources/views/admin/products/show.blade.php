<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Details') }} : {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Product Info -->
                <div class="md:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">General Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Brand</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $product->brand->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">SKU</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $product->sku }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Unit</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $product->unit }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-500">Current Price</label>
                                    <p class="mt-1 text-lg font-bold text-primary-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                                <div class="col-span-1 md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-500">Description</label>
                                    <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $product->description ?: '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Price History -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Price History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Old Price</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">New Price</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @forelse($product->priceHistories as $history)
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-gray-900">{{ $history->effective_date->format('d M Y H:i') }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">Rp {{ number_format($history->old_price, 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 text-sm font-medium text-gray-900">Rp {{ number_format($history->new_price, 0, ',', '.') }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $history->reason }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-500">{{ $history->creator->name ?? 'System' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-2 text-center text-sm text-gray-500">No price history available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Image -->
                <div class="md:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6 text-gray-900">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Product Image</h3>
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full rounded-lg shadow-sm">
                            @else
                                <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                                    No Image Uploaded
                                </div>
                            @endif
                            
                            <div class="mt-6 border-t pt-4">
                                <label class="block text-sm font-medium text-gray-500">Status</label>
                                <span class="mt-2 inline-flex px-3 py-1 rounded-full text-sm font-semibold {{ $product->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
