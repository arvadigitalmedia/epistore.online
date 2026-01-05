<?php

namespace App\Imports;

use App\Models\Brand;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BrandsImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Brand([
            'name'        => $row['name'],
            'slug'        => Str::slug($row['name']),
            'description' => $row['description'] ?? null,
            'status'      => $row['status'] ?? 'active',
            'created_by'  => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255|unique:brands,name',
            'status' => 'nullable|in:active,inactive',
        ];
    }
}
