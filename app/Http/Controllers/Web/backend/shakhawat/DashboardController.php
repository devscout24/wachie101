<?php

namespace App\Http\Controllers\Web\Backend\Shakhawat;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
}
