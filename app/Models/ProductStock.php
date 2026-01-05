<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'store_location_id',
        'product_id',
        'quantity',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    public function storeLocation()
    {
        return $this->belongsTo(StoreLocation::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
