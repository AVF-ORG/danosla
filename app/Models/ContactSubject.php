<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class ContactSubject extends Model
{
    use HasTranslations, SoftDeletes;

    protected $fillable = ['name', 'slug', 'is_active'];

    public array $translatable = ['name', 'slug'];

    /**
     * Get the contacts for the subject.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'contact_subject_id');
    }
}
