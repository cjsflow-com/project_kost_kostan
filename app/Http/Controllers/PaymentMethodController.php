<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;
use BaseResponse;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    //

    public function index()
    {
        $payment_methods = PaymentMethod::active()->get();
        return BaseResponse::success(
            PaymentMethodResource::collection($payment_methods),
            'Daftar metode pembayaran berhasil diambil'
        );
    }

    public function show($reservation_id)
    {
        $payment_method = PaymentMethod::whereHas('payments', function ($query) use ($reservation_id) {
            $query->where('reservation_id', $reservation_id);
        })->first();

        if (!$payment_method) {
            return BaseResponse::error('Metode pembayaran untuk reservasi ini tidak ditemukan', 404);
        }

        return BaseResponse::success(
            new PaymentMethodResource($payment_method),
            'Metode pembayaran untuk reservasi berhasil diambil'
        );
    }
}
