<?php

namespace App\Http\Controllers;

use App\Models\ChatBot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ChatbotController extends Controller
{
    /**
     * Show the chat interface with messages.
     */
    public function index()
    {
        $messages = ChatBot::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('ChatBot', [
            'messages' => $messages,
        ]);
    }

    /**
     * Store a new chat message and generate a bot reply.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        // Save user's message
        $userMessage = ChatBot::create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_bot' => false,
        ]);

        // Very simple bot logic (replace with AI later)
        $botReply = ChatBot::create([
            'user_id' => Auth::id(),
            'message' => "You said: {$validated['message']} 🤖",
            'is_bot' => true,
        ]);

        // Return JSON for Inertia to update without full page reload
        return response()->json([
            'userMessage' => $userMessage,
            'botReply' => $botReply,
        ]);
    }

    /**
     * Clear the chat history.
     */
    public function clear()
    {
        ChatBot::where('user_id', Auth::id())->delete();

        return response()->json(['status' => 'cleared']);
    }
}
