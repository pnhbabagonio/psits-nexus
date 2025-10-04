<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'deadline',
        'total_users',
        'paid',
        'unpaid',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'deadline' => 'date',
        'total_users' => 'integer',
        'paid' => 'integer',
        'unpaid' => 'integer',
    ];

    /**
     * Many-to-Many relationship with Users through payments
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'payments')
                    ->withPivot('paid_at', 'amount_paid', 'status')
                    ->withTimestamps();
    }

    /**
     * Get users who have paid this requirement
     */
    public function paidUsers()
    {
        return $this->belongsToMany(User::class, 'payments')
                    ->wherePivot('status', 'paid')
                    ->withPivot('paid_at', 'amount_paid')
                    ->withTimestamps();
    }

    /**
     * Get users who haven't paid this requirement
     */
    public function unpaidUsers()
    {
        return $this->belongsToMany(User::class, 'payments')
                    ->wherePivot('status', 'pending')
                    ->withPivot('paid_at', 'amount_paid')
                    ->withTimestamps();
    }

    // If you want to calculate unpaid automatically
    public function getUnpaidAttribute()
    {
        return $this->total_users - $this->paid;
    }

    // Status accessor (similar to your Vue logic)
    public function getStatusAttribute()
    {
        $now = now();
        $deadline = $this->deadline;

        if ($this->paid < $this->total_users) {
            return $deadline >= $now ? 'Pending' : 'Overdue';
        }
        return 'Done';
    }

    /**
     * Calculate paid count from actual payments
     */
    public function calculatePaidCount()
    {
        return $this->paidUsers()->count();
    }
}