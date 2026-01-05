<?php

namespace App\Services;

use App\Models\Distributor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DistributorShippingService
{
    protected $rajaOngkir;

    public function __construct(RajaOngkirService $rajaOngkir)
    {
        $this->rajaOngkir = $rajaOngkir;
    }

    /**
     * Get normalized shipping settings for a distributor.
     * Merges DB columns (priority) with JSON config (fallback/extras).
     */
    public function getSettings(Distributor $distributor): array
    {
        $config = $distributor->config['shipping'] ?? [];

        // DB Columns take precedence for location data
        if ($distributor->province_id) {
            $config['origin_province_id'] = $distributor->province_id;
            $config['origin_province_name'] = $distributor->province_name;
            $config['origin_city_id'] = $distributor->city_id;
            $config['origin_city_name'] = $distributor->city_name;
            $config['origin_district_id'] = $distributor->district_id;
            $config['origin_district_name'] = $distributor->district_name;
            $config['origin_subdistrict_id'] = $distributor->subdistrict_id;
            $config['origin_subdistrict_name'] = $distributor->subdistrict_name;
            $config['origin_postal_code'] = $distributor->postal_code;
            $config['origin_address'] = $distributor->address;
        }

        // Defaults
        return array_merge([
            'origin_province_id' => '',
            'origin_province_name' => '',
            'origin_city_id' => '',
            'origin_city_name' => '',
            'origin_district_id' => '',
            'origin_district_name' => '',
            'origin_subdistrict_id' => '', // Optional/Hidden in simplified UI
            'origin_subdistrict_name' => '',
            'origin_postal_code' => '',
            'origin_address' => '',
            'default_weight' => 1000,
            'couriers' => [],
            'margin' => 0,
            'enable_store_pickup' => false,
        ], $config);
    }

    /**
     * Update shipping settings for a distributor.
     * Saves location data to DB columns and extra config to JSON.
     */
    public function updateSettings(Distributor $distributor, array $data): void
    {
        Log::info('Updating shipping settings for distributor: ' . $distributor->id, $data);

        DB::transaction(function () use ($distributor, $data) {
            // 1. Update DB Columns (Core Location)
            $distributor->update([
                'province_id' => $data['origin_province_id'],
                'province_name' => $data['origin_province_name'] ?? null,
                'city_id' => $data['origin_city_id'],
                'city_name' => $data['origin_city_name'] ?? null,
                'district_id' => $data['origin_district_id'] ?? null,
                'district_name' => $data['origin_district_name'] ?? null,
                'subdistrict_id' => $data['origin_subdistrict_id'] ?? null,
                'subdistrict_name' => $data['origin_subdistrict_name'] ?? null,
                'postal_code' => $data['origin_postal_code'] ?? null,
                'address' => $data['origin_address'] ?? null,
            ]);

            // 2. Update JSON Config (Preferences)
            $config = $distributor->config ?? [];
            $config['shipping'] = array_merge($config['shipping'] ?? [], [
                'origin_province_id' => $data['origin_province_id'], // Redundant but safe for JSON readers
                'origin_city_id' => $data['origin_city_id'],
                'origin_district_id' => $data['origin_district_id'] ?? null,
                'default_weight' => $data['default_weight'] ?? 1000,
                'couriers' => $data['couriers'] ?? [],
                'margin' => $data['margin'] ?? 0,
                'enable_store_pickup' => isset($data['enable_store_pickup']) ? (bool) $data['enable_store_pickup'] : false,
            ]);
            
            $distributor->config = $config;
            $distributor->save();
        });
    }

    /**
     * Calculate shipping cost with distributor's specific settings (origin, margin).
     * This is the method external checkout systems should call.
     */
    public function calculateShipping(Distributor $distributor, string $destinationCityId, ?string $destinationDistrictId, int $weight, string $courier): array
    {
        $settings = $this->getSettings($distributor);

        // Determine best origin ID
        // Note: For RajaOngkir Starter (Free), we MUST use City ID. 
        // Subdistrict ID is only supported in Pro account with 'originType' parameter.
        // To ensure success, we default to City ID.
        $origin = $settings['origin_city_id'];
        
        // Determine destination ID
        // Similarly, RajaOngkir Starter only accepts City ID for destination.
        $destination = $destinationCityId;

        // Calculate Base Cost
        $result = $this->rajaOngkir->calculateCost(
            $origin,
            $destination,
            $weight,
            $courier
        );

        if (!$result) {
            throw new \Exception("Gagal mengambil data ongkir dari kurir $courier");
        }

        // Apply Margin
        $margin = (int) ($settings['margin'] ?? 0);
        $result['costs'] = array_map(function ($service) use ($margin) {
            // Add margin to every cost value
            $service['cost'][0]['value'] += $margin;
            // Add note about handling fee (optional, maybe internal only)
            // $service['cost'][0]['note'] = "Includes handling fee"; 
            return $service;
        }, $result['costs'] ?? []);

        // Log Calculation
        $this->logCalculation($distributor, $origin, $destinationCityId, $weight, $courier, $result, $margin);

        return $result;
    }

    protected function logCalculation($distributor, $origin, $destination, $weight, $courier, $result, $margin)
    {
        try {
            DB::table('shipping_calculation_logs')->insert([
                'distributor_id' => $distributor->id,
                'origin_city_id' => $origin,
                'origin_city_name' => $result['origin_details']['city_name'] ?? 'Unknown',
                'destination_city_id' => $destination,
                'destination_city_name' => $result['destination_details']['city_name'] ?? 'Unknown',
                'weight' => $weight,
                'courier' => $courier,
                'service' => $result['costs'][0]['service'] ?? 'Unknown',
                'cost' => ($result['costs'][0]['cost'][0]['value'] ?? 0) - $margin, // Base cost
                'margin' => $margin,
                'total_price' => $result['costs'][0]['cost'][0]['value'] ?? 0, // Total with margin
                'raw_response' => json_encode($result),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log shipping calculation: ' . $e->getMessage());
        }
    }
}
