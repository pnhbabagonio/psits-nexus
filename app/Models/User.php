<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'user_id';
    public $timestamps = false;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password_hash', // Make sure this is included
        'student_id',
        'role_id',
        'department',
        'year_level',
        'contact_number',
        'account_status',
        'date_registered'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'date_registered' => 'datetime',
        'last_login' => 'datetime',
    ];

    /**
     * Get the password for the user.
     * Overriding default password field name
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relationship to Role
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * REMOVE OR COMMENT OUT THIS MUTATOR - passwords are already hashed
     * Set the password hash attribute
     */
    // public function setPasswordHashAttribute($value)
    // {
    //     $this->attributes['password_hash'] = bcrypt($value);
    // }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->role && $this->role->role_name === 'admin';
    }

    /**
     * Check if user account is active
     */
    public function isActive(): bool
    {
        return $this->account_status === 'active';
    }

    /**
     * Get full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}