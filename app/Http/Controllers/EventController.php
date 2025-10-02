<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');
        
        $events = Event::withCount(['attendees as registered_count' => function($query) {
                $query->where('attendance_status', '!=', 'cancelled');
            }])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('location', 'like', "%{$search}%")
                      ->orWhere('category', 'like', "%{$search}%");
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'date' => $event->date->format('Y-m-d'),
                    'time' => $event->time->format('H:i'),
                    'location' => $event->location,
                    'capacity' => $event->capacity,
                    'registered' => $event->registered_count,
                    'status' => $event->status,
                    'category' => $event->category,
                    'is_full' => $event->registered_count >= $event->capacity,
                    'created_at' => $event->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $event->updated_at->format('Y-m-d H:i:s'),
                ];
            });

        return Inertia::render('EventManagement', [
            'events' => $events,
            'filters' => ['search' => $search],
        ]);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'title' => ['required', 'string', 'max:255'],
        'description' => ['required', 'string'],
        'date' => ['required', 'date', 'after_or_equal:today'],
        'time' => ['required', 'date_format:H:i'],
        'location' => ['required', 'string', 'max:255'],
        'capacity' => ['required', 'integer', 'min:1'],
        'category' => ['required', 'string', 'max:255'],
        'status' => ['required', 'in:upcoming,ongoing,completed,cancelled'],
    ]);

        $event = Event::create([
        'title' => $validated['title'],
        'description' => $validated['description'],
        'date' => $validated['date'],
        'time' => $validated['time'],
        'location' => $validated['location'],
        'capacity' => $validated['capacity'],
        'category' => $validated['category'],
        'status' => $validated['status'],
        ]);

        return redirect()->route('event-management')->with('success', 'Event created successfully!');
    }

    public function show(Event $event)
    {
        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'description' => $event->description,
            'date' => $event->date->format('Y-m-d'),
            'time' => $event->time->format('H:i'),
            'location' => $event->location,
            'capacity' => $event->capacity,
            'registered' => $event->registered,
            'status' => $event->status,
            'category' => $event->category,
            'is_full' => $event->is_full,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'date' => ['required', 'date'],
            'time' => ['required', 'date_format:H:i'],
            'location' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'category' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:upcoming,ongoing,completed,cancelled'],
        ]);

        // Ensure capacity is not less than current registered count
        $currentRegistered = $event->attendees()->where('attendance_status', '!=', 'cancelled')->count();
        if ($validated['capacity'] < $currentRegistered) {
            return redirect()->back()->with('error', 'Capacity cannot be less than current registered attendees (' . $currentRegistered . ').');
        }

        $event->update($validated);

        return redirect()->route('event-management')->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('event-management')->with('success', 'Event deleted successfully!');
    }

    public function getStats()
    {
        $totalEvents = Event::count();
        $upcomingEvents = Event::where('status', 'upcoming')->count();
        $ongoingEvents = Event::where('status', 'ongoing')->count();
        $completedEvents = Event::where('status', 'completed')->count();

        return response()->json([
            'total_events' => $totalEvents,
            'upcoming_events' => $upcomingEvents,
            'ongoing_events' => $ongoingEvents,
            'completed_events' => $completedEvents,
        ]);
    }
}