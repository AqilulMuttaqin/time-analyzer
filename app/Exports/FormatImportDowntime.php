<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormatImportDowntime implements FromCollection, WithTitle, WithStyles, WithEvents
{
    public function collection()
    {
        return collect([
            ['Tanggal', 'Week', 'Shift', 'Sub Golongan', 'Downtime Code', 'Detail Problem', 'Minute', 'Man Hours']
        ]);
    }

    public function title(): string
    {
        return "Format Downtime Import";
    }

    public function styles(Worksheet $sheet)
    {
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A1:H1')->applyFromArray($styleArray);
        $sheet->getStyle('A2:H' . ($sheet->getHighestRow()))->applyFromArray($styleArray);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:H1';

                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B0F0']],
                ]);
                
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);

                foreach (range('A','H') as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
