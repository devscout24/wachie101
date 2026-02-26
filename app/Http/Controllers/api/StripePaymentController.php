<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Order;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Str;
use App\Models\Payment;

class StripePaymentController extends Controller
{


    public function checkoutBooking(Request $request)
    {
        $booking = Booking::where('id', $request->booking_id)
            ->where('status', 'request')
            ->firstOrFail();

        // Create Order
        $order = Order::create([
            'booking_id' => $booking->id,
            'order_number' => Str::uuid(),
            'amount' => $booking->total_price,
            'currency' => 'aud',
            'status' => 'pending',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::create([
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Booking #' . $booking->id,
                    ],
                    'unit_amount' => $order->amount * 100,
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'order_id' => $order->id,
                'booking_id' => $booking->id,
            ],
            'success_url' => config('app.url') . '/payment-success',
            'cancel_url' => config('app.url') . '/payment-cancel',
        ]);

        $order->update(['stripe_session_id' => $session->id]);

        return response()->json([
            'checkout_url' => $session->url,
        ]);
    }

    

    
}
