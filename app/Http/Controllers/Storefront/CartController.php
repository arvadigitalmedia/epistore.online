<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $distributor = $request->get('current_distributor');
        
        $cart = Cart::where('user_id', Auth::id())
                    ->where('distributor_id', $distributor->id)
                    ->with(['items.product'])
                    ->first();
                    
        return view('storefront.cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $distributor = $request->get('current_distributor');
        
        $cart = Cart::firstOrCreate(
            ['user_id' => Auth::id(), 'distributor_id' => $distributor->id]
        );

        $product = Product::find($request->product_id);
        
        $cartItem = $cart->items()->where('product_id', $product->id)->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('storefront.cart.index')->with('success', 'Product added to cart!');
    }

    public function update(Request $request, $itemId)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        
        $distributor = $request->get('current_distributor');
        $cart = Cart::where('user_id', Auth::id())
                    ->where('distributor_id', $distributor->id)
                    ->firstOrFail();
                    
        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->update(['quantity' => $request->quantity]);
        
        return redirect()->back()->with('success', 'Cart updated!');
    }

    public function remove(Request $request, $itemId)
    {
        $distributor = $request->get('current_distributor');
        $cart = Cart::where('user_id', Auth::id())
                    ->where('distributor_id', $distributor->id)
                    ->firstOrFail();
                    
        $cart->items()->where('id', $itemId)->delete();
        
        return redirect()->back()->with('success', 'Item removed!');
    }
}
