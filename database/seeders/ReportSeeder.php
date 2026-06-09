<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        Report::updateOrCreate(
            ['model_class' => User::class],
            ['nama_model' => 'Laporan Admin']
        );

        Report::updateOrCreate(
            ['model_class' => Room::class],
            ['nama_model' => 'Laporan Kamar']
        );
    }
}
