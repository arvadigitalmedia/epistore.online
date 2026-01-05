<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\ProductStock;
use Illuminate\Database\Seeder;

class ProductStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $distributors = Distributor::all();

        if ($products->isEmpty() || $distributors->isEmpty()) {
            return;
        }

        foreach ($distributors as $distributor) {
            foreach ($products as $product) {
                ProductStock::firstOrCreate(
                    [
                        'distributor_id' => $distributor->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => 100, // Default stock for testing
                    ]
                );
            }
        }
    }
}
