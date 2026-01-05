<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class PriceHistory extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'product_id',
        'old_price',
        'new_price',
        'reason',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'effective_date',
    ];

    protected $casts = [
        'effective_date' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
