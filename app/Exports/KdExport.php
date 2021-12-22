<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KdExport implements FromView, ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function query($tingkat_pendidikan_id, $rombongan_belajar_id, $mata_pelajaran_id, $kompetensi_id)
    {
        $this->tingkat_pendidikan_id = $tingkat_pendidikan_id;
        $this->rombongan_belajar_id = $rombongan_belajar_id;
        $this->mata_pelajaran_id = $mata_pelajaran_id;
        $this->kompetensi_id = $kompetensi_id;
		return $this;
    }
	public function view(): View
    {
		//$rombongan_belajar_id = $this->rombongan_belajar_id;
		$data['get_siswa'] = Anggota_rombel::with(['siswa','kehadiran'])->where('rombongan_belajar_id', $this->rombongan_belajar_id)->order()->get();
        return view('laporan.kehadiran_excel', $data);
    }
}
