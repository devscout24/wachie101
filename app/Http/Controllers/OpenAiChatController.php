<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiChatController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
        $conversationHistory = $request->input('history', []); // Optional: pass previous context

        // 1. Initial Call to GPT
        $gptResponse = $this->callGPT($userMessage, $conversationHistory);
        $data = json_decode($gptResponse, true);

        // 2. VALIDATION LAYER (Your If Conditions)
        
        // Check Confidence
        if (($data['confidence'] ?? 0) < 0.6) {
            return $this->askForClarification($userMessage);
        }

        // Check Missing Critical Info for Booking
        if ($data['intent'] === 'create_booking') {
            $missing = $data['missing_info'] ?? [];
            
            // Custom Logic: Even if GPT didn't catch it, verify dates exist in DB context
            if (empty($data['entities']['start_date']) || empty($data['entities']['end_date'])) {
                if (!in_array('start_date', $missing)) $missing[] = 'start_date';
                if (!in_array('end_date', $missing)) $missing[] = 'end_date';
            }

            if (!empty($missing)) {
                // 3. RE-PROMPT STRATEGY
                // Tell GPT exactly what is missing so it generates a specific question
                $missingList = implode(', ', $missing);
                $refinementPrompt = "The user wants to book, but we are missing: {$missingList}. Please generate a polite question asking specifically for these details. Update the JSON 'missing_info' field and 'response_draft'.";
                
                $refinedResponse = $this->callGPT($refinementPrompt, [], true); // true = refinement mode
                return response()->json(json_decode($refinedResponse, true));
            }

            // 4. EXECUTE BUSINESS LOGIC (If validation passes)
            return $this->processBookingLogic($data['entities']);
        }

        if ($data['intent'] === 'search_property') {
            return $this->processSearchLogic($data['entities']);
        }

        // Default fallback
        return response()->json(['response' => $data['response_draft']]);
    }

    private function processBookingLogic($entities)
    {
        // Verify Property Exists
        $property = Property::where('property_ref_id', $entities['property_ref_id'])->first();
        
        if (!$property) {
            // If property ID is invalid, trigger another re-prompt loop
            return $this->triggerReprompt("Invalid property ID provided. Ask the user to select a valid property.");
        }

        // Check Availability (Custom Laravel Logic)
        $isAvailable = $this->checkAvailability($property->id, $entities['start_date'], $entities['end_date']);

        if (!$isAvailable) {
            return response()->json([
                'response' => "Sorry, this property is not available from {$entities['start_date']} to {$entities['end_date']}. Would you like to try different dates?"
            ]);
        }

        // Calculate Price (Using your schema fields)
        $start = Carbon::parse($entities['start_date']);
        $end = Carbon::parse($entities['end_date']);
        $nights = $start->diffInDays($end);
        
        $totalPrice = ($property->price * $nights) + $property->cleaning_fee + $property->booking_fee;

        return response()->json([
            'action' => 'confirm_booking',
            'summary' => [
                'property' => $property->title,
                'nights' => $nights,
                'total_price' => $totalPrice,
                'breakdown' => [
                    'base' => $property->price * $nights,
                    'cleaning' => $property->cleaning_fee,
                    'fee' => $property->booking_fee
                ]
            ],
            'response' => "Great! The total for {$nights} nights at {$property->title} is \${$totalPrice}. Shall I proceed with the payment?"
        ]);
    }

    
}
