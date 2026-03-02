<?php

namespace App\Http\Controllers\Web\backend\Raihan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NewsLetterController extends Controller
{
    public function index(Request $request){
        return view('backend.raihan.newsletter.index');
    }

    public function create(Request $request){
        return view('backend.raihan.newsletter.form');
    }

    public function store(Request $request){
        return redirect()->route('raihan.newsletter.index')->with('success', 'Newsletter created successfully.');
    }
}
