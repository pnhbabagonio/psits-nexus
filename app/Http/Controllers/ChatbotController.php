<?php

namespace App\Http\Controllers;

use App\Models\ChatbotSession;
use App\Models\ChatbotMessage;
use App\Models\ChatbotIntent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function processMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'session_id' => 'nullable|string',
            'user_id' => 'nullable|exists:users,user_id'
        ]);

        // Get or create session
        $session = $this->getOrCreateSession($validated['session_id'], $validated['user_id'] ?? null);
        
        // Process message and get intent
        $intent = $this->detectIntent($validated['message']);
        
        // Store user message
        $userMessage = ChatbotMessage::create([
            'conversation_id' => $session->conversations()->latest()->first()?->conversation_id,
            'session_id' => $session->session_id,
            'message_type' => 'text',
            'message_content' => $validated['message'],
            'intent_id' => $intent->intent_id,
            'is_user_message' => true
        ]);

        // Generate response
        $response = $this->generateResponse($intent, $validated['message']);
        
        // Store bot response
        $botMessage = ChatbotMessage::create([
            'conversation_id' => $session->conversations()->latest()->first()?->conversation_id,
            'session_id' => $session->session_id,
            'message_type' => 'text',
            'message_content' => $response,
            'intent_id' => $intent->intent_id,
            'is_user_message' => false
        ]);

        return response()->json([
            'session_id' => $session->session_id,
            'response' => $response,
            'intent' => $intent->intent_name
        ]);
    }

    private function getOrCreateSession($sessionId = null, $userId = null)
    {
        if ($sessionId) {
            $session = ChatbotSession::find($sessionId);
            if ($session) {
                $session->update(['last_activity' => now()]);
                return $session;
            }
        }

        return ChatbotSession::create([
            'session_id' => Str::uuid(),
            'user_id' => $userId,
            'user_platform' => request()->header('User-Agent'),
            'ip_address' => request()->ip()
        ]);
    }

    private function detectIntent($message)
    {
        $message = strtolower(trim($message));
        
        // Simple intent detection - you might want to use NLP here
        $intents = ChatbotIntent::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->get();

        foreach ($intents as $intent) {
            foreach ($intent->training_phrases as $phrase) {
                if (str_contains($message, strtolower($phrase))) {
                    return $intent;
                }
            }
        }

        // Fallback to default intent
        return ChatbotIntent::where('intent_name', 'greeting')->first();
    }

    private function generateResponse($intent, $userMessage)
    {
        $responses = $intent->responses;
        return $responses[array_rand($responses)] ?? 'I apologize, but I don\'t have a response for that.';
    }
}