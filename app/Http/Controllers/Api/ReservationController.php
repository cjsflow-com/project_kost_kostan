<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use App\Models\Room;
use App\Models\ReservationStatusHistory;
use App\Helpers\BaseResponse;

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
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
            'customer_email' => 'nullable|email',
            'customer_address' => 'nullable|string',
            'start_date' => 'required|date',
            'duration_month' => 'required|integer|min:1',
            'deposit' => 'required|numeric|min:0',
        ]);

        $room = Room::findOrFail($request->room_id);

        $total_price = ($room->price * $request->duration_month) + $request->deposit + $room->admin_fee;

        DB::beginTransaction();

        try{
            // Step 1: Create Reservation
            $reservation = Reservation::create([
                'user_id' => auth()->id(),
                'room_id' => $request->room_id,
                'customer_id' => null, // karena kita simpan data customer langsung di reservation, jadi tidak pakai relasi customer_id
                'start_date' => $request->start_date,
                'duration_month' => $request->duration_month,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'customer_address' => $request->customer_address,
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

            // Step 3: insert initial status history
            ReservationStatusHistory::create([
                'reservation_id' => $reservation->id,
                'status' => 'pending',
                'title' => 'Pending',
                'description' => 'Reservasi telah dibuat, menunggu konfirmasi admin',
            ]);

             DB::commit();
            return response()->json($reservation, 201);

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

        if($reservation->status != 'pending'){
            return response()->json(['message' => 'Reservasi tidak bisa diapprove'], 400);
        }

        $reservation->update([
            'status' => 'waiting_payment',
            'approved_at' => now(),
            'payment_due_at' => now()->addDays(2) // batas pembayaran 2 hari
        ]);

        // insert status history
        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => 'waiting_payment',
            'title' => 'Menunggu Pembayaran',
            'description' => 'Reservasi diterima, silakan lakukan pembayaran sebelum batas waktu',
        ]);

        return response()->json($reservation);
    }

    /**
     * Reject reservasi (Admin)
     */
    public function reject($id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => 'rejected',
            'title' => 'Ditolak',
            'description' => 'Reservasi ditolak admin',
        ]);

        return response()->json($reservation);
    }

     /**
     * Cancel reservasi (Customer)
     */
    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);

        $reservation->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => 'cancelled',
            'title' => 'Dibatalkan',
            'description' => 'Reservasi dibatalkan customer',
        ]);

        return response()->json($reservation);
    }
 /**
     * Cek status reservasi via code + phone (untuk user tanpa login)
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'reservation_code' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        $reservation = Reservation::with(['room', 'payments', 'statusHistories'])
            ->where('reservation_code', $request->reservation_code)
            ->where('customer_phone', $request->customer_phone)
            ->first();

        if(!$reservation){
            return response()->json(['message' => 'Reservasi tidak ditemukan'], 404);
        }

        return response()->json($reservation);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $reservation = Reservation::with(['room', 'payments', 'statusHistories'])->findOrFail($id);
        return BaseResponse::success($reservation);
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
