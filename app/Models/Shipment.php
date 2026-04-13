<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $fillable = [
        'user_id',
        'description',
        'comment',
        'total_value',
        'total_volume',
        'total_weight',
        'pickup_address',
        'pickup_type',
        'pickup_options',
        'delivery_address',
        'delivery_type',
        'delivery_options',
        'latest_pickup_date',
        'latest_pickup_time',
        'pickup_notify_time',
        'latest_delivery_date',
        'latest_delivery_time',
        'delivery_notify_time',
        'requirements',
        'status',
    ];

    protected $casts = [
        'pickup_options' => 'array',
        'delivery_options' => 'array',
        'requirements' => 'array',
        'latest_pickup_date' => 'date',
        'latest_delivery_date' => 'date',
    ];

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
