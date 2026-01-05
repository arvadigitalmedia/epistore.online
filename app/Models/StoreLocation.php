<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'name',
        'address',
        'rt_rw',
        'subdistrict',
        'district',
        'city',
        'province',
        'postal_code',
        'phone',
        'latitude',
        'longitude',
        'notes',
        'opening_hours',
        'is_active',
        'is_primary',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'is_primary' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
