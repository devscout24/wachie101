<?php

use App\Http\Controllers\api\AiChatController;
use App\Http\Controllers\api\AmenityController;
use App\Http\Controllers\api\BookingAddressController;
use App\Http\Controllers\api\BookingController;
use App\Http\Controllers\api\MailCollectionController;
use App\Http\Controllers\api\PropertyController;
use App\Http\Controllers\api\ReviewsController;
use App\Http\Controllers\api\StripeController;
use App\Http\Controllers\api\StripeWebhookController;
use App\Http\Controllers\api\TeamController;
use App\Http\Controllers\api\UserAuthBDController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Login & Register

Route::controller(UserAuthBDController::class)->group(function () {
    // Authentication
    Route::post('user/login', 'login');
    Route::post('customer/register', 'customerRegister');
    Route::post('owner/register', 'ownerRegister');
    Route::get('user/logout', 'logout');
    Route::post('user/update', 'updateProfile');
    Route::post('change/password', 'changePassword');

    // Password Reset
    Route::post('verify-otp-password', 'verifyOtp');
    Route::post('resend-otp', 'resendOtp');
    Route::post('forget-password', 'forgetPassword');
    Route::post('reset-password', 'resetPassword');

    Route::get('profile',  'getProfile');

    // User Info
    Route::get('/login/user',  'showUser')->middleware('auth:api');

    // Social login
    Route::post('social-login/google', 'googleLogin');
    Route::post('social-login/apple', 'appleLogin');
});





// admin routes can be added here
// Amenity Routes
Route::get('amenity/index', [AmenityController::class, 'index']);
Route::get('amenity/getone/{id}', [AmenityController::class, 'getone']);


// Property Routes
Route::get('properties', [PropertyController::class, 'index']);
Route::get('property/getone/{id}', [PropertyController::class, 'getone']);
Route::get('property/blocked-dates/{id}', [PropertyController::class, 'availableDates']);


// Review Routes
Route::get('review/property/{property_id}', [ReviewsController::class, 'getPropertyReviews']);
Route::post('review/add', [ReviewsController::class, 'addReview']);
Route::get('review/index', [ReviewsController::class, 'index']);


// Booking Routes
Route::group(['middleware'=> 'auth:api'], function () {
    Route::post('booking/store', [BookingController::class, 'store']);
});

Route::get('booking/all', [BookingController::class, 'getAll']);
Route::post('booking/only', [BookingController::class, 'onlybooking']);
Route::post('booking/total-expense', [BookingController::class, 'totalAmount']);




// team routes can be added here
Route::get('team/all', [TeamController::class, 'getAll']);
Route::get('team/{id}', [TeamController::class, 'getOne']);


// Stripe Payment Routes
Route::get('/stripe-success', [StripeController::class, 'success']);
Route::get('/stripe-cancel', [StripeController::class, 'cancel']);

// Route::post('stripe/verify-payment', [StripeController::class, 'verifyPayment']);


// booking address routes can be added here
Route::post('booking/address/', [BookingAddressController::class, 'BookingAddressStore']);


// OpenAI Chat Routes
Route::post('/openai-chat/send', [AiChatController::class, 'send'])->name('openai.send');

Route::get('test', function () {
    
return response()->json(['message' => 'API is working']);
})->middleware('auth:api');




Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);


Route::post('/subscriber-store', [MailCollectionController::class, 'store']);