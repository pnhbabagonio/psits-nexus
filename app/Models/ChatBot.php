<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatBot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'is_bot',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
    ];

    protected $attributes = [
        'is_bot' => false,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
