<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FormatImportSubgolongan implements FromCollection, WithTitle, WithStyles, WithEvents
{
    public function collection()
    {
        return collect([
            ['Sub Golongan', 'Golongan']
        ]);
    }

    public function title(): string
    {
        return "Format Sub Golongan Import";
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

        $sheet->getStyle('A1:B1')->applyFromArray($styleArray);
        $sheet->getStyle('A2:B' . ($sheet->getHighestRow()))->applyFromArray($styleArray);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:B1';

                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B0F0']],
                ]);
                
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);
                foreach (range('A','B') as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
}
