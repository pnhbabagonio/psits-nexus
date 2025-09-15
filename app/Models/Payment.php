<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $primaryKey = 'payment_id';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'amount',
        'payment_type_id',
        'reference_number',
        'qr_code_id',
        'payment_status',
        'payment_method',
        'notes',
        'receipt_url',
        'processed_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
        'payment_status' => 'string',
        'created_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paymentType(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class, 'payment_type_id');
    }

    public function qrCode(): BelongsTo
    {
        return $this->belongsTo(QrCode::class, 'qr_code_id');
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function eventRegistration(): HasOne
    {
        return $this->hasOne(EventRegistration::class, 'payment_id');
    }

    public function financialTransaction(): HasOne
    {
        return $this->hasOne(FinancialTransaction::class, 'payment_id');
    }
}