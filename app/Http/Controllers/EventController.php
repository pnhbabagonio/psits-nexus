<?php
// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    public function index(Request $request): Response
    {
        $status = $request->get('status', 'All');

        $events = Event::with('user')
            ->when($status !== 'All', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get()
            ->map(function ($event) {
                return $this->formatEvent($event);
            });

        return Inertia::render('Event', [
            'events' => $events, // Make sure this is passed correctly
            'filters' => $request->only(['status']),
            'stats' => $this->getStats(),
        ]);
    }

    public function data(Request $request)
    {
        $status = $request->get('status', 'All');

        $events = Event::with('user')
            ->when($status !== 'All', function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->latest()
            ->get()
            ->map(function ($event) {
                return $this->formatEvent($event);
            });

        return response()->json([
            'events' => $events,
            'stats' => $this->getStats(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'venue' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required|in:Upcoming,Ongoing,Completed',
            'max_capacity' => 'required|integer|min:1',
            'organizer' => 'required|string|max:255',
        ]);

        $event = Event::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'registered' => 0,
        ]);

        // Return the created event in the response
        return redirect()->route('events')->with([
            'success' => 'Event created successfully',
            'created_event' => $this->formatEvent($event) // Return the formatted event
        ]);
    }

    public function update(Request $request, Event $event)
    {
        // Manual authorization check
        if ($event->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Unauthorized to update this event');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'venue' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required|in:Upcoming,Ongoing,Completed',
            'max_capacity' => 'required|integer|min:1',
            'organizer' => 'required|string|max:255',
        ]);

        $event->update($validated);

        return redirect()->back()->with('success', 'Event updated successfully');
    }

    public function destroy(Request $request, Event $event)
    {
        // Manual authorization check
        if ($event->user_id !== $request->user()->id) {
            return redirect()->back()->with('error', 'Unauthorized to delete this event');
        }

        $event->delete();

        return redirect()->back()->with('success', 'Event deleted successfully');
    }

    private function getStats(): array
    {
        return [
            'total_events' => Event::count(),
            'active_registrations' => Event::sum('registered'),
            'upcoming_events' => Event::where('status', 'Upcoming')->count(),
            'average_attendance' => Event::where('status', 'Completed')
                ->avg('registered') ?? 0,
        ];
    }

    private function formatEvent(Event $event): array
    {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'date' => $event->formatted_date,
            'time' => $event->formatted_time,
            'venue' => $event->venue,
            'address' => $event->address,
            'status' => $event->status,
            'registered' => (string) $event->registered,
            'max_capacity' => $event->max_capacity,
            'organizer' => $event->organizer,
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
        ];
    }
}
