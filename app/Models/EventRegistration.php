<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRegistration extends Model
{
    use HasFactory;

    protected $primaryKey = 'registration_id';
    public $timestamps = true;

    protected $fillable = [
        'event_id',
        'user_id',
        'payment_status',
        'payment_id',
        'attendance_status',
        'check_in_time',
        'check_out_time',
        'notes'
    ];

    protected $casts = [
        'registration_date' => 'datetime',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'payment_status' => 'string',
        'attendance_status' => 'string'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }
}