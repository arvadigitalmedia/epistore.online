<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\PriceHistory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Auth;

class PriceUpdateImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip if missing essential data
            if (!isset($row['sku']) || !isset($row['new_price'])) {
                continue;
            }

            $product = Product::where('sku', $row['sku'])->first();
            
            // Skip if product not found
            if (!$product) {
                continue;
            }

            // Skip if price hasn't changed
            if ($product->price == $row['new_price']) {
                continue;
            }

            // Create pending price update
            PriceHistory::create([
                'product_id' => $product->id,
                'old_price' => $product->price,
                'new_price' => $row['new_price'],
                'reason' => $row['reason'] ?? 'Bulk Import',
                'status' => 'pending', // Always pending for imports for safety
                'created_by' => Auth::id(),
                'effective_date' => isset($row['effective_date']) ? \Carbon\Carbon::parse($row['effective_date']) : now(),
            ]);
        }
    }
}
