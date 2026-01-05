<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">
        <!-- Progress Bar -->
        <div class="mb-8 max-w-4xl mx-auto">
            <div class="flex items-center justify-between relative">
                <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 -z-10"></div>
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                    <span class="text-sm font-medium mt-2 text-blue-600">Invoice</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                    <span class="text-sm font-medium mt-2 text-blue-600">Pembayaran</span>
                </div>
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center font-bold">3</div>
                    <span class="text-sm font-medium mt-2 text-gray-500">Selesai</span>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-800">Selesaikan Pembayaran</h2>
                <p class="text-gray-600 text-sm mt-1">Order #{{ $order->order_number }}</p>
            </div>

            <div class="p-6">
                <!-- Total Amount Highlight -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 text-center">
                    <p class="text-sm text-blue-600 font-medium uppercase tracking-wide">Total yang harus dibayar</p>
                    <p class="text-3xl font-bold text-blue-800 mt-2">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-blue-500 mt-1">*Pastikan transfer sesuai hingga 3 digit terakhir</p>
                </div>

                <!-- Bank Info -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Metode Transfer Bank</h3>
                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- BCA -->
                        <div class="border rounded-lg p-4 hover:border-blue-400 transition cursor-pointer">
                            <div class="flex items-center mb-2">
                                <span class="font-bold text-lg">BCA</span>
                            </div>
                            <p class="text-gray-600 text-sm">No. Rekening:</p>
                            <p class="font-mono text-lg font-bold text-gray-800 tracking-wide">123-456-7890</p>
                            <p class="text-sm text-gray-500 mt-1">a.n PT Emas Perak Indonesia</p>
                        </div>
                        <!-- Mandiri -->
                        <div class="border rounded-lg p-4 hover:border-blue-400 transition cursor-pointer">
                            <div class="flex items-center mb-2">
                                <span class="font-bold text-lg">MANDIRI</span>
                            </div>
                            <p class="text-gray-600 text-sm">No. Rekening:</p>
                            <p class="font-mono text-lg font-bold text-gray-800 tracking-wide">098-765-4321</p>
                            <p class="text-sm text-gray-500 mt-1">a.n PT Emas Perak Indonesia</p>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <form action="{{ route('orders.store-payment', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Konfirmasi Pembayaran</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="bank_sender" class="block text-sm font-medium text-gray-700">Bank Pengirim (Opsional)</label>
                                <input type="text" name="bank_sender" id="bank_sender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: BCA">
                            </div>

                            <div>
                                <label for="account_name" class="block text-sm font-medium text-gray-700">Nama Pemilik Rekening (Opsional)</label>
                                <input type="text" name="account_name" id="account_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Nama Anda">
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="payment_proof" class="block text-sm font-medium text-gray-700">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition bg-gray-50">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-2">
                                            <span>Upload file</span>
                                            <input id="payment_proof" name="payment_proof" type="file" class="sr-only" accept="image/*,.pdf" required>
                                        </label>
                                        <p class="pl-1">atau drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF up to 2MB</p>
                                </div>
                            </div>
                            @error('payment_proof')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-6 flex items-center justify-between">
                        <a href="{{ route('orders.invoice', $order) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                            &larr; Kembali ke Invoice
                        </a>
                        <button type="submit" class="inline-flex justify-center py-3 px-8 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Kirim Bukti Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
