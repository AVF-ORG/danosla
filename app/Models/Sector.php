<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;
class Sector extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = ['name', 'slug', 'is_active'];

    public array $translatable = ['name', 'slug'];
}
