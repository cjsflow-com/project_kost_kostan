<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Report;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportDownloadController extends Controller
{
    //

     public function download(Report $report): StreamedResponse
    {
        $reports = $this->reportDefinitions();

        abort_unless(isset($reports[$report->model_class]), 404);

        $definition = $reports[$report->model_class];

        $modelClass = $report->model_class;
        $columns = $definition['columns'];
        $fileName = now()->format('Ymd_His') . '-' . $definition['file_name'];

        return response()->streamDownload(function () use ($modelClass, $definition, $columns) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, array_values($columns));

            $modelClass::query()
                ->with($definition['with'] ?? [])
                ->chunk(500, function ($records) use ($handle, $columns) {
                    foreach ($records as $record) {
                        $row = [];

                        foreach (array_keys($columns) as $column) {
                            $value = data_get($record, $column);

                            if ($value instanceof CarbonInterface) {
                                $value = $value->format('Y-m-d');
                            }

                            $row[] = $value;
                        }

                        fputcsv($handle, $row);
                    }
                });

            fclose($handle);
        }, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function reportDefinitions(): array
    {
        return [
            Reservation::class => [
                'file_name' => 'laporan-reservasi.csv',
                'with' => ['customer', 'room'],
                'columns' => [
                    'id' => 'ID',
                    'customer.name' => 'Nama Pelanggan',
                    'room.name' => 'Nama Kamar',
                    'reservation_code' => 'Kode Pemesanan',
                    'start_date' => 'Tanggal Mulai',
                    'duration_month' => 'Durasi Bulan',
                    'room.price' => 'Harga Kamar',
                    'admin_fee' => 'Biaya Admin',
                ],
            ],

            Payment::class => [
                'file_name' => 'laporan-pembayaran.csv',
                'with' => ['reservation.customer', 'reservation.room'],
                'columns' => [
                    'id' => 'ID',
                    'reservation.customer.name' => 'Nama Pelanggan',
                    'reservation.room.name' => 'Nama Kamar',
                    'amount' => 'Total Pembayaran',
                    'status' => 'Status',
                    'created_at' => 'Tanggal Pembayaran',
                ],
            ],

            Customer::class => [
                'file_name' => 'laporan-customer.csv',
                'columns' => [
                    'id' => 'ID',
                    'name' => 'Nama Customer',
                    'email' => 'Email',
                    'phone' => 'Nomor HP',
                    'gender' => 'Jenis Kelamin',
                    'created_at' => 'Tanggal Daftar',
                ],
            ],
            Room::class => [
                'file_name' => 'laporan-kamar.csv',
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
                'file_name' => 'laporan-admin.csv',
                'columns' => [
                    'id' => 'ID',
                    'name' => 'Nama Admin',
                    'email' => 'Email',
                    'gender' => 'Jenis Kelamin',
                    'created_at' => 'Tanggal Dibuat',
                ],
            ],
        ];
    }

}
