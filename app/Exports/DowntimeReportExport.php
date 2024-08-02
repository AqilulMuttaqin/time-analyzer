<?php

namespace App\Exports;

use App\Models\Downtime;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DowntimeReportExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        $sheets = [];
        
        $query = Downtime::with(['subgolongan', 'downtimecode']);

        if ($this->startDate && $this->endDate) {
            $start_date = Carbon::parse($this->startDate)->startOfDay();
            $end_date = Carbon::parse($this->endDate)->endOfDay();
            $query->whereBetween('downtime.tanggal', [$start_date, $end_date])->orderBy('week');
        }
        
        $downtime = $query->get();

        $groupedDowntime = $downtime->groupBy('week');

        foreach ($groupedDowntime as $week => $downtimeGroup) {
            $weekGroup = $downtimeGroup->first();
            $sheets[] = new ReportDowntimeExport($weekGroup->week, $start_date, $end_date);
        }

        $sheets[] = new ReportDowntimeExportAll($start_date, $end_date);

        return $sheets;
    }
}
