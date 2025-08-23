<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// app/Models/User.php

// Add this import at the top of the file, under the others
use App\Notifications\QueuedResetPasswordNotification;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // ... your existing fillable, hidden, casts methods ...

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
