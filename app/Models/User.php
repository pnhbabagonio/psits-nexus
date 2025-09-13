<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'student_id',
        'password_hash',
        'role_id',
        'department',
        'year_level',
        'profile_image',
        'contact_number',
        'account_status'
    ];

    protected $hidden = [
        'password_hash'
    ];

    protected $casts = [
        'date_registered' => 'datetime',
        'last_login' => 'datetime',
        'account_status' => 'string'
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function organizedEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'organizer_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'user_id');
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'recorded_by');
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(QrCode::class, 'created_by');
    }

    public function chatbotSessions(): HasMany
    {
        return $this->hasMany(ChatbotSession::class, 'user_id');
    }
}