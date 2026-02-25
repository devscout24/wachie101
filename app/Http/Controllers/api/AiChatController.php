<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiChatController extends Controller
{
        public function send(Request $request)
        {
            $prompt = $request->input('prompt');

            if (!$prompt) {
                return response()->json([
                    'reply' => 'Prompt is required'
                ], 422);
            }

            $apiKey = env('OPENAI_API_KEY');

            if (!$apiKey) {
                return response()->json([
                    'reply' => 'API key not configured. Please set OPENAI_API_KEY in .env file'
                ], 500);
            }

            try {
                $response = Http::timeout(30)->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                ])->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'temperature' => 0.2,
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                $data = $response->json();

                return response()->json([
                    'reply' => $data['choices'][0]['message']['content'] ?? 'No reply'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'reply' => $e->getMessage()
                ], 500);
            }
        }
}
