<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'comment',
        'total_value',
        'total_volume',
        'total_weight',
        'pickup_address',
        'pickup_options',
        'delivery_address',
        'delivery_options',
        'latest_pickup_date',
        'latest_pickup_time',
        'pickup_notify_time',
        'latest_delivery_date',
        'latest_delivery_time',
        'delivery_notify_time',
        'validity_date',
        'delivery_price',
        'requirements',
        'status',
    ];

    protected $casts = [
        'pickup_options' => 'array',
        'delivery_options' => 'array',
        'requirements' => 'array',
        'latest_pickup_date' => 'date',
        'latest_delivery_date' => 'date',
        'validity_date' => 'datetime',
        'delivery_price' => 'decimal:2',
    ];

    public function lot(): HasOne
    {
        return $this->hasOne(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(ShipmentBid::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
