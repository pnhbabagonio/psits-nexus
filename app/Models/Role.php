<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'role_id';
    public $timestamps = true;

    protected $fillable = [
        'role_name',
        'description',
        'permissions'
    ];

    protected $casts = [
        'permissions' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}