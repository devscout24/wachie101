<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAddress extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'post',
        'message',
        'property_id',
        'booking_id',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
