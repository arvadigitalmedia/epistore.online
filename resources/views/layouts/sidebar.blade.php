<div class="bg-white border-r border-gray-200 min-h-screen flex flex-col transition-all duration-300 z-40 fixed sm:static h-full shadow-lg sm:shadow-none" 
     x-data="{ open: true, mobileOpen: false }" 
     :class="{ 'w-64': open, 'w-20': !open, 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen && window.innerWidth < 640, 'w-64': window.innerWidth < 640 }"
     @resize.window="if(window.innerWidth >= 640) { sidebarOpen = true } else { sidebarOpen = false }">
    
    <!-- Logo -->
    <div class="h-16 flex items-center justify-center border-b border-gray-100 bg-gradient-to-r from-white to-gray-50">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 transition-all duration-300 overflow-hidden" :class="open ? 'px-4' : 'px-0'">
            <x-application-logo class="block h-8 w-auto fill-current text-primary-600 shrink-0" />
            <span class="font-bold text-lg text-gray-800 tracking-tight whitespace-nowrap" x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0">EPI-OSS</span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-6 px-3 space-y-6 custom-scrollbar">
        <!-- Dashboard -->
        <ul class="space-y-1">
            <li>
                <a href="{{ route('dashboard') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('distributor.dashboard') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                    <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('dashboard') || request()->routeIs('distributor.dashboard') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </div>
                    <span class="whitespace-nowrap" x-show="open">Dashboard</span>
                </a>
            </li>
        </ul>

        <!-- Distributor Section -->
        @if(auth()->user()->distributor_id)
        <div class="space-y-1">
            <div class="px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider transition-opacity duration-200 flex items-center gap-2" x-show="open">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                Store Management
            </div>
            
            <ul class="space-y-1">
                <!-- Orders -->
                <li>
                    <a href="{{ route('distributor.orders.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('distributor.orders.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('distributor.orders.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">Orders</span>
                    </a>
                </li>
                
                <!-- Shipping -->
                <li>
                    <a href="{{ route('distributor.shipping.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('distributor.shipping.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('distributor.shipping.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v3.28a1 1 0 00.684.948l6 2.5a1 1 0 00.316.98 2.48 2.48 0 01.5-1.5H9"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 6v10m0 0v2m0-2h4m-4-2h4m0-4h4"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 16V8a2 2 0 00-1-1.73l-7-3.28"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">Shipping Settings</span>
                    </a>
                </li>
                
                <!-- Store Profile -->
                <li>
                    <a href="{{ route('distributor.store-profile.edit') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('distributor.store-profile.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('distributor.store-profile.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">Informasi Toko</span>
                    </a>
                </li>

                <!-- Domain Management -->
                <li>
                    <a href="{{ route('distributor.domains.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('distributor.domains.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('distributor.domains.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">Domain & Link</span>
                    </a>
                </li>
            </ul>
        </div>
        @endif

        <!-- Super Admin Section -->
        @role('super_admin')
        <div class="space-y-1">
            <div class="px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider transition-opacity duration-200 flex items-center gap-2" x-show="open">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ __('Administration') }}
            </div>
            
            <ul class="space-y-1">
                <!-- Users & Roles -->
                <li>
                    <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Users & Roles') }}</span>
                    </a>
                </li>
                
                <!-- Distributors -->
                <li>
                    <a href="{{ route('admin.distributors.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.distributors.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                         <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('admin.distributors.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Distributors') }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="space-y-1">
            <div class="px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider transition-opacity duration-200 flex items-center gap-2" x-show="open">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>
                {{ __('Master Data') }}
            </div>
            
            <ul class="space-y-1">
                <!-- Brands -->
                <li>
                    <a href="{{ route('admin.brands.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.brands.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('admin.brands.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Brands') }}</span>
                    </a>
                </li>

                <!-- Products -->
                <li>
                    <a href="{{ route('admin.products.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('admin.products.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Products') }}</span>
                    </a>
                </li>

                <!-- Price List -->
                <li>
                    <a href="{{ route('admin.prices.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.prices.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                         <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('admin.prices.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Central Price List') }}</span>
                    </a>
                </li>

                <!-- Courier Management -->
                <li>
                    <a href="{{ route('admin.couriers.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('admin.couriers.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('admin.couriers.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Courier Management') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        @endrole



        <!-- Customer Section -->
        <div class="space-y-1 mt-2">
            <div class="px-3 mb-2 text-xs font-bold text-gray-400 uppercase tracking-wider transition-opacity duration-200 flex items-center gap-2" x-show="open">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                {{ __('My Shopping') }}
            </div>
            <ul class="space-y-1">
                <!-- Shop -->
                <li>
                    <a href="{{ route('shop.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('shop.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('shop.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Shop') }}</span>
                    </a>
                </li>
                
                <!-- Cart -->
                 <li>
                    <a href="{{ route('cart.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('cart.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('cart.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">{{ __('Cart') }}</span>
                    </a>
                </li>

                <!-- My Orders -->
                <li>
                    <a href="{{ route('orders.index') }}" class="group flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('orders.*') ? 'bg-primary-50 text-primary-600 font-semibold shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-primary-600' }}">
                        <div class="shrink-0 w-5 h-5 flex items-center justify-center transition-colors duration-200 {{ request()->routeIs('orders.*') ? 'text-primary-600' : 'text-gray-400 group-hover:text-primary-600' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>
                        <span class="whitespace-nowrap" x-show="open">My Orders</span>
                    </a>
                </li>
            </ul>
        </div>


    </nav>

    <!-- User Profile & Footer -->
    <div class="border-t border-gray-200 bg-gray-50 mt-auto">
        <!-- Profile Section -->
        <div class="p-4" :class="open ? 'block' : 'hidden sm:flex sm:justify-center'">
             <div class="flex items-center gap-3" :class="open ? '' : 'justify-center'">
                <!-- Avatar (Clickable to Profile) -->
                <a href="{{ route('profile.edit') }}" class="shrink-0 relative group cursor-pointer block">
                    <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold hover:bg-primary-200 transition-colors shadow-sm border border-primary-200">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                </a>
                
                <!-- Info & Actions -->
                <div class="flex-1 min-w-0" x-show="open">
                    <a href="{{ route('profile.edit') }}" class="flex flex-col group">
                         <span class="text-sm font-bold text-gray-900 truncate group-hover:text-primary-600 transition-colors">{{ Auth::user()->name }}</span>
                         <span class="text-xs text-gray-500 truncate capitalize">{{ Auth::user()->roles->pluck('name')->first() ?? 'User' }}</span>
                    </a>
                </div>
                
                <!-- Separate Logout Button -->
                <form method="POST" action="{{ route('logout') }}" class="inline" x-show="open">
                    @csrf
                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Log Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
             </div>
        </div>

        <!-- Collapse Button -->
        <div class="p-4 border-t border-gray-200 hidden sm:block">
            <button @click="open = !open" class="flex items-center gap-2 text-gray-400 hover:text-primary-600 transition-colors focus:outline-none w-full" :class="open ? 'justify-start' : 'justify-center'">
                <svg class="w-5 h-5 transition-transform duration-300" :class="open ? 'rotate-0' : 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium" x-show="open">Collapse</span>
            </button>
        </div>
    </div>
</div>

<!-- Mobile Overlay -->
<div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-30 sm:hidden" 
     x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false">
</div>
