<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use App\Pembelajaran;
use App\Anggota_rombel;
use CustomHelper;
class LaporanExport implements FromView
{
    use Exportable;
    public function query($query, $rombongan_belajar_id, $pembelajaran_id)
    {
        $this->query = $query;
        $this->rombongan_belajar_id = $rombongan_belajar_id;
		$this->pembelajaran_id = $pembelajaran_id;
        
        return $this;
    }
    public function view(): View
    {
        $pembelajaran = Pembelajaran::find($this->pembelajaran_id);
        $get_mapel_agama = CustomHelper::filter_agama_siswa($this->pembelajaran_id, $this->rombongan_belajar_id);
		$callback = function($query) use ($get_mapel_agama) {
			if($get_mapel_agama){
				$query->where('agama_id', $get_mapel_agama);
			}
        };
        if($this->query == 'nilai-us'){
            $get_siswa = Anggota_rombel::whereHas('siswa', $callback)->with(['siswa' => $callback])->with(['nilai_us' => function($query){
                $query->where('pembelajaran_id', $this->pembelajaran_id);
            }])->where('rombongan_belajar_id', $this->rombongan_belajar_id)->order()->get();
        } else {
            $get_siswa = Anggota_rombel::whereHas('siswa', $callback)->with(['siswa' => $callback])->with(['nilai_un' => function($query){
                $query->where('pembelajaran_id', $this->pembelajaran_id);
            }])->where('rombongan_belajar_id', $this->rombongan_belajar_id)->order()->get();
        }
		$query = $this->query;
		return view('laporan.export_excel', compact('get_siswa', 'pembelajaran', 'query'));
    }
}
