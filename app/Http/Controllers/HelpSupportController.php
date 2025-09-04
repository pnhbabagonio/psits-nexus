<?php

namespace App\Http\Controllers;

use App\Models\HelpSupport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HelpSupportController extends Controller
{
    /**
     * Display a listing of the user's support tickets.
     */
    public function index()
    {
        $tickets = HelpSupport::where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('HelpSupport/Index', [
            'tickets' => $tickets,
        ]);
    }

    /**
     * Store a newly created support ticket in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'category' => 'required|string',
            'priority' => 'required|string',
        ]);

        HelpSupport::create([
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => 'open', // default status
        ]);

        return redirect()
            ->route('help-support.index')
            ->with('status', 'Support ticket submitted successfully!');
    }
}
