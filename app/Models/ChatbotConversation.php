<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotConversation extends Model
{
    use HasFactory;

    protected $primaryKey = 'conversation_id';
    public $timestamps = true;

    protected $fillable = [
        'session_id',
        'conversation_type',
        'satisfaction_rating',
        'feedback',
        'escalated_to_human',
        'escalated_to_user_id',
        'escalation_time'
    ];

    protected $casts = [
        'conversation_start' => 'datetime',
        'conversation_end' => 'datetime',
        'escalation_time' => 'datetime',
        'conversation_type' => 'string',
        'satisfaction_rating' => 'string',
        'escalated_to_human' => 'boolean'
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatbotSession::class, 'session_id');
    }

    public function escalatedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'escalated_to_user_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'conversation_id');
    }
}