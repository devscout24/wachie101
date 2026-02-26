<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
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

            $order = Order::find($session->metadata->order_id);

            if ($order && $order->status !== 'paid') {

                Payment::create([
                    'order_id' => $order->id,
                    'stripe_payment_intent' => $session->payment_intent,
                    'amount' => $session->amount_total / 100,
                    'currency' => $session->currency,
                    'status' => 'paid',
                ]);

                $order->update(['status' => 'paid']);
                $order->booking->update(['status' => 'paid']);
            }
        }

        return response()->json(['success' => true]);
    }
}
