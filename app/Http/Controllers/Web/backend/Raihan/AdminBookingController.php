<?php

namespace App\Http\Controllers\Web\Backend\Raihan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Booking::with(['property'])->select('bookings.*');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('multiple_image', function ($row) {
                    if ($row->property->images->count() > 0) {
                        $img = asset($row->property->images->first()->image);
                        return '<img src="' . $img . '" style="width:80px;height:40px;object-fit:cover;">';
                    }
                    return 'No Image';
                })
                ->addColumn('amenity_id', function ($row) {
                    return $row->amenities->pluck('name')->implode(', ');
                })
                ->addColumn('price', function ($row) {
                    return '$' . number_format($row->price, 2);
                })
                ->addColumn('cleaning_fee', function ($row) {
                    return '$' . number_format($row->cleaning_fee, 2);
                })
                ->addColumn('description', function ($row) {
                    return Str::limit(strip_tags($row->description), 30);
                })
                ->addColumn('status', function ($row) {
                    return $row->status ? 'Active' : 'Inactive';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('admin.property.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <a href="' . route('admin.property.show', $row->id) . '" class="btn btn-sm btn-info">Show</a>
                        <button data-id="' . $row->id . '" class="btn btn-sm btn-danger btn-delete">Delete</button>
                    ';
                })
                ->rawColumns(['multiple_image', 'action'])
                ->make(true);
        }

        return view('admin.property.index');
    }
}
