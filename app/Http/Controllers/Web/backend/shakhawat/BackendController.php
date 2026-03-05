<?php

namespace App\Http\Controllers\Web\backend\shakhawat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BackendController extends Controller
{
    public function index()
    {
        return view('backend.layouts.home.index');
    }
}
