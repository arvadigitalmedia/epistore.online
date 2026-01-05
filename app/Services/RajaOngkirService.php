<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RajaOngkirService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.rajaongkir.key');
        $this->baseUrl = config('services.rajaongkir.base_url', 'https://api.rajaongkir.com/starter');
    }

    public function getProvinces()
    {
        return Cache::remember('rajaongkir_provinces', 86400, function () {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->withoutVerifying()->get($this->baseUrl . '/destination/province');

            if ($response->successful()) {
                return $this->normalizeProvinces($response->json());
            }

            Log::error('RajaOngkir Get Provinces Failed', ['response' => $response->body()]);
            return [];
        });
    }

    public function getCities($provinceId = null)
    {
        $cacheKey = 'rajaongkir_cities' . ($provinceId ? '_' . $provinceId : '');

        return Cache::remember($cacheKey, 86400, function () use ($provinceId) {
            $url = $this->baseUrl . '/destination/city';
            if ($provinceId) {
                $url .= '/' . $provinceId;
            }
            
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->withoutVerifying()->get($url);

            if ($response->successful()) {
                return $this->normalizeCities($response->json());
            }

            Log::error('RajaOngkir Get Cities Failed', ['response' => $response->body()]);
            return [];
        });
    }

    public function getDistricts($cityId)
    {
        $cacheKey = 'rajaongkir_districts_' . $cityId;

        return Cache::remember($cacheKey, 86400, function () use ($cityId) {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->withoutVerifying()->get($this->baseUrl . '/destination/district/' . $cityId);

            if ($response->successful()) {
                return $this->normalizeDistricts($response->json());
            }

            Log::warning('RajaOngkir Get Districts Failed', ['response' => $response->body()]);
            return [];
        });
    }

    public function getSubDistricts($districtId)
    {
        $cacheKey = 'rajaongkir_subdistricts_' . $districtId;

        return Cache::remember($cacheKey, 86400, function () use ($districtId) {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::withHeaders([
                'key' => $this->apiKey
            ])->withoutVerifying()->get($this->baseUrl . '/destination/subdistrict/' . $districtId);

            if ($response->successful()) {
                return $this->normalizeSubDistricts($response->json());
            }

            Log::warning('RajaOngkir Get SubDistricts Failed', ['response' => $response->body()]);
            return [];
        });
    }

    public function calculateCost($origin, $destination, $weight, $courier)
    {
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::withHeaders([
            'key' => $this->apiKey,
            'content-type' => 'application/x-www-form-urlencoded'
        ])->withoutVerifying()->asForm()->post($this->baseUrl . '/calculate/domestic-cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier
        ]);

        if ($response->successful()) {
            return $this->normalizeCost($response->json());
        }

        Log::error('RajaOngkir Cost Calculation Failed', ['response' => $response->body()]);
        throw new \Exception('Gagal menghitung ongkos kirim: ' . ($response->json()['meta']['message'] ?? 'Unknown error'));
    }

    protected function normalizeProvinces($json)
    {
        // Handle API structure with 'data'
        if (isset($json['data'])) {
            return array_map(function ($item) {
                return [
                    'province_id' => $item['id'] ?? $item['province_id'],
                    'province' => $item['name'] ?? $item['province_name'] ?? $item['province']
                ];
            }, $json['data']);
        }
        
        // Handle standard RajaOngkir structure
        if (isset($json['rajaongkir']['results'])) {
             return $json['rajaongkir']['results'];
        }

        return [];
    }

    protected function normalizeCities($json)
    {
        // Handle API structure with 'data'
        if (isset($json['data'])) {
            return array_map(function ($item) {
                return [
                    'city_id' => $item['id'] ?? $item['city_id'],
                    'city_name' => $item['name'] ?? $item['city_name'],
                    'type' => $item['type'] ?? 'Kota/Kab',
                    'postal_code' => $item['zip_code'] ?? $item['postal_code'] ?? ''
                ];
            }, $json['data']);
        }

        // Handle standard RajaOngkir structure
        if (isset($json['rajaongkir']['results'])) {
             return $json['rajaongkir']['results'];
        }

        return [];
    }

    protected function normalizeDistricts($json)
    {
        // Handle API structure with 'data'
        if (isset($json['data'])) {
            return array_map(function ($item) {
                return [
                    'subdistrict_id' => $item['id'] ?? $item['subdistrict_id'], // Legacy key for View
                    'district_id' => $item['id'] ?? $item['subdistrict_id'],     // New clear key
                    'subdistrict_name' => $item['name'] ?? $item['district_name'] ?? $item['subdistrict_name']
                ];
            }, $json['data']);
        }

        // Handle standard RajaOngkir structure
        if (isset($json['rajaongkir']['results'])) {
             return $json['rajaongkir']['results'];
        }

        return [];
    }

    protected function normalizeSubDistricts($json)
    {
        if (isset($json['data'])) {
            return array_map(function ($item) {
                return [
                    'id' => $item['id'],
                    'name' => $item['name'] ?? $item['subdistrict_name'] ?? ''
                ];
            }, $json['data']);
        }
        return [];
    }

    protected function normalizeCost($json)
    {
        // New API Structure (Komerce V2 - Flattened List)
        if (isset($json['data']) && is_array($json['data'])) {
            $data = $json['data'];
            if (empty($data)) return null;

            $normalizedCosts = [];
            $courierCode = $data[0]['code'] ?? '';
            $courierName = $data[0]['name'] ?? $courierCode;

            foreach ($data as $item) {
                $normalizedCosts[] = [
                    'service' => $item['service'],
                    'description' => $item['description'] ?? '',
                    'cost' => [
                        [
                            'value' => $item['cost'], // Direct integer value
                            'etd' => $item['etd'] ?? '',
                            'note' => ''
                        ]
                    ]
                ];
            }

            return [
                'code' => $courierCode,
                'name' => $courierName,
                'costs' => $normalizedCosts
            ];
        }

        // Handle standard RajaOngkir structure (Legacy)
        return $json['rajaongkir']['results'][0] ?? null;
    }
}
