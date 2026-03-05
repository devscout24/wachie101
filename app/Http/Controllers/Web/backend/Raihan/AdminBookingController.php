<?php

namespace App\Http\Controllers\Web\Backend\Raihan;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class AdminBookingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Booking::with(['property', 'user'])->select('bookings.*');

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('user_name', function ($row) {
                    return $row->user ? $row->user->name : '-';
                })
                ->addColumn('start_date', function ($row) {
                    return $row->start_date;
                })
                ->addColumn('end_date', function ($row) {
                    return $row->end_date;
                })
                ->addColumn('adults', function ($row) {
                    return $row->adults;
                })
                ->addColumn('children', function ($row) {
                    return $row->children;
                })
                ->addColumn('total_price', function ($row) {
                    return $row->total_price;
                })
                ->addColumn('description', function ($row) {
                    return Str::limit(strip_tags($row->description), 30);
                })
                ->addColumn('payment_status', function ($row) {
                    return $row->payment_status;
                })
                ->addColumn('payment_status', function ($row) {
                    $statuses = ['request', 'confirmed', 'paid', 'completed', 'refunded', 'cancelled'];
                    
                    $options = '';
                    foreach ($statuses as $status) {
                        $selected = ($row->payment_status === $status) ? 'selected' : '';
                        // Capitalize first letter for display
                        $label = ucfirst($status); 
                        $options .= "<option value='{$status}' {$selected}>{$label}</option>";
                    }

                    return '
                        <select class="form-control form-control-sm status-change" data-id="' . $row->id . '">
                            ' . $options . '
                        </select>
                    ';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <a href="' . route('admin.property.show', $row->id) . '" class="btn btn-sm btn-info">Show</a>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.booking.index');
    }

    public function show($id)
    {
        $booking = Booking::with(['property', 'user'])->findOrFail($id);
        return view('admin.booking.show', compact('booking'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:bookings,id',
            'payment_status' => 'required|in:request,confirmed,paid,completed,refunded,cancelled'
        ]);

        $booking = Booking::find($request->id);
        $booking->payment_status = $request->payment_status;
        
        if ($booking->save()) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false], 500);
    }
}
