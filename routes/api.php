<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\RoomController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware('auth:customer')->post('/logout', [AuthController::class, 'logout']);
Route::get('/kost-profile', [AuthController::class, 'getProfileKost']);
Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

Route::prefix('rooms')->controller(RoomController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');
});

Route::get('/payment-methods', [PaymentMethodController::class, 'index']);

Route::middleware('auth:customer')->prefix('reservations')->controller(ReservationController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/create', 'store');
    Route::post('/{id}/approve', 'approve');
    Route::post('/{id}/cancel', 'cancel');
    Route::post('/{id}/verify-payment', 'verifyPayment');
    Route::get('check-status', 'checkStatus');
    Route::get('my-reservations', 'myReservations');
    Route::get('/{reservationId}/status-history', 'getStatusHistory');
    Route::get('/{id}/show', 'show');

});



Route::middleware('auth:customer')->prefix('payments')->controller(PaymentController::class)->group(function () {
    Route::post('/create', 'store');
    Route::get('/history', 'paymentHistory');
    Route::post('/{id}/upload-proof', 'uploadPaymentProof');
});


