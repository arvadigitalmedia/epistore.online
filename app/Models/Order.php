<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'distributor_id',
        'total_amount',
        'status',
        'recipient_name',
        'recipient_phone',
        'delivery_type',
        'pickup_store_id',
        'pickup_at',
        'pickup_token',
        'qr_code_path',
        'shipping_courier',
        'shipping_service',
        'shipping_tracking_number',
        'shipping_cost',
        'shipping_address',
        'shipping_note',
        'payment_method',
        'payment_status',
        'payment_proof_path',
        'coupon_code',
        'discount_amount',
        'paid_at',
        'shipped_at',
        'estimated_delivery_date',
        'delivered_at',
        'received_at',
        'notes',
    ];

    protected $casts = [
        'pickup_at' => 'datetime',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'estimated_delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'received_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
    ];

    // Status Constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SHIPPING = 'shipping'; // Dalam Pengiriman
    const STATUS_DELIVERED = 'delivered'; // Barang Sampai (System/Courier)
    const STATUS_COMPLETED = 'completed'; // Diterima User
    const STATUS_CANCELLED = 'cancelled';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(Distributor::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function pickupStore(): BelongsTo
    {
        return $this->belongsTo(StoreLocation::class, 'pickup_store_id');
    }
}
