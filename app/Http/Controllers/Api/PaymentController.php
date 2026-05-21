<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\ReservationStatusHistory;
use BaseResponse;

class PaymentController extends Controller
{
    //
    // Customer upload bukti pembayaran
    public function uploadPaymentProof(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if($reservation->status != Reservation::STATUS['waiting_payment']){
            return BaseResponse::error('Bukti pembayaran tidak bisa diupload', 400);
        }

        /**
         * cek expired
         */
        if ($reservation->payment_due_at < now()) {

            $reservation->update([
                'status' => Reservation::STATUS['expired']
            ]);

            return response()->json([
                'message' => 'Batas pembayaran telah habis'
            ], 400);
        }

        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        // Simpan file bukti pembayaran
        $file = $request->file('payment_proof');
        $filename = 'payment_' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/payment_proofs', $filename);

        $payment = Payment::findOrFail($id);
        $payment->update([
            'payment_proof' => $filename,
            'status' => Payment::STATUS['uploaded'],
            'paid_at' => now(),
        ]);

        // insert status history
        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => Reservation::STATUS['payment_uploaded'],
            'title' => 'Bukti Pembayaran Diupload',
            'description' => 'Customer telah mengupload bukti pembayaran, menunggu konfirmasi admin',
        ]);

        return BaseResponse::success($reservation);
    }

    public function paymentHistory(Request $request)
    {
        /**
         * ambil customer login
         */
        $customer = auth('customer')->user();

        /**
         * ambil semua payment milik customer
         */
        $payments = Payment::with([
                'reservation',
                'paymentMethod'
            ])
            ->whereHas('reservation', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'message' => 'Daftar pembayaran customer',
            'data' => $payments
        ]);
    }
}
