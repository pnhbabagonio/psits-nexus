<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\QueuedResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

<<<<<<< HEAD
    // ... your existing fillable, hidden, casts methods ...
=======
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
    'name',
    'email',
    'password',
    'student_id', 
    'program',
    'year',
    'role',
    'status',
    'last_login', 
    ];
>>>>>>> f051cdd9295d8bb62643759c26b5bb84a7dfe03d

    /**
     * Send the password reset notification.
     * This overrides the default method and queues the notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // This will queue the notification instead of sending it immediately
        $this->notify(new QueuedResetPasswordNotification($token));
    }
}
