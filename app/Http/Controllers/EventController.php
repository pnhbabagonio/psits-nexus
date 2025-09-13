<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('organizer');
        
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('type')) {
            $query->where('event_type', $request->type);
        }

        if ($request->has('organizer')) {
            $query->where('organizer_id', $request->organizer);
        }

        if ($request->has('upcoming') && $request->upcoming) {
            $query->where('start_datetime', '>', now());
        }

        $events = $query->orderBy('start_datetime')->paginate(15);

        return response()->json($events);
    }

    public function show($id)
    {
        $event = Event::with(['organizer', 'registrations.user'])->findOrFail($id);
        return response()->json($event);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'start_datetime' => 'required|date|after:now',
            'end_datetime' => 'required|date|after:start_datetime',
            'location' => 'required|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'registration_deadline' => 'required|date|before:start_datetime',
            'event_type' => 'required|string|max:100',
            'is_paid' => 'required|boolean',
            'fee_amount' => 'required_if:is_paid,true|numeric|min:0',
            'organizer_id' => 'required|exists:users,user_id',
            'banner_image' => 'nullable|url'
        ]);

        $event = Event::create([
            ...$validated,
            'status' => 'draft'
        ]);

        return response()->json($event, 201);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:200',
            'description' => 'sometimes|string',
            'start_datetime' => 'sometimes|date',
            'end_datetime' => 'sometimes|date|after:start_datetime',
            'location' => 'sometimes|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'registration_deadline' => 'sometimes|date|before:start_datetime',
            'event_type' => 'sometimes|string|max:100',
            'is_paid' => 'sometimes|boolean',
            'fee_amount' => 'required_if:is_paid,true|numeric|min:0',
            'status' => 'sometimes|in:draft,published,cancelled,completed',
            'banner_image' => 'nullable|url'
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    public function publish($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'published']);

        return response()->json(['message' => 'Event published successfully']);
    }

    public function cancel($id)
    {
        $event = Event::findOrFail($id);
        $event->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Event cancelled successfully']);
    }
}