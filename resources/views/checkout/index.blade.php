<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50" x-data="checkoutForm()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('checkout.store') }}" method="POST" @submit="validateForm">
                @csrf
                
                <div class="lg:grid lg:grid-cols-12 lg:gap-x-8 lg:items-start">
                    
                    <!-- LEFT COLUMN: Detail Tagihan / Shipping Info -->
                    <section class="lg:col-span-7 mb-8 lg:mb-0 space-y-8">
                        
                        <!-- 1. Informasi Pengiriman (Gabungan Data Pemesan & Alamat) -->
                        <div class="bg-white shadow-sm rounded-lg p-6 sm:p-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <span class="bg-amber-100 text-amber-600 w-8 h-8 rounded-full flex items-center justify-center mr-3 text-sm">1</span>
                                Informasi Pengiriman
                            </h2>
                            
                            <!-- Data Pemesan (Selalu Muncul) -->
                            <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 gap-x-4 mb-6 pb-6 border-b border-gray-100">
                                <div class="sm:col-span-2">
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Data Pemesan</h3>
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                                    <input type="text" value="{{ Auth::user()->name }}" readonly class="w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 shadow-sm sm:text-sm py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                    <input type="email" value="{{ Auth::user()->email }}" readonly class="w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 shadow-sm sm:text-sm py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                                    <input type="text" value="{{ Auth::user()->phone }}" readonly class="w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 shadow-sm sm:text-sm py-2">
                                </div>
                            </div>

                            <!-- Delivery Method Selection -->
                            <div class="mb-6 pb-6 border-b border-gray-100">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Metode Pengiriman</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <!-- Shipping Option -->
                                    <div class="relative flex items-center p-4 border rounded-lg cursor-pointer transition-all"
                                         :class="deliveryMethod === 'shipping' ? 'border-amber-500 bg-amber-50 ring-1 ring-amber-500' : 'border-gray-200 hover:bg-gray-50'"
                                         @click="deliveryMethod = 'shipping'">
                                        <div class="flex items-center h-5">
                                            <input type="radio" name="delivery_type" value="shipping" x-model="deliveryMethod" 
                                                   class="h-4 w-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                                        </div>
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-900">Kirim ke Alamat</span>
                                            <span class="block text-xs text-gray-500">Kurir JNE, J&T, dll.</span>
                                        </div>
                                    </div>

                                    <!-- Pickup Option -->
                                    <div class="relative flex items-center p-4 border rounded-lg cursor-pointer transition-all"
                                         :class="deliveryMethod === 'pickup' ? 'border-amber-500 bg-amber-50 ring-1 ring-amber-500' : 'border-gray-200 hover:bg-gray-50'"
                                         @click="deliveryMethod = 'pickup'">
                                        <div class="flex items-center h-5">
                                            <input type="radio" name="delivery_type" value="pickup" x-model="deliveryMethod" 
                                                   class="h-4 w-4 text-amber-600 border-gray-300 focus:ring-amber-500">
                                        </div>
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-900">Ambil di Toko</span>
                                            <span class="block text-xs text-gray-500">Bebas ongkir, ambil sendiri.</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pickup Form (Conditional) -->
                            <div x-show="deliveryMethod === 'pickup'" x-transition class="space-y-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Detail Pengambilan</h3>
                                
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 flex items-start">
                                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <div>
                                        <h4 class="text-sm font-bold text-blue-800">Info Pengambilan</h4>
                                        <p class="text-xs text-blue-700 mt-1">
                                            Silakan ambil pesanan Anda di lokasi toko berikut. Tunjukkan kode QR atau Token saat mengambil pesanan.
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 gap-x-4">
                                    <!-- Store Selection (Single Store Display) -->
                                    <div class="sm:col-span-2" x-data="{
                                        store: {{ $storeLocations->first() ? $storeLocations->first()->toJson() : 'null' }}
                                    }" x-init="if(store) { document.getElementById('pickup_store_id').value = store.id; }">
                                        
                                        <template x-if="store">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Toko</label>
                                                <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                                    <div class="flex items-start">
                                                        <div class="flex-shrink-0 bg-amber-100 rounded-full p-2">
                                                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <div class="ml-4 flex-1">
                                                            <h4 class="text-base font-bold text-gray-900" x-text="store.name"></h4>
                                                            <p class="text-sm text-gray-600 mt-1" x-text="store.address"></p>
                                                            <div class="flex flex-wrap gap-2 mt-2 text-xs text-gray-500">
                                                                <span class="bg-gray-100 px-2 py-1 rounded" x-text="store.city"></span>
                                                                <span class="bg-gray-100 px-2 py-1 rounded" x-text="store.province"></span>
                                                                <span class="bg-gray-100 px-2 py-1 rounded" x-text="store.postal_code"></span>
                                                            </div>
                                                            <template x-if="store.notes">
                                                                <div class="mt-3 text-xs text-gray-500 bg-gray-50 p-2 rounded border border-gray-100">
                                                                    <span class="font-semibold">Catatan:</span> <span x-text="store.notes"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="pickup_store_id" id="pickup_store_id" :required="deliveryMethod === 'pickup'">
                                            </div>
                                        </template>

                                        <template x-if="!store">
                                            <div class="bg-red-50 border border-red-200 rounded-md p-4 text-red-700 text-sm">
                                                Maaf, belum ada lokasi toko yang tersedia untuk pengambilan. Hubungi admin toko.
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Pickup Time -->
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Pengambilan <span class="text-red-500">*</span></label>
                                        <input type="datetime-local" name="pickup_at" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2"
                                               :required="deliveryMethod === 'pickup'"
                                               min="{{ now()->format('Y-m-d\TH:i') }}">
                                    </div>
                                </div>
                            </div>

                            <!-- Alamat Tagihan / Default (HILANG jika kirim ke alamat berbeda) -->
                            <div x-show="deliveryMethod === 'shipping' && !shipToDifferentAddress" x-transition class="space-y-6">
                                <h3 class="text-sm font-semibold text-gray-900 mb-3">Alamat Tujuan</h3>
                                
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                    <!-- Recipient Info (Default) -->
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span class="text-red-500">*</span></label>
                                        <input type="text" name="recipient_name" x-model="billing.recipientName"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" 
                                               :required="deliveryMethod === 'shipping' && !shipToDifferentAddress">
                                    </div>
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Penerima <span class="text-red-500">*</span></label>
                                        <input type="text" name="recipient_phone" x-model="billing.recipientPhone"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2"
                                               :required="deliveryMethod === 'shipping' && !shipToDifferentAddress">
                                    </div>

                                    <!-- Wilayah (Billing) -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah <span class="text-red-500">*</span></label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <!-- Province -->
                                            <div>
                                                <select name="shipping_province" x-model="billing.provinceId" @change="fetchCities('billing')" 
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" 
                                                        :required="deliveryMethod === 'shipping' && !shipToDifferentAddress">
                                                    <option value="">Provinsi</option>
                                                    @foreach($provinces as $province)
                                                        <option value="{{ $province['province_id'] }}">{{ $province['province'] }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="shipping_province_name" x-model="billing.provinceName">
                                            </div>
                                            <!-- City -->
                                            <div>
                                                <select name="shipping_city" x-model="billing.cityId" @change="fetchDistricts('billing')" 
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" 
                                                        :disabled="!billingCities.length" :required="deliveryMethod === 'shipping' && !shipToDifferentAddress">
                                                    <option value="">Kota/Kab</option>
                                                    <template x-for="city in billingCities" :key="city.city_id">
                                                        <option :value="city.city_id" x-text="city.type + ' ' + city.city_name"></option>
                                                    </template>
                                                </select>
                                                <input type="hidden" name="shipping_city_name" x-model="billing.cityName">
                                            </div>
                                            <!-- District -->
                                            <div>
                                                <select name="shipping_district" x-model="billing.districtId" @change="updateDistrictName('billing')" 
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" 
                                                        :disabled="!billingDistricts.length" :required="deliveryMethod === 'shipping' && !shipToDifferentAddress">
                                                    <option value="">Kecamatan</option>
                                                    <template x-for="district in billingDistricts" :key="district.subdistrict_id">
                                                        <option :value="district.subdistrict_id" x-text="district.subdistrict_name"></option>
                                                    </template>
                                                </select>
                                                <input type="hidden" name="shipping_district_name" x-model="billing.districtName">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                                        <textarea name="shipping_address" rows="2" x-model="billing.address"
                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"
                                                  placeholder="Nama jalan, nomor rumah..." :required="deliveryMethod === 'shipping' && !shipToDifferentAddress"></textarea>
                                    </div>

                                    <!-- Postal Code -->
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos <span class="text-red-500">*</span></label>
                                        <input type="text" name="shipping_postal_code" x-model="billing.postalCode"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" 
                                               :required="deliveryMethod === 'shipping' && !shipToDifferentAddress">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ship to Different Address Checkbox -->
                            <div class="mt-6 pt-6 border-t border-gray-100" x-show="deliveryMethod === 'shipping'">
                                <div class="flex items-center">
                                    <input id="ship_to_different_address" name="ship_to_different_address" type="checkbox" 
                                           x-model="shipToDifferentAddress"
                                           class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300 rounded">
                                    <label for="ship_to_different_address" class="ml-2 block text-sm font-medium text-gray-900">
                                        Kirim ke alamat yang berbeda?
                                    </label>
                                </div>
                            </div>

                            <!-- Shipping Address Form (Conditional) -->
                            <div x-show="shipToDifferentAddress && deliveryMethod === 'shipping'" x-transition class="mt-6 pt-6 border-t border-gray-100 space-y-6">
                                <h3 class="text-lg font-medium text-gray-900">Alamat Pengiriman Baru</h3>
                                
                                <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                                    <!-- Recipient Name -->
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima <span class="text-red-500">*</span></label>
                                        <input type="text" name="recipient_name_new" :required="shipToDifferentAddress"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2">
                                    </div>
                                    <!-- Recipient Phone -->
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Penerima <span class="text-red-500">*</span></label>
                                        <input type="text" name="recipient_phone_new" :required="shipToDifferentAddress"
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2">
                                    </div>

                                    <!-- Wilayah (Shipping) -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah Pengiriman <span class="text-red-500">*</span></label>
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <!-- Province -->
                                            <div>
                                                <select name="shipping_province_new" x-model="shipping.provinceId" @change="fetchCities('shipping')" 
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2"
                                                        :required="shipToDifferentAddress">
                                                    <option value="">Provinsi</option>
                                                    @foreach($provinces as $province)
                                                        <option value="{{ $province['province_id'] }}">{{ $province['province'] }}</option>
                                                    @endforeach
                                                </select>
                                                <input type="hidden" name="shipping_province_name_new" x-model="shipping.provinceName">
                                            </div>
                                            <!-- City -->
                                            <div>
                                                <select name="shipping_city_new" x-model="shipping.cityId" @change="fetchDistricts('shipping')" 
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" 
                                                        :disabled="!shippingCities.length" :required="shipToDifferentAddress">
                                                    <option value="">Kota/Kab</option>
                                                    <template x-for="city in shippingCities" :key="city.city_id">
                                                        <option :value="city.city_id" x-text="city.type + ' ' + city.city_name"></option>
                                                    </template>
                                                </select>
                                                <input type="hidden" name="shipping_city_name_new" x-model="shipping.cityName">
                                            </div>
                                            <!-- District -->
                                            <div>
                                                <select name="shipping_district_new" x-model="shipping.districtId" @change="updateDistrictName('shipping')" 
                                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" 
                                                        :disabled="!shippingDistricts.length" :required="shipToDifferentAddress">
                                                    <option value="">Kecamatan</option>
                                                    <template x-for="district in shippingDistricts" :key="district.subdistrict_id">
                                                        <option :value="district.subdistrict_id" x-text="district.subdistrict_name"></option>
                                                    </template>
                                                </select>
                                                <input type="hidden" name="shipping_district_name_new" x-model="shipping.districtName">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Address -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                                        <textarea name="shipping_address_new" rows="2" 
                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"
                                                  placeholder="Nama jalan, nomor rumah..." :required="shipToDifferentAddress"></textarea>
                                    </div>

                                    <!-- Postal Code -->
                                    <div class="sm:col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos <span class="text-red-500">*</span></label>
                                        <input type="text" name="shipping_postal_code_new" 
                                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm py-2" :required="shipToDifferentAddress">
                                    </div>
                                    
                                    <!-- Shipping Note -->
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pengiriman (Opsional)</label>
                                        <textarea name="shipping_note" rows="2" 
                                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-sm"
                                                  placeholder="Petunjuk khusus untuk kurir..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>


                    <!-- RIGHT COLUMN: Order Summary -->
                    <section class="lg:col-span-5">
                        <div class="bg-white shadow-md rounded-lg p-6 sm:p-8 border border-gray-100 sticky top-6">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h2>
                            
                            <!-- Items List -->
                            <ul class="divide-y divide-gray-100 mb-6 max-h-60 overflow-y-auto custom-scrollbar">
                                @foreach($cart->items as $item)
                                    <li class="flex py-4">
                                        <div class="h-14 w-14 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 bg-gray-100">
                                            @if($item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}" class="h-full w-full object-cover object-center">
                                            @else
                                                <svg class="h-full w-full text-gray-300 p-2" fill="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            @endif
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="flex justify-between text-sm font-medium text-gray-900">
                                                <h3 class="line-clamp-1">{{ $item->product->name }}</h3>
                                                <p class="ml-2">Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}</p>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">{{ $item->quantity }} x Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Delivery & Payment Section (Moved to Summary) -->
                            <div class="border-t border-gray-100 py-6 space-y-6">
                                <!-- Courier Selection -->
                                <div x-show="deliveryMethod === 'shipping'" x-transition>
                                    <h4 class="text-sm font-bold text-gray-900 mb-3">Layanan Pengiriman</h4>
                                    
                                    <div x-show="isLoadingShipping" class="flex items-center justify-center py-4 bg-gray-50 rounded-lg border border-gray-200 border-dashed">
                                        <svg class="animate-spin h-5 w-5 mr-2 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        <span class="text-xs text-gray-500">Hitung ongkir...</span>
                                    </div>

                                    <div x-show="!isLoadingShipping && shippingServices.length === 0" class="text-xs text-yellow-700 bg-yellow-50 p-3 rounded-lg border border-yellow-200">
                                        <p>Lengkapi alamat untuk melihat kurir.</p>
                                    </div>

                                    <div x-show="!isLoadingShipping && shippingServices.length > 0" class="space-y-2">
                                        <template x-for="service in shippingServices" :key="service.id">
                                            <div class="flex items-center p-3 border rounded-lg cursor-pointer bg-white hover:bg-gray-50 transition-colors"
                                                 :class="selectedService && selectedService.id === service.id ? 'border-amber-500 ring-1 ring-amber-500 bg-amber-50' : 'border-gray-200'"
                                                 @click="selectService(service)">
                                                <input type="radio" name="shipping_service_selection" :value="service.id" :checked="selectedService && selectedService.id === service.id" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                                <div class="ml-3 flex-1 flex justify-between items-center">
                                                    <div>
                                                        <span class="block text-sm font-bold text-gray-900" x-text="service.courier_name"></span>
                                                        <span class="block text-xs text-gray-500" x-text="service.service + ' (' + service.etd + ' hr)'"></span>
                                                    </div>
                                                    <span class="block text-sm font-bold text-amber-600" x-text="service.formatted_cost"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    
                                    <input type="hidden" name="shipping_courier" :value="deliveryMethod === 'pickup' ? '' : (selectedService ? selectedService.courier_code : '')">
                                    <input type="hidden" name="shipping_service" :value="deliveryMethod === 'pickup' ? '' : (selectedService ? selectedService.service : '')">
                                    <input type="hidden" name="shipping_cost" :value="deliveryMethod === 'pickup' ? 0 : (selectedService ? selectedService.cost : 0)">
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 mb-3">Metode Pembayaran</h4>
                                    <div class="space-y-2">
                                        <!-- Bank Transfer -->
                                        <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input id="pay_bank" name="payment_method" type="radio" value="bank_transfer" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300" required>
                                            <label for="pay_bank" class="ml-3 block text-sm font-medium text-gray-900 w-full cursor-pointer">
                                                Transfer Bank
                                            </label>
                                        </div>
                                        <!-- E-Wallet -->
                                        <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input id="pay_ewallet" name="payment_method" type="radio" value="e_wallet" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                            <label for="pay_ewallet" class="ml-3 block text-sm font-medium text-gray-900 w-full cursor-pointer">
                                                E-Wallet (QRIS)
                                            </label>
                                        </div>
                                        <!-- COD -->
                                        <div class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                            <input id="pay_cod" name="payment_method" type="radio" value="cod" class="h-4 w-4 text-amber-600 focus:ring-amber-500 border-gray-300">
                                            <label for="pay_cod" class="ml-3 w-full cursor-pointer">
                                                <span class="block text-sm font-medium text-gray-900">COD (Bayar di Tempat)</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Coupon Code -->
                            <div class="py-4 border-t border-gray-100" x-data="{ showCoupon: false }">
                                <button type="button" class="text-sm text-amber-600 font-medium hover:text-amber-700 flex items-center" 
                                        @click="showCoupon = !showCoupon" x-show="!showCoupon">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                                    Ada kode kupon?
                                </button>
                                
                                <div x-show="showCoupon" x-transition class="mt-2">
                                    <div class="flex space-x-2">
                                        <input type="text" x-model="couponCode" placeholder="Masukkan kode kupon" 
                                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500 sm:text-xs">
                                        <button type="button" @click="applyCoupon" 
                                                class="bg-gray-800 text-white px-3 py-2 rounded-md text-xs font-medium hover:bg-gray-700 transition-colors">
                                            Terapkan
                                        </button>
                                    </div>
                                    <p class="text-xs mt-1" :class="couponSuccess ? 'text-green-600' : 'text-red-500'" x-text="couponMessage"></p>
                                    <input type="hidden" name="coupon_code" x-model="appliedCouponCode">
                                    <input type="hidden" name="discount_amount" x-model="discountAmount">
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="border-t border-gray-200 pt-4 space-y-3">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <p>Subtotal</p>
                                    <p>Rp {{ number_format($cart->total, 0, ',', '.') }}</p>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <p>Pengiriman</p>
                                    <p class="font-medium" :class="deliveryMethod === 'pickup' ? 'text-green-600' : 'text-gray-900'" 
                                       x-text="deliveryMethod === 'pickup' ? 'Gratis (Ambil di Toko)' : (selectedService ? selectedService.formatted_cost : '-')"></p>
                                </div>
                                <div class="flex justify-between text-sm text-green-600 font-medium" x-show="discountAmount > 0">
                                    <p>Diskon</p>
                                    <p x-text="'- ' + formattedDiscount"></p>
                                </div>
                                <div class="flex justify-between text-base font-bold text-gray-900 pt-3 border-t border-gray-100">
                                    <p>Total Tagihan</p>
                                    <p x-text="'Rp ' + (subtotal + shippingCostDisplay - discountAmount).toLocaleString('id-ID')"></p>
                                </div>
                            </div>

                            <button type="submit" 
                                    class="mt-6 w-full bg-amber-500 border border-transparent rounded-md shadow-sm py-3 px-4 text-base font-bold text-white hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all transform hover:scale-[1.02]">
                                Buat Pesanan
                            </button>
                        </div>
                    </section>
                </div>
            </form>
        </div>
    </div>

    @php
        $user = Auth::user();
        $cartWeight = $cart->items->sum(fn($i) => $i->quantity * ($i->product->weight ?? 1000));
        
        $jsConfig = [
            'subtotal' => $cart->total,
            'weight' => $cartWeight,
            'user' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'provinceId' => $user->province_id ?? "",
                'provinceName' => $user->province_name ?? "",
                'cityId' => $user->city_id ?? "",
                'cityName' => $user->city_name ?? "",
                'districtId' => $user->district_id ?? "",
                'districtName' => $user->district_name ?? "",
                'address' => $user->address ?? "",
                'postalCode' => $user->postal_code ?? "",
            ],
            'routes' => [
                'checkShipping' => route('checkout.check-shipping'),
                'checkCoupon' => route('checkout.check-coupon'),
                'getCities' => '/checkout/cities/',
                'getDistricts' => '/checkout/districts/',
            ],
            'csrfToken' => csrf_token(),
        ];
    @endphp

    <script>
        const checkoutConfig = <?php echo json_encode($jsConfig); ?>;

        function checkoutForm() {
            return {
                shipToDifferentAddress: false,
                deliveryMethod: 'shipping',
                subtotal: checkoutConfig.subtotal,
                
                // Billing Address (Default)
                billing: {
                    recipientName: checkoutConfig.user.name,
                    recipientPhone: checkoutConfig.user.phone,
                    provinceId: checkoutConfig.user.provinceId,
                    provinceName: checkoutConfig.user.provinceName,
                    cityId: checkoutConfig.user.cityId,
                    cityName: checkoutConfig.user.cityName,
                    districtId: checkoutConfig.user.districtId,
                    districtName: checkoutConfig.user.districtName,
                    address: checkoutConfig.user.address,
                    postalCode: checkoutConfig.user.postalCode
                },
                billingCities: [],
                billingDistricts: [],

                // Shipping Address (New)
                shipping: {
                    provinceId: '',
                    provinceName: '',
                    cityId: '',
                    cityName: '',
                    districtId: '',
                    districtName: '',
                    address: '',
                    postalCode: ''
                },
                shippingCities: [],
                shippingDistricts: [],

                // Shipping Service State
                shippingServices: [],
                selectedService: null,
                isLoadingShipping: false,

                // Coupon State
                couponCode: '',
                appliedCouponCode: '',
                discountAmount: 0,
                formattedDiscount: '',
                couponMessage: '',
                couponSuccess: false,

                // Computed Logic
                get effectiveCityId() {
                    return this.shipToDifferentAddress ? this.shipping.cityId : this.billing.cityId;
                },
                get effectiveDistrictId() {
                    return this.shipToDifferentAddress ? this.shipping.districtId : this.billing.districtId;
                },
                get shippingCostDisplay() {
                    if (this.deliveryMethod === 'pickup') return 0;
                    return this.selectedService ? this.selectedService.cost : 0;
                },

                init() {
                    // Load initial data for Billing
                    if (this.billing.provinceId) {
                        this.fetchCities('billing').then(() => {
                            if (this.billing.cityId) {
                                this.fetchDistricts('billing');
                                // Check shipping initially
                                this.checkShipping();
                            }
                        });
                    }
                    
                    // Watchers
                    this.$watch('shipToDifferentAddress', value => this.checkShipping());
                    this.$watch('deliveryMethod', value => {
                        // Reset shipping cost logic if pickup
                        if (value === 'pickup') {
                            this.selectedService = null;
                        } else {
                            this.checkShipping();
                        }
                    });
                },

                async fetchCities(type) {
                    const target = type === 'billing' ? this.billing : this.shipping;
                    const listName = type === 'billing' ? 'billingCities' : 'shippingCities';
                    
                    if (!target.provinceId) return;

                    // Update Name
                    const select = document.querySelector(`select[x-model="${type}.provinceId"]`);
                    if (select) target.provinceName = select.options[select.selectedIndex].text;

                    try {
                        const res = await fetch(checkoutConfig.routes.getCities + target.provinceId);
                        this[listName] = await res.json();
                        target.cityId = ''; 
                        target.districtId = '';
                        this[type === 'billing' ? 'billingDistricts' : 'shippingDistricts'] = [];
                    } catch (e) { console.error(e); }
                },

                async fetchDistricts(type) {
                    const target = type === 'billing' ? this.billing : this.shipping;
                    const listName = type === 'billing' ? 'billingDistricts' : 'shippingDistricts';
                    
                    if (!target.cityId) return;

                    // Update Name
                    const select = document.querySelector(`select[x-model="${type}.cityId"]`);
                    if (select) target.cityName = select.options[select.selectedIndex].text.replace(/^(Kabupaten|Kota)\s+/, '');

                    try {
                        const res = await fetch(checkoutConfig.routes.getDistricts + target.cityId);
                        this[listName] = await res.json();
                        target.districtId = '';
                        // Trigger shipping check if this is the active address
                        if ((type === 'billing' && !this.shipToDifferentAddress) || 
                            (type === 'shipping' && this.shipToDifferentAddress)) {
                            this.checkShipping();
                        }
                    } catch (e) { console.error(e); }
                },
                
                updateDistrictName(type) {
                    const target = type === 'billing' ? this.billing : this.shipping;
                    const select = document.querySelector(`select[x-model="${type}.districtId"]`);
                    if (select) target.districtName = select.options[select.selectedIndex].text;
                    
                    if ((type === 'billing' && !this.shipToDifferentAddress) || 
                        (type === 'shipping' && this.shipToDifferentAddress)) {
                        this.checkShipping();
                    }
                },

                async checkShipping() {
                    if (this.deliveryMethod !== 'shipping') return;
                    
                    const cityId = this.effectiveCityId;
                    const districtId = this.effectiveDistrictId; // Optional for now
                    
                    if (!cityId) {
                        this.shippingServices = [];
                        return;
                    }

                    this.isLoadingShipping = true;
                    this.shippingServices = [];
                    this.selectedService = null;

                    try {
                        const res = await fetch(checkoutConfig.routes.checkShipping, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': checkoutConfig.csrfToken
                            },
                            body: JSON.stringify({
                                city_id: cityId,
                                district_id: districtId,
                                weight: checkoutConfig.weight
                            })
                        });
                        const data = await res.json();
                        this.shippingServices = data;
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isLoadingShipping = false;
                    }
                },

                selectService(service) {
                    this.selectedService = service;
                },

                async applyCoupon() {
                    if (!this.couponCode) return;
                    
                    try {
                        const res = await fetch(checkoutConfig.routes.checkCoupon, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': checkoutConfig.csrfToken },
                            body: JSON.stringify({ coupon_code: this.couponCode })
                        });
                        const data = await res.json();
                        
                        if (data.success) {
                            this.couponSuccess = true;
                            this.couponMessage = data.message;
                            this.discountAmount = data.discount_amount;
                            this.formattedDiscount = data.formatted_discount;
                            this.appliedCouponCode = this.couponCode;
                        } else {
                            this.couponSuccess = false;
                            this.couponMessage = data.message || 'Kupon tidak valid';
                            this.discountAmount = 0;
                            this.appliedCouponCode = '';
                        }
                    } catch (e) {
                        this.couponSuccess = false;
                        this.couponMessage = 'Terjadi kesalahan saat mengecek kupon.';
                    }
                },
                
                validateForm(e) {
                    // Optional: Custom validation before submit if needed
                }
            };
        }
    </script>
</x-app-layout>
