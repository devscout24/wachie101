<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login(Request $request){

        $request->validate([
            "email"=> "required|email|exists:users,email",
            "password"=> "required"
        ]);

         if( Auth::attempt(['email' => $request->email,'password'=> $request->password]) ){
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard', absolute: false));
        }
        return back()->with(
            'error', 'Invalid credentials provided.'
        );

    }

    

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

}
