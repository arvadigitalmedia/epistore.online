<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\RajaOngkirService;

use App\Services\DistributorShippingService;

class CheckoutController extends Controller
{
    protected $rajaOngkir;
    protected $shippingService;

    public function __construct(RajaOngkirService $rajaOngkir, DistributorShippingService $shippingService)
    {
        $this->rajaOngkir = $rajaOngkir;
        $this->shippingService = $shippingService;
    }

    public function index()
    {
        $cart = Cart::with(['items.product'])->where('user_id', Auth::id())->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $provinces = $this->rajaOngkir->getProvinces();
        
        // Determine Distributor
        $distributorId = Auth::user()->distributor_id ?? 1;
        $distributor = \App\Models\Distributor::find($distributorId);
        
        // Get Shipping Settings
        $shippingConfig = $this->shippingService->getSettings($distributor);

        // Fetch Store Locations for Pickup
        $storeLocations = \App\Models\StoreLocation::where('distributor_id', $distributorId)
                            ->where('is_active', true)
                            ->orderBy('is_primary', 'desc')
                            ->get();

        return view('checkout.index', compact('cart', 'provinces', 'distributor', 'shippingConfig', 'storeLocations'));
    }

    public function getCities($provinceId)
    {
        $cities = $this->rajaOngkir->getCities($provinceId);
        return response()->json($cities);
    }

    public function getDistricts($cityId)
    {
        $districts = $this->rajaOngkir->getDistricts($cityId);
        return response()->json($districts);
    }

    public function checkShipping(Request $request)
    {
        $request->validate([
            'city_id' => 'required',
            'weight' => 'required|numeric'
        ]);

        $distributorId = Auth::user()->distributor_id ?? 1;
        $distributor = \App\Models\Distributor::find($distributorId);
        
        if (!$distributor) {
            return response()->json(['error' => 'Distributor not found'], 404);
        }

        $settings = $this->shippingService->getSettings($distributor);
        $couriers = $settings['couriers'] ?? ['jne']; // Default fallback
        
        // Ensure couriers is array (handle legacy JSON)
        if (!is_array($couriers)) {
            $couriers = ['jne'];
        }

        $results = [];
        $weight = $request->weight < 1 ? 1000 : $request->weight; // Safety minimum 1g, usually passed in grams

        foreach ($couriers as $courier) {
            try {
                // Determine destination district if available (for future Pro compatibility)
                $districtId = $request->district_id ?? null;

                $rate = $this->shippingService->calculateShipping(
                    $distributor, 
                    $request->city_id, 
                    $districtId, 
                    $weight, 
                    $courier
                );

                if (!empty($rate['costs'])) {
                    foreach ($rate['costs'] as $cost) {
                        $results[] = [
                            'id' => $courier . '_' . $cost['service'],
                            'courier_code' => $courier,
                            'courier_name' => strtoupper($courier),
                            'service' => $cost['service'],
                            'description' => $cost['description'],
                            'cost' => $cost['cost'][0]['value'],
                            'etd' => $cost['cost'][0]['etd'] ?? '-',
                            'formatted_cost' => 'Rp ' . number_format($cost['cost'][0]['value'], 0, ',', '.')
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Log::error("Shipping calc error for $courier: " . $e->getMessage());
                // Continue to next courier
            }
        }

        return response()->json($results);
    }

    public function checkCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string',
        ]);

        // Mock Coupon Logic
        $code = strtoupper($request->coupon_code);
        if ($code === 'DISKON10') {
            return response()->json([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'discount_amount' => 10000, // Flat 10k for now
                'formatted_discount' => 'Rp 10.000'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid coupon code.'
        ], 422);
    }

    public function store(Request $request)
    {
        $cart = Cart::with(['items.product'])->where('user_id', Auth::id())->firstOrFail();

        if ($cart->items->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'delivery_type' => 'required|in:shipping,pickup',
            'payment_method' => 'required|string|in:bank_transfer,e_wallet,credit_card,cod',
        ]);

        // Common validation
        $rules = [];
        
        if ($request->delivery_type === 'shipping') {
            $rules = [
                'shipping_courier' => 'required|string',
                'shipping_service' => 'required|string',
                'shipping_cost' => 'required|numeric|min:0',
            ];

            if ($request->has('ship_to_different_address') && $request->ship_to_different_address) {
                 $rules = array_merge($rules, [
                    'recipient_name_new' => 'required|string|max:255',
                    'recipient_phone_new' => 'required|string|max:20',
                    'shipping_address_new' => 'required|string',
                    'shipping_province_new' => 'required',
                    'shipping_city_new' => 'required',
                    'shipping_district_new' => 'required',
                    'shipping_postal_code_new' => 'required',
                 ]);
            } else {
                 $rules = array_merge($rules, [
                    'recipient_name' => 'required|string|max:255',
                    'recipient_phone' => 'required|string|max:20',
                    'shipping_address' => 'required|string',
                    'shipping_province' => 'required',
                    'shipping_city' => 'required',
                    'shipping_district' => 'required',
                    'shipping_postal_code' => 'required',
                 ]);
            }
        } else {
            // Store Pickup Validation
            $rules = [
                'pickup_store_id' => 'required|exists:store_locations,id',
                'pickup_at' => 'required|date', // Basic validation, stricter in JS or custom rule if needed
            ];
            
            // Check Store Pickup availability
            $distributorId = Auth::user()->distributor_id ?? 1;
            $distributor = \App\Models\Distributor::find($distributorId);
            $settings = $this->shippingService->getSettings($distributor);
            
            if (empty($settings['enable_store_pickup'])) {
                return back()->with('error', 'Store pickup is not available for this distributor.');
            }
        }
        
        $request->validate($rules);

        // Validate Stock Availability
        $distributorId = Auth::user()->distributor_id ?? 1;
        $targetStoreId = $request->delivery_type === 'pickup' ? $request->pickup_store_id : null;

        foreach ($cart->items as $item) {
            $query = \App\Models\ProductStock::where('distributor_id', $distributorId)
                        ->where('product_id', $item->product_id);
            
            if ($targetStoreId) {
                $query->where('store_location_id', $targetStoreId);
            } else {
                $query->whereNull('store_location_id');
            }

            $stock = $query->value('quantity') ?? 0;
            
            if ($stock < $item->quantity) {
                $locationName = $targetStoreId ? 'Store Branch' : 'Main Warehouse';
                return back()->with('error', 'Insufficient stock for product "' . $item->product->name . '" at ' . $locationName . '. Available: ' . $stock);
            }
        }

        try {
            DB::beginTransaction();

            $shippingCost = 0;
            $shippingCourier = null;
            $shippingService = null;
            $fullAddress = '';
            
            // Order Variables
            $recipientName = $request->recipient_name ?? Auth::user()->name;
            $recipientPhone = $request->recipient_phone ?? Auth::user()->phone;
            $pickupStoreId = null;
            $pickupAt = null;
            $pickupToken = null;
            $deliveryType = $request->delivery_type;

            if ($deliveryType === 'shipping') {
                if ($request->has('ship_to_different_address') && $request->ship_to_different_address) {
                    // Use New Address
                    $fullAddress = $request->shipping_address_new . ', ' . 
                                  $request->shipping_district_name_new . ', ' .
                                  $request->shipping_city_name_new . ', ' . 
                                  $request->shipping_province_name_new . ', ' . 
                                  $request->shipping_postal_code_new;
                    
                    $recipientName = $request->recipient_name_new;
                    $recipientPhone = $request->recipient_phone_new;
                } else {
                    // Use Default Address
                    $recipientName = $request->recipient_name;
                    $recipientPhone = $request->recipient_phone;

                    // Update User Profile
                    /** @var \App\Models\User $user */
                    $user = Auth::user();
                    $user->update([
                        'address' => $request->shipping_address,
                        'province_id' => $request->shipping_province,
                        'province_name' => $request->shipping_province_name,
                        'city_id' => $request->shipping_city,
                        'city_name' => $request->shipping_city_name,
                        'district_id' => $request->shipping_district,
                        'district_name' => $request->shipping_district_name,
                        'postal_code' => $request->shipping_postal_code,
                    ]);

                    $fullAddress = $request->shipping_address . ', ' . 
                                  $request->shipping_district_name . ', ' .
                                  $request->shipping_city_name . ', ' . 
                                  $request->shipping_province_name . ', ' . 
                                  $request->shipping_postal_code;
                }

                $shippingCourier = $request->shipping_courier;
                $shippingService = $request->shipping_service;
                $shippingCost = $request->shipping_cost;

            } else {
                // Store Pickup Logic
                $pickupStoreId = $request->pickup_store_id;
                $store = \App\Models\StoreLocation::find($pickupStoreId);
                
                $fullAddress = "STORE PICKUP: " . $store->name . " - " . $store->address;
                $shippingCourier = 'store_pickup';
                $shippingCost = 0;
                
                $pickupAt = $request->pickup_at;
                $pickupToken = strtoupper(Str::random(6)); // Simple 6-char token
            }

            // Generate Order Number
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            
            // Calculate Discount
            $discountAmount = 0;
            $couponCode = null;
            if ($request->coupon_code && $request->discount_amount) {
                 if (strtoupper($request->coupon_code) === 'DISKON10') {
                     $discountAmount = 10000;
                     $couponCode = 'DISKON10';
                 }
            }

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'distributor_id' => Auth::user()->distributor_id ?? 1,
                'status' => Order::STATUS_PENDING,
                'recipient_name' => $recipientName,
                'recipient_phone' => $recipientPhone,
                'total_amount' => ($cart->total + $shippingCost) - $discountAmount, 
                'shipping_address' => $fullAddress,
                'shipping_courier' => $shippingCourier, 
                'shipping_service' => $shippingService,
                'shipping_cost' => $shippingCost,
                'shipping_tracking_number' => null,
                'shipping_note' => $request->shipping_note,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'coupon_code' => $couponCode,
                'discount_amount' => $discountAmount,
                'notes' => $request->notes,
                'delivery_type' => $deliveryType,
                'pickup_store_id' => $pickupStoreId,
                'pickup_at' => $pickupAt,
                'pickup_token' => $pickupToken,
            ]);

            // Create Order Items
            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'total_price' => $item->quantity * $item->product->price,
                ]);

                // Deduct Stock
                $stockQuery = \App\Models\ProductStock::where('distributor_id', $distributorId)
                    ->where('product_id', $item->product_id);
                
                if ($pickupStoreId) {
                    $stockQuery->where('store_location_id', $pickupStoreId);
                } else {
                    $stockQuery->whereNull('store_location_id');
                }

                $stockQuery->decrement('quantity', $item->quantity);
            }

            // Clear Cart
            $cart->items()->delete();

            DB::commit();

            return redirect()->route('orders.invoice', $order)->with('success', 'Order placed successfully! Please complete payment.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to process order: ' . $e->getMessage());
        }
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }
}
