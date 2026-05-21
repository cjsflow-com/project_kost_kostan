<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\ReservationStatusHistory;
use App\Models\Customer;
use App\Models\Payment;
use BaseResponse as GlobalBaseResponse;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'start_date' => 'required|date',
            'duration_month' => 'required|integer|min:1',
            'deposit' => 'required|numeric|min:0',
            'customer_ktp_card' => 'nullable|string',
        ]);

        $room = Room::findOrFail($request->room_id);

        $customer = auth('customer')->user();

        if (!$customer) {
            $customer = Customer::create([
                'name' => $request->customer_name,
                'email' => $request->customer_email,
                'phone' => $request->customer_phone,
                'address' => $request->customer_address,
                'password' => bcrypt($request->customer_password), // default password, bisa diubah nanti
                'gender' => $request->customer_gender
            ]);
        }

        $total_price = ($room->price * $request->duration_month) + $request->deposit + $room->admin_fee;

        DB::beginTransaction();

        try{
            // Step 1: Create Reservation
            $reservation = Reservation::create([
                'user_id' => null, // karena reservasi bisa dibuat tanpa login, jadi user_id kita set null
                'customer_id' => $customer->id,
                'room_id' => $request->room_id,
                'start_date' => $request->start_date,
                'duration_month' => $request->duration_month,
                'customer_ktp_card' => $request->customer_ktp_card,
                'room_price' => $room->price_per_month,
                'admin_fee' => $room->admin_fee,
                'deposit' => $request->deposit,
                'total_price' => $total_price,
                'status' => Reservation::STATUS['pending'],
            ]);

            // Step 2: generate reservation  code
            $reservation->update([
                'reservation_code' => 'RK-' . now()->format('Ymd') . '-' . str_pad($reservation->id, 4, '0', STR_PAD_LEFT),
            ]);

            $payment = Payment::create([
                'reservation_id' => $reservation->id,
                'payment_method_id' => $request->payment_method_id, // nanti diupdate saat customer upload bukti pembayaran
                'amount' => $total_price,
                'status' => Payment::STATUS['pending'],
            ]);

            $payment->update([
                'payment_code' => 'PAY-' . now()->format('Ymd') . '-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT),
            ]);

            // Step 3: insert initial status history
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'status' => Reservation::STATUS['pending'],
                'title' => 'Pending',
                'description' => 'Reservasi telah dibuat, menunggu konfirmasi admin',
            ]);

             DB::commit();
            return GlobalBaseResponse::success([
            'reservation' => $reservation,
            'payment' => $payment
        ], 'Reservasi berhasil dibuat');
        

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat reservasi', 'error' => $e->getMessage()], 500);
        }
    }

     /**
     * Approve reservasi (Admin)
     */
    public function approve($id)
    {
        $reservation = Reservation::findOrFail($id);

        if($reservation->status != Reservation::STATUS['pending']){
            // return response()->json(['message' => 'Reservasi tidak bisa diapprove'], 400);
            return GlobalBaseResponse::error('Reservasi tidak bisa diapprove', 400);
        }

        $reservation->update([
            'status' => Reservation::STATUS['waiting_payment'],
            'approved_at' => now(),
            'payment_due_at' => now()->addDays(2) // batas pembayaran 2 hari
        ]);



        // insert status history
        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => Reservation::STATUS['waiting_payment'],
            'title' => 'Menunggu Pembayaran',
            'description' => 'Reservasi diterima, silakan lakukan pembayaran sebelum batas waktu',
        ]);

        return GlobalBaseResponse::success($reservation);
    }


    public function verifyPayment($id)
    {
        $reservation = Reservation::findOrFail($id);

        if($reservation->status != Reservation::STATUS['waiting_payment']){
            return GlobalBaseResponse::error('Reservasi tidak dalam status menunggu pembayaran', 400);
        }

        $payment = $reservation->payment;

        if(!$payment || $payment->status != Payment::STATUS['uploaded']){
            return GlobalBaseResponse::error('Bukti pembayaran belum diupload', 400);
        }

        $payment->update([
            'status' => Payment::STATUS['verified'],
            'verified_at' => now(),
        ]);

        $reservation->update([
            'status' => Reservation::STATUS['approved'],
            'approved_at' => now(),
        ]);

        // insert status history
        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => Reservation::STATUS['approved'],
            'title' => 'Pembayaran Diverifikasi',
            'description' => 'Pembayaran telah diverifikasi, reservasi disetujui',
        ]);

        return GlobalBaseResponse::success($reservation);
    }
    

    /**
     * Reject reservasi (Admin)
     */
    public function reject($id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'status' => Reservation::STATUS['rejected'],
            'rejected_at' => now(),
        ]);

        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => Reservation::STATUS['rejected'],
            'title' => 'Ditolak',
            'description' => 'Reservasi ditolak admin',
        ]);

        return GlobalBaseResponse::success($reservation);
    }

     /**
     * Cancel reservasi (Customer)
     */
    public function cancel($id)
    {
       $reservation = Reservation::findOrFail($id);

       if ($reservation->customer_id != auth('customer')->user()->id) {
            return GlobalBaseResponse::error('Unauthorized', 401);
        }

        $allowed_statuses = [Reservation::STATUS['pending'], Reservation::STATUS['waiting_payment']];

        if(!in_array($reservation->status, $allowed_statuses)){
            return GlobalBaseResponse::error('Reservasi tidak bisa dibatalkan', 400);
        }

        $reservation->update([
            'status' => Reservation::STATUS['cancelled'],
            'cancelled_at' => now(),
        ]);

        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => Reservation::STATUS['cancelled'],
            'title' => 'Dibatalkan',
            'description' => 'Reservasi dibatalkan oleh customer',
        ]);

        return GlobalBaseResponse::success($reservation);
    }
 /**
     * Cek status reservasi via code + phone (untuk user tanpa login)
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'reservation_code' => 'required|string',
        ]);

        $reservation = Reservation::with(['room', 'payments', 'statusHistories'])
            ->where('reservation_code', $request->reservation_code)
            ->first();

        if(!$reservation){
            return GlobalBaseResponse::error('Reservasi tidak ditemukan dengan kode tersebut', 404);
        }

        return GlobalBaseResponse::success($reservation);
    }

    public function myReservations()
    {
        $customer = auth('customer')->user();

        if(!$customer){
            return GlobalBaseResponse::error('Unauthorized', 401);
        }

        $reservations = Reservation::with(['room', 'payments', 'statusHistories'])
            ->where('customer_id', $customer->id)
            ->orederBy('created_at', 'desc')
            ->paginate(10);

        return GlobalBaseResponse::success($reservations);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $reservation = Reservation::with(['room', 'payments', 'statusHistories'])->findOrFail($id);
        return GlobalBaseResponse::success($reservation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
