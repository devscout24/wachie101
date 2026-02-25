<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
  

    protected $fillable = [
        'order_id',
        'stripe_payment_intent',
        'amount',
        'currency',
        'status'
    ];
}
