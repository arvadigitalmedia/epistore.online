<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Distributor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'address',
        'province_id',
        'province_name',
        'city_id',
        'city_name',
        'district_id',
        'district_name',
        'subdistrict_id',
        'subdistrict_name',
        'postal_code',
        'phone',
        'email',
        'logo',
        'status',
        'level',
        'config',
        'bank_account_info',
        'subdomain',
    ];

    protected $casts = [
        'config' => 'array',
        'bank_account_info' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function productStocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function domains()
    {
        return $this->hasMany(DistributorDomain::class);
    }

    public function storeLocations()
    {
        return $this->hasMany(StoreLocation::class);
    }
}
