<?php

namespace App\Http\Controllers;

use App\Models\HelpSupport;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class HelpSupportController extends Controller
{
    /**
     * Display the help and support page.
     */
    public function index()
    {
        // Check if user is admin
        $isAdmin = Auth::user()->is_admin ?? false;

        // Get tickets based on user role
        if ($isAdmin) {
            // Admin sees ALL tickets
            $tickets = HelpSupport::with('user')
                ->latest()
                ->get();
        } else {
            // Regular users see only their tickets
            $tickets = HelpSupport::where('user_id', Auth::id())
                ->latest()
                ->get();
        }

        return Inertia::render('HelpSupport/Index', [
            'tickets' => $tickets,
            'isAdmin' => $isAdmin, // Pass admin status to frontend
            'status' => session('status'),
        ]);
    }

    /**
     * Store a new support ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'category' => 'required|in:technical,billing,account,feature_request,other',
            'priority' => 'required|in:low,medium,high,urgent',
        ]);

        $ticket = HelpSupport::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        return redirect()->route('help-support.index')
            ->with('status', 'Your support ticket has been submitted successfully!');
    }
}
