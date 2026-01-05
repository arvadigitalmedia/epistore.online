<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Pengiriman') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="shippingSettings()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Settings Form -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Konfigurasi Dasar') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Atur lokasi asal pengiriman dan preferensi kurir toko Anda.
                    </p>
                </header>

                <form method="post" action="{{ route('distributor.shipping.update') }}" class="mt-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Origin Province (Searchable) -->
                        <div x-data="{
                            search: '',
                            open: false,
                            provinces: {{ json_encode($provinces) }},
                            selectedName: '{{ $shippingConfig['origin_province_name'] ?? '' }}',
                            init() {
                                // Find initial name if value exists
                                const initial = this.provinces.find(p => p.province_id == this.originProvince);
                                if(initial) {
                                    this.selectedName = initial.province;
                                    this.originProvinceName = initial.province;
                                }
                            },
                            get filtered() {
                                if (this.search === '') return this.provinces;
                                return this.provinces.filter(p => p.province.toLowerCase().includes(this.search.toLowerCase()));
                            },
                            select(id, name) {
                                this.originProvince = id;
                                this.originProvinceName = name;
                                this.selectedName = name;
                                this.open = false;
                                this.search = '';
                                this.loadCities(id, 'origin');
                            }
                        }" @click.away="open = false">
                            <x-input-label for="origin_province_id" :value="__('Provinsi Asal')" />
                            
                            <div class="relative mt-1">
                                <input type="hidden" name="origin_province_id" x-model="originProvince">
                                <input type="hidden" name="origin_province_name" x-model="originProvinceName">
                                
                                <button type="button" @click="open = !open" 
                                    class="relative w-full cursor-default rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 text-left shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm">
                                    <span class="block truncate" x-text="selectedName || 'Pilih Provinsi'"></span>
                                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                </button>

                                <div x-show="open" 
                                    class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm"
                                    style="display: none;">
                                    <div class="sticky top-0 z-10 bg-white px-2 py-1.5">
                                        <input type="text" x-model="search" class="w-full rounded-md border-gray-300 py-1 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Cari provinsi...">
                                    </div>
                                    <ul class="max-h-50 overflow-auto">
                                        <template x-for="province in filtered" :key="province.province_id">
                                            <li @click="select(province.province_id, province.province)"
                                                class="relative cursor-default select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-indigo-600 hover:text-white">
                                                <span class="block truncate" x-text="province.province" :class="{ 'font-semibold': originProvince == province.province_id }"></span>
                                                <span x-show="originProvince == province.province_id" class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600 hover:text-white">
                                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </li>
                                        </template>
                                        <li x-show="filtered.length === 0" class="py-2 pl-3 pr-9 text-gray-500 italic">Tidak ditemukan</li>
                                    </ul>
                                </div>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('origin_province_id')" />
                        </div>

                        <!-- Origin City -->
                        <div>
                            <x-input-label for="origin_city_id" :value="__('Kota/Kabupaten Asal')" />
                            <select id="origin_city_id" name="origin_city_id" required class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                x-model="originCity" @change="updateCityName(); loadDistricts(originCity, 'origin')">
                                <option value="">Pilih Kota</option>
                                {{-- SERVER-SIDE FALLBACK: Ensure selected option exists immediately --}}
                                @if(old('origin_city_id', $shippingConfig['origin_city_id'] ?? ''))
                                    <option value="{{ old('origin_city_id', $shippingConfig['origin_city_id']) }}" selected>
                                        {{ old('origin_city_name', $shippingConfig['origin_city_name']) }}
                                    </option>
                                @endif
                                <template x-for="city in originCities" :key="city.city_id">
                                    <option :value="city.city_id" x-text="city.type + ' ' + city.city_name" 
                                        x-show="city.city_id != '{{ old('origin_city_id', $shippingConfig['origin_city_id'] ?? '') }}'"></option>
                                </template>
                            </select>
                            <input type="hidden" name="origin_city_name" x-model="originCityName">
                            <x-input-error class="mt-2" :messages="$errors->get('origin_city_id')" />
                        </div>

                        <!-- Origin District (Kecamatan) -->
                        <div>
                            <x-input-label for="origin_district_id" :value="__('Kecamatan Asal')" />
                            <select id="origin_district_id" name="origin_district_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                x-model="originDistrict" @change="updateDistrictName();">
                                <option value="">Pilih Kecamatan</option>
                                {{-- SERVER-SIDE FALLBACK: Ensure selected option exists immediately --}}
                                @if(old('origin_district_id', $shippingConfig['origin_district_id'] ?? ''))
                                    <option value="{{ old('origin_district_id', $shippingConfig['origin_district_id']) }}" selected>
                                        {{ old('origin_district_name', $shippingConfig['origin_district_name']) }}
                                    </option>
                                @endif
                                <template x-for="district in originDistricts" :key="district.subdistrict_id">
                                    <option :value="district.subdistrict_id" x-text="district.subdistrict_name"
                                        x-show="district.subdistrict_id != '{{ old('origin_district_id', $shippingConfig['origin_district_id'] ?? '') }}'"></option>
                                </template>
                            </select>
                            <input type="hidden" name="origin_district_name" x-model="originDistrictName">
                            <x-input-error class="mt-2" :messages="$errors->get('origin_district_id')" />
                        </div>

                        <!-- Fields Removed: SubDistrict, Postal Code, Address as per request -->
                        <input type="hidden" name="origin_subdistrict_id" value="">
                        <input type="hidden" name="origin_subdistrict_name" value="">
                        <input type="hidden" name="origin_postal_code" value="">
                        <input type="hidden" name="origin_address" value="">

                        <!-- Default Weight -->
                        <div>
                            <x-input-label for="default_weight" :value="__('Berat Default (gram)')" />
                            <x-text-input id="default_weight" name="default_weight" type="number" class="mt-1 block w-full" :value="old('default_weight', $shippingConfig['default_weight'])" required />
                            <x-input-error class="mt-2" :messages="$errors->get('default_weight')" />
                        </div>

                        <!-- Margin -->
                        <div>
                            <x-input-label for="margin" :value="__('Margin Biaya (IDR)')" />
                            <x-text-input id="margin" name="margin" type="number" class="mt-1 block w-full" :value="old('margin', $shippingConfig['margin'])" />
                            <p class="text-xs text-gray-500 mt-1">Tambahan biaya packing atau admin per pengiriman.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('margin')" />
                        </div>
                    </div>

                    <!-- Couriers -->
                    <div>
                        <x-input-label :value="__('Layanan Kurir')" class="mb-2" />
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($couriers as $courier)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="couriers[]" value="{{ $courier->code }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                        @if(in_array($courier->code, old('couriers', $shippingConfig['couriers']))) checked @endif>
                                    <span class="ml-2">{{ $courier->name }} ({{ strtoupper($courier->code) }})</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('couriers')" />
                    </div>

                    <!-- Store Pickup Toggle -->
                    <div class="mt-6">
                        <div class="flex items-center justify-between bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Aktifkan Pembayaran di Toko (COD)</h3>
                                <p class="text-xs text-gray-500 mt-1">Izinkan pelanggan mengambil pesanan di lokasi toko dan membayar tunai.</p>
                            </div>
                            <div class="flex items-center">
                                <label for="enable_store_pickup" class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" id="enable_store_pickup" name="enable_store_pickup" value="1" class="sr-only peer"
                                        @if(old('enable_store_pickup', $shippingConfig['enable_store_pickup'] ?? false)) checked @endif>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>{{ __('Simpan Pengaturan') }}</x-primary-button>

                        @if (session('success'))
                            <p
                                x-data="{ show: true }"
                                x-show="show"
                                x-transition
                                x-init="setTimeout(() => show = false, 2000)"
                                class="text-sm text-gray-600"
                            >{{ session('success') }}</p>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Preview Calculator -->
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <header>
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('Simulasi Ongkos Kirim') }}
                    </h2>
                    <p class="mt-1 text-sm text-gray-600">
                        Cek perkiraan ongkos kirim dari lokasi Anda.
                    </p>
                </header>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Dest Province -->
                    <div>
                        <x-input-label for="preview_province_id" :value="__('Provinsi Tujuan')" />
                        <select id="preview_province_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            x-model="previewProvince" @change="loadCities(previewProvince, 'preview')">
                            <option value="">Pilih Provinsi</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province['province_id'] }}">{{ $province['province'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Dest City -->
                    <div>
                        <x-input-label for="preview_city_id" :value="__('Kota Tujuan')" />
                        <select id="preview_city_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            x-model="previewCity" @change="loadDistricts(previewCity, 'preview')">
                            <option value="">Pilih Kota</option>
                            <template x-for="city in previewCities" :key="city.city_id">
                                <option :value="city.city_id" x-text="city.type + ' ' + city.city_name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Dest District -->
                    <div>
                        <x-input-label for="preview_district_id" :value="__('Kecamatan Tujuan')" />
                        <select id="preview_district_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            x-model="previewDistrict">
                            <option value="">Pilih Kecamatan</option>
                            <template x-for="district in previewDistricts" :key="district.subdistrict_id">
                                <option :value="district.subdistrict_id" x-text="district.subdistrict_name"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Courier -->
                    <div>
                        <x-input-label for="preview_courier" :value="__('Kurir')" />
                        <select id="preview_courier" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            x-model="previewCourier">
                            <option value="">Pilih Kurir</option>
                            @foreach($couriers as $courier)
                                <option value="{{ $courier->code }}">{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                     <!-- Weight -->
                     <div>
                        <x-input-label for="preview_weight" :value="__('Berat (gram)')" />
                        <x-text-input id="preview_weight" type="number" class="mt-1 block w-full" x-model="previewWeight" />
                    </div>
                </div>

                <div class="mt-6">
                    <button type="button" @click="checkCost" :disabled="loading"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <span x-show="!loading">{{ __('Cek Ongkir') }}</span>
                        <span x-show="loading">{{ __('Memuat...') }}</span>
                    </button>
                </div>

                <!-- Result -->
                <div class="mt-6" x-show="previewResult">
                    <h3 class="font-medium text-gray-900 mb-2">Hasil Perhitungan:</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <template x-if="previewResult && previewResult.costs">
                            <div class="space-y-2">
                                <template x-for="cost in previewResult.costs" :key="cost.service">
                                    <div class="flex justify-between items-center border-b border-gray-200 last:border-0 pb-2 last:pb-0">
                                        <div>
                                            <span class="font-bold text-gray-800" x-text="cost.service"></span>
                                            <span class="text-sm text-gray-500 block" x-text="cost.description"></span>
                                            <span class="text-xs text-gray-400 block" x-text="'Est: ' + cost.cost[0].etd + ' hari'"></span>
                                        </div>
                                        <div class="text-right">
                                            <span class="font-bold text-indigo-600" x-text="formatRupiah(cost.cost[0].value)"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        <template x-if="!previewResult || !previewResult.costs">
                            <p class="text-gray-500">Tidak ada data ongkir ditemukan.</p>
                        </template>
                    </div>
                </div>
                
                <div class="mt-6" x-show="errorMessage">
                    <p class="text-red-600" x-text="errorMessage"></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function shippingSettings() {
            return {
                originProvince: '{{ old("origin_province_id", $shippingConfig["origin_province_id"] ?? "") }}',
                originProvinceName: '{{ old("origin_province_name", $shippingConfig["origin_province_name"] ?? "") }}',
                originCity: '{{ old("origin_city_id", $shippingConfig["origin_city_id"] ?? "") }}',
                originCityName: '{{ old("origin_city_name", $shippingConfig["origin_city_name"] ?? "") }}',
                originDistrict: '{{ old("origin_district_id", $shippingConfig["origin_district_id"] ?? "") }}',
                originDistrictName: '{{ old("origin_district_name", $shippingConfig["origin_district_name"] ?? "") }}',
                
                originCities: [],
                originDistricts: [],
                
                previewProvince: '',
                previewCity: '',
                previewDistrict: '',
                previewCities: [],
                previewDistricts: [],
                previewCourier: '{{ $couriers->first()->code ?? "" }}',
                previewWeight: '{{ $shippingConfig["default_weight"] }}',
                previewResult: null,
                errorMessage: null,
                loading: false,

                init() {
                    // Load full data if province is selected
                    // We pass 'false' to avoid resetting the currently selected value during page load
                    if (this.originProvince) {
                        this.loadCities(this.originProvince, 'origin', false);
                    }
                },

                updateCityName() {
                    const city = this.originCities.find(c => c.city_id == this.originCity);
                    if (city) {
                        this.originCityName = city.type + ' ' + city.city_name;
                    }
                },

                updateDistrictName() {
                    const district = this.originDistricts.find(d => d.subdistrict_id == this.originDistrict);
                    if (district) {
                        this.originDistrictName = district.subdistrict_name;
                    }
                },

                async loadCities(provinceId, type, resetChild = true) {
                    if (!provinceId) return;
                    
                    this.errorMessage = null;

                    try {
                        const url = "{{ route('distributor.shipping.cities', ':id') }}".replace(':id', provinceId);
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Gagal memuat data kota');
                        
                        const cities = await response.json();
                        
                        if (type === 'origin') {
                            this.originCities = cities;
                            if (resetChild) {
                                this.originCity = '';
                                this.originCityName = '';
                                this.originDistricts = [];
                                this.originDistrict = '';
                                this.originDistrictName = '';
                            } else {
                                if (this.originCity) {
                                    this.loadDistricts(this.originCity, 'origin', false);
                                }
                            }
                        } else {
                            this.previewCities = cities;
                            this.previewCity = '';
                            this.previewDistricts = [];
                            this.previewDistrict = '';
                        }
                    } catch (error) {
                        console.error('Error loading cities:', error);
                        this.errorMessage = 'Gagal memuat daftar kota. Silakan coba lagi.';
                    }
                },

                async loadDistricts(cityId, type, resetChild = true) {
                    if (!cityId) return;
                    
                    this.errorMessage = null;

                    try {
                        const url = "{{ route('distributor.shipping.districts', ':id') }}".replace(':id', cityId);
                        const response = await fetch(url);
                        if (!response.ok) throw new Error('Gagal memuat data kecamatan');

                        const districts = await response.json();
                        
                        if (type === 'origin') {
                            this.originDistricts = districts;
                            if (resetChild) {
                                this.originDistrict = '';
                                this.originDistrictName = '';
                            }
                        } else {
                            this.previewDistricts = districts;
                            this.previewDistrict = '';
                        }
                    } catch (error) {
                        console.error('Error loading districts:', error);
                        this.errorMessage = 'Gagal memuat daftar kecamatan. Silakan coba lagi.';
                    }
                },

                async checkCost() {
                    if (!this.previewCity || !this.previewWeight || !this.previewCourier) {
                        this.errorMessage = 'Mohon lengkapi data tujuan, berat, dan kurir.';
                        return;
                    }

                    this.loading = true;
                    this.errorMessage = null;
                    this.previewResult = null;

                    try {
                        const response = await fetch('{{ route("distributor.shipping.preview") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                destination_city_id: this.previewCity,
                                destination_district_id: this.previewDistrict, // Send even if backend doesn't use it yet
                                weight: this.previewWeight,
                                courier: this.previewCourier
                            })
                        });

                        if (!response.ok) {
                            const error = await response.json();
                            throw new Error(error.message || 'Gagal menghitung ongkir');
                        }

                        this.previewResult = await response.json();
                    } catch (error) {
                        this.errorMessage = error.message;
                    } finally {
                        this.loading = false;
                    }
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(number);
                }
            }
        }
    </script>
</x-app-layout>