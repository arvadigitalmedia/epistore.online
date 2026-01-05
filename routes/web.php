<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$appUrl = config('app.url');
$mainDomain = parse_url($appUrl, PHP_URL_HOST);

// ==================================================================================
// 1. MAIN DOMAIN ROUTES (Admin, Distributor Panel, Central Member Area)
// ==================================================================================
Route::domain($mainDomain)->group(function () {

    Route::get('/dashboard', function () {
        $user = Illuminate\Support\Facades\Auth::user();
        if ($user->distributor_id) {
            return redirect()->route('distributor.dashboard');
        }
        return view('dashboard');
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        // Shop (Central/Member)
        Route::get('/shop', [\App\Http\Controllers\ShopController::class, 'index'])->name('shop.index');
        Route::get('/shop/{product}', [\App\Http\Controllers\ShopController::class, 'show'])->name('shop.show');

        // Cart (Central)
        Route::get('/cart', [\App\Http\Controllers\CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/add', [\App\Http\Controllers\CartController::class, 'add'])->name('cart.add');
        Route::patch('/cart/{item}', [\App\Http\Controllers\CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{item}', [\App\Http\Controllers\CartController::class, 'remove'])->name('cart.remove');

        // Checkout (Central)
        Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout.index');
        Route::post('/checkout', [\App\Http\Controllers\CheckoutController::class, 'store'])->name('checkout.store');
        Route::get('/checkout/cities/{province}', [\App\Http\Controllers\CheckoutController::class, 'getCities'])->name('checkout.cities');
        Route::get('/checkout/districts/{city}', [\App\Http\Controllers\CheckoutController::class, 'getDistricts'])->name('checkout.districts');
        Route::post('/checkout/check-shipping', [\App\Http\Controllers\CheckoutController::class, 'checkShipping'])->name('checkout.check-shipping');
        Route::post('/checkout/check-coupon', [\App\Http\Controllers\CheckoutController::class, 'checkCoupon'])->name('checkout.check-coupon');
        Route::get('/checkout/success/{order}', [\App\Http\Controllers\CheckoutController::class, 'success'])->name('checkout.success');
        
        // Store Search
        Route::get('/stores/search', [\App\Http\Controllers\StoreController::class, 'search'])->name('stores.search');

        // Member Upgrade
        Route::get('/member/upgrade', [\App\Http\Controllers\EpiMemberController::class, 'show'])->name('member.upgrade');
        Route::post('/member/upgrade', [\App\Http\Controllers\EpiMemberController::class, 'upgrade'])->name('member.upgrade.process');

        // Customer Orders
        Route::resource('orders', \App\Http\Controllers\OrderController::class)->only(['index', 'show']);
        Route::post('orders/{order}/confirm-receipt', [\App\Http\Controllers\OrderController::class, 'confirmReceipt'])->name('orders.confirm-receipt');
        
        // Order Payment Flow
        Route::get('orders/{order}/invoice', [\App\Http\Controllers\OrderPaymentController::class, 'invoice'])->name('orders.invoice');
        Route::get('orders/{order}/payment', [\App\Http\Controllers\OrderPaymentController::class, 'payment'])->name('orders.payment');
        Route::post('orders/{order}/payment', [\App\Http\Controllers\OrderPaymentController::class, 'storePayment'])->name('orders.store-payment');
        Route::get('orders/{order}/confirmation', [\App\Http\Controllers\OrderPaymentController::class, 'confirmation'])->name('orders.confirmation');
    });

    // Distributor Panel
    Route::middleware(['auth'])->prefix('distributor')->name('distributor.')->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Distributor\DashboardController::class, 'index'])->name('dashboard');
        Route::get('orders', [\App\Http\Controllers\Distributor\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [\App\Http\Controllers\Distributor\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/shipping', [\App\Http\Controllers\Distributor\OrderController::class, 'updateShipping'])->name('orders.update-shipping');
        Route::patch('orders/{order}/status', [\App\Http\Controllers\Distributor\OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::patch('orders/{order}/cancel', [\App\Http\Controllers\Distributor\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('orders/{order}/invoice', [\App\Http\Controllers\Distributor\OrderController::class, 'invoice'])->name('orders.invoice');
        
        // Shipping Settings
        Route::get('shipping', [\App\Http\Controllers\Distributor\ShippingController::class, 'index'])->name('shipping.index');
        Route::post('shipping', [\App\Http\Controllers\Distributor\ShippingController::class, 'update'])->name('shipping.update');
        Route::get('shipping/cities/{province}', [\App\Http\Controllers\Distributor\ShippingController::class, 'getCities'])->name('shipping.cities');
        Route::get('shipping/districts/{city}', [\App\Http\Controllers\Distributor\ShippingController::class, 'getDistricts'])->name('shipping.districts');
        Route::get('shipping/subdistricts/{district}', [\App\Http\Controllers\Distributor\ShippingController::class, 'getSubDistricts'])->name('shipping.subdistricts');
        Route::post('shipping/preview', [\App\Http\Controllers\Distributor\ShippingController::class, 'preview'])->name('shipping.preview');

        // Store Profile Management
        Route::get('store-profile', [\App\Http\Controllers\Distributor\StoreProfileController::class, 'edit'])->name('store-profile.edit');
        Route::post('store-profile', [\App\Http\Controllers\Distributor\StoreProfileController::class, 'update'])->name('store-profile.update');

        // Domain Management
        Route::get('domains', [\App\Http\Controllers\Distributor\DomainController::class, 'index'])->name('domains.index');
        Route::post('domains/subdomain', [\App\Http\Controllers\Distributor\DomainController::class, 'updateSubdomain'])->name('domains.update-subdomain');
        Route::post('domains', [\App\Http\Controllers\Distributor\DomainController::class, 'storeDomain'])->name('domains.store');
        Route::post('domains/{domain}/verify', [\App\Http\Controllers\Distributor\DomainController::class, 'verifyDomain'])->name('domains.verify');
        Route::post('domains/{domain}/primary', [\App\Http\Controllers\Distributor\DomainController::class, 'setPrimary'])->name('domains.primary');
        Route::delete('domains/{domain}', [\App\Http\Controllers\Distributor\DomainController::class, 'destroyDomain'])->name('domains.destroy');
    });

    Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('distributors', \App\Http\Controllers\Admin\DistributorController::class);
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

        // Brands Import/Export
        Route::get('brands/export', [\App\Http\Controllers\Admin\BrandController::class, 'export'])->name('brands.export');
        Route::post('brands/import', [\App\Http\Controllers\Admin\BrandController::class, 'import'])->name('brands.import');
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);

        // Products Import/Export
        Route::get('products/export', [\App\Http\Controllers\Admin\ProductController::class, 'export'])->name('products.export');
        Route::post('products/import', [\App\Http\Controllers\Admin\ProductController::class, 'import'])->name('products.import');
        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class);
        
        // Price Management
        Route::get('prices', [\App\Http\Controllers\Admin\PriceController::class, 'index'])->name('prices.index');
        Route::post('prices/bulk-update', [\App\Http\Controllers\Admin\PriceController::class, 'bulkUpdate'])->name('prices.bulk-update');
        Route::get('prices/history', [\App\Http\Controllers\Admin\PriceController::class, 'history'])->name('prices.history');
        Route::get('prices/pending', [\App\Http\Controllers\Admin\PriceController::class, 'pending'])->name('prices.pending');
        Route::post('prices/{priceHistory}/approve', [\App\Http\Controllers\Admin\PriceController::class, 'approve'])->name('prices.approve');
        Route::post('prices/{priceHistory}/reject', [\App\Http\Controllers\Admin\PriceController::class, 'reject'])->name('prices.reject');

        // Courier Management
        Route::get('couriers', [\App\Http\Controllers\Admin\CourierController::class, 'index'])->name('couriers.index');
        Route::post('couriers/bulk-action', [\App\Http\Controllers\Admin\CourierController::class, 'bulkAction'])->name('couriers.bulk-action');
        Route::post('couriers/{courier}/toggle', [\App\Http\Controllers\Admin\CourierController::class, 'toggleStatus'])->name('couriers.toggle');
        Route::patch('couriers/{courier}/priority', [\App\Http\Controllers\Admin\CourierController::class, 'updatePriority'])->name('couriers.priority');
    });

    // Auth Routes (Admin/Main)
    require __DIR__.'/auth.php';
});

// ==================================================================================
// 2. STOREFRONT ROUTES (Subdomains & Custom Domains)
// ==================================================================================
// These routes catch any request NOT matched above (i.e., not on main domain).
Route::middleware(['identify.distributor'])->group(function () {
    Route::get('/', [\App\Http\Controllers\Storefront\HomeController::class, 'index'])->name('storefront.home');

    // Guest Auth
    Route::middleware('guest')->group(function () {
        Route::get('/login', [\App\Http\Controllers\Storefront\AuthController::class, 'showLoginForm'])->name('storefront.login');
        Route::post('/login', [\App\Http\Controllers\Storefront\AuthController::class, 'login']);
        Route::get('/register', [\App\Http\Controllers\Storefront\AuthController::class, 'showRegisterForm'])->name('storefront.register');
        Route::post('/register', [\App\Http\Controllers\Storefront\AuthController::class, 'register']);
    });

    Route::post('/logout', [\App\Http\Controllers\Storefront\AuthController::class, 'logout'])->name('storefront.logout')->middleware('auth');

    // Products
    Route::get('/products', [\App\Http\Controllers\Storefront\ProductController::class, 'index'])->name('storefront.products.index');
    Route::get('/products/{slug}', [\App\Http\Controllers\Storefront\ProductController::class, 'show'])->name('storefront.products.show');

    // Cart
    Route::middleware('auth')->group(function () {
        Route::get('/cart', [\App\Http\Controllers\Storefront\CartController::class, 'index'])->name('storefront.cart.index');
        Route::post('/cart/add', [\App\Http\Controllers\Storefront\CartController::class, 'add'])->name('storefront.cart.add');
        Route::patch('/cart/{item}', [\App\Http\Controllers\Storefront\CartController::class, 'update'])->name('storefront.cart.update');
        Route::delete('/cart/{item}', [\App\Http\Controllers\Storefront\CartController::class, 'remove'])->name('storefront.cart.remove');
        
        // Checkout
        Route::get('/checkout', [\App\Http\Controllers\Storefront\CheckoutController::class, 'index'])->name('storefront.checkout.index');
        Route::post('/checkout', [\App\Http\Controllers\Storefront\CheckoutController::class, 'store'])->name('storefront.checkout.store');
    });
});
