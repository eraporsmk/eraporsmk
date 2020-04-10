<?php

namespace App\Exports;

use App\Kd_nilai;
use App\Rencana_penilaian;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
class NilaiExport implements FromView
{
	use Exportable;
    public function query($rencana_penilaian_id, $agama_id)
    {
        $this->rencana_penilaian_id = $rencana_penilaian_id;
		$this->agama_id = $agama_id;
        
        return $this;
    }
	public function view(): View
    {
		$agama_id = $this->agama_id;
		if($agama_id){
			$rencana_penilaian = Rencana_penilaian::with(['kd_nilai.nilai', 'pembelajaran', 'pembelajaran.rombongan_belajar','pembelajaran.anggota_rombel' => function($query) use ($agama_id){
				$query->order();
				$callback = function($sq) use ($agama_id) {
					$sq->where('agama_id', $agama_id);
				};
				$query->whereHas('siswa', $callback)->with(['siswa' => $callback]);
			}])->where('rencana_penilaian_id', '=', $this->rencana_penilaian_id)->first();
		} else {
			$rencana_penilaian = Rencana_penilaian::with(['kd_nilai.nilai', 'pembelajaran', 'pembelajaran.rombongan_belajar','pembelajaran.anggota_rombel' => function($query) use ($agama_id){
				$query->order();
				$query->with('siswa');
			}])->where('rencana_penilaian_id', '=', $this->rencana_penilaian_id)->first();
		}
		return view('penilaian.export', [
            'rencana_penilaian_id' => $this->rencana_penilaian_id,
			'rencana_penilaian' => $rencana_penilaian,
        ]);
    }
}
