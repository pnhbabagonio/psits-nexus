<?php
// app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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
        'max_capacity',
        'organizer',
        'user_id',
        'registered',
        'image_url',
        'registration_deadline'
    ];

    protected $casts = [
        'date' => 'date',
        'registration_deadline' => 'datetime',
    ];

    // Status constants
    const STATUS_UPCOMING = 'Upcoming';
    const STATUS_ONGOING = 'Ongoing';
    const STATUS_COMPLETED = 'Completed';

    // Relationship with user (event creator)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with registrations
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_UPCOMING);
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', self::STATUS_ONGOING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeWithAvailableSpots($query)
    {
        return $query->whereRaw('registered_count < max_capacity');
    }

    // Accessors
    public function getFormattedDateAttribute(): string
    {
        return $this->date->format('M j, Y');
    }

    public function getFormattedTimeAttribute(): string
    {
        return Carbon::parse($this->time)->format('g:i A');
    }

    public function getRegisteredCountAttribute(): int
    {
        return $this->registrations()->registered()->count();
    }

    public function getWaitlistedCountAttribute(): int
    {
        return $this->registrations()->waitlisted()->count();
    }

    public function getHasAvailableSpotsAttribute(): bool
    {
        return $this->registered_count < $this->max_capacity;
    }

    public function getAvailableSpotsAttribute(): int
    {
        return max(0, $this->max_capacity - $this->registered_count);
    }

    public function getIsFullAttribute(): bool
    {
        return $this->registered_count >= $this->max_capacity;
    }

    // Business logic methods
    public function isUserRegistered(int $userId): bool
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->registered()
            ->exists();
    }

    public function isUserWaitlisted(int $userId): bool
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->waitlisted()
            ->exists();
    }

    public function getUserRegistration(int $userId): ?Registration
    {
        return $this->registrations()
            ->where('user_id', $userId)
            ->active()
            ->first();
    }

    public function canRegister($user = null): bool
    {
        if (!$user) return false;

        // Check if already registered
        if ($this->getUserRegistration($user->id)) {
            return false;
        }

        // Check registration deadline
        if ($this->registration_deadline && $this->registration_deadline->isPast()) {
            return false;
        }

        // Check if event is in the future
        if ($this->date->isPast()) {
            return false;
        }

        // Check if event is active
        if ($this->status !== self::STATUS_UPCOMING) {
            return false;
        }

        return true;
    }

    public function getWaitlistPosition(int $userId): ?int
    {
        $registration = $this->registrations()
            ->waitlisted()
            ->where('user_id', $userId)
            ->first();

        if (!$registration) return null;

        return $this->registrations()
            ->waitlisted()
            ->where('registered_at', '<=', $registration->registered_at)
            ->count();
    }

    // Promotion methods for waitlist
    public function promoteFirstWaitlisted(): ?Registration
    {
        $firstWaitlisted = $this->registrations()
            ->waitlisted()
            ->orderBy('registered_at')
            ->first();

        if ($firstWaitlisted && $this->has_available_spots) {
            $firstWaitlisted->markAsRegistered();
            return $firstWaitlisted;
        }

        return null;
    }
}
