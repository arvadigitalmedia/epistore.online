<x-app-layout>
    <div class="max-w-4xl mx-auto py-12 text-center">
        <!-- Success Icon -->
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
            <svg class="h-12 w-12 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight sm:text-4xl mb-2">
            Terima Kasih!
        </h1>
        <p class="text-lg text-gray-500 mb-8">
            Pesanan Anda #{{ $order->order_number }} telah kami terima.
        </p>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8 max-w-2xl mx-auto mb-10">
            <h2 class="text-lg font-medium text-gray-900 mb-4">Status Pesanan Saat Ini</h2>
            
            <div class="flex items-center justify-center mb-6">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-blue-100 text-blue-800">
                    <svg class="-ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <p class="text-gray-600 mb-6">
                Kami sedang memverifikasi pembayaran Anda. Anda akan menerima notifikasi saat pesanan dikirim.
                @if($order->estimated_delivery_date)
                    <br><span class="font-medium">Estimasi Pengiriman: {{ $order->estimated_delivery_date->format('d M Y') }}</span>
                @endif
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Lacak Pesanan
                </a>
                <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Lanjut Belanja
                </a>
            </div>
        </div>

        <!-- Rekomendasi Produk (Placeholder) -->
        <div class="mt-16 text-left">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Mungkin Anda Juga Suka</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Logic to fetch related products would go here -->
                <!-- Example Static Cards -->
                @foreach(\App\Models\Product::inRandomOrder()->limit(4)->get() as $product)
                <div class="group relative bg-white border border-gray-200 rounded-lg flex flex-col overflow-hidden hover:shadow-lg transition">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 group-hover:opacity-75 sm:h-56">
                         <!-- Image placeholder since we don't know the exact path setup yet, using generic or nothing -->
                         <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-500">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <span>No Image</span>
                            @endif
                         </div>
                    </div>
                    <div class="flex-1 p-4 space-y-2 flex flex-col">
                        <h3 class="text-sm font-medium text-gray-900">
                            <a href="{{ route('shop.show', $product) }}">
                                <span aria-hidden="true" class="absolute inset-0"></span>
                                {{ $product->name }}
                            </a>
                        </h3>
                        <p class="text-sm text-gray-500 line-clamp-2">{{ $product->description }}</p>
                        <div class="flex-1 flex items-end justify-between">
                            <p class="text-base font-medium text-gray-900">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
