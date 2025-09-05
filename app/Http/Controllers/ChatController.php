<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;



class ChatController extends Controller
{
    /**
     * Display the chat interface.
     */
    public function index()
    {
        $messages = ChatMessage::where('user_id', Auth::id())
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Chat/Index', [
            'messages' => $messages,
            'status' => session('status'),
        ]);
    }

    /**
     * Store a new chat message and generate bot response.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        // Store user message
        $userMessage = ChatMessage::create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'sender' => 'user',
            'message_type' => 'text',
        ]);

        // Generate bot response using simple logic
        $botResponse = $this->generateBotResponse($validated['message']);

        // Store bot message
        ChatMessage::create([
            'user_id' => Auth::id(),
            'message' => $botResponse,
            'sender' => 'bot',
            'message_type' => 'text',
        ]);

        return redirect()->route('chat.index');
    }

    /**
     * Simple bot response generator.
     */
    private function generateBotResponse(string $userMessage): string
    {
        $message = strtolower($userMessage);

        if (Str::contains($message, ['hello', 'hi', 'hey'])) {
            return "Hello! How can I assist you today?";
        }

        if (Str::contains($message, ['account', 'profile', 'login'])) {
            return "I can help you with account-related issues. Are you having trouble logging in or updating your profile?";
        }

        if (Str::contains($message, ['billing', 'payment', 'subscription', 'charge'])) {
            return "For billing inquiries, please contact our billing team at billing@example.com.";
        }

        // Default response
        return "I understand you need help with: \"$userMessage\". Could you provide a bit more detail?";
    }

    /**
     * Clear chat history.
     */
    public function clear()
    {
        ChatMessage::where('user_id', Auth::id())->delete();

        return redirect()->route('chat.index')
            ->with('status', 'Chat history has been cleared.');
    }
}
