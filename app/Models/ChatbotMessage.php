<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotMessage extends Model
{
    use HasFactory;

    protected $primaryKey = 'message_id';
    public $timestamps = true;

    protected $fillable = [
        'conversation_id',
        'session_id',
        'message_type',
        'message_content',
        'intent_id',
        'entities_extracted',
        'is_user_message',
        'quick_replies',
        'message_metadata'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'entities_extracted' => 'array',
        'quick_replies' => 'array',
        'message_metadata' => 'array',
        'is_user_message' => 'boolean',
        'message_type' => 'string'
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatbotConversation::class, 'conversation_id');
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(ChatbotSession::class, 'session_id');
    }

    public function intent(): BelongsTo
    {
        return $this->belongsTo(ChatbotIntent::class, 'intent_id');
    }
}