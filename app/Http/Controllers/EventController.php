<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // Main event management page
        return Inertia::render('Events/Index');
    }

    public function show($id)
    {
        // Show a single event (details page)
        return Inertia::render('Events/Show', [
            'eventId' => $id,
        ]);
    }

    public function attendees()
    {
        // Attendee management page
        return Inertia::render('Events/Attendees');
    }

    public function analysis()
    {
        // Event analysis page
        return Inertia::render('Events/Analysis');
    }
}
