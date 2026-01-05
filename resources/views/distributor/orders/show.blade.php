<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order #{{ $order->order_number }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('distributor.orders.invoice', $order) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print Invoice
                </a>
                <a href="{{ route('distributor.orders.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ showCancelModal: false, showShippingModal: false, showStatusModal: false }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Column: Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Items -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Item Pesanan</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td class="px-4 py-2">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->product_name }}</div>
                                                <div class="text-xs text-gray-500">{{ $item->variant_name }}</div>
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm text-gray-500">
                                                Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-2 text-center text-sm text-gray-900">
                                                {{ $item->quantity }}
                                            </td>
                                            <td class="px-4 py-2 text-right text-sm font-medium text-gray-900">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-500">Subtotal</td>
                                        <td class="px-4 py-2 text-right text-sm font-bold text-gray-900">Rp {{ number_format($order->total_amount - $order->shipping_cost + $order->discount_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-500">Ongkos Kirim</td>
                                        <td class="px-4 py-2 text-right text-sm font-bold text-gray-900">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-sm font-medium text-gray-500">Diskon</td>
                                        <td class="px-4 py-2 text-right text-sm font-bold text-red-600">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-right text-base font-bold text-gray-900">Total Pembayaran</td>
                                        <td class="px-4 py-2 text-right text-base font-bold text-blue-600">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Timeline / Notes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Catatan & Riwayat</h3>
                        <div class="bg-gray-50 p-4 rounded-md border border-gray-200 text-sm text-gray-700 whitespace-pre-wrap font-mono">{{ $order->notes ?? 'Tidak ada catatan.' }}</div>
                        
                        <div class="mt-4">
                            <form action="{{ route('distributor.orders.update-status', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $order->status }}">
                                <div>
                                    <x-input-label for="new_note" :value="__('Tambah Catatan Internal')" />
                                    <textarea id="new_note" name="notes" rows="2" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Tulis catatan tambahan..."></textarea>
                                </div>
                                <div class="mt-2 text-right">
                                    <x-primary-button>Simpan Catatan</x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Info & Actions -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Status Pesanan</h3>
                        <div class="mb-4">
                            <span class="block text-center px-4 py-2 rounded-lg text-lg font-bold 
                                {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 
                                   ($order->status === 'shipping' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            @if($order->status !== 'cancelled' && $order->status !== 'completed')
                                <button @click="showStatusModal = true" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Update Status
                                </button>
                            @endif

                            @if($order->status === 'processing' || $order->status === 'shipping')
                                <button @click="showShippingModal = true" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Input Resi / Pengiriman
                                </button>
                            @endif

                            @if($order->status !== 'cancelled' && $order->status !== 'completed' && $order->status !== 'delivered')
                                <button @click="showCancelModal = true" class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Batalkan Pesanan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Customer Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Pelanggan</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Nama</div>
                                <div class="font-medium">{{ $order->user->name ?? 'Guest' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Email</div>
                                <div class="font-medium">{{ $order->user->email ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Telepon</div>
                                <div class="font-medium">{{ $order->recipient_phone }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pengiriman</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Penerima</div>
                                <div class="font-medium">{{ $order->recipient_name }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Alamat</div>
                                <div class="text-sm">{{ $order->shipping_address }}</div>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Metode</div>
                                <div class="font-medium">{{ ucfirst($order->delivery_type) }}</div>
                                @if($order->pickupStore)
                                    <div class="text-xs text-gray-600">Store: {{ $order->pickupStore->name }}</div>
                                @endif
                            </div>
                            @if($order->shipping_tracking_number)
                                <div class="p-2 bg-green-50 rounded border border-green-200">
                                    <div class="text-xs text-green-800 font-bold">Resi: {{ $order->shipping_tracking_number }}</div>
                                    <div class="text-xs text-green-600">Kurir: {{ $order->shipping_courier }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                 <!-- Payment Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pembayaran</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="text-xs text-gray-500 uppercase">Status</div>
                                <div class="font-medium {{ $order->payment_status === 'paid' ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </div>
                            </div>
                            @if($order->payment_proof_path)
                                <div>
                                    <div class="text-xs text-gray-500 uppercase mb-1">Bukti Bayar</div>
                                    <a href="{{ Storage::url($order->payment_proof_path) }}" target="_blank" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                        Lihat File
                                    </a>
                                </div>
                                @if($order->payment_status !== 'paid')
                                    <form action="{{ route('distributor.orders.update-status', $order) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="processing">
                                        <input type="hidden" name="notes" value="Payment verified manually.">
                                        <!-- In a real scenario, maybe separate payment status update. For now, assume verifying payment moves order to processing/paid logic if handled in controller -->
                                        <!-- Since controller only handles order status, let's just create a quick link or note -->
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modals -->
        
        <!-- Update Status Modal -->
        <div x-show="showStatusModal" class="fixed z-10 inset-0 overflow-y-auto" style="display:none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showStatusModal" @click="showStatusModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('distributor.orders.update-status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Update Status Order</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <x-input-label for="status_select" :value="__('Status Baru')" />
                                    <select id="status_select" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Shipping</option>
                                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="status_notes" :value="__('Catatan (Opsional)')" />
                                    <textarea id="status_notes" name="notes" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan
                            </button>
                            <button type="button" @click="showStatusModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Shipping Modal -->
        <div x-show="showShippingModal" class="fixed z-10 inset-0 overflow-y-auto" style="display:none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showShippingModal" @click="showShippingModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('distributor.orders.update-shipping', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Input Informasi Pengiriman</h3>
                            <div class="mt-4 space-y-4">
                                <div>
                                    <x-input-label for="shipping_courier" :value="__('Kurir / Ekspedisi')" />
                                    <x-text-input id="shipping_courier" class="block mt-1 w-full" type="text" name="shipping_courier" :value="$order->shipping_courier" required />
                                </div>
                                <div>
                                    <x-input-label for="shipping_tracking_number" :value="__('Nomor Resi')" />
                                    <x-text-input id="shipping_tracking_number" class="block mt-1 w-full" type="text" name="shipping_tracking_number" :value="$order->shipping_tracking_number" required />
                                </div>
                                <div>
                                    <x-input-label for="estimated_delivery_date" :value="__('Estimasi Tiba')" />
                                    <x-text-input id="estimated_delivery_date" class="block mt-1 w-full" type="date" name="estimated_delivery_date" :value="$order->estimated_delivery_date ? $order->estimated_delivery_date->format('Y-m-d') : ''" />
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan & Kirim Notifikasi
                            </button>
                            <button type="button" @click="showShippingModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Cancel Modal -->
        <div x-show="showCancelModal" class="fixed z-10 inset-0 overflow-y-auto" style="display:none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showCancelModal" @click="showCancelModal = false" class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form action="{{ route('distributor.orders.cancel', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Batalkan Pesanan?</h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan. Stok produk akan dikembalikan.</p>
                                        <div class="mt-4">
                                            <x-input-label for="cancellation_reason" :value="__('Alasan Pembatalan')" />
                                            <textarea id="cancellation_reason" name="cancellation_reason" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Konfirmasi Pembatalan
                            </button>
                            <button type="button" @click="showCancelModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
