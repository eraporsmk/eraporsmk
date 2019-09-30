<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use App\Nilai;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
//class NilaiSemesterExport implements FromQuery, WithTitle
class NilaiSemesterExport implements FromView, WithTitle
{
    private $month;
    private $year;

    public function __construct(int $year, int $month)
    {
        $this->month = $month;
        $this->year  = $year;
    }

    /**
    * @return Builder
    */
    public function query()
    {
        return Nilai
            ::query()
            ->whereYear('created_at', $this->year)
            ->whereMonth('created_at', $this->month);
    }
	
	public function view(): View
    {
		$data = array(
			'nilai' 		=> Nilai::query()->whereYear('created_at', $this->year)->whereMonth('created_at', $this->month)->get(),
		);
        return view('rekap_nilai', $data);
    }
    /**
     * @return string
     */
    public function title(): string
    {
        return 'Month ' . $this->month;
    }
}
