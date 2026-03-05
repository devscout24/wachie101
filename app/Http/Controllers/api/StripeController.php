<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

use Stripe\Stripe;

class StripeController extends Controller
{




    public function success(Request $request)
    {
        // Get session_id from query parameter
        $sessionId = $request->query('session_id');

        // Retrieve Stripe session to get metadata
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($sessionId);

            $bookingId  = $session->metadata->booking_id ?? null;
            $propertyId = $session->metadata->property_id ?? null;
            

            return redirect()->away(config('app.frontend_url'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid session ID'
            ], 400);
        }
    }


    public function verifyPayment(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
        ]);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        try {
            // Retrieve Stripe session
            $session = \Stripe\Checkout\Session::retrieve(
                $request->session_id,
                ['expand' => ['payment_intent']]
            );

            if ($session->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed'
                ], 400);
            }

            // Get booking ID from metadata
            $bookingId = $session->metadata->booking_id ?? null;

            if (!$bookingId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking reference not found'
                ], 400);
            }

            // Update booking payment status
            $booking = Booking::where('id', $bookingId)->first();
            if (!$booking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking not found'
                ], 404);
            }

            $booking->update([
                'payment_status'    => 'paid',
                'stripe_session_id' => $session->id,
            ]);

            // Return both booking_id and property_id
            return response()->json([
                'success' => true,
                'message' => 'Payment verified & booking confirmed',
                'booking_id' => $booking->id,
                'property_id' => $booking->property_id,
                'stripe_payment_status' => $session->payment_status



            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    
}
