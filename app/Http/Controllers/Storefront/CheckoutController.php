<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $distributor = $request->get('current_distributor');
        
        $cart = \App\Models\Cart::where('user_id', Auth::id())
                    ->where('distributor_id', $distributor->id)
                    ->with(['items.product'])
                    ->first();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty.');
        }

        return view('storefront.checkout.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'required|string|max:100',
            'postal-code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
        ]);

        $distributor = $request->get('current_distributor');
        $user = Auth::user();

        $cart = \App\Models\Cart::where('user_id', $user->id)
                    ->where('distributor_id', $distributor->id)
                    ->with(['items.product'])
                    ->first();

        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('storefront.cart.index')->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();

        try {
            $totalAmount = $cart->items->sum(function($item) {
                return $item->product->price * $item->quantity;
            });

            // Basic shipping calculation (placeholder)
            $shippingCost = 0; 

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'distributor_id' => $distributor->id,
                'total_amount' => $totalAmount + $shippingCost,
                'status' => Order::STATUS_PENDING,
                'recipient_name' => $user->name,
                'recipient_phone' => $request->phone,
                'shipping_address' => $request->address . ', ' . $request->city . ', ' . $request->province . ' ' . $request->input('postal-code'),
                'shipping_cost' => $shippingCost,
                'payment_status' => 'unpaid',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku ?? 'SKU-' . $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->product->price * $item->quantity,
                ]);
            }

            // Clear cart
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return redirect()->route('storefront.home')->with('success', 'Order placed successfully! Order #' . $order->order_number);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }
}
