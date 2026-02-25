<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\apiresponse;

class AmenityController extends Controller
{
    use apiresponse;
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|string',
        ]);

        $amenity = new Amenity();
        $amenity->name = $request->input('name');
        if ($request->has('image')) {
            $amenity->image = $request->input('image');
        }
        $amenity->save();

        return response()->json([
            'message' => 'Amenity created successfully',
            'data' => $amenity
        ], 201);
    }

    public function index()
    {
        $amenities = Amenity::select('id', 'name', 'image')->get();

        return response()->json([
            'data' => $amenities
        ], 200);
    }

    public function getone($id)
    {
        $amenity = Amenity::find($id);

        if (!$amenity) {
            return response()->json([
                'message' => 'Amenity not found'
            ], 404);
        }

        return response()->json([
            'data' => $amenity
        ], 200);
    }
}
