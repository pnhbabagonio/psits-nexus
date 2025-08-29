<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendeeController extends Controller
{
    // Mock data for now
    private $mockAttendees = [
        [
            'id' => 1,
            'first_name' => 'Maria',
            'middle_name' => 'D.',
            'last_name' => 'Santos',
            'email' => 'maria@example.com',
            'status' => 'present',
            'checkin_type' => 'qr',
            'checkin_time' => '09:15',
            'date' => '2025-09-15',
        ],
        [
            'id' => 2,
            'first_name' => 'Juan',
            'middle_name' => null,
            'last_name' => 'Dela Cruz',
            'email' => 'juan@example.com',
            'status' => 'absent',
            'checkin_type' => null,
            'checkin_time' => null,
            'date' => '2025-09-15',
        ],
        [
            'id' => 3,
            'first_name' => 'Ana',
            'middle_name' => 'M.',
            'last_name' => 'Reyes',
            'email' => 'ana@example.com',
            'status' => 'present',
            'checkin_type' => 'manual',
            'checkin_time' => '10:05',
            'date' => '2025-09-15',
        ],
    ];

    /**
     * Show all attendees
     */
    public function index()
    {
        return Inertia::render('Attendee', [
            'attendees' => $this->mockAttendees,
        ]);
    }

    /**
     * Store a new attendee (mock only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email',
            'status'     => 'required|in:present,absent',
        ]);

        // Pretend we saved it
        return redirect()->back()->with('success', 'New attendee added (mock).');
    }

    /**
     * Update attendee (mock only)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'first_name' => 'nullable|string|max:100',
            'last_name'  => 'nullable|string|max:100',
            'email'      => 'nullable|email',
            'status'     => 'nullable|in:present,absent',
        ]);

        return redirect()->back()->with('success', "Attendee #{$id} updated (mock).");
    }

    /**
     * Delete attendee (mock only)
     */
    public function destroy($id)
    {
        return redirect()->back()->with('success', "Attendee #{$id} deleted (mock).");
    }

    /**
     * Export attendees (mock only)
     */
    public function export()
    {
        // Just pretend an export happened
        return redirect()->back()->with('success', 'Attendees exported (mock).');
    }
}
