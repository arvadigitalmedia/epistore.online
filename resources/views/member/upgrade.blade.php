<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upgrade ke EPI Channel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Berhasil!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Gagal!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="mb-4 bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('info') }}</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Keuntungan EPI Channel</h3>
                            <ul class="list-disc list-inside space-y-2 text-gray-600">
                                <li>Akses harga spesial member (EPI Channel Price).</li>
                                <li>Prioritas layanan pengiriman.</li>
                                <li>Akses ke promo eksklusif.</li>
                                <li>Dukungan prioritas dari tim EPI.</li>
                            </ul>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Status Anda</h3>
                            
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-6">
                                <div class="flex items-center justify-between">
                                    <span class="text-gray-600">Email Terdaftar:</span>
                                    <span class="font-semibold">{{ $user->email }}</span>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-gray-600">Status Saat Ini:</span>
                                    @if($user->member_status === 'epi_channel')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            EPI Channel
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Regular
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($user->member_status !== 'epi_channel')
                                <form action="{{ route('member.upgrade.process') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Cek & Upgrade Sekarang
                                    </button>
                                    <p class="mt-2 text-xs text-gray-500 text-center">
                                        Sistem akan memeriksa email Anda secara otomatis.
                                    </p>
                                </form>
                            @else
                                <div class="text-center p-4 bg-green-50 rounded-lg text-green-700 border border-green-200">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p>Anda sudah menjadi bagian dari EPI Channel.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
