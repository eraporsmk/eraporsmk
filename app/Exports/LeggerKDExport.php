<?php

namespace App\Exports;

use App\Rombongan_belajar;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;

class LeggerKDExport implements FromView
{
	use Exportable;
    public function query($rombongan_belajar_id)
    {
        $this->rombongan_belajar_id = $rombongan_belajar_id;
		return $this;
    }
	public function view(): View
    {
		$rombongan_belajar = Rombongan_belajar::with(['anggota_rombel' => function($query){
			$query->with('siswa');
			$query->order();
			$query->with(['nilai_kd_pengetahuan' => function($q){
				$q->with('kd_nilai');
				$q->orderBy('kompetensi_dasar_id');
			}]);
			$query->with(['nilai_kd_keterampilan' => function($q){
				$q->with('kd_nilai');
				$q->orderBy('kompetensi_dasar_id');
			}]);
		}])->with('jurusan')->with('sekolah')->with('semester')->with(['pembelajaran' => function($query){
			$query->with(['kd_nilai_p' => function($query){
				$query->select(['kompetensi_dasar_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->orderBy('kompetensi_id', 'asc');
				$query->orderBy('kompetensi_dasar_id', 'asc');
				$query->groupBy(['kompetensi_dasar_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->with('kompetensi_dasar');
			}]);
			$query->with(['kd_nilai_k' => function($query){
				$query->select(['kompetensi_dasar_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->orderBy('kompetensi_id', 'asc');
				$query->orderBy('kompetensi_dasar_id', 'asc');
				$query->groupBy(['kompetensi_dasar_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->with('kompetensi_dasar');
			}]);
			$query->whereNotNull('kelompok_id');
			$query->orderBy('kelompok_id', 'asc');
			$query->orderBy('no_urut', 'asc');
		}])->find($this->rombongan_belajar_id);
        $params = array(
			'rombongan_belajar' => $rombongan_belajar,
		);
		return view('laporan.legger_kd')->with($params);
    }
}
