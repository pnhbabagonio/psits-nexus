<?php

namespace App\Http\Controllers;

use App\Models\EventRegistration;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class EventRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = EventRegistration::with(['event', 'user', 'payment']);
        
        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status')) {
            $query->where('attendance_status', $request->status);
        }

        $registrations = $query->orderBy('registration_date', 'desc')->paginate(20);

        return response()->json($registrations);
    }

    public function show($id)
    {
        $registration = EventRegistration::with(['event', 'user', 'payment'])->findOrFail($id);
        return response()->json($registration);
    }

    public function register(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $user = auth()->user();

        // Check if registration deadline has passed
        if (now() > $event->registration_deadline) {
            return response()->json(['message' => 'Registration deadline has passed'], 400);
        }

        // Check if event is at capacity
        if ($event->max_participants) {
            $currentRegistrations = EventRegistration::where('event_id', $eventId)->count();
            if ($currentRegistrations >= $event->max_participants) {
                return response()->json(['message' => 'Event is at capacity'], 400);
            }
        }

        // Check if user is already registered
        $existingRegistration = EventRegistration::where('event_id', $eventId)
            ->where('user_id', $user->user_id)
            ->first();

        if ($existingRegistration) {
            return response()->json(['message' => 'Already registered for this event'], 400);
        }

        $registration = EventRegistration::create([
            'event_id' => $eventId,
            'user_id' => $user->user_id,
            'payment_status' => $event->is_paid ? 'pending' : 'completed',
            'attendance_status' => 'registered'
        ]);

        return response()->json($registration, 201);
    }

    public function updateAttendance(Request $request, $id)
    {
        $registration = EventRegistration::findOrFail($id);

        $validated = $request->validate([
            'attendance_status' => 'required|in:registered,attended,no_show,cancelled',
            'check_in_time' => 'nullable|date',
            'check_out_time' => 'nullable|date|after:check_in_time',
            'notes' => 'nullable|string'
        ]);

        $registration->update($validated);

        return response()->json($registration);
    }

    public function destroy($id)
    {
        $registration = EventRegistration::findOrFail($id);
        $registration->delete();

        return response()->json(['message' => 'Registration deleted successfully']);
    }
}