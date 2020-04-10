<?php

namespace App\Exports;

use App\Pembelajaran;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class NilaiAkhirExport implements FromView, ShouldAutoSize
{
	use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function pembelajaran_id(string $pembelajaran_id)
    {
        $this->pembelajaran_id = $pembelajaran_id;
        
        return $this;
    }
	public function view(): View
    {
		$pembelajaran_id = $this->pembelajaran_id;
		$pembelajaran = Pembelajaran::with(['anggota_rombel' => function($query){
			$query->order();
		}, 'anggota_rombel.siswa', 'anggota_rombel.nilai_akhir_pengetahuan' => function($query) use ($pembelajaran_id){
			$query->where('pembelajaran_id', '=', $pembelajaran_id);
		}, 'anggota_rombel.nilai_akhir_keterampilan' => function($query) use ($pembelajaran_id){
			$query->where('pembelajaran_id', '=', $pembelajaran_id);
		}])->find($this->pembelajaran_id);
		$data = array(
			'pembelajaran_id' 	=> $this->pembelajaran_id,
			'pembelajaran'		=> $pembelajaran,
			'rasio_p' 			=> ($pembelajaran->rasio_p) ? $pembelajaran->rasio_p : 50,
			'rasio_k' 			=> ($pembelajaran->rasio_k) ? $pembelajaran->rasio_k : 50,
		);
        return view('monitoring.export_nilai', $data);
    }
}
