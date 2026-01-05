<x-app-layout>
    <div class="max-w-4xl mx-auto py-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                    <span class="text-sm font-medium mt-2 text-blue-600">Invoice</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-bold">2</div>
                    <span class="text-sm font-medium mt-2 text-gray-500">Pembayaran</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-bold">3</div>
                    <span class="text-sm font-medium mt-2 text-gray-500">Selesai</span>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-100">
            <!-- Header Invoice -->
            <div class="p-6 bg-gray-50 border-b border-gray-200 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">INVOICE</h1>
                    <p class="text-sm text-gray-500">#{{ $order->order_number }}</p>
                    <div class="mt-2">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full 
                            {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 
                               ($order->payment_status == 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Tanggal Pesanan</p>
                    <p class="font-medium">{{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Info Pengirim (Distributor) -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Diterbitkan Oleh</h3>
                    <div class="font-medium text-gray-900">{{ $order->distributor->name }}</div>
                    @if($order->distributor->address)
                        <div class="text-sm text-gray-600 mt-1">{{ $order->distributor->address }}</div>
                    @endif
                </div>

                <!-- Info Penerima -->
                <div class="text-right md:text-left">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">Ditagihkan Kepada</h3>
                    <div class="font-medium text-gray-900">{{ $order->recipient_name }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ $order->recipient_phone }}</div>
                    <div class="text-sm text-gray-600 mt-1">{{ $order->shipping_address }}</div>
                </div>
            </div>

            <!-- Tabel Item -->
            <div class="px-6 py-4">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 text-sm font-semibold text-gray-600">Produk</th>
                            <th class="py-3 text-sm font-semibold text-gray-600 text-center">Qty</th>
                            <th class="py-3 text-sm font-semibold text-gray-600 text-right">Harga</th>
                            <th class="py-3 text-sm font-semibold text-gray-600 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="py-4">
                                <div class="font-medium text-gray-900">{{ $item->product_name }}</div>
                                <div class="text-xs text-gray-500">{{ $item->product_sku }}</div>
                            </td>
                            <td class="py-4 text-center text-gray-700">{{ $item->quantity }}</td>
                            <td class="py-4 text-right text-gray-700">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="py-4 text-right font-medium text-gray-900">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Ringkasan Pembayaran -->
            <div class="p-6 bg-gray-50 border-t border-gray-200">
                <div class="flex justify-end">
                    <div class="w-full md:w-1/2 lg:w-1/3 space-y-2">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->items->sum('total_price'), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Ongkos Kirim ({{ strtoupper($order->shipping_courier) }})</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        @if($order->discount_amount > 0)
                        <div class="flex justify-between text-sm text-green-600">
                            <span>Diskon ({{ $order->coupon_code }})</span>
                            <span>- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold text-gray-900 pt-2 border-t border-gray-200">
                            <span>Total Tagihan</span>
                            <span class="text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="p-6 flex justify-between items-center border-t border-gray-200">
                <a href="{{ route('shop.index') }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium flex items-center">
                    &larr; Kembali Belanja
                </a>
                
                @if($order->payment_status === 'pending')
                    <a href="{{ route('orders.payment', $order) }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Konfirmasi Pembayaran &rarr;
                    </a>
                @else
                    <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Lihat Detail Pesanan
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
