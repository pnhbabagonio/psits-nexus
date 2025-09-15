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
        'password_hash',
        'student_id',
        'role_id',
        'department',
        'year_level',
        'contact_number',
        'account_status',
        'date_registered',
        'last_login'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    protected $casts = [
        'date_registered' => 'datetime',
        'last_login' => 'datetime',
    ];

    // 👇 Keep only non-conflicting computed attributes
    protected $appends = [
        'name',
        'program',
        'year',
        'role_name', // renamed from 'role'
        'status',
        'last_login_formatted'
    ];

    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getProgramAttribute()
    {
        return $this->department;
    }

    public function getYearAttribute()
    {
        return $this->year_level;
    }

    // 👇 Fix: renamed to role_name
    public function getRoleNameAttribute()
    {
        return $this->role?->role_name ?? 'Member';
    }

    public function getStatusAttribute()
    {
        $statusMap = [
            'active' => 'active',
            'inactive' => 'inactive',
            'suspended' => 'inactive',
            'pending' => 'inactive'
        ];

        return $statusMap[$this->account_status] ?? 'inactive';
    }

    public function getLastLoginFormattedAttribute()
    {
        return $this->last_login ? $this->last_login->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get the password for the user.
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
