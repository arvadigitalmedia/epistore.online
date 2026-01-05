<?php

namespace App\Exports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BrandsExport implements FromQuery, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function query()
    {
        return Brand::query();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Slug',
            'Description',
            'Status',
            'Created At',
        ];
    }

    public function map($brand): array
    {
        return [
            $brand->id,
            $brand->name,
            $brand->slug,
            $brand->description,
            $brand->status,
            $brand->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
