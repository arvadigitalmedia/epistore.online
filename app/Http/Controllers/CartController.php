<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        // Handle unauthenticated access gracefully
        if (!Auth::check()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }
            return redirect()->route('login');
        }

        $cart = Cart::with(['items.product.brand'])->firstOrCreate([
            'user_id' => Auth::id()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'cart' => $cart,
                'total_quantity' => $cart->total_quantity,
                'total_price' => $cart->total,
                'discount_amount' => 0,
            ]);
        }

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id()
        ]);

        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        // Refresh cart to get updated totals and relations
        $cart->load(['items.product.brand']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart' => $cart,
                'total_quantity' => $cart->total_quantity,
                'total_price' => $cart->total,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = CartItem::whereHas('cart', function($q) {
            $q->where('user_id', Auth::id());
        })->where('id', $itemId)->firstOrFail();

        $cartItem->update([
            'quantity' => $request->quantity
        ]);

        if ($request->wantsJson()) {
            $cart = $cartItem->cart;
            $cart->load(['items.product.brand']);
            
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully.',
                'cart' => $cart,
                'total_quantity' => $cart->total_quantity,
                'total_price' => $cart->total,
                'discount_amount' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    public function remove(Request $request, $itemId)
    {
        $cartItem = CartItem::whereHas('cart', function($q) {
            $q->where('user_id', Auth::id());
        })->where('id', $itemId)->firstOrFail();

        $cart = $cartItem->cart;
        $cartItem->delete();

        if ($request->wantsJson()) {
            $cart->load(['items.product.brand']);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
                'cart' => $cart,
                'total_quantity' => $cart->total_quantity,
                'total_price' => $cart->total,
            ]);
        }

        return redirect()->back()->with('success', 'Item removed from cart.');
    }
}
