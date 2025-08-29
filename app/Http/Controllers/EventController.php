<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Event', [
            'stats' => [
                'totalEvents' => 24,
                'activeRegistrations' => 187,
                'upcomingEvents' => 8,
                'averageAttendance' => 94.2,
            ],
            'recentEvents' => [
                [
                    'title' => 'PSITS Tech Summit 2025',
                    'date' => 'September 15, 2025',
                    'time' => '09:00 AM',
                    'location' => 'PSITS Auditorium',
                    'status' => 'Upcoming',
                    'registered' => '87/150'
                ],
                [
                    'title' => 'Coding Bootcamp',
                    'date' => 'September 20, 2025',
                    'time' => '10:00 AM',
                    'location' => 'Computer Lab 1',
                    'status' => 'Ongoing',
                    'registered' => '28/30'
                ],
            ],
            'recentActivity' => [
                ['message' => 'New registration from Maria Santos', 'time' => '3 hours ago'],
                ['message' => 'Event details updated', 'time' => '1 day ago'],
                ['message' => 'Reminder sent to 87 attendees', 'time' => '2 days ago'],
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Components/EventForm'); 
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'time' => 'required',
            'venue' => 'required|string|max:255',
            'address' => 'required|string',
            'max_capacity' => 'nullable|integer|min:1',
            'registration_fee' => 'nullable|numeric|min:0',
            'requires_approval' => 'boolean',
        ]);

        return redirect()->route('events.index')->with('success', 'Event created successfully!');
    }

    public function update(Request $request, $id)
    {
        // validate for now
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'date'  => 'nullable|date',
            'time'  => 'nullable',
            'venue' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        return redirect()->back()->with('success', "Event #{$id} updated successfully (mock).");
    }
}
