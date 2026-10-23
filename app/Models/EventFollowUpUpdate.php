<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFollowUpUpdate extends Model
{
    protected $fillable = [
        'update_type',
        'notes',
        'next_action',
        'author_id',
    ];

    public function followUp(): BelongsTo
    {
        return $this->belongsTo(EventFollowUp::class, 'event_follow_up_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}