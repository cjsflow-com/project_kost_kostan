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
}
