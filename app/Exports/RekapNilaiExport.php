<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RekapNilaiExport implements WithMultipleSheets{
    use Exportable;
    protected $year;
    
    public function __construct(int $year){
        $this->year = $year;
    }

    /**
     * @return array
     */
    public function sheets(): array{
        $sheets = [];
        for ($month = 1; $month <= 12; $month++) {
            $sheets[] = new NilaiSemesterExport($this->year, $month);
        }
        return $sheets;
    }
}
