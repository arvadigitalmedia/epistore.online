<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Informasi Toko') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('distributor.store-profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    
                    <!-- Left Column: Branding & Bank -->
                    <div class="md:col-span-1 space-y-6">
                        <!-- Logo Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Branding Toko</h3>
                            
                            <div class="mb-4 text-center">
                                @if($distributor->logo)
                                    <img src="{{ Storage::url($distributor->logo) }}" alt="Logo" class="mx-auto h-32 w-32 object-contain border rounded-full">
                                @else
                                    <div class="mx-auto h-32 w-32 bg-gray-100 border rounded-full flex items-center justify-center text-gray-400">
                                        <span class="text-sm">No Logo</span>
                                    </div>
                                @endif
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700">Upload Logo Baru</label>
                                <input type="file" name="logo" class="mt-1 block w-full text-sm text-gray-500
                                  file:mr-4 file:py-2 file:px-4
                                  file:rounded-full file:border-0
                                  file:text-sm file:font-semibold
                                  file:bg-blue-50 file:text-blue-700
                                  hover:file:bg-blue-100" accept="image/*">
                                @error('logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Bank Info Card -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Rekening</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Bank</label>
                                    <input type="text" name="bank_account_info[bank_name]" value="{{ $distributor->bank_account_info['bank_name'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="Contoh: BCA">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nomor Rekening</label>
                                    <input type="text" name="bank_account_info[account_number]" value="{{ $distributor->bank_account_info['account_number'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Atas Nama</label>
                                    <input type="text" name="bank_account_info[account_holder]" value="{{ $distributor->bank_account_info['account_holder'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Location Details -->
                    <div class="md:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Lokasi & Alamat Toko</h3>
                            <p class="text-sm text-gray-500 mb-6">Informasi ini akan ditampilkan kepada pelanggan saat memilih metode "Ambil di Toko".</p>

                            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                                
                                <!-- Address -->
                                <div class="sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700">Alamat Lengkap (Jalan, No, Blok)</label>
                                    <textarea name="address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('address', $storeLocation->address ?? '') }}</textarea>
                                    @error('address') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <!-- RT/RW -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">RT / RW</label>
                                    <input type="text" name="rt_rw" value="{{ old('rt_rw', $storeLocation->rt_rw ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <!-- Kelurahan -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Kelurahan</label>
                                    <input type="text" name="subdistrict" value="{{ old('subdistrict', $storeLocation->subdistrict ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <!-- Kecamatan -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Kecamatan</label>
                                    <input type="text" name="district" value="{{ old('district', $storeLocation->district ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <!-- Kota -->
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Kota / Kabupaten</label>
                                    <input type="text" name="city" value="{{ old('city', $storeLocation->city ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <!-- Provinsi -->
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Provinsi</label>
                                    <input type="text" name="province" value="{{ old('province', $storeLocation->province ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <!-- Kode Pos -->
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Kode Pos</label>
                                    <input type="text" name="postal_code" value="{{ old('postal_code', $storeLocation->postal_code ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                </div>

                                <div class="sm:col-span-6 border-t pt-4 mt-2">
                                    <h4 class="text-sm font-bold text-gray-800 mb-2">Koordinat & Catatan</h4>
                                </div>

                                <!-- Latitude -->
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Latitude (Google Maps)</label>
                                    <input type="text" name="latitude" value="{{ old('latitude', $storeLocation->latitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="-6.2088">
                                </div>

                                <!-- Longitude -->
                                <div class="sm:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700">Longitude (Google Maps)</label>
                                    <input type="text" name="longitude" value="{{ old('longitude', $storeLocation->longitude ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" placeholder="106.8456">
                                </div>
                                
                                <!-- Notes -->
                                <div class="sm:col-span-6">
                                    <label class="block text-sm font-medium text-gray-700">Catatan Lokasi (Patokan, Warna Gedung, dll)</label>
                                    <textarea name="notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">{{ old('notes', $storeLocation->notes ?? '') }}</textarea>
                                </div>

                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow-lg transition duration-150 ease-in-out">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
