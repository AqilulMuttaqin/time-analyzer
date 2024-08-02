<?php

namespace App\Exports;

use App\Models\Carline;
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

class ReportDowntimeExport implements FromView, WithStyles, WithTitle
{
    private $week;
    protected $startDate;
    protected $endDate;

    public function __construct($week, $startDate, $endDate)
    {
        $this->week = $week;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
    public function view(): View
    {
        $start_date = Carbon::parse($this->startDate)->startOfDay();
        $end_date = Carbon::parse($this->endDate)->endOfDay();
        $week = $this->week;
        $downtime = Downtime::whereBetween('tanggal', [$start_date, $end_date])
            ->with([
            'subgolongan' => function ($query) {
                $query->with([
                    'golongan' => function ($query) {
                        $query->withTrashed();
                    }
                ])->withTrashed();
            },'downtimecode'])
            ->where('week', '=', $week)
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
            ->where('week', '=', $week)
            ->get();

        $section = Section::with(['downtimecode' => function ($query) use ($week, $start_date, $end_date) {
            $query->with(['downtime' => function ($query) use ($week, $start_date, $end_date) {
                $query->whereBetween('tanggal', [$start_date, $end_date])
                    ->where('week', '=', $week)
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
        // $downtime = Downtime::with([
        //     'line' => function ($query) {
        //         $query->with([
        //             'carline' => function ($query) {
        //                 $query->withTrashed();
        //             }
        //         ])->withTrashed();
        //     },'cccode'])
        //     ->where('week', '=', $week)
        //     ->whereBetween('tanggal', [$start_date, $end_date])
        //     ->get();

        // $effective = Effective::with([
        //     'line' => function ($query) {
        //         $query->with([
        //             'carline' => function ($query) {
        //                 $query->withTrashed();
        //             }
        //         ])->withTrashed();
        //     }])
        //     ->where('week', '=', $week)
        //     ->whereBetween('tanggal', [$start_date, $end_date])
        //     ->get();

        // $section = Section::with(['cccode' => function ($query) use ($week, $start_date, $end_date) {
        //     $query->with(['downtime' => function ($query) use ($week, $start_date, $end_date) {
        //         $query->whereBetween('tanggal', [$start_date, $end_date])
        //             ->where('week', '=', $week)
        //             ->with(['line' => function ($query) {
        //                 $query->with([
        //                     'carline' => function ($query) {
        //                         $query->withTrashed();
        //                     }
        //                 ])->withTrashed();
        //             }]);
        //         }]);
        //     }])->get();

        // $carlineT = Carline::with('line')
        //     ->where('lokasi', 'T')
        //     ->get();

        // $carlineB = Carline::with('line')
        //     ->where('lokasi', 'B')
        //     ->get();

        // $start = Carbon::parse($this->startDate)->startOfDay()->setTimezone('Asia/Jakarta')->format('j F Y');
        // $end = Carbon::parse($this->endDate)->endOfDay()->setTimezone('Asia/Jakarta')->subDays(1)->format('j F Y');
        // $labelWeek = '(Week-' . $week .')';

        // $countCl = [];
        // $sectionCcTotal = [];
        // $sectionClTotal = [];

        // foreach ($section as $s) {
        //     $clTotal = [];
        //     foreach ($s->cccode as $cc) {
        //         foreach ($carlineB as $l) {
        //             foreach ($l->line as $cl) {
        //                 $totalCl = $cc->downtime->where('id_line', $cl->id)->sum('minute');
        //                 $count[$cc->id][$cl->id] = $totalCl;
        //                 $ccTotal[$cc->id] = isset($ccTotal[$cc->id]) ? $ccTotal[$cc->id] + $totalCl : $totalCl;
        //                 $clTotal[$cl->id] = isset($clTotal[$cl->id]) ? $clTotal[$cl->id] + $totalCl : $totalCl;
        //             }
        //         }
        //         foreach ($carlineT as $l) {
        //             foreach ($l->line as $cl) {
        //                 $totalCl = $cc->downtime->where('id_line', $cl->id)->sum('minute');
        //                 $count[$cc->id][$cl->id] = $totalCl;
        //                 $ccTotal[$cc->id] = isset($ccTotal[$cc->id]) ? $ccTotal[$cc->id] + $totalCl : $totalCl;
        //                 $clTotal[$cl->id] = isset($clTotal[$cl->id]) ? $clTotal[$cl->id] + $totalCl : $totalCl;
        //             }
        //         }
        //     }
        //     $countCl[$s->id] = $count;
        //     $sectionCcTotal[$s->id] = $ccTotal;
        //     $sectionClTotal[$s->id] = $clTotal;
        // }

        // $allClTotal = [];
        // $allTotalMinute = 0;
        // foreach ($sectionClTotal as $clTId => $clT) {
        //     $totalSection = 0;
        //     $sectionAllCl = [];
        //     foreach ($clT as $clId => $t) {
        //         $totalSection += $t;
        //         if (isset($allClTotal[$clId])) {
        //             $allClTotal[$clId] += $t;
        //         } else {
        //             $allClTotal[$clId] = $t;
        //         }
        //         $allTotalMinute += $t;
        //     }
        //     $sectionAllCl = $totalSection;
        //     $sectionAllClTotal[$clTId] = $sectionAllCl;
        // }

        // $totalClEh = [];
        // $allTotalClEh = 0;
        // foreach ($effective as $e) {
        //     $lineId = $e->line->id;
        //     $regulerEh = $e->reguler_eh + $e->overtime;
        //     $totalRegulerEh = $regulerEh * 60;

        //     if (isset($totalClEh[$lineId])) {
        //         $totalClEh[$lineId] += $totalRegulerEh;
        //     } else {
        //         $totalClEh[$lineId] = $totalRegulerEh;
        //     }
        //     $allTotalClEh += $totalRegulerEh;
        // }

        // $percentLossTimeAll = $allTotalClEh != 0 ? ($allTotalMinute / $allTotalClEh) * 100 : 0;
        // $percentLossTime = [];
        // foreach ($allClTotal as $lineId => $clTotal) {
        //     if (isset($totalClEh[$lineId])) {
        //         $totalEh = $totalClEh[$lineId];
        //         $percentLossTime[$lineId] = $totalEh != 0 ? ($clTotal / $totalEh) * 100 : 0;
        //     } else {
        //         $percentLossTime[$lineId] = 0;
        //     }
        // }

        // $totalClLB = 0;
        // $totalClEhLB = 0;
        // foreach ($carlineB as $l) {
        //     $totalClL = 0;
        //     $totalClEhL = 0;
        //     foreach ($l->line as $cl) {
        //         $totalClL += $allClTotal[$cl->id];
        //         $totalClLB += $allClTotal[$cl->id];
        //         if (isset($totalClEh[$cl->id])) {
        //             $totalClEhL += $totalClEh[$cl->id];
        //             $totalClEhLB += $totalClEh[$cl->id];
        //         } else {
        //             $totalClEhL += 0;
        //             $totalClEhLB += 0;
        //         }
        //     }
        //     $percentLossTimeLine[$l->id] = $totalClEhL != 0 ? ($totalClL / $totalClEhL) * 100 : 0;
        // }
        // $percentLossTimeCarlineB = $totalClEhLB != 0 ? ($totalClLB / $totalClEhLB) * 100 : 0;

        // $totalClLT = 0;
        // $totalClEhLT = 0;
        // foreach ($carlineT as $l) {
        //     $totalClL = 0;
        //     $totalClEhL = 0;
        //     foreach ($l->line as $cl) {
        //         $totalClL += $allClTotal[$cl->id];
        //         $totalClLT += $allClTotal[$cl->id];
        //         if (isset($totalClEh[$cl->id])) {
        //             $totalClEhL += $totalClEh[$cl->id];
        //             $totalClEhLT += $totalClEh[$cl->id];
        //         } else {
        //             $totalClEhL += 0;
        //             $totalClEhLT += 0;
        //         }
        //     }
        //     $percentLossTimeLine[$l->id] = $totalClEhL != 0 ? ($totalClL / $totalClEhL) * 100 : 0;
        // }
        // $percentLossTimeCarlineT = $totalClEhLT != 0 ? ($totalClLT / $totalClEhLT) * 100 : 0;

        // return view('pages.export.downtime-report', [
        //     'title' => 'Table',
        //     'downtime' => $downtime,
        //     'effective' => $effective,
        //     'section' => $section,
        //     'carlineT' => $carlineT,
        //     'carlineB' => $carlineB,
        //     'start' => $start,
        //     'end' => $end,
        //     'week' => $labelWeek,
        //     'countCl' => $countCl,
        //     'sectionCcTotal' => $sectionCcTotal,
        //     'sectionClTotal' => $sectionClTotal,
        //     'sectionAllClTotal' => $sectionAllClTotal,
        //     'allClTotal' => $allClTotal,
        //     'allTotalMinute' => $allTotalMinute,
        //     'totalClEh' => $totalClEh,
        //     'allTotalClEh' => $allTotalClEh,
        //     'percentLossTime' => $percentLossTime,
        //     'percentLossTimeLine' => $percentLossTimeLine,
        //     'percentLossTimeCarlineB' => $percentLossTimeCarlineB,
        //     'percentLossTimeCarlineT' => $percentLossTimeCarlineT,
        //     'percentLossTimeAll' => $percentLossTimeAll,
        // ]);
    }

    public function title(): string
    {
        return 'Prod DT-' . $this->week;
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
