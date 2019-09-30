<?php

namespace App\Exports;

use App\Nilai;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Anggota_rombel;
use App\Pembelajaran;
class LeggerNilaiRaporExport implements FromView
{
	use Exportable;
    public function query(string $rombongan_belajar_id)
    {
        $this->rombongan_belajar_id = $rombongan_belajar_id;
        
        return $this;
    }
	public function view(): View
    {
		$get_siswa = Anggota_rombel::with('siswa')->where('rombongan_belajar_id', $this->rombongan_belajar_id)->order()->get();
		$all_pembelajaran = Pembelajaran::where('rombongan_belajar_id', $this->rombongan_belajar_id)->whereNotNull('kelompok_id')->orderBy('kelompok_id', 'asc')->orderBy('no_urut', 'asc')->get();
		$params = array(
			'get_siswa' => $get_siswa,
			'all_pembelajaran'	=> $all_pembelajaran,
		);
		return view('laporan.legger_nilai_rapor', $params);
    }
}
