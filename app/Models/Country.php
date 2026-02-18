<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use SoftDeletes, HasTranslations;

    protected $fillable = [
        'name',
        'iso2',
        'international_code',
        'svg',
    ];

    public array $translatable = ['name'];
    
    protected $casts = [
        'name' => 'array',
    ];
    public function regions()
    {
        return $this->belongsToMany(Region::class, 'country_region')
            ->withTimestamps();
    }
}
