<?php

namespace App\Http\Controllers\Web\backend\shakhawat;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class BackendController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $usersLastMonth = User::where('created_at', '>=', now()->subMonth())->count();
        if($usersCount > 0){
            $usersLastMonth = round(($usersLastMonth / $usersCount) * 100, 0);
        }else{
            $usersLastMonth = 0;
        }


        $totalOrders = Booking::count();
        $ordersLastMonth = Booking::where('created_at', '>=', now()->subMonth())->count();

        $convertion = Booking::whereIn('payment_status', ['confirmed', 'paid', 'completed'])->count();
        if($totalOrders > 0){
            $convertion = round(($convertion / $totalOrders) * 100, 2);
        }else{
            $convertion = 0;
        }

        $convertionLastMonth = Booking::whereIn('payment_status', ['confirmed', 'paid', 'completed'])->count();
        if($totalOrders > 0){
            $convertionLastMonth = round(($convertionLastMonth / $totalOrders) * 100, 2);
        }else{
            $convertionLastMonth = 0;
        }

        $totalPayment = Payment::where('status', 'succeeded')->sum('amount');
        $paymentLastMonth = Payment::where('status', 'succeeded')->where('created_at', '>=', now()->subMonth())->sum('amount');

        if($totalPayment > 0){
            $paymentLastMonth = round(($paymentLastMonth / $totalPayment) * 100, 2);
        }else{
            $paymentLastMonth = 0;
        }

        return view('backend.layouts.home.index', compact('usersCount', 'usersLastMonth', 
        'totalOrders', 'ordersLastMonth', 'convertion', 'convertionLastMonth', 'totalPayment', 'paymentLastMonth'));
    }
}
