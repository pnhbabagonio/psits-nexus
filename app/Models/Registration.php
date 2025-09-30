<?php
// app/Models/Registration.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'status',
        'registered_at',
        'cancelled_at',
        'notes',
        'attended'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'attended' => 'boolean',
    ];

    // Status constants
    const STATUS_REGISTERED = 'registered';
    const STATUS_WAITLISTED = 'waitlisted';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ATTENDED = 'attended';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_REGISTERED,
            self::STATUS_WAITLISTED,
            self::STATUS_CANCELLED,
            self::STATUS_ATTENDED,
        ];
    }

    // Relationship with event
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // Relationship with user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeRegistered(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REGISTERED);
    }

    public function scopeWaitlisted(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_WAITLISTED);
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    public function scopeAttended(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ATTENDED);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_REGISTERED, self::STATUS_WAITLISTED]);
    }

    public function scopeForEvent(Builder $query, int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    // Helper methods
    public function isRegistered(): bool
    {
        return $this->status === self::STATUS_REGISTERED;
    }

    public function isWaitlisted(): bool
    {
        return $this->status === self::STATUS_WAITLISTED;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isActive(): bool
    {
        return in_array($this->status, [self::STATUS_REGISTERED, self::STATUS_WAITLISTED]);
    }

    // Status change methods
    public function markAsRegistered(): bool
    {
        return $this->update([
            'status' => self::STATUS_REGISTERED,
            'cancelled_at' => null,
        ]);
    }

    public function markAsWaitlisted(): bool
    {
        return $this->update([
            'status' => self::STATUS_WAITLISTED,
            'cancelled_at' => null,
        ]);
    }

    public function markAsCancelled(): bool
    {
        return $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
        ]);
    }

    public function markAsAttended(): bool
    {
        return $this->update([
            'status' => self::STATUS_ATTENDED,
            'attended' => true,
        ]);
    }

    // Validation rules
    public static function validationRules(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'status' => 'required|in:' . implode(',', self::getStatuses()),
            'registered_at' => 'required|date',
            'cancelled_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
            'attended' => 'boolean',
        ];
    }

    // Check if user can cancel registration
    public function canBeCancelled(): bool
    {
        if ($this->isCancelled()) {
            return false;
        }

        // Allow cancellation up to 24 hours before event or if event is cancelled
        if ($this->event->status === Event::STATUS_COMPLETED) {
            return false;
        }

        return $this->event->date->subHours(24)->isFuture();
    }

    // Get waitlist position
    public function getWaitlistPosition(): ?int
    {
        if (!$this->isWaitlisted()) {
            return null;
        }

        return self::waitlisted()
            ->forEvent($this->event_id)
            ->where('registered_at', '<=', $this->registered_at)
            ->count();
    }

    // Check if registration can be promoted from waitlist
    public function canBePromoted(): bool
    {
        return $this->isWaitlisted() && $this->event->has_available_spots;
    }
}
