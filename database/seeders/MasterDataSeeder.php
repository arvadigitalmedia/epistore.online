<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;
use App\Models\Product;
use App\Models\PriceHistory;
use Illuminate\Support\Facades\DB;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create Brands
        $brands = [
            ['name' => 'Antam', 'slug' => 'antam', 'status' => 'active'],
            ['name' => 'UBS', 'slug' => 'ubs', 'status' => 'active'],
            ['name' => 'Lotus Archi', 'slug' => 'lotus-archi', 'status' => 'active'],
            ['name' => 'Galeri 24', 'slug' => 'galeri-24', 'status' => 'active'],
        ];

        foreach ($brands as $b) {
            Brand::create($b);
        }

        // Create Products for Antam
        $antam = Brand::where('slug', 'antam')->first();
        $products = [
            [
                'name' => 'Antam 1g',
                'slug' => 'antam-1g',
                'sku' => 'ANT-001',
                'unit' => 'pcs',
                'price' => 1400000,
                'status' => 'active',
                'description' => 'Logam Mulia Antam 1 gram Certieye',
            ],
            [
                'name' => 'Antam 5g',
                'slug' => 'antam-5g',
                'sku' => 'ANT-005',
                'unit' => 'pcs',
                'price' => 6800000,
                'status' => 'active',
                'description' => 'Logam Mulia Antam 5 gram Certieye',
            ],
            [
                'name' => 'Antam 10g',
                'slug' => 'antam-10g',
                'sku' => 'ANT-010',
                'unit' => 'pcs',
                'price' => 13500000,
                'status' => 'active',
                'description' => 'Logam Mulia Antam 10 gram Certieye',
            ],
        ];

        foreach ($products as $p) {
            $product = Product::create([
                'brand_id' => $antam->id,
                'name' => $p['name'],
                'slug' => $p['slug'],
                'sku' => $p['sku'],
                'unit' => $p['unit'],
                'price' => $p['price'],
                'status' => $p['status'],
                'description' => $p['description'],
            ]);

            // Create initial price history
            PriceHistory::create([
                'product_id' => $product->id,
                'old_price' => 0,
                'new_price' => $p['price'],
                'reason' => 'Initial Seeding',
                'status' => 'applied',
                'created_by' => 1, // Super Admin
                'effective_date' => now(),
            ]);
        }

        // Create Products for UBS
        $ubs = Brand::where('slug', 'ubs')->first();
        $ubsProducts = [
            [
                'name' => 'UBS 1g',
                'slug' => 'ubs-1g',
                'sku' => 'UBS-001',
                'unit' => 'pcs',
                'price' => 1380000,
                'status' => 'active',
                'description' => 'Logam Mulia UBS 1 gram',
            ],
            [
                'name' => 'UBS 5g',
                'slug' => 'ubs-5g',
                'sku' => 'UBS-005',
                'unit' => 'pcs',
                'price' => 6700000,
                'status' => 'active',
                'description' => 'Logam Mulia UBS 5 gram',
            ],
        ];

        foreach ($ubsProducts as $p) {
            $product = Product::create([
                'brand_id' => $ubs->id,
                'name' => $p['name'],
                'slug' => $p['slug'],
                'sku' => $p['sku'],
                'unit' => $p['unit'],
                'price' => $p['price'],
                'status' => $p['status'],
                'description' => $p['description'],
            ]);

            PriceHistory::create([
                'product_id' => $product->id,
                'old_price' => 0,
                'new_price' => $p['price'],
                'reason' => 'Initial Seeding',
                'status' => 'applied',
                'created_by' => 1,
                'effective_date' => now(),
            ]);
        }
    }
}
