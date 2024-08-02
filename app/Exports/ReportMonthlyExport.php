<?php

namespace App\Exports;

use App\Models\Report;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportMonthlyExport implements FromView, WithStyles, WithTitle
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $idReport = $this->id;
        $report = Report::with('section', 'concern.action')->find($idReport);

        return view('pages.export.monthly-report', [
            'title' => 'Table',
            'report' => $report
        ]);
    }

    public function title(): string
    {
        return 'Monthly Report';
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A5:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($styles);
    }
}
