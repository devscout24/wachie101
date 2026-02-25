<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiChatController extends Controller
{
    public function index()
    {
        return view('admin.aichat');
    }

    public function send(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string'
        ]);

        $prompt = $request->prompt;
        $apiKey = env('OPENAI_API_KEY');

        if (!$apiKey) {
            return response()->json([
                'reply' => 'API key not configured. Please set OPENAI_API_KEY in .env file'
            ], 500);
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 200,
                ]);

            if ($response->failed()) {
                return response()->json([
                    'reply' => 'API Error: ' . $response->body()
                ], $response->status());
            }

            $data = $response->json();

            $reply = $data['choices'][0]['message']['content'] ?? 'No reply received';

            return response()->json([
                'reply' => trim($reply)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'reply' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
