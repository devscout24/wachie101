<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiService
{

    private function askForClarification(string $originalUserMessage){
           $clarificationPrompt = <<<EOT
            The previous analysis of the user's message was unclear or had low confidence.
            User Message: "{$originalUserMessage}"

            Please analyze this again. Since the intent is ambiguous, do NOT guess.
            Instead:
            1. Set "intent" to "clarify".
            2. In "missing_info", list what specific detail is needed (e.g., location, dates, number of guests).
            3. In "response_draft", write a polite, short question asking the user for that missing detail.
            Example: "I'd love to help with that! Could you clarify which city you are looking to stay in?"

            Return ONLY valid JSON.
            EOT;

            $response = $this->callGPT($clarificationPrompt, [], true);

            $data = json_decode($response, true);

            // 3. Return the AI's clarifying question to the frontend
            return response()->json([
                'status' => 'clarification_needed',
                'intent' => $data['intent'] ?? 'clarify',
                'message' => $data['response_draft'] ?? "Could you please provide more details?",
                'missing_fields' => $data['missing_info'] ?? []
            ]);
    }
    private function callGPT(string $prompt, array $history = [], bool $isRefinement = false)
    {
        
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            return response()->json([
                'reply' => 'API key not configured. Please set OPENAI_API_KEY in .env file'
            ], 500);
        }

        $defaultSystemPrompt = config('services.openai.system_prompt');

        $refinementSystemPrompt = config('services.openai.refinement_prompt');


        $messages[] = [
            'role' => 'system', 
            'content' => $isRefinement ? $refinementSystemPrompt : $defaultSystemPrompt
        ];
    
        if (!empty($history)) {
            foreach ($history as $msg) {
                // Basic validation to ensure history format is correct
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = $msg;
                }
            }
        }

        // Add the Current Prompt
        // If it's a refinement, the $prompt usually contains the specific error instruction.
        // If it's normal, $prompt is the user's message.
        $messages[] = [
            'role' => 'user', 
            'content' => $prompt
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post('https://api.openai.com/v1/chat/completions', [ 
                     'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                    'temperature' => $isRefinement ? 0.2 : 0.3, // Lower temp for corrections
                    'max_tokens' => 800, // Increased for detailed responses
                    'response_format' => ['type' => 'json_object'], // CRITICAL: Forces JSON
                ]);

             // Handle API Errors
            if ($response->failed()) {
                Log::error('OpenAI API Failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                
                // Return a safe fallback JSON string so json_decode doesn't crash later
                return json_encode([
                    'intent' => 'error',
                    'confidence' => 1.0,
                    'entities' => [],
                    'missing_info' => ['system_error'],
                    'response_draft' => "I'm having trouble connecting to the booking system right now. Please try again."
                ]);
            }

            $data = $response->json();

            $content = $data['choices'][0]['message']['content'] ?? '';
            if (empty($content)) {
                throw new \Exception("Empty content received from OpenAI");
            }

            // Safety Cleanup: Remove markdown code blocks if the AI ignores the JSON rule
            $content = preg_replace('/^```json\s*/', '', $content);
            $content = preg_replace('/^```\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
            $content = trim($content);

             return $content;

        }
         catch (\Exception $e) {
            Log::error('OpenAI Call Exception', ['message' => $e->getMessage()]);
            
            return json_encode([
                'intent' => 'error',
                'confidence' => 1.0,
                'entities' => [],
                'missing_info' => ['system_error'],
                'response_draft' => "An unexpected error occurred while processing your request."
            ]);
        }
    }
}
