<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialTransaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';
    public $timestamps = true;

    protected $fillable = [
        'transaction_type',
        'amount',
        'description',
        'category_id',
        'payment_id',
        'event_id',
        'recorded_by',
        'receipt_image',
        'approval_status',
        'approved_by',
        'approval_date',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
        'approval_date' => 'datetime',
        'transaction_type' => 'string',
        'approval_status' => 'string'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}