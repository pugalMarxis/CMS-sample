<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_code',
        'title',
        'description',
        'event_date',
        'start_time',
        'end_time',
        'location',
        'status',
        'enable_attendance',
        'enable_questionnaires',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'enable_attendance' => 'boolean',
            'enable_questionnaires' => 'boolean',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(EventFollowUp::class);
    }

    public function questionnaires(): HasMany
{
    return $this->hasMany(Questionnaire::class);
}
}