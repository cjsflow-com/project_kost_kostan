<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\ReservationStatusHistory;

class CancelExpiredReservations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cance-expired-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel expired reservations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $reservations = Reservation::where('status', Reservation::STATUS['waiting_payment'])
        ->where('payment_due_at', '<', now())
        ->get();

    foreach ($reservations as $reservation) {

        $reservation->update([
            'status' => Reservation::STATUS['cancelled']
        ]);

        // simpan history
        ReservationStatusHistory::create([
            'reservation_id' => $reservation->id,
            'status' => Reservation::STATUS['cancelled'],
            'title' => 'Reservasi Dibatalkan',
            'description' => 'Reservasi otomatis dibatalkan karena melewati batas pembayaran'
        ]);
        $this->info('Expired reservations checked successfully');
    }

    return Command::SUCCESS;
    }
}
