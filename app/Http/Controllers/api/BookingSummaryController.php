<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class BookingSummaryController extends Controller
{
    public function store(Request $request)
    {

       Booking::create([
           'property_id' => $request->property_id,
           'user_id'     => $request->user_id,
           'start_date'  => $request->start_date,
           'end_date'    => $request->end_date,
           'total_price' => $request->total_price,
       ]);

       User::where('id', $request->user_id)->update([
           'phone' => $request->phone,
       ]);



    }
}
