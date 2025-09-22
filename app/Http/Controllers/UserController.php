<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id','name','email','student_id','program','year','role','status','last_login')
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'student_id' => $user->student_id ?? '',
                    'program' => $user->program ?? '',
                    'year' => $user->year ?? '',
                    'role' => $user->role,
                    'status' => $user->status,
                    'last_login' => $user->last_login ? $user->last_login->format('Y-m-d') : 'Never',
                ];
            });

        return Inertia::render('UserManagement', [
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => ['required','string','max:255'],
            'email'      => ['required','email','unique:users,email'],
            'studentId'  => ['nullable','string','max:50','unique:users,student_id'],
            'program'    => ['required','string','max:255'],
            'year'       => ['required','string','max:50'],
            'role'       => ['required','in:Member,Officer,Admin'], // Added Admin here
            'status'     => ['required','in:active,inactive'],
            'lastLogin'  => ['nullable','date'],
        ]);

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make('password123'), // Default password
            'student_id'  => $validated['studentId'] ?? null,
            'program'     => $validated['program'] ?? null,
            'year'        => $validated['year'] ?? null,
            'role'        => $validated['role'],
            'status'      => $validated['status'],
            'last_login'  => $validated['lastLogin'] ? now()->parse($validated['lastLogin']) : now(),
        ]);

        return redirect()->back()->with('success', 'User added successfully!');
    }

    public function show(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'student_id' => $user->student_id ?? '',
            'program' => $user->program ?? '',
            'year' => $user->year ?? '',
            'role' => $user->role,
            'status' => $user->status,
            'last_login' => $user->last_login ? $user->last_login->format('Y-m-d') : 'Never',
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => ['required','string','max:255'],
            'email'      => ['required','email', Rule::unique('users')->ignore($user->id)],
            'studentId'  => ['nullable','string','max:50', Rule::unique('users', 'student_id')->ignore($user->id)],
            'program'    => ['required','string','max:255'],
            'year'       => ['required','string','max:50'],
            'role'       => ['required','in:Member,Officer,Admin'], // Added Admin here too
            'status'     => ['required','in:active,inactive'],
            'lastLogin'  => ['nullable','date'],
        ]);

        $user->update([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'student_id'  => $validated['studentId'] ?? null,
            'program'     => $validated['program'] ?? null,
            'year'        => $validated['year'] ?? null,
            'role'        => $validated['role'],
            'status'      => $validated['status'],
            'last_login'  => $validated['lastLogin'] ? now()->parse($validated['lastLogin']) : $user->last_login,
        ]);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}