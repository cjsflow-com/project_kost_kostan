<?php

namespace App\Exports;

use Carbon\CarbonInterface;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class DynamicReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithEvents
{
    public function __construct(
        private string $modelClass,
        private array $definition,
        private array $columns
    ) {}

    public function query()
    {
        return $this->modelClass::query()
            ->with($this->definition['with'] ?? []);
    }

    public function headings(): array
    {
        return array_values($this->columns);
    }

    public function map($record): array
    {
        $row = [];

        foreach (array_keys($this->columns) as $column) {
            $value = data_get($record, $column);

            if ($value instanceof CarbonInterface) {
                $value = $value->format('Y-m-d');
            }

            if (
                in_array($column, $this->definition['money_columns'] ?? [], true)
                && is_numeric($value)
            ) {
                $value = 'Rp ' . number_format($value, 0, ',', '.');
            }

            $row[] = $value;
        }

        return $row;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                $tableRange = "A1:{$highestColumn}{$highestRow}";
                $headerRange = "A1:{$highestColumn}1";

                // Hilangkan gridline Excel di area kosong bawah
                $sheet->setShowGridlines(false);

                // Freeze header
                $sheet->freezePane('A2');

                // Tinggi header
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Style header manual
                $sheet->getStyle($headerRange)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => [
                            'argb' => 'FFFFFFFF',
                        ],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'argb' => 'FF0F5B2B',
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Style isi tabel
                $sheet->getStyle($tableRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => [
                                'argb' => 'FFD9D9D9',
                            ],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Kalau minimal ada data, buat Excel Table asli
                if ($highestRow >= 2) {
                    $tableName = 'ReportTable_' . uniqid();

                    $table = new \PhpOffice\PhpSpreadsheet\Worksheet\Table(
                        $tableRange,
                        $tableName
                    );

                    $tableStyle = new \PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle();
                    $tableStyle->setTheme(
                        \PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle::TABLE_STYLE_MEDIUM4
                    );
                    $tableStyle->setShowRowStripes(true);

                    $table->setStyle($tableStyle);

                    $sheet->addTable($table);
                }
            },
        ];
    }
}