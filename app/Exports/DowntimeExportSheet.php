<?php

namespace App\Exports;

use App\Models\Downtime;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DowntimeExportSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, WithEvents
{
    private $subgolongan;
    protected $startDate;
    protected $endDate;

    public function __construct($subgolongan, $startDate, $endDate)
    {
        $this->subgolongan = $subgolongan;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        $query = Downtime::with(['subgolongan', 'downtimecode'])
            ->where('downtime.id_subgolongan', $this->subgolongan->id);
        
        if ($this->startDate && $this->endDate) {
            $start_date = Carbon::parse($this->startDate)->startOfDay();
            $end_date = Carbon::parse($this->endDate)->endOfDay();
    
            $query->whereBetween('downtime.tanggal', [$start_date, $end_date]);
        }
        
        $downtime = $query->orderBy('tanggal', 'asc')->get();

        $data = [];

        foreach ($downtime as $dt) {
            $data[] = [
                'Tanggal' => Date::PHPToExcel($dt->tanggal),
                'Week' => $dt->week,
                'Shift' => $dt->shift,
                'CC Code' => $dt->downtimecode ? $dt->downtimecode->kode : '',
                'Detail Problem' => $dt->detail,
                'Minute' => $dt->minute,
                'Man Hours' => $dt->man_hours
            ];
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Week',
            'Shift',
            'Downtime Code',
            'Detail Problem',
            'Minute',
            'Man Hours'
        ];
    }

    public function title(): string
    {
        return $this->subgolongan->nama;
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

        $sheet->getStyle('A1:G1')->applyFromArray($styleArray);
        $sheet->getStyle('A2:G' . ($sheet->getHighestRow()))->applyFromArray($styleArray);
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_DATE_DDMMYYYY, 
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $cellRange = 'A1:G1';

                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00B0F0']],
                ]);
                
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setBold(true);

                foreach (range('A','D') as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
                foreach (range('F','G') as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setAutoSize(true);
                }
                $event->sheet->getStyle('A:A')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);

                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(80);
                $event->sheet->getStyle('E')->getAlignment()->setWrapText(true);

                $event->sheet->getDelegate()->getStyle('A1:G' . ($event->sheet->getDelegate()->getHighestRow()))
                    ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            },
        ];
    }
}
