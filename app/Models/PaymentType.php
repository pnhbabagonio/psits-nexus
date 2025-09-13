<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_type_id';
    public $timestamps = true;

    protected $fillable = [
        'type_name',
        'description',
        'amount',
        'is_recurring',
        'recurrence_period',
        'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_recurring' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'payment_type_id');
    }

    public function knowledgeBaseEntries(): HasMany
    {
        return $this->hasMany(ChatbotKnowledgeBase::class, 'related_payment_type_id');
    }
}