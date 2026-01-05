<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $distributor = $request->get('current_distributor');

        if (!$distributor) {
            return view('welcome');
        }

        // In a real app, we might filter by what products the distributor actually carries
        // For now, show all active products
        $featuredProducts = Product::where('status', 'active')
            ->latest()
            ->take(8)
            ->get();
            
        return view('storefront.home', compact('featuredProducts'));
    }
}
