<?php

namespace App\Http\Controllers\Web\backend\shakhawat;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
}
