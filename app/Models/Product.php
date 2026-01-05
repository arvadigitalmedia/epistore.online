<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Auditable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'brand_id',
        'name',
        'slug',
        'sku',
        'description',
        'unit',
        'price',
        'member_price',
        'image',
        'status',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function priceHistories()
    {
        return $this->hasMany(PriceHistory::class)->latest();
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }
    
    public function stockForDistributor($distributorId)
    {
        return $this->stocks()->where('distributor_id', $distributorId)->value('quantity') ?? 0;
    }
}
