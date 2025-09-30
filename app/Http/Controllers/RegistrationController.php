<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegistrationController extends Controller
{
    // Register user for event
    public function store(Request $request, Event $event)
    {
        $user = $request->user();

        // Check if already registered or waitlisted
        $existingRegistration = Registration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['registered', 'waitlisted'])
            ->first();

        if ($existingRegistration) {
            return response()->json([
                'success' => false,
                'message' => $existingRegistration->status === 'registered'
                    ? 'You are already registered for this event.'
                    : 'You are already on the waitlist for this event.'
            ], 409);
        }

        DB::transaction(function () use ($event, $user) {
            $registeredCount = Registration::where('event_id', $event->id)
                ->where('status', 'registered')
                ->count();

            $status = ($registeredCount < $event->max_capacity) ? 'registered' : 'waitlisted';

            Registration::create([
                'event_id' => $event->id,
                'user_id' => $user->id,
                'status' => $status,
                'registered_at' => now(),
            ]);
        });

        $message = ($event->available_spots > 0)
            ? 'Successfully registered for the event!'
            : 'Event is full. You have been added to the waitlist.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_waitlisted' => ($event->available_spots <= 0)
        ]);
    }

    // Cancel registration
    public function destroy(Request $request, Event $event)
    {
        $user = $request->user();

        $registration = Registration::where('event_id', $event->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['registered', 'waitlisted'])
            ->first();

        if (!$registration) {
            return response()->json([
                'success' => false,
                'message' => 'You are not registered for this event.'
            ], 404);
        }

        DB::transaction(function () use ($registration, $event) {
            $wasRegistered = $registration->status === 'registered';
            $registration->delete();

            // Promote first waitlisted user if this was a registered spot
            if ($wasRegistered) {
                $this->promoteFirstWaitlisted($event);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Registration cancelled successfully.'
        ]);
    }

    // Get event registrants (admin only)
    public function getRegistrants(Request $request, Event $event)
    {
        // Authorization - only event owner can view registrants
        if ($event->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized to view registrants for this event.');
        }

        $registrants = Registration::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'registered')
            ->orderBy('registered_at')
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'user_id' => $registration->user_id,
                    'name' => $registration->user->name,
                    'email' => $registration->user->email,
                    'registered_at' => $registration->registered_at->format('M j, Y g:i A'),
                    'program' => $registration->user->program ?? 'N/A',
                    'student_id' => $registration->user->student_id ?? 'N/A',
                ];
            });

        $waitlisted = Registration::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'waitlisted')
            ->orderBy('registered_at')
            ->get()
            ->map(function ($registration) {
                return [
                    'id' => $registration->id,
                    'user_id' => $registration->user_id,
                    'name' => $registration->user->name,
                    'email' => $registration->user->email,
                    'registered_at' => $registration->registered_at->format('M j, Y g:i A'),
                    'program' => $registration->user->program ?? 'N/A',
                    'student_id' => $registration->user->student_id ?? 'N/A',
                ];
            });

        return response()->json([
            'registrants' => $registrants,
            'waitlisted' => $waitlisted,
            'stats' => [
                'registered_count' => $registrants->count(),
                'waitlisted_count' => $waitlisted->count(),
                'max_capacity' => $event->max_capacity,
                'available_spots' => max(0, $event->max_capacity - $registrants->count()),
            ]
        ]);
    }

    // Remove registrant (admin only)
    public function removeRegistrant(Request $request, Event $event, $registrationId)
    {
        // Authorization - only event owner can remove registrants
        if ($event->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized to remove registrants from this event.');
        }

        $registration = Registration::where('event_id', $event->id)
            ->where('id', $registrationId)
            ->firstOrFail();

        DB::transaction(function () use ($registration, $event) {
            $wasRegistered = $registration->status === 'registered';
            $registration->delete();

            // Promote first waitlisted user if this was a registered spot
            if ($wasRegistered) {
                $this->promoteFirstWaitlisted($event);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Registrant removed successfully.'
        ]);
    }

    /**
     * Promote the first waitlisted user to registered status
     */
    private function promoteFirstWaitlisted(Event $event)
    {
        $firstWaitlisted = Registration::where('event_id', $event->id)
            ->where('status', 'waitlisted')
            ->orderBy('registered_at')
            ->first();

        if ($firstWaitlisted) {
            $firstWaitlisted->update(['status' => 'registered']);

            // Optional: Send notification to the promoted user
            // Notification::send($firstWaitlisted->user, new WaitlistPromotionNotification($event));
        }
    }
}
