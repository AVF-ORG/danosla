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

    /**
     * Helpers for shipment phases and status
     */

    public function getValidityDiffAttribute(): int
    {
        return $this->validity_date ? (int) now()->diffInMinutes($this->validity_date, false) : 9999;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->validity_diff < 0;
    }

    public function getCanNegotiateAttribute(): bool
    {
        return $this->getRawOriginal('status') === 'pending' && $this->validity_diff >= 0 && $this->validity_diff <= 180;
    }

    public function getCanDemandAttribute(): bool
    {
        return $this->getRawOriginal('status') === 'pending' && $this->validity_diff >= 0;
    }

    public function getStatusAttribute($value)
    {
        if ($value === 'pending' && $this->is_expired) {
            return 'expired';
        }
        return $value;
    }

    public function getStatusConfigAttribute(): array
    {
        $status = $this->status; // Uses the accessor

        return match ($status) {
            'pending' => $this->validity_diff > 180
                ? [
                    'label' => 'Ouvert (Demandes)',
                    'color' => 'bg-blue-50 text-blue-700 border-blue-100',
                    'badge' => 'bg-blue-500',
                    'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                ]
                : [
                    'label' => 'Phase Négociation',
                    'color' => 'bg-amber-50 text-amber-700 border-amber-100',
                    'badge' => 'bg-amber-500',
                    'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
                ],
            'expired' => [
                'label' => 'Expiré',
                'color' => 'bg-red-50 text-red-700 border-red-100',
                'badge' => 'bg-red-500',
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
            'active' => [
                'label' => 'Actif',
                'color' => 'bg-blue-50 text-blue-700 border-blue-100',
                'badge' => 'bg-blue-500',
                'icon' => 'M13 10V3L4 14h7v7l9-11h-7z',
            ],
            'completed' => [
                'label' => 'Terminé',
                'color' => 'bg-success-50 text-success-700 border-success-100',
                'badge' => 'bg-green-500',
                'icon' => 'M5 13l4 4L19 7',
            ],
            'cancelled', 'canceled' => [
                'label' => 'Annulé',
                'color' => 'bg-red-50 text-red-700 border-red-100',
                'badge' => 'bg-red-500',
                'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z',
            ],
            default => [
                'label' => ucfirst($status),
                'color' => 'bg-gray-50 text-gray-700 border-gray-100',
                'badge' => 'bg-gray-500',
                'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            ],
        };
    }
}

