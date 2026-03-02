<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {


        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');
        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sig,
            config('services.stripe.webhook_secret')
        );

        Log::info('stripe event data', [$event]);
        Log::debug('Subscribe type Stripe event type ' . $event->type);
        Log::info("Dir. => ".__FILE__." ==> handle" );
        Log::debug("event entered. => {$event}" );
        
        if ($event->type === 'checkout.session.completed') {

            $session = $event->data->object;

            $payment = Payment::where('booking_id', $session->metadata->booking_id)->first();
            
            if(!$payment) {
                $payment = Payment::create([
                    'booking_id' => $session->metadata->booking_id,
                    'amount' => $session->amount_total / 100,
                    'stripe_session_id' => $session->id,
                    'currency' => 'aud',
                    'status' => 'pending',
                ]);
            }

            $payment->update([
                'stripe_payment_intent' => $session->payment_intent,
                'amount_cents'=> $session->amount_total,
                'status' => 'succeeded',
            ]);

            $booking = Booking::find($session->metadata->booking_id);
            $booking->update([
                'payment_status' => 'paid',
            ]);

            
        }

        return response()->json(['success' => true]);
    }
}
