<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;


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

        // Greeting responses
        if (str_contains($message, 'hello') || str_contains($message, 'hi') || str_contains($message, 'hey')) {
            return "Hello! I'm here to help you with any questions or issues you might have. How can I assist you today?";
        }

        // Account related
        if (str_contains($message, 'account') || str_contains($message, 'profile') || str_contains($message, 'login')) {
            return "I can help you with account-related issues. Are you having trouble logging in, updating your profile, or something else account-related?";
        }

        // Billing related
        if (str_contains($message, 'billing') || str_contains($message, 'payment') || str_contains($message, 'subscription') || str_contains($message, 'charge')) {
            return "For billing inquiries, I can provide general information. If you need specific account details or have payment issues, please contact our billing team directly at billing@example.com";
        }

        // Technical issues
        if (str_contains($message, 'bug') || str_contains($message, 'error') || str_contains($message, 'broken') || str_contains($message, 'not working')) {
            return "I'm sorry to hear you're experiencing technical issues. Can you provide more details about what's not working? This will help me better assist you or direct you to the right resources.";
        }

        // Password related
        if (str_contains($message, 'password') || str_contains($message, 'forgot') || str_contains($message, 'reset')) {
            return "To reset your password, you can use the 'Forgot Password' link on the login page. If you're still having trouble, please let me know and I can guide you through the process.";
        }

        // Feature requests
        if (str_contains($message, 'feature') || str_contains($message, 'suggestion') || str_contains($message, 'improve')) {
            return "Thank you for your feedback! Feature suggestions are valuable to us. While I can't implement features directly, I encourage you to share your ideas with our development team.";
        }

        // Goodbye
        if (str_contains($message, 'bye') || str_contains($message, 'goodbye') || str_contains($message, 'thanks')) {
            return "You're welcome! If you have any other questions, feel free to ask. Have a great day!";
        }

        // Default response
        return "I understand you need help with: \"" . $userMessage . "\". Could you provide a bit more detail so I can better assist you? You can also contact our support team directly if you need immediate assistance.";
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
