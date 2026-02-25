<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Booking;
use App\Models\Payment;

class Order extends Model
{
        protected $fillable = [
        'booking_id','order_number',
        'amount','currency','status',
        'stripe_session_id'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
