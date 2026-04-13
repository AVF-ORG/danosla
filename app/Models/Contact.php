<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contact_subject_id',
        'name',
        'email',
        'phone',
        'content',
        'reply_content',
        'replied_at',
        'replied_by'
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    /**
     * Get the subject that owns the contact message.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(ContactSubject::class, 'contact_subject_id');
    }

    /**
     * Get the admin who replied to the message.
     */
    public function repliedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    /**
     * Check if the message has been replied to.
     */
    public function isReplied(): bool
    {
        return !is_null($this->replied_at);
    }
}
