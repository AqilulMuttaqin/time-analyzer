<?php

namespace App\Exports;

use App\Models\Downtime;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DowntimeExport implements WithMultipleSheets
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
            $query->whereBetween('downtime.tanggal', [$start_date, $end_date]);
        }
        
        $downtime = $query->get();

        $groupedDowntime = $downtime->groupBy('id_subgolongan');

        foreach ($groupedDowntime as $subgolonganid => $downtimeGroup) {
            $subgolongan = $downtimeGroup->first()->subgolongan;
            $sheets[] = new DowntimeExportSheet($subgolongan, $start_date, $end_date);
        }

        return $sheets;
    }
}
