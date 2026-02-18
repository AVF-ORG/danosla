<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'name', 'is_active'];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }
}
