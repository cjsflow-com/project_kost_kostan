<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\PaymentMethodController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::get('/kost-profile', [AuthController::class, 'getProfileKost']);
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::prefix('rooms')->controller(App\Http\Controllers\RoomController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});

Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

Route::middleware('auth:customers')->prefix('reservations')->controller(App\Http\Controllers\Api\ReservationController::class)->group(function () {
    Route::post('/', 'store');
    Route::post('/{id}/approve', 'approve');
    Route::post('/{id}/cancel', 'cancel');
    Route::post('/{id}/upload-payment-proof', 'uploadPaymentProof');
    Route::post('/{id}/verify-payment', 'verifyPayment');
    Route::get('check-status', 'checkStatus');
    Route::get('my-reservations', 'myReservations');
});


