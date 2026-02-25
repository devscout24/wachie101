<?php
namespace App\Traits;

use App\Models\User;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Hash;



trait apiresponse
{
    public function success($data, $message = null, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }

    public function error($data, $message = null, $code = 500)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ], $code);
    }

    public function generateOtp(User $user)
    {
        $otp = rand(1000, 9999);
        $user->otp = Hash::make($otp);
        $user->otp_created_at = now();

        

        $user->notify(new OtpNotification($otp));

        $user->save();

        return response()->json(['message' => 'OTP sent to your email!']);
    }
    
}
