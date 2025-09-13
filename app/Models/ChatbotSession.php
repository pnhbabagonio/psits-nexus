<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotSession extends Model
{
    use HasFactory;

    protected $primaryKey = 'session_id';
    public $keyType = 'string';
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'session_id',
        'user_id',
        'session_status',
        'user_platform',
        'user_browser',
        'ip_address',
        'initial_query'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'last_activity' => 'datetime',
        'session_status' => 'string'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(ChatbotConversation::class, 'session_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'session_id');
    }
}