<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'property_id',
        'user_id',
        'start_date',
        'end_date',

        // Guests
        'adults',
        'children',

        // Customer Info
        'first_name',
        'last_name',
        'email',
        'phone',

        // Address
        'address',
        'city',
        'country',
        'postal_code',

        'comments',

        // Price
        'nights',
        'price_per_night',
        'price_total',
        'cleaning_fee',
        'booking_fee',
        'total_price',
        'payment_status',
        'stripe_session_id',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
