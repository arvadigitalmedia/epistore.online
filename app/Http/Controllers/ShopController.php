<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Distributor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('brand')->where('status', 'active');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->has('brand') && $request->brand) {
            $query->whereHas('brand', function($q) use ($request) {
                $q->where('slug', $request->brand);
            });
        }

        $products = $query->latest()->paginate(12);
        $brands = Brand::where('status', 'active')->orderBy('name')->get();

        // Get current user's distributor or fallback to default/first one for display
        $distributor = null;
        if (Auth::check()) {
            if (Auth::user()->distributor_id) {
                $distributor = Auth::user()->distributor;
            } else {
                // Fallback for customers not yet assigned or admin browsing
                $distributor = Distributor::find(1); 
            }
        }
        
        // If still null (e.g. no distributors exist yet), handle gracefully in view
        
        return view('shop.index', compact('products', 'brands', 'distributor'));
    }
    
    public function show(Product $product)
    {
        if ($product->status !== 'active') {
            abort(404);
        }
        
        $product->load('brand');
        
        // Related products logic could go here
        
        return view('shop.show', compact('product'));
    }
}
