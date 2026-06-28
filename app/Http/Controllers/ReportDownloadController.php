<?php

namespace App\Http\Controllers;

use App\Exports\DynamicReportExport;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportDownloadController extends Controller
{
    public function download(Report $report): BinaryFileResponse
    {
        $reports = $this->reportDefinitions();

        abort_unless(isset($reports[$report->model_class]), 404);

        $definition = $reports[$report->model_class];

        $modelClass = $report->model_class;
        $columns = $definition['columns'];

        $fileName = now()->format('Ymd_His') . '-' . $definition['file_name'];

        return Excel::download(
            new DynamicReportExport($modelClass, $definition, $columns),
            $fileName,
            ExcelFormat::XLSX
        );
    }

    private function reportDefinitions(): array
    {
        return [
            Reservation::class => [
                'file_name' => 'laporan-reservasi.xlsx',
                'with' => ['customer', 'room'],
                'money_columns' => [
                    'room.price_per_month',
                    'admin_fee',
                ],
                'columns' => [
                    'id' => 'ID',
                    'customer.name' => 'Nama Pelanggan',

                    // Kalau di tabel rooms kolomnya title, pakai room.title
                    'room.title' => 'Nama Kamar',

                    'reservation_code' => 'Kode Pemesanan',
                    'start_date' => 'Tanggal Mulai',
                    'duration_month' => 'Durasi Bulan',
                    'room.price_per_month' => 'Harga Kamar',
                    'admin_fee' => 'Biaya Admin',
                ],
            ],

            Payment::class => [
                'file_name' => 'laporan-pembayaran.xlsx',
                'with' => ['reservation.customer', 'reservation.room'],
                'money_columns' => [
                    'amount',
                ],
                'columns' => [
                    'id' => 'ID',
                    'reservation.customer.name' => 'Nama Pelanggan',

                    // Kalau di tabel rooms kolomnya title, pakai reservation.room.title
                    'reservation.room.title' => 'Nama Kamar',

                    'amount' => 'Total Pembayaran',
                    'status' => 'Status',
                    'created_at' => 'Tanggal Pembayaran',
                ],
            ],

            Customer::class => [
                'file_name' => 'laporan-customer.xlsx',
                'columns' => [
                    'id' => 'ID',
                    'name' => 'Nama Pelanggan',
                    'email' => 'Email',
                    'phone' => 'Nomor HP',
                    'gender_label' => 'Jenis Kelamin',
                    'created_at' => 'Tanggal Daftar',
                ],
            ],

            Room::class => [
                'file_name' => 'laporan-kamar.xlsx',
                'money_columns' => [
                    'price_per_month',
                ],
                'columns' => [
                    'id' => 'ID',
                    'title' => 'Nama Kamar',
                    'price_per_month' => 'Harga Kamar per Bulan',
                    'room_size' => 'Ukuran Kamar',
                    'floor' => 'Lantai',
                    'capacity' => 'Kapasitas',
                    'status_name' => 'Status',
                    'created_at' => 'Tanggal Dibuat',
                ],
            ],

            User::class => [
                'file_name' => 'laporan-admin.xlsx',
                'columns' => [
                    'id' => 'ID',
                    'name' => 'Nama Admin',
                    'email' => 'Email',
                    'created_at' => 'Tanggal Dibuat',
                ],
            ],
        ];
    }
}