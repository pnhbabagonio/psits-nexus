<?php
// app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'date',
        'time',
        'venue',
        'address',
        'status',
        'registered',
        'max_capacity',
        'organizer',
        'user_id',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor for formatted date
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('F j, Y');
    }

    // Accessor for formatted time
    public function getFormattedTimeAttribute(): string
    {
        return $this->time->format('g:i A');
    }

    // Scope for filtering by status
    public function scopeStatus($query, $status)
    {
        if ($status && $status !== 'All') {
            return $query->where('status', $status);
        }
        return $query;
    }
}
