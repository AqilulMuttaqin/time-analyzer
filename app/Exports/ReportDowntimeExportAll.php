<?php

namespace App\Exports;

use App\Models\Downtime;
use App\Models\Effective;
use App\Models\Golongan;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportDowntimeExportAll implements FromView, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    public function view(): View
    {
        $start_date = Carbon::parse($this->startDate)->startOfDay();
        $end_date = Carbon::parse($this->endDate)->endOfDay();
        $downtime = Downtime::whereBetween('tanggal', [$start_date, $end_date])
            ->with([
            'subgolongan' => function ($query) {
                $query->with([
                    'golongan' => function ($query) {
                        $query->withTrashed();
                    }
                ])->withTrashed();
            },'downtimecode'])
            ->get();

        $effective = Effective::whereBetween('tanggal', [$start_date, $end_date])
            ->with([
            'subgolongan' => function ($query) {
                $query->with([
                    'golongan' => function ($query) {
                        $query->withTrashed();
                    }
                ])->withTrashed();
            }])
            ->get();

        $section = Section::with(['downtimecode' => function ($query) use ($start_date, $end_date) {
            $query->with(['downtime' => function ($query) use ($start_date, $end_date) {
                $query->whereBetween('tanggal', [$start_date, $end_date])
                    ->with(['subgolongan' => function ($query) {
                        $query->with([
                            'golongan' => function ($query) {
                                $query->withTrashed();
                            }
                        ])->withTrashed();
                    }]);
                }]);
            }])->get();

        $golonganT = Golongan::with('subgolongan')
            ->where('lokasi', 'T')
            ->get();

        $golonganB = Golongan::with('subgolongan')
            ->where('lokasi', 'B')
            ->get();

        $labelWeek = '';

        $countG = [];
        $sectionDcTotal = [];
        $sectionGTotal = [];

        foreach ($section as $s) {
            $gTotal = [];
            foreach ($s->downtimecode as $dc) {
                foreach ($golonganB as $g) {
                    foreach ($g->subgolongan as $sg) {
                        $totalG = $dc->downtime->where('id_subgolongan', $sg->id)->sum('minute');
                        $count[$dc->id][$sg->id] = $totalG;
                        $dcTotal[$dc->id] = isset($dcTotal[$dc->id]) ? $dcTotal[$dc->id] + $totalG : $totalG;
                        $gTotal[$sg->id] = isset($gTotal[$sg->id]) ? $gTotal[$sg->id] + $totalG : $totalG;
                    }
                }
                foreach ($golonganT as $g) {
                    foreach ($g->subgolongan as $sg) {
                        $totalG = $dc->downtime->where('id_subgolongan', $sg->id)->sum('minute');
                        $count[$dc->id][$sg->id] = $totalG;
                        $dcTotal[$dc->id] = isset($dcTotal[$dc->id]) ? $dcTotal[$dc->id] + $totalG : $totalG;
                        $gTotal[$sg->id] = isset($gTotal[$sg->id]) ? $gTotal[$sg->id] + $totalG : $totalG;
                    }
                }
            }
            $countG[$s->id] = $count;
            $sectionDcTotal[$s->id] = $dcTotal;
            $sectionGTotal[$s->id] = $gTotal;
        }

        $allGTotal = [];
        $allTotalMinute = 0;
        foreach ($sectionGTotal as $gTId => $gT) {
            $totalSection = 0;
            $sectionAllG = [];
            foreach ($gT as $gId => $t) {
                $totalSection += $t;
                if (isset($allGTotal[$gId])) {
                    $allGTotal[$gId] += $t;
                } else {
                    $allGTotal[$gId] = $t;
                }
                $allTotalMinute += $t;
            }
            $sectionAllG = $totalSection;
            $sectionAllGTotal[$gTId] = $sectionAllG;
        }

        $totalGEh = [];
        $allTotalGEh = 0;
        foreach ($effective as $e) {
            $subgolonganId = $e->subgolongan->id;
            $regulerEh = $e->reguler_eh + $e->overtime;
            $totalRegulerEh = $regulerEh * 60;

            if (isset($totalGEh[$subgolonganId])) {
                $totalGEh[$subgolonganId] += $totalRegulerEh;
            } else {
                $totalGEh[$subgolonganId] = $totalRegulerEh;
            }
            $allTotalGEh += $totalRegulerEh;
        }

        $percentLossTimeAll = $allTotalGEh != 0 ? ($allTotalMinute / $allTotalGEh) * 100 : 0;
        $percentLossTime = [];
        foreach ($allGTotal as $subgolonganId => $gTotal) {
            if (isset($totalGEh[$subgolonganId])) {
                $totalEh = $totalGEh[$subgolonganId];
                $percentLossTime[$subgolonganId] = $totalEh != 0 ? ($gTotal / $totalEh) * 100 : 0;
            } else {
                $percentLossTime[$subgolonganId] = 0;
            }
        }

        $totalGSgB = 0;
        $totalGEhSgB = 0;
        foreach ($golonganB as $l) {
            $totalGSg = 0;
            $totalGEhSg = 0;
            foreach ($l->subgolongan as $g) {
                $totalGSg += $allGTotal[$g->id];
                $totalGSgB += $allGTotal[$g->id];
                if (isset($totalGEh[$g->id])) {
                    $totalGEhSg += $totalGEh[$g->id];
                    $totalGEhSgB += $totalGEh[$g->id];
                } else {
                    $totalGEhSg += 0;
                    $totalGEhSgB += 0;
                }
            }
            $percentLossTimeSubgolongan[$l->id] = $totalGEhSg != 0 ? ($totalGSg / $totalGEhSg) * 100 : 0;
        }
        $percentLossTimeGolonganB = $totalGEhSgB != 0 ? ($totalGSgB / $totalGEhSgB) * 100 : 0;

        $totalGSgT = 0;
        $totalGEhSgT = 0;
        foreach ($golonganT as $l) {
            $totalGSg = 0;
            $totalGEhSg = 0;
            foreach ($l->subgolongan as $g) {
                $totalGSg += $allGTotal[$g->id];
                $totalGSgT += $allGTotal[$g->id];
                if (isset($totalGEh[$g->id])) {
                    $totalGEhSg += $totalGEh[$g->id];
                    $totalGEhSgT += $totalGEh[$g->id];
                } else {
                    $totalGEhSg += 0;
                    $totalGEhSgT += 0;
                }
            }
            $percentLossTimeSubgolongan[$l->id] = $totalGEhSg != 0 ? ($totalGSg / $totalGEhSg) * 100 : 0;
        }
        $percentLossTimeGolonganT = $totalGEhSgT != 0 ? ($totalGSgT / $totalGEhSgT) * 100 : 0;

        return view('pages.export.downtime-report', [
            'title' => 'Table',
            'downtime' => $downtime,
            'effective' => $effective,
            'section' => $section,
            'golonganT' => $golonganT,
            'golonganB' => $golonganB,
            'start' => $start_date,
            'end' => $end_date,
            'week' => $labelWeek,
            'countG' => $countG,
            'sectionDcTotal' => $sectionDcTotal,
            'sectionGTotal' => $sectionGTotal,
            'sectionAllGTotal' => $sectionAllGTotal,
            'allGTotal' => $allGTotal,
            'allTotalMinute' => $allTotalMinute,
            'totalGEh' => $totalGEh,
            'allTotalGEh' => $allTotalGEh,
            'percentLossTime' => $percentLossTime,
            'percentLossTimeSubgolongan' => $percentLossTimeSubgolongan,
            'percentLossTimeGolonganB' => $percentLossTimeGolonganB,
            'percentLossTimeGolonganT' => $percentLossTimeGolonganT,
            'percentLossTimeAll' => $percentLossTimeAll,
        ]);
    }

    public function title(): string
    {
        return 'Prod Down Total';
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
