<!--
  Cart Sidebar Component
  
  Props: None
  
  Description:
  A sliding sidebar component that displays the user's shopping cart.
  It uses Alpine.js for state management and interacts with the CartController.
  
  Features:
  - Real-time updates via AJAX
  - Optimistic UI updates for better UX
  - LocalStorage persistence for offline recovery
  - Responsive design with smooth transitions
  - Error handling and loading states
-->
@if(request()->routeIs('shop.*'))
<style>
    [x-cloak] { display: none !important; }
</style>
<div x-data="cartSidebar()" 
     x-init="init()"
     x-cloak
     @keydown.window.escape="close()"
     class="relative z-50" 
     aria-labelledby="slide-over-title" 
     role="dialog" 
     aria-modal="true">

    <!-- Background backdrop -->
    <div x-show="isOpen" 
         x-transition:enter="ease-in-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in-out duration-300" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm z-40" 
        @click="close()"
        aria-hidden="true"></div>

    <div class="fixed inset-0 overflow-hidden z-50 pointer-events-none">
        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <!-- Sidebar Panel -->
            <div x-show="isOpen"
                     x-transition:enter="transform transition ease-in-out duration-300"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-300"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="pointer-events-auto w-[90vw] sm:w-[28rem]"
                     @click.stop>
                    
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl">
                        <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">{{ __('Keranjang Belanja') }}</h2>
                                    <p class="mt-1 text-sm text-gray-500"><span x-text="totalItems"></span> item di keranjang Anda</p>
                                </div>
                                <div class="ml-3 flex h-7 items-center">
                                    <button type="button" @click="close()" class="relative -m-2 p-2 text-gray-400 hover:text-gray-500 transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                        <span class="absolute -inset-0.5"></span>
                                        <span class="sr-only">{{ __('Close panel') }}</span>
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8">
                                <div class="flow-root">
                                    <template x-if="isLoading && cartItems.length === 0">
                                        <div class="flex justify-center items-center py-12">
                                            <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </template>

                                    <!-- Error State -->
                                    <template x-if="hasError">
                                        <div class="text-center py-8">
                                            <div class="inline-flex items-center justify-center flex-shrink-0 w-12 h-12 rounded-full bg-red-100 sm:h-10 sm:w-10 mb-4">
                                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900">Gagal memuat keranjang</h3>
                                            <p class="mt-2 text-sm text-gray-500">Terjadi kesalahan saat mengambil data.</p>
                                            <button @click="fetchCart()" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Coba Lagi
                                            </button>
                                        </div>
                                    </template>

                                    <template x-if="!isLoading && !hasError && cartItems.length === 0">
                                        <div class="text-center py-12 flex flex-col items-center">
                                            <div class="bg-gray-100 p-6 rounded-full mb-4">
                                                <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                            </div>
                                            <h3 class="mt-2 text-lg font-medium text-gray-900">{{ __('Keranjang Kosong') }}</h3>
                                            <p class="mt-1 text-sm text-gray-500 max-w-xs mx-auto">{{ __('Keranjang Anda masih kosong. Yuk mulai belanja!') }}</p>
                                            <div class="mt-6">
                                                <button @click="close()" class="inline-flex items-center rounded-md border border-transparent bg-blue-600 px-6 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                                    {{ __('Mulai Belanja') }}
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <ul role="list" class="-my-6 divide-y divide-gray-200" x-show="cartItems.length > 0">
                                        <template x-for="item in cartItems" :key="item.id">
                                            <li class="flex py-6 transition-opacity duration-200" :class="{'opacity-50': item.isUpdating}">
                                                <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 bg-gray-50">
                                                    <!-- Image handling: check if product has image, else use placeholder -->
                                                     <img :src="item.product.image ? '/storage/' + item.product.image : 'https://placehold.co/100x100?text=No+Image'" 
                                                          :alt="item.product.name" 
                                                          class="h-full w-full object-cover object-center">
                                                </div>

                                                <div class="ml-4 flex flex-1 flex-col">
                                                    <div>
                                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                                            <h3>
                                                                <a :href="'/shop/' + item.product.slug" @click.stop x-text="item.product.name" class="hover:text-blue-600 transition-colors"></a>
                                                            </h3>
                                                            <p class="ml-4 font-bold text-blue-600" x-text="formatRupiah(item.product.price * item.quantity)"></p>
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-500" x-text="item.product.brand ? item.product.brand.name : ''"></p>
                                                    </div>
                                                    <div class="flex flex-1 items-end justify-between text-sm">
                                                        <div class="flex items-center border border-gray-300 rounded-md bg-white shadow-sm">
                                                            <button @click.stop="updateQuantity(item.id, item.quantity - 1)" 
                                                                    class="px-3 py-1 text-gray-600 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-50 transition-colors rounded-l-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                                    :disabled="item.quantity <= 1 || item.isUpdating">
                                                                -
                                                            </button>
                                                            <span class="px-2 py-1 text-gray-900 min-w-[2rem] text-center font-medium select-none" x-text="item.quantity"></span>
                                                            <button @click.stop="updateQuantity(item.id, item.quantity + 1)" 
                                                                    class="px-3 py-1 text-gray-600 hover:bg-gray-100 hover:text-gray-900 disabled:opacity-50 transition-colors rounded-r-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                                                    :disabled="item.isUpdating">
                                                                +
                                                            </button>
                                                        </div>

                                                        <div class="flex">
                                                            <button type="button" 
                                                                    @click.stop="removeItem(item.id)"
                                                                    class="font-medium text-red-500 hover:text-red-700 transition-colors flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                                    :disabled="item.isUpdating">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                                {{ __('Hapus') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 px-4 py-6 sm:px-6 bg-gray-50" x-show="cartItems.length > 0">
                            <!-- Promo Code Section -->
                            <div class="mb-6">
                                <label for="promo-code" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Kode Promo') }}</label>
                                <div class="flex space-x-2">
                                    <input type="text" 
                                           id="promo-code" 
                                           @click.stop
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                           placeholder="Masukkan kode promo">
                                    <button type="button" 
                                            @click.stop
                                            class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                        {{ __('Pakai') }}
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-between text-base font-medium text-gray-900 mb-2">
                                <p>{{ __('Subtotal') }}</p>
                                <p class="text-lg font-bold text-blue-600" x-text="formatRupiah(totalPrice)"></p>
                            </div>
                            <div class="flex justify-between text-sm text-gray-500 mb-4" x-show="discountAmount > 0">
                                <p>{{ __('Hemat') }}</p>
                                <p class="text-green-600" x-text="'- ' + formatRupiah(discountAmount)"></p>
                            </div>
                            <p class="mt-0.5 text-xs text-gray-500 mb-6">{{ __('Ongkos kirim dan pajak dihitung saat checkout.') }}</p>
                            
                            <div class="grid gap-3">
                                <a href="{{ route('checkout.index') }}" @click.stop class="flex items-center justify-center rounded-md border border-transparent bg-blue-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-blue-700 transition-colors focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    {{ __('Checkout Sekarang') }}
                                </a>
                                <button type="button" class="flex items-center justify-center rounded-md border border-gray-300 bg-white px-6 py-3 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 transition-colors" @click="close()">
                                    {{ __('Lanjut Belanja') }}
                                </button>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    
    <!-- Notification Toast -->
    <div x-show="showToast" 
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed bottom-4 right-4 z-[60] max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden border-l-4"
         :class="toastType === 'success' ? 'border-green-500' : 'border-red-500'"
         style="display: none;">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <template x-if="toastType === 'success'">
                        <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                    <template x-if="toastType === 'error'">
                        <svg class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium text-gray-900" x-text="toastMessage"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="showToast = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cartSidebar', () => ({
                isOpen: false,
                isLoading: false,
                hasError: false,
                cartItems: [],
                totalPrice: 0,
                discountAmount: 0, // Placeholder for future promo logic
                showToast: false,
                toastMessage: '',
                toastType: 'success',

                get totalItems() {
                    return this.cartItems.reduce((sum, item) => sum + item.quantity, 0);
                },

                init() {
                    // Load state from localStorage
                    this.loadFromStorage();

                    // Watch for state changes to persist
                    this.$watch('isOpen', value => {
                        localStorage.setItem('cartSidebarOpen', value);
                        if (value) {
                            document.body.style.overflow = 'hidden'; // Prevent body scroll
                        } else {
                            document.body.style.overflow = '';
                        }
                    });
                    
                    // Persist cart data on change
                    this.$watch('cartItems', () => this.saveToStorage());
                    this.$watch('totalPrice', () => this.saveToStorage());

                    // Listen for global event to open cart
                    window.addEventListener('open-cart', () => {
                        this.open(true);
                    });
                    
                    // Listen for add-to-cart event from other components
                    window.addEventListener('add-to-cart', (event) => {
                        this.addToCart(event.detail.productId, event.detail.quantity);
                    });
                    
                    // Initial fetch to sync with server
                    // Use a small delay to allow Alpine to initialize fully
                    setTimeout(() => {
                        if (localStorage.getItem('cartSidebarOpen') === 'true') {
                            this.open();
                        } else {
                            // Even if closed, we might want to fetch fresh data if storage is old
                            // But to save bandwidth, we'll rely on storage until opened
                            // However, let's verify if storage is empty
                            if (this.cartItems.length === 0) {
                                this.fetchCart(true); // Silent fetch
                            }
                        }
                    }, 100);
                },

                loadFromStorage() {
                    const savedOpen = localStorage.getItem('cartSidebarOpen');
                    this.isOpen = savedOpen === 'true';
                    
                    const savedCart = localStorage.getItem('epi_cart_data');
                    if (savedCart) {
                        try {
                            const parsed = JSON.parse(savedCart);
                            this.cartItems = parsed.items || [];
                            this.totalPrice = parsed.total || 0;
                            this.discountAmount = parsed.discount || 0;
                        } catch (e) {
                            console.error('Failed to parse cart storage', e);
                            localStorage.removeItem('epi_cart_data');
                        }
                    }
                },

                saveToStorage() {
                    localStorage.setItem('epi_cart_data', JSON.stringify({
                        items: this.cartItems,
                        total: this.totalPrice,
                        discount: this.discountAmount,
                        timestamp: new Date().getTime()
                    }));
                },

                open(doFetch = true) {
                    this.isOpen = true;
                    if (doFetch) {
                        this.fetchCart();
                    }
                },

                close() {
                    this.isOpen = false;
                },

                async fetchCart(silent = false) {
                    if (!silent) this.isLoading = true;
                    this.hasError = false;
                    try {
                        const response = await fetch('{{ route("cart.index") }}', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!response.ok) {
                            if (response.status === 401 || response.redirected) {
                                this.showNotification('Silakan login untuk menggunakan keranjang.', 'error');
                                this.isOpen = false;
                                return;
                            }
                            throw new Error('Network response was not ok');
                        }
                        
                        const data = await response.json();
                        
                        // Map items to include isUpdating flag if not present
                        this.cartItems = (data.cart ? data.cart.items : []).map(item => ({
                            ...item,
                            isUpdating: false
                        }));
                        this.totalPrice = data.total_price;
                        this.discountAmount = data.discount_amount || 0;
                        
                        // Update storage with fresh data
                        this.saveToStorage();
                        
                    } catch (error) {
                        console.error('Error fetching cart:', error);
                        if (!silent) this.hasError = true;
                    } finally {
                        if (!silent) this.isLoading = false;
                    }
                },

                async addToCart(productId, quantity = 1) {
                    // Optimistic UI could be tricky with new items as we need product details
                    // So we'll use a loading indicator or just wait.
                    // For better UX, we show a toast immediately saying "Processing..."?
                    // Or just rely on fast server response.
                    
                    try {
                        const response = await fetch('{{ route("cart.add") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_id: productId,
                                quantity: quantity
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            this.cartItems = data.cart.items.map(item => ({ ...item, isUpdating: false }));
                            this.totalPrice = data.total_price;
                            this.showNotification('Produk berhasil ditambahkan!', 'success');
                            if (!this.isOpen) this.open(false);
                        } else {
                            this.showNotification(data.message || 'Gagal menambahkan produk.', 'error');
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        this.showNotification('Terjadi kesalahan koneksi.', 'error');
                    }
                },

                async updateQuantity(itemId, newQuantity) {
                    if (newQuantity < 1) return;
                    
                    // Find item and mark as updating
                    const index = this.cartItems.findIndex(i => i.id === itemId);
                    if (index === -1) return;
                    
                    // Optimistic update
                    const oldQuantity = this.cartItems[index].quantity;
                    const itemPrice = this.cartItems[index].product.price;
                    
                    this.cartItems[index].quantity = newQuantity;
                    this.cartItems[index].isUpdating = true;
                    
                    // Calculate estimated new total (rough, assumes no complex discounts)
                    // We'll let server correct it
                    const diff = newQuantity - oldQuantity;
                    this.totalPrice += (diff * itemPrice);

                    try {
                        const response = await fetch(`/cart/${itemId}`, {
                            method: 'PATCH', 
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                quantity: newQuantity
                            })
                        });

                        const data = await response.json();
                        if (response.ok) {
                            this.cartItems = data.cart.items.map(item => ({ ...item, isUpdating: false }));
                            this.totalPrice = data.total_price;
                        } else {
                            // Revert on failure
                            this.cartItems[index].quantity = oldQuantity;
                            this.cartItems[index].isUpdating = false;
                            this.showNotification('Gagal mengupdate jumlah.', 'error');
                        }
                    } catch (error) {
                        console.error('Error updating quantity:', error);
                        this.cartItems[index].quantity = oldQuantity;
                        this.cartItems[index].isUpdating = false;
                        this.showNotification('Terjadi kesalahan koneksi.', 'error');
                    }
                },

                async removeItem(itemId) {
                    if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) return;
                    
                    // Mark as updating
                    const index = this.cartItems.findIndex(i => i.id === itemId);
                    if (index !== -1) {
                        this.cartItems[index].isUpdating = true;
                    }

                    try {
                        const response = await fetch(`/cart/${itemId}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();
                        if (response.ok) {
                            this.cartItems = data.cart.items.map(item => ({ ...item, isUpdating: false }));
                            this.totalPrice = data.total_price;
                            this.discountAmount = data.discount_amount || 0;
                            this.showNotification('Item dihapus dari keranjang.', 'success');
                        } else {
                            if (index !== -1) this.cartItems[index].isUpdating = false;
                            this.showNotification('Gagal menghapus item.', 'error');
                        }
                    } catch (error) {
                        console.error('Error removing item:', error);
                        if (index !== -1) this.cartItems[index].isUpdating = false;
                        this.showNotification('Terjadi kesalahan koneksi.', 'error');
                    }
                },

                showNotification(message, type = 'success') {
                    this.toastMessage = message;
                    this.toastType = type;
                    this.showToast = true;
                    setTimeout(() => {
                        this.showToast = false;
                    }, 3000);
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(number);
                }
            }));
        });
    </script>
</div>
@endif
