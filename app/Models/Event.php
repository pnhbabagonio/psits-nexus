<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'location',
        'max_participants',
        'registration_deadline',
        'event_type',
        'is_paid',
        'fee_amount',
        'organizer_id',
        'status',
        'banner_image'
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
        'registration_deadline' => 'datetime',
        'is_paid' => 'boolean',
        'fee_amount' => 'decimal:2',
        'max_participants' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class, 'event_id');
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'event_id');
    }

    public function knowledgeBaseEntries(): HasMany
    {
        return $this->hasMany(ChatbotKnowledgeBase::class, 'related_event_id');
    }
}