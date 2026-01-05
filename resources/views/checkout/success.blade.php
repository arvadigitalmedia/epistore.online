<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Confirmed') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
                        <svg class="h-10 w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Thank you for your order!</h2>
                    <p class="text-gray-500 mb-8">Your order has been placed successfully. We've sent a confirmation email to your inbox.</p>
                    
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left">
                        <div class="flex justify-between items-center mb-4 border-b border-gray-200 pb-4">
                            <span class="text-sm text-gray-500">Order Number</span>
                            <span class="text-lg font-bold text-gray-900">{{ $order->order_number }}</span>
                        </div>
                        
                        @if($order->delivery_type === 'pickup')
                        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="text-lg font-bold text-blue-900 mb-2 text-center">Kode Pengambilan (Token)</h4>
                            <div class="text-4xl font-mono font-bold text-center tracking-widest text-blue-800 mb-2 bg-white rounded py-2 border border-blue-100 shadow-sm">
                                {{ $order->pickup_token }}
                            </div>
                            <p class="text-xs text-blue-700 text-center mb-4">Tunjukkan kode ini kepada petugas toko saat mengambil pesanan.</p>
                            
                            <div class="text-sm text-left border-t border-blue-200 pt-3 mt-3">
                                <p class="font-bold text-blue-900">Lokasi Pengambilan:</p>
                                <p class="text-blue-800 font-semibold">{{ $order->pickupStore->name ?? 'Toko' }}</p>
                                <p class="text-blue-800">{{ $order->pickupStore->address ?? '-' }}</p>
                                <p class="text-blue-800 mt-2"><span class="font-bold">Waktu:</span> {{ $order->pickup_at ? \Carbon\Carbon::parse($order->pickup_at)->format('d M Y, H:i') : '-' }}</p>
                            </div>
                        </div>
                        @endif

                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-2">Payment Instructions</h4>
                            @if($order->payment_method == 'bank_transfer')
                                <p class="text-sm text-gray-600">Please transfer <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong> to:</p>
                                <div class="mt-2 p-3 bg-white border border-gray-200 rounded">
                                    <p class="font-bold">Bank BCA</p>
                                    <p class="font-mono">123-456-7890</p>
                                    <p class="text-xs text-gray-500">PT Emas Perak Indonesia</p>
                                </div>
                            @else
                                <p class="text-sm text-gray-600">Please complete your payment using {{ str_replace('_', ' ', ucfirst($order->payment_method)) }}.</p>
                            @endif
                        </div>

                        <div class="flex justify-center gap-4">
                            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                View Order
                            </a>
                            <a href="{{ route('shop.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
