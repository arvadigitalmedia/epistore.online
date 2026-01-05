<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Models\StoreLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StoreProfileController extends Controller
{
    /**
     * Show the form for editing the store profile.
     */
    public function edit()
    {
        $distributor = Auth::user()->distributor;
        // Fetch primary store location or create a dummy one if none exists
        $storeLocation = $distributor->storeLocations()->where('is_primary', true)->first();

        if (!$storeLocation) {
            // Fallback to first active location or create new empty instance
            $storeLocation = $distributor->storeLocations()->first() ?? new StoreLocation();
        }

        return view('distributor.store-profile.edit', compact('distributor', 'storeLocation'));
    }

    /**
     * Update the store profile.
     */
    public function update(Request $request)
    {
        $distributor = Auth::user()->distributor;

        $validated = $request->validate([
            // Distributor fields
            'logo' => 'nullable|image|max:2048',
            'bank_account_info' => 'nullable|array',
            'bank_account_info.bank_name' => 'nullable|string',
            'bank_account_info.account_number' => 'nullable|string',
            'bank_account_info.account_holder' => 'nullable|string',
            
            // Store Location fields
            'address' => 'required|string',
            'rt_rw' => 'nullable|string',
            'subdistrict' => 'required|string', // Kelurahan
            'district' => 'required|string',    // Kecamatan
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        // 1. Update Distributor Info (Logo & Bank)
        if ($request->hasFile('logo')) {
            if ($distributor->logo) {
                Storage::disk('public')->delete($distributor->logo);
            }
            $path = $request->file('logo')->store('logos', 'public');
            $distributor->logo = $path;
        }

        $distributor->bank_account_info = $validated['bank_account_info'] ?? [];
        $distributor->save();

        // 2. Update or Create Primary Store Location
        $storeLocation = $distributor->storeLocations()->where('is_primary', true)->first();
        
        if (!$storeLocation) {
            // Check if any location exists, if so make it primary, else create new
            $storeLocation = $distributor->storeLocations()->first();
            if (!$storeLocation) {
                $storeLocation = new StoreLocation();
                $storeLocation->distributor_id = $distributor->id;
                $storeLocation->name = $distributor->name . ' (Main Store)'; // Default name
            }
            $storeLocation->is_primary = true;
        }

        $storeLocation->fill([
            'address' => $validated['address'],
            'rt_rw' => $validated['rt_rw'],
            'subdistrict' => $validated['subdistrict'],
            'district' => $validated['district'],
            'city' => $validated['city'],
            'province' => $validated['province'],
            'postal_code' => $validated['postal_code'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'notes' => $validated['notes'],
            'is_active' => true,
        ]);
        
        $storeLocation->save();

        return redirect()->route('distributor.store-profile.edit')
            ->with('success', 'Informasi Toko berhasil diperbarui.');
    }
}
