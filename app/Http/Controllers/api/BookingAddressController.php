<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Property;
use App\Models\BookingAddress;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\apiresponse;


class BookingAddressController extends Controller
{
    use apiresponse;

    public function BookingAddressStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id'   => 'required|exists:bookings,id',
            'property_id'  => 'required|exists:properties,id',

            'first_name'   => 'required|string|max:255',
            'last_name'    => 'nullable|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'city'         => 'nullable|string|max:255',
            'country'      => 'nullable|string|max:255',
            'post'         => 'nullable|string|max:50',
            'message'      => 'nullable|string',
            'start_date'   => 'required|date',
            'end_date'     => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        // ✅ Get booking dates from bookings table
        $booking = Booking::findOrFail($request->booking_id);

        $bookingAddress = BookingAddress::create([
            'booking_id'  => $request->booking_id,
            'property_id' => $request->property_id,

            'first_name'  => $request->first_name,
            'last_name'   => $request->last_name,
            'email'       => $request->email,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'city'        => $request->city,
            'country'     => $request->country,
            'post'        => $request->post,
            'message'     => $request->message,

            // ✅ Auto-fill from booking
            'start_date'  => $booking->start_date,
            'end_date'    => $booking->end_date,
            'adults'      => $booking->adults,
            'children'    => $booking->children
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking address saved successfully',
            'data'    => $bookingAddress
        ], 201);
    }
}
