<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;
use App\Traits\apiresponse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    use apiresponse;



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'adults'      => 'required|integer|min:1',
            'children'    => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation failed', 422);
        }

        $property = Property::find($request->property_id);

        if (!$property) {
            return response()->json([
                'success' => false,
                'message'=> 'property not found'
            ]);
        }

        // 🗓 Calculate nights
        
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $nights    = $startDate->diffInDays($endDate);

        if ($nights < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum 1 night required.'
            ], 422);
        }

        $token = config('services.beds24.token');

        if (!$token) {
            return response()->json([
                'error' => 'Beds24 token not found'
            ]);
        }
        
        $bookingData = [
            [
                "roomId" => $property->room_ref_id,
                "status" => "request",  //confirmed
                "arrival" => $request->start_date,
                "departure" => $request->end_date,
                "numAdult" => $request->adults,
                "numChild" => $request->children,
                "title" => "Mr",
                "firstName" => $request->first_name,
                "lastName" => $request->last_name,
                "email" => $request->email,
                "mobile" => $request->mobile,
                "address" => $request->address,
                "city" => $request->city,
                "state" => $request->state ?? "Victoria",
                "postcode" => $request->postcode,
                "country" => $request->country,
            ]
        ];

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'token'  => $token,
            'Content-Type' => 'application/json',
        ])->post('https://beds24.com/api/v2/bookings', $bookingData);

        $data = $response->json();
        $firstItem = $data[0];

        // 💰 Price calculations
        $pricePerNight = $property->price;
        $priceTotal    = $pricePerNight * $nights;
        $cleaningFee   = $property->cleaning_fee;
        $bookingFee    = round($priceTotal * ($property->booking_fee ?? 0.045), 2);
        $total         = $priceTotal + $cleaningFee + $bookingFee;

        

        // ✅ STORE FULL BOOKING + PRICES
        $booking = Booking::create([
            'property_id'      => $property->id,
            'booking_ref_id' => $firstItem['new']['id'],
            'user_id'          => auth()->id(),
            'start_date'       => $request->start_date,
            'end_date'         => $request->end_date,
            'adults'           => $request->adults,
            'children'         => $request->children ?? 0,

            // 🔑 PRICE DATA (VERY IMPORTANT)
            'nights'           => $nights,
            'price_per_night'  => $pricePerNight,
            'price_total'      => $priceTotal,
            'cleaning_fee'     => $cleaningFee,
            'booking_fee'      => $bookingFee,
            'total_price'      => $total,

            'payment_status'   => 'pending',
        ]);

        // ✅ API RESPONSE (frontend friendly)
        return response()->json([
            'success' => $firstItem['success'],
            'data' => $firstItem,
            'messsage' => 'booking created successfully'
        ], 201);
    }





    /**
     * Get all bookings for admin
     */
    public function getAll()
    {
        $bookings = Booking::with('property:id,title')->get();

        return response()->json([
            'success' => true,
            'data'    => $bookings
        ]);
    }

    

   
    public function summary()
    {
        // Check login
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = auth()->user();

        // Fetch bookings for only this user
        $bookings = Booking::where('user_id', $user->id)
            ->with('property:id,title,price,image')
            ->get();

        $totalSpent = $bookings->sum('total_price');

        return response()->json([
            'success' => true,
            'data' => [
                'total_bookings' => $bookings->count(),
                'total_spent'    => $totalSpent,
                'bookings'       => $bookings
            ]
        ]);
    }

    
}
