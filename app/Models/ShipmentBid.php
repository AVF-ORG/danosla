<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShipmentBid extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipment_id',
        'user_id',
        'price',
        'latest_pickup_date',
        'latest_pickup_time',
        'latest_delivery_date',
        'latest_delivery_time',
        'status',
    ];

    protected $casts = [
        'latest_pickup_date' => 'date',
        'latest_delivery_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(BidMessage::class, 'bid_id');
    }
}
