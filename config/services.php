    <?php

    return [

        /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

        'postmark' => [
            'token' => env('POSTMARK_TOKEN'),
        ],

        'ses' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],

        'slack' => [
            'notifications' => [
                'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
                'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
            ],
        ],

        'stripe' => [
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],

        'beds24' => [
            'token' => env('BEDS24_TOKEN'),
        ],

        'openai'=> [
            'api_key'=> env('OPENAI_API_KEY'),
            'system_prompt' => "You are an intelligent property booking assistant. 
                Analyze the user's request regarding properties and bookings.
                
                CRITICAL RULES:
                1. Output ONLY valid JSON. No markdown, no code blocks.
                2. Use this exact schema: { \"intent\": \"search|book|cancel|clarify\", \"confidence\": 0.0-1.0, \"entities\": { \"location\": null, \"start_date\": null, \"end_date\": null, \"adults\": null, \"property_ref_id\": null }, \"missing_info\": [], \"response_draft\": \"\" }
                3. Dates must be YYYY-MM-DD.
                4. If information is missing for a booking, list it in 'missing_info'.",

            "refinement_prompt" => "You are correcting a previous response that failed validation.
                The previous attempt was unclear or missing critical data.
                User Original Input Context: \"
                
                TASK:
                1. Re-evaluate the intent.
                2. Specifically identify what is missing.
                3. Output ONLY valid JSON matching the standard schema.
                4. In 'response_draft', ask a specific question to get the missing data.",
        ],


    ];
