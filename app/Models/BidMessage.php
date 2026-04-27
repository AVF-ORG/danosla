<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BidMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'bid_id',
        'user_id',
        'parent_id',
        'message',
        'is_read',
    ];

    public function bid(): BelongsTo
    {
        return $this->belongsTo(ShipmentBid::class, 'bid_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(BidMessage::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(BidMessage::class, 'parent_id');
    }
}
