<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'student_id' => 'nullable|string|max:50|unique:users,student_id',
            'department' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:20',
        ]);

        // Create the user with all required fields from your database schema
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'student_id' => $request->student_id,
            'role_id' => 3, // Student role based on your default data
            'department' => $request->department,
            'year_level' => $request->year_level,
            'contact_number' => $request->contact_number,
            'account_status' => 'pending', // Default status from your schema
            'date_registered' => now(),
        ]);

        event(new Registered($user));

        // Don't login automatically - redirect to pending approval page
        return redirect()->route('pending-approval', ['email' => $user->email]);
    }
}