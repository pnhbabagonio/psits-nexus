<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class UserController extends Controller
{
    // Authentication Methods
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => true,
            'status' => session('status'),
        ]);
    }

    public function showRegisterForm()
    {
        return Inertia::render('Auth/Register', [
            'roles' => Role::where('role_name', '!=', 'super_admin')
                          ->orderBy('role_name')
                          ->get(['role_id', 'role_name']),
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'last_name' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'],
            'role_id' => ['required', 'exists:roles,role_id'],
            'student_id' => ['nullable', 'unique:users,student_id', 'regex:/^[0-9]{4}-[0-9]{4,6}$/'],
            'department' => ['nullable', 'string', 'max:100'],
            'year_level' => ['nullable', 'string', 'max:50', 'in:1st Year,2nd Year,3rd Year,4th Year,5th Year,Graduate'],
            'contact_number' => ['nullable', 'string', 'max:20', 'regex:/^[+]?[\d\s\-()]+$/'],
            'terms' => ['required', 'accepted'],
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
            'first_name.regex' => 'First name can only contain letters and spaces.',
            'last_name.regex' => 'Last name can only contain letters and spaces.',
            'student_id.regex' => 'Student ID must be in format: YYYY-XXXXXX (e.g., 2024-123456)',
            'contact_number.regex' => 'Please enter a valid contact number.',
        ]);

        // Default role for student registration
        if (!$validated['role_id']) {
            $studentRole = Role::where('role_name', 'student')->first();
            $validated['role_id'] = $studentRole->role_id ?? 1;
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'student_id' => $validated['student_id'] ?? null,
            'department' => $validated['department'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'account_status' => 'pending', // Require admin approval
        ]);

        // Send verification email or admin notification here
        
        return redirect()->route('login')->with('status', 'Registration successful! Your account is pending approval. You will receive an email once approved.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if user exists and is active
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if ($user->account_status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Your account is ' . $user->account_status . '. Please contact administrator.'],
            ]);
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials do not match our records.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // User Management Methods
    public function index(Request $request)
    {
        $query = User::with('role');
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }
        
        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('role_name', $request->role);
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('account_status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($users);
        }

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
            'roles' => Role::all(['role_id', 'role_name']),
        ]);
    }

    public function show($id)
    {
        $user = User::with(['role', 'organizedEvents', 'payments'])->findOrFail($id);
        
        if (request()->expectsJson()) {
            return response()->json($user);
        }

        return Inertia::render('Users/Show', [
            'user' => $user,
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::all(['role_id', 'role_name']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,role_id',
            'student_id' => 'nullable|unique:users,student_id',
            'department' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:20',
            'account_status' => 'sometimes|in:active,inactive,suspended,pending'
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password_hash' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'student_id' => $validated['student_id'] ?? null,
            'department' => $validated['department'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'account_status' => $validated['account_status'] ?? 'pending',
            'email_verified_at' => now(), // Auto-verify for admin created users
        ]);

        if ($request->expectsJson()) {
            return response()->json($user->load('role'), 201);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::with('role')->findOrFail($id);
        
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => Role::all(['role_id', 'role_name']),
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => ['sometimes', 'email', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'sometimes|exists:roles,role_id',
            'student_id' => ['nullable', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'department' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:20',
            'account_status' => 'sometimes|in:active,inactive,suspended,pending'
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password_hash'] = Hash::make($validated['password']);
            unset($validated['password']);
        }

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json($user->load('role'));
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting own account
        if ($user->user_id === Auth::id()) {
            if (request()->expectsJson()) {
                return response()->json(['message' => 'You cannot delete your own account.'], 422);
            }
            return redirect()->back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $user->delete();

        if (request()->expectsJson()) {
            return response()->json(['message' => 'User deleted successfully']);
        }

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validated = $request->validate([
            'account_status' => 'required|in:active,inactive,suspended,pending'
        ]);

        $user->update($validated);

        if ($request->expectsJson()) {
            return response()->json($user->load('role'));
        }

        return redirect()->back()->with('success', 'User status updated successfully.');
    }

    // Profile Methods
    public function profile()
    {
        return Inertia::render('Profile/Show', [
            'user' => Auth::user()->load('role'),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'student_id' => ['nullable', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'department' => 'nullable|string|max:100',
            'year_level' => 'nullable|string|max:50',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password_hash' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}