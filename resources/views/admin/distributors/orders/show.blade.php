<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Order #{{ $order->order_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Order Status Tracker -->
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                 <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status: <span class="uppercase text-primary-600">{{ $order->status }}</span></h3>
                 <div class="flex items-center justify-between relative">
                     <div class="absolute left-0 top-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                     
                     <!-- Step 1: Pending -->
                     <div class="flex flex-col items-center bg-white px-2">
                         <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $order->created_at ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                             1
                         </div>
                         <span class="text-xs mt-1">Pending</span>
                         <span class="text-xs text-gray-500">{{ $order->created_at->format('d/m H:i') }}</span>
                     </div>
                     
                     <!-- Step 2: Shipping -->
                     <div class="flex flex-col items-center bg-white px-2">
                         <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $order->shipped_at ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                             2
                         </div>
                         <span class="text-xs mt-1">Shipping</span>
                         @if($order->shipped_at)
                            <span class="text-xs text-gray-500">{{ $order->shipped_at->format('d/m H:i') }}</span>
                         @endif
                     </div>

                     <!-- Step 3: Delivered/Completed -->
                     <div class="flex flex-col items-center bg-white px-2">
                         <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $order->received_at ? 'bg-green-500 text-white' : 'bg-gray-300' }}">
                             3
                         </div>
                         <span class="text-xs mt-1">Completed</span>
                         @if($order->received_at)
                            <span class="text-xs text-gray-500">{{ $order->received_at->format('d/m H:i') }}</span>
                         @endif
                     </div>
                 </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left Column: Items -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Items</h3>
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
                                        <td colspan="3" class="px-4 py-3 text-right text-sm font-bold">Subtotal</td>
                                        <td class="px-4 py-3 text-right text-sm font-bold">Rp {{ number_format($order->total_amount - $order->shipping_cost, 0, ',', '.') }}</td>
                                    </tr>
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

                <!-- Right Column: Info & Actions -->
                <div class="space-y-6">
                    <!-- Customer Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Customer Info</h3>
                            <p class="text-sm font-bold">{{ $order->user->name }}</p>
                            <p class="text-sm text-gray-600">{{ $order->user->email }}</p>
                            <div class="mt-4">
                                <h4 class="text-xs font-uppercase text-gray-500">Shipping Address</h4>
                                <p class="text-sm text-gray-700 mt-1">{{ $order->shipping_address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Action (Only for Distributor) -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Shipping Update</h3>
                            
                            @if($order->status == 'shipping' || $order->status == 'completed')
                                <div class="bg-gray-50 p-3 rounded text-sm mb-4">
                                    <p><strong>Courier:</strong> {{ $order->shipping_courier }}</p>
                                    <p><strong>Tracking No:</strong> {{ $order->shipping_tracking_number }}</p>
                                    <p><strong>Shipped At:</strong> {{ $order->shipped_at ? $order->shipped_at->format('d M Y') : '-' }}</p>
                                </div>
                            @endif

                            @if($order->status == 'pending' || $order->status == 'processing' || $order->status == 'shipping')
                                <form action="{{ route('distributor.orders.update-shipping', $order) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <div class="mb-4">
                                        <x-input-label for="shipping_courier" :value="__('Courier / Logistics')" />
                                        <x-text-input id="shipping_courier" class="block mt-1 w-full" type="text" name="shipping_courier" :value="old('shipping_courier', $order->shipping_courier)" required />
                                    </div>

                                    <div class="mb-4">
                                        <x-input-label for="shipping_tracking_number" :value="__('Tracking Number (Resi)')" />
                                        <x-text-input id="shipping_tracking_number" class="block mt-1 w-full" type="text" name="shipping_tracking_number" :value="old('shipping_tracking_number', $order->shipping_tracking_number)" required />
                                    </div>
                                    
                                    <div class="mb-4">
                                        <x-input-label for="estimated_delivery_date" :value="__('Est. Delivery Date')" />
                                        <x-text-input id="estimated_delivery_date" class="block mt-1 w-full" type="date" name="estimated_delivery_date" :value="old('estimated_delivery_date', $order->estimated_delivery_date ? $order->estimated_delivery_date->format('Y-m-d') : '')" />
                                    </div>

                                    <x-primary-button class="w-full justify-center">
                                        {{ __('Update Shipping') }}
                                    </x-primary-button>
                                </form>
                            @else
                                <p class="text-sm text-gray-500">Shipping cannot be updated for this status.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
