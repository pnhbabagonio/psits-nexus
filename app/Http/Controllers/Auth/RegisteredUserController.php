<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // ✅ For querying roles
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
        return Inertia::render('auth/Register', [
            'roles' => DB::table('roles')
                ->where('role_name', '!=', 'Admin')
                ->get(['role_id', 'role_name']),
        ]);
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role_id' => 'required|exists:roles,role_id',
            'student_id' => 'nullable|string|max:50|unique:users,student_id',
            'department' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:50',
        ]);

        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'student_id'    => $request->student_id,
            'password_hash' => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'department'    => $request->department,
            'year_level'    => $request->year_level,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
