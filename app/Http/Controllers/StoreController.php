<?php

namespace App\Http\Controllers;

use App\Models\StoreLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StoreController extends Controller
{
    /**
     * Search stores by keyword or location.
     */
    public function search(Request $request)
    {
        $distributorId = Auth::user()->distributor_id ?? 1;
        $keyword = $request->query('q');
        $lat = $request->query('lat');
        $lng = $request->query('lng');

        $query = StoreLocation::where('distributor_id', $distributorId)
            ->where('is_active', true);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('city', 'like', "%{$keyword}%")
                  ->orWhere('address', 'like', "%{$keyword}%");
            });
        }

        $stores = $query->get();

        // Calculate distance if coordinates are provided
        if ($lat && $lng) {
            $stores->transform(function ($store) use ($lat, $lng) {
                // Assuming StoreLocation has lat/lng, if not we'll just return as is or mock it
                // For now, let's assume we don't have lat/lng in DB yet, so distance is 0 or handled by client
                // If we had lat/lng columns:
                /*
                $distance = $this->calculateDistance($lat, $lng, $store->latitude, $store->longitude);
                $store->distance = round($distance, 1);
                */
                $store->distance = null; // Placeholder
                return $store;
            });
        }

        return response()->json($stores);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344); // Kilometers
    }
}
