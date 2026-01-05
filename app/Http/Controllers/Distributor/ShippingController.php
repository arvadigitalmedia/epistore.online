<?php

namespace App\Http\Controllers\Distributor;

use App\Http\Controllers\Controller;
use App\Services\DistributorShippingService;
use App\Services\RajaOngkirService;
use App\Models\Courier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    protected $shippingService;
    protected $rajaOngkir;

    public function __construct(DistributorShippingService $shippingService, RajaOngkirService $rajaOngkir)
    {
        $this->shippingService = $shippingService;
        $this->rajaOngkir = $rajaOngkir;
    }

    public function index()
    {
        $distributor = Auth::user()->distributor;
        $provinces = $this->rajaOngkir->getProvinces();
        $shippingConfig = $this->shippingService->getSettings($distributor);
        $couriers = Courier::active()->orderBy('priority')->get();

        return view('distributor.shipping.index', compact('distributor', 'provinces', 'shippingConfig', 'couriers'));
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

    public function getSubDistricts($districtId)
    {
        $subDistricts = $this->rajaOngkir->getSubDistricts($districtId);
        return response()->json($subDistricts);
    }

    public function update(Request $request)
    {
        Log::info('Shipping update request:', $request->all());

        $request->validate([
            'origin_province_id' => 'required',
            'origin_province_name' => 'nullable|string',
            'origin_city_id' => 'required',
            'origin_city_name' => 'nullable|string',
            'origin_district_id' => 'nullable',
            'origin_district_name' => 'nullable|string',
            'origin_subdistrict_id' => 'nullable',
            'origin_subdistrict_name' => 'nullable|string',
            'origin_postal_code' => 'nullable|string',
            'origin_address' => 'nullable|string',
            'default_weight' => 'required|numeric|min:1',
            'couriers' => 'array',
            'margin' => 'nullable|numeric|min:0',
            'enable_store_pickup' => 'nullable|boolean',
        ]);

        $distributor = Auth::user()->distributor;
        $this->shippingService->updateSettings($distributor, $request->all());

        return back()->with('success', 'Pengaturan pengiriman berhasil disimpan.');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'destination_city_id' => 'required',
            'weight' => 'required|numeric|min:1',
            'courier' => 'required|string',
        ]);

        $distributor = Auth::user()->distributor;

        try {
            $result = $this->shippingService->calculateShipping(
                $distributor,
                $request->destination_city_id,
                $request->destination_district_id, // Nullable
                $request->weight,
                $request->courier
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
