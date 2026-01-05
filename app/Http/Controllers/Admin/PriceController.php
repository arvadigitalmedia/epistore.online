<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PriceHistory;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PriceUpdateImport;

class PriceController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('brand')->where('status', 'active');

        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->orderBy('name')->paginate(50); // Pagination needed
        $brands = Brand::orderBy('name')->get();

        return view('admin.prices.index', compact('products', 'brands'));
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            Excel::import(new PriceUpdateImport, $request->file('file'));
            return redirect()->route('admin.prices.pending')
                ->with('success', 'Prices imported successfully and are waiting for approval.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error importing prices: ' . $e->getMessage());
        }
    }

    public function history()
    {
        $histories = PriceHistory::with(['product', 'creator', 'approver'])
            ->latest()
            ->paginate(20);
            
        return view('admin.prices.history', compact('histories'));
    }

    public function pending()
    {
        $pendingPrices = PriceHistory::with(['product', 'creator'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('admin.prices.pending', compact('pendingPrices'));
    }

    public function approve(PriceHistory $priceHistory)
    {
        if ($priceHistory->status !== 'pending') {
            return redirect()->back()->with('error', 'This price change is not pending.');
        }

        DB::transaction(function () use ($priceHistory) {
            // Update the history status
            $priceHistory->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

            // Apply the new price to the product
            $priceHistory->product->update([
                'price' => $priceHistory->new_price,
            ]);
        });

        return redirect()->back()->with('success', 'Price change approved and applied.');
    }

    public function reject(PriceHistory $priceHistory)
    {
        if ($priceHistory->status !== 'pending') {
            return redirect()->back()->with('error', 'This price change is not pending.');
        }

        $priceHistory->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(), // Rejected by
            'approved_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Price change rejected.');
    }
}
