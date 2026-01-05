<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'distributor_id'];

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }

    // Helper untuk hitung total
    public function getTotalAttribute()
    {
        return $this->items->sum(function ($item) {
            // Use product price or dynamic price calculation
            return $item->quantity * $item->product->price;
        });
    }
    
    // Helper untuk hitung total item
    public function getTotalQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }
}
