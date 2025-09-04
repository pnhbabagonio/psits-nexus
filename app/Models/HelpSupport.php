<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpSupport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // ← Make sure this is included
        'subject',
        'message',
        'category',
        'priority',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
