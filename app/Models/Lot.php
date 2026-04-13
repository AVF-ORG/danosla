<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lot extends Model
{
    protected $fillable = [
        'shipment_id',
        'type',
        'quantity',
        'weight',
        'length',
        'width',
        'height',
        'palette_type',
        'container_type',
        'brand',
        'model',
        'vehicle_weight',
        'is_rolling',
        'is_stackable',
        'volume',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}
