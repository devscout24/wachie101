<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class MailCollectionController extends Controller
{
    
    public function store(Request $request){
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|unique:subscribers,mail',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            Subscriber::create([
                'mail' => $request->email,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store mail collection.', 'error' => $e->getMessage()], 500);
        }
        return response()->json(['message' => 'Mail collection stored successfully.', 'email' => $request->email]);
    }
}
