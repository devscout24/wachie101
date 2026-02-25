<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{

    public function create()
    {
        return view('admin.reviews.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|numeric|min:0|max:5',
            'comment' => 'nullable|string',
        ]);

        Review::create([
            'property_id' => $request->property_id,
            'user_id' => $request->user_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => 1,
        ]);
        return redirect()->back()->with('success', 'Review submitted successfully.');
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $reviews = Review::with(['property', 'user'])->latest()->get();
            return datatables()->of($reviews)
                ->addIndexColumn()
                ->addColumn('property', function ($review) {
                    return $review->property ? $review->property->name : 'N/A';
                })
                ->addColumn('user', function ($review) {
                    return $review->user ? $review->user->name : 'N/A';
                })
                ->addColumn('rating', function ($review) {
                    return number_format($review->rating, 1);
                })
                ->addColumn('comment', function ($review) {
                    return $review->comment;
                })
                ->addColumn('is_approved', function ($review) {
                    return $review->is_approved ? 'Approved' : 'Pending';
                })
                ->rawColumns(['property', 'user', 'rating', 'comment', 'is_approved'])
                ->make(true);
        }
        return view('admin.reviews.index');
    }
}
