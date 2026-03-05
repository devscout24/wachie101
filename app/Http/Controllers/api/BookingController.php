<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;

class BookingController extends Controller
{
    use apiresponse;

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:properties,id',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after:start_date',
            'adults'      => 'required|integer|min:1',
            'children'    => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors(), 'Validation failed', 422);
        }

        $property = Property::find($request->id);

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

        
        
        $bookingData = [
            [
                // "roomId" => $property->room_ref_id,
                "status" => "request",  //confirmed
                // "status" => "confirmed", 
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



        // 💰 Price calculations
        $pricePerNight = $property->price;
        $priceTotal    = $pricePerNight * $nights;
        $cleaningFee   = $property->cleaning_fee;
        $bookingFee    = round($priceTotal * ($property->booking_fee/100 ?? 0.045), 2);
        $total         = $priceTotal + $cleaningFee + $bookingFee;

        

        // ✅ STORE FULL BOOKING + PRICES
        $booking = Booking::create([
            'property_id'      => $property->id,
            // 'booking_ref_id' => $firstItem['new']['id'] ?? null,
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

            'payment_status'   => 'request',
        ]);

        
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'aud',
                    'unit_amount' => (int) round($booking->total_price * 100),
                    'product_data' => [
                        'name' => 'Booking #' . $booking->id,
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'property_id' => $booking->property_id,
                'booking_id' => $booking->id,
                'user_id'    => $booking->user_id,
            ],
            'success_url' => url('/stripe-success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => url('/stripe-cancel'),
        ]);

        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_price,
            'stripe_session_id' => $session->id,
            'currency' => 'aud',
            'status' => 'pending',
        ]);

        $booking->update(['stripe_session_id' => $session->id]);

        return response()->json([
            'success' => true,
            'checkout_url' => $session->url,
            'session_id' => $session->id,
            'booking_id' => $booking->id,
            'property_id' => $booking->property_id,
            'message' => 'Stripe checkout session created successfully',
        ]);

        
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

    public function totalAmount(Request $request){
        $property = Property::find($request->id);    
        if (!$property) {
            return response()->json([
                'success' => false,
                'message'=> 'property not found'
            ]);
        }

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $nights    = $startDate->diffInDays($endDate);

        if ($nights < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum 1 night required.'
            ], 422);
        }

         // 💰 Price calculations
        $pricePerNight = $property->price;
        $priceTotal    = $pricePerNight * $nights;
        $cleaningFee   = $property->cleaning_fee;
        $bookingFee    = round($priceTotal * ($property->booking_fee/100 ?? 0.045), 2);
        $total         = $priceTotal + $cleaningFee + $bookingFee;

        return response()->json([
            'success' => true,
            'data' => [
                'price_per_night' => $pricePerNight,
                'price_total'     => $priceTotal,
                'cleaning_fee'    => $cleaningFee,
                'booking_fee'     => $bookingFee,
                'total_price'     => $total
            ]
        ]);
    }
    
}
