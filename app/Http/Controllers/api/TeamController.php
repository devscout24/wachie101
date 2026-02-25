<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    public function getAll()
    {
        $teams = Team::where('is_active', 1)->get()->map(function ($team) {

            // ✅ Ensure full path with correct filename and extension
            $imagePath = $team->image;

            if ($imagePath && file_exists(public_path($imagePath))) {
                // Encode spaces for URL
                $imageUrl = url(str_replace(' ', '%20', $imagePath));
            } else {
                $imageUrl = url('uploads/teams/default.png'); // fallback
            }

            return [
                'id'          => $team->id,
                'name'        => $team->name,
                'designation' => $team->designation,
                'bio'         => strip_tags($team->bio),
                'is_active'   => $team->is_active,
                'image'       => $imageUrl,
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $teams,
            'message' => 'Teams retrieved successfully'
        ], 200);
    }



    public function getOne($id)
    {
        $team = Team::find($id);

        if (!$team) {
            return response()->json([
                'success' => false,
                'message' => 'Team member not found.'
            ], 404);
        }

        // ✅ IMAGE FIX
        if ($team->image && file_exists(public_path($team->image))) {
            $imageUrl = url(str_replace(' ', '%20', $team->image)); // encode spaces
        } else {
            $imageUrl = url('uploads/teams/default.png'); // fallback
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id'          => $team->id,
                'name'        => $team->name,
                'designation' => $team->designation,
                'bio'         => strip_tags($team->bio),
                'is_active'   => $team->is_active,
                'image'       => $imageUrl,
            ]
        ], 200);
    }
}
