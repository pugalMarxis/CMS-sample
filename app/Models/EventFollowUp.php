<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventFollowUp extends Model
{
    protected $table = 'event_follow_ups';

    protected $fillable = [
        'event_id',
        'title',
        'description',
        'reason',
        'assigned_to',
        'due_date',
        'reminder_at',
        'priority',
        'status',
        'outcome',
        'initial_notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'reminder_at' => 'datetime',
        ];
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updates(): HasMany
    {
        return $this->hasMany(EventFollowUpUpdate::class)->latest();
    }

    public function actions(): HasMany
    {
        return $this->hasMany(EventFollowUpAction::class)->orderBy('id');
    }
}
