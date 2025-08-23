<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $users = User::select('id','name','email','student_id','program','year','role','status','last_login')
            ->orderBy('id', 'desc')
            ->get();

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
        'program'    => ['nullable','string','max:255'],
        'year'       => ['nullable','string','max:50'],
        'role'       => ['required','string','max:50'],
        'status'     => ['required','string','max:50'],
        'lastLogin'  => ['nullable','date'],
    ]);

    User::create([
        'name'        => $validated['name'],
        'email'       => $validated['email'],
        'password'    => Hash::make(Str::random(16)),
        'student_id'  => $validated['studentId'] ?? null,
        'program'     => $validated['program'] ?? null,
        'year'        => $validated['year'] ?? null,
        'role'        => $validated['role'],
        'status'      => $validated['status'],
        'last_login'  => $validated['lastLogin'] ?? null,
    ]);

    return redirect()->back()->with('success', 'User added!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => ['required','string','max:255'],
            'email'      => ['required','email','unique:users,email,'.$user->id],
            'studentId'  => ['nullable','string','max:50','unique:users,student_id,'.$user->id.',id'],
            'program'    => ['nullable','string','max:255'],
            'year'       => ['nullable','string','max:50'],
            'role'       => ['required','string','max:50'],
            'status'     => ['required','string','max:50'],
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
            'last_login'  => $validated['lastLogin'] ?? null,
        ]);

        return redirect()->back()->with('success', 'User updated!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted!');
    }
}
