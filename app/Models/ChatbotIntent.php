<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatbotIntent extends Model
{
    use HasFactory;

    protected $primaryKey = 'intent_id';
    public $timestamps = true;

    protected $fillable = [
        'intent_name',
        'description',
        'training_phrases',
        'responses',
        'intent_category',
        'is_active',
        'priority'
    ];

    protected $casts = [
        'training_phrases' => 'array',
        'responses' => 'array',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatbotMessage::class, 'intent_id');
    }

    public function knowledgeBaseEntries(): HasMany
    {
        return $this->hasMany(ChatbotKnowledgeBase::class, 'intent_id');
    }
}