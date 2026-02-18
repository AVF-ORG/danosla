<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Region extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];

    public array $translatable = ['name', 'description'];

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_region')
            ->withTimestamps();
    }
}
