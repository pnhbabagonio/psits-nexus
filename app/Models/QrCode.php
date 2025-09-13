<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QrCode extends Model
{
    use HasFactory;

    protected $primaryKey = 'qr_code_id';
    public $timestamps = true;

    protected $fillable = [
        'qr_code_id',
        'entity_type',
        'entity_id',
        'qr_data',
        'expiration_datetime',
        'created_by',
        'is_active',
        'usage_count',
        'last_used'
    ];

    protected $casts = [
        'expiration_datetime' => 'datetime',
        'last_used' => 'datetime',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
        'created_at' => 'datetime'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'qr_code_id');
    }
}