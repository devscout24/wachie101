<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use log;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeController extends Controller
{



    public function checkout($bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

         $order = Order::create([
            'booking_id' => $booking->id,
            'order_number' => Str::uuid(),
            'amount' => $booking->total_price,
            'currency' => 'aud',
            'status' => 'pending',
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
                'order_id' => $order->id,
                'property_id' => $booking->property_id,
                'booking_id' => $booking->id,
                'user_id'    => $booking->user_id,
            ],
            'success_url' => url('/stripe-success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => url('/stripe-cancel'),
        ]);

        return response()->json([
            'success' => true,
            'checkout_url' => $session->url,
            'session_id' => $session->id,
            'booking_id' => $booking->id,
            'property_id' => $booking->property_id,
            'message' => 'Stripe checkout session created successfully',
        ]);
    }



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

            return response()->json([
                'success' => true,
                'booking_id' => $bookingId,
                'property_id' => $propertyId,
                'session_id' => $sessionId,
                'message' => 'Payment successful'
            ]);
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
