<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorDomain extends Model
{
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'domain',
        'status',
        'dns_verification_record',
        'is_primary',
        'verified_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'verified_at' => 'datetime',
    ];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class);
    }
}
