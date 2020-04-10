<?php

namespace App\Exports;

use App\Anggota_rombel;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AbsensiExport implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query($rombongan_belajar_id)
    {
        $this->rombongan_belajar_id = $rombongan_belajar_id;
		return $this;
    }
	public function view(): View
    {
		//$rombongan_belajar_id = $this->rombongan_belajar_id;
		$data['get_siswa'] = Anggota_rombel::with(['siswa','kehadiran'])->where('rombongan_belajar_id', $this->rombongan_belajar_id)->order()->get();
        return view('laporan.kehadiran_excel', $data);
    }
}
