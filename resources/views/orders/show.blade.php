<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Order #{{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
             <!-- Status Tracking -->
             <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between relative">
                    <div class="absolute left-0 top-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                    
                    <!-- Step 1: Placed -->
                    <div class="flex flex-col items-center bg-white px-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $order->created_at ? 'bg-green-500 text-white' : 'bg-gray-300' }}">✓</div>
                        <span class="text-xs mt-1">Placed</span>
                    </div>
                    
                    <!-- Step 2: Shipping/Ready -->
                    <div class="flex flex-col items-center bg-white px-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $order->shipped_at ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                           @if($order->shipped_at) ✓ @else 2 @endif
                        </div>
                        <span class="text-xs mt-1">{{ $order->delivery_type === 'pickup' ? 'Ready for Pickup' : 'Shipped' }}</span>
                    </div>

                    <!-- Step 3: Received/Picked Up -->
                    <div class="flex flex-col items-center bg-white px-2">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $order->received_at ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                            @if($order->received_at) ✓ @else 3 @endif
                        </div>
                        <span class="text-xs mt-1">{{ $order->delivery_type === 'pickup' ? 'Picked Up' : 'Received' }}</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Items -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Items from {{ $order->distributor->name }}</h3>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->product_sku }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center text-sm text-gray-500">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-right text-sm font-bold">Shipping Cost</td>
                                        <td class="px-4 py-3 text-right text-sm font-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr class="bg-gray-50">
                                        <td colspan="3" class="px-4 py-3 text-right text-base font-bold text-primary-600">Grand Total</td>
                                        <td class="px-4 py-3 text-right text-base font-bold text-primary-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right: Info & Actions -->
                <div class="space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            @if($order->status == 'pending' && $order->payment_status == 'pending')
                                <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                                    <h3 class="text-sm font-medium text-yellow-800 mb-2">Payment Required</h3>
                                    <p class="text-xs text-yellow-700 mb-3">Please complete your payment to process the order.</p>
                                    <a href="{{ route('orders.payment', $order) }}" class="block w-full text-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        Pay Now
                                    </a>
                                </div>
                            @endif

                            @if($order->delivery_type === 'pickup')
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Pickup Details</h3>
                                <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
                                    <p class="text-xs text-blue-600 uppercase font-bold mb-1">Pickup Token</p>
                                    <p class="text-2xl font-mono font-bold text-blue-800 tracking-widest">{{ $order->pickup_token }}</p>
                                </div>
                                <p class="text-sm text-gray-600 mb-1"><span class="font-bold">Store:</span> {{ $order->pickupStore->name ?? 'Store' }}</p>
                                <p class="text-sm text-gray-600 mb-1"><span class="font-bold">Address:</span> {{ $order->pickupStore->address ?? '-' }}</p>
                                <p class="text-sm text-gray-600 mb-4"><span class="font-bold">Pickup Time:</span> {{ $order->pickup_at ? \Carbon\Carbon::parse($order->pickup_at)->format('d M Y, H:i') : '-' }}</p>
                            @else
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Shipping Details</h3>
                                <p class="text-sm text-gray-600 mb-1">Address: {{ $order->shipping_address }}</p>
                                
                                @if($order->status == 'shipping' || $order->status == 'completed')
                                    <div class="mt-4 bg-blue-50 p-3 rounded border border-blue-100">
                                        <p class="text-sm text-blue-800 font-bold">Courier: {{ $order->shipping_courier }}</p>
                                        <p class="text-sm text-blue-800">Tracking: {{ $order->shipping_tracking_number }}</p>
                                        @if($order->estimated_delivery_date)
                                        <p class="text-xs text-blue-600 mt-1">Est. Arrival: {{ $order->estimated_delivery_date->format('d M Y') }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endif

                            @if($order->status == 'shipping')
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <h4 class="text-md font-bold text-gray-900 mb-2">Have you received this order?</h4>
                                    <form action="{{ route('orders.confirm-receipt', $order) }}" method="POST">
                                        @csrf
                                        <p class="text-xs text-gray-500 mb-3">By clicking confirm, you acknowledge that the goods have been received in good condition.</p>
                                        <x-primary-button class="w-full justify-center bg-green-600 hover:bg-green-700">
                                            {{ __('Confirm Receipt') }}
                                        </x-primary-button>
                                    </form>
                                </div>
                            @elseif($order->status == 'completed')
                                <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Order Completed
                                    </span>
                                    <p class="text-xs text-gray-500 mt-2">Received on {{ $order->received_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
