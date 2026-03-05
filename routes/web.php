<?php

use Illuminate\Support\Facades\Route;



Route::get('/', function () {return redirect()->route('login');});



require __DIR__.'/shakhawat_backend.php';
require __DIR__.'/abdullah_backend.php';
require __DIR__.'/auth.php';
require __DIR__.'/api.php';