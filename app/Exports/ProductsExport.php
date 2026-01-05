<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromQuery, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return Product::with('brand');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Brand',
            'Name',
            'Slug',
            'SKU',
            'Price',
            'Status',
            'Created At',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->brand->name ?? '-',
            $product->name,
            $product->slug,
            $product->sku,
            $product->price,
            $product->status,
            $product->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
