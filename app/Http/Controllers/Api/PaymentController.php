<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Payment;
use App\Models\ReservationStatusHistory;
use BaseResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PaymentController extends Controller
{
    //

    public function store(Request $request)
    {
        $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'amount' => 'required|integer|min:0',
        ]);

        $reservation = Reservation::findOrFail($request->reservation_id);

        $existingPayment = Payment::where('reservation_id', $reservation->id)
        ->whereIn('status', [Payment::STATUS_PENDING, Payment::STATUS_UPLOADED])
        ->exists();

        if ($existingPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Sudah ada pembayaran yang sedang diproses untuk reservasi ini.'
            ], 400);
        }


        DB::beginTransaction();
        try {

            $payment = Payment::create([
                'reservation_id' => $reservation->id,
                'payment_method_id' => $request->payment_method_id,
                'amount' => $request->amount,
                'status' => Payment::STATUS_PENDING,
            ]);


            $payment->update([
                'payment_code' => 'PAY-' . now()->format('Ymd') . '-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT),
            ]);

            DB::commit();
            return BaseResponse::success([
                'payment' => $payment
            ], 'Payment berhasil dibuat');

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Error create payment: '.$e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'request' => $request->all(),
                ]);
            return BaseResponse::error('Gagal membuat payment', 500);
        }
    }

    // Customer upload bukti pembayaran
    public function uploadPaymentProof(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if($reservation->status != Reservation::STATUS_WAITING_PAYMENT) {
            return BaseResponse::error('Bukti pembayaran tidak bisa diupload', 400);
        }

        /**
         * cek expired
         */
        if ($reservation->payment_due_at->isPast()) {

            $reservation->update([
                'status' => Reservation::STATUS_EXPIRED,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Batas pembayaran telah habis'
            ], 400);
        }

        $request->validate([
            'payment_proof' => 'required|image|max:2048',
        ]);

        // Simpan file bukti pembayaran
        $file = $request->file('payment_proof');
        $filename = 'payment_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('payment_proofs', $filename, 'public');

        $payment = Payment::where('reservation_id', $reservation->id)->firstOrFail();
        $payment->update([
            'payment_proof' => $path,
            'status' => Payment::STATUS_UPLOADED,
            'paid_at' => now(),
        ]);

        $reservation->update([
            'status' => Reservation::STATUS_UPLOADED,
        ]);

        // insert status history
        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => Payment::STATUS_UPLOADED,
            'title' => 'Bukti Pembayaran Diupload',
            'description' => 'Customer telah mengupload bukti pembayaran, menunggu konfirmasi admin',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bukti pembayaran ' . $payment->payment_code . ' berhasil diupload',
        ]);
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

            return BaseResponse::success($payments, 'Riwayat pembayaran berhasil diambil');
    }
}
