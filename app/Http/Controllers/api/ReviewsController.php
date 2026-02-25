<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Review;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;

class ReviewsController extends Controller
{
    use apiresponse;


    public function addReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'user_id'     => 'required|exists:users,id',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // Fetch the property first
        $property = Property::find($request->property_id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message' => 'Property not found.'
            ], 404);
        }

        // Create the review
        $review = $property->reviews()->create([
            'user_id' => $request->user_id,
            'rating'  => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'data'    => $review,
            'message' => 'Review added successfully'
        ], 201);
    }

    public function index()
    {
        $reviews = Review::with('property:id')->get();

        return response()->json([
            'success' => true,
            'data'    => $reviews,
            'message' => 'Reviews retrieved successfully'
        ]);
    }


}
