<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $brand = Brand::where('name', $row['brand'])->first();

        if (!$brand) {
            return null;
        }

        return new Product([
            'brand_id'    => $brand->id,
            'name'        => $row['name'],
            'slug'        => Str::slug($row['name'] . '-' . $row['sku']),
            'sku'         => $row['sku'],
            'description' => $row['description'] ?? null,
            'price'       => $row['price'],
            'status'      => $row['status'] ?? 'draft',
            'created_by'  => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'brand'  => 'required|exists:brands,name',
            'name'   => 'required|string|max:255',
            'sku'    => 'required|string|max:100|unique:products,sku',
            'price'  => 'required|numeric|min:0',
            'status' => 'nullable|in:active,draft,inactive',
        ];
    }
}
