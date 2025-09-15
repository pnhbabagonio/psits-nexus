<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransactionCategory extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';
    public $timestamps = true;

    protected $fillable = [
        'category_name',
        'parent_category_id',
        'transaction_type',
        'description',
        'budget_amount',
        'budget_period'
    ];

    protected $casts = [
        'budget_amount' => 'decimal:2',
        'transaction_type' => 'string',
        'created_at' => 'datetime'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'parent_category_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TransactionCategory::class, 'parent_category_id');
    }

    public function financialTransactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'category_id');
    }
}