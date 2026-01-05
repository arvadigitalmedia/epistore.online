<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Distributor;
use App\Models\StoreLocation;
use App\Models\Product;
use App\Models\ProductStock;

class StoreLocationSeeder extends Seeder
{
    public function run()
    {
        $distributor = Distributor::find(1);
        if ($distributor) {
            $store1 = StoreLocation::create([
                'distributor_id' => $distributor->id,
                'name' => 'Cabang Utama (Pusat)',
                'address' => $distributor->address ?? 'Jl. Jendral Sudirman No. 1',
                'city' => $distributor->city_name ?? 'Jakarta Pusat',
                'province' => $distributor->province_name ?? 'DKI Jakarta',
                'postal_code' => $distributor->postal_code ?? '10220',
                'phone' => $distributor->phone ?? '021-12345678',
                'opening_hours' => 'Senin - Jumat: 09:00 - 17:00',
                'is_active' => true,
            ]);

            $store2 = StoreLocation::create([
                'distributor_id' => $distributor->id,
                'name' => 'Cabang Gudang Selatan',
                'address' => 'Jl. Fatmawati No. 99',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'postal_code' => '12430',
                'phone' => '021-87654321',
                'opening_hours' => 'Senin - Sabtu: 08:00 - 20:00',
                'is_active' => true,
            ]);

            // Seed Stock for these stores
            $products = Product::all();
            foreach ($products as $product) {
                // Main Warehouse Stock (Null store)
                ProductStock::firstOrCreate(
                    [
                        'distributor_id' => $distributor->id, 
                        'product_id' => $product->id, 
                        'store_location_id' => null
                    ],
                    ['quantity' => 100]
                );

                // Store 1 Stock
                ProductStock::create([
                    'distributor_id' => $distributor->id,
                    'product_id' => $product->id,
                    'store_location_id' => $store1->id,
                    'quantity' => 50
                ]);

                // Store 2 Stock
                ProductStock::create([
                    'distributor_id' => $distributor->id,
                    'product_id' => $product->id,
                    'store_location_id' => $store2->id,
                    'quantity' => 30
                ]);
            }
        }
    }
}
