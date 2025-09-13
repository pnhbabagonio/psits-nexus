<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotKnowledgeBase extends Model
{
    use HasFactory;

    protected $primaryKey = 'knowledge_id';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'content',
        'intent_id',
        'related_event_id',
        'related_payment_type_id',
        'keywords',
        'knowledge_type',
        'view_count',
        'helpful_count',
        'unhelpful_count',
        'created_by',
        'is_published'
    ];

    protected $casts = [
        'view_count' => 'integer',
        'helpful_count' => 'integer',
        'unhelpful_count' => 'integer',
        'is_published' => 'boolean',
        'knowledge_type' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function intent(): BelongsTo
    {
        return $this->belongsTo(ChatbotIntent::class, 'intent_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'related_event_id');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class, 'related_payment_type_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}