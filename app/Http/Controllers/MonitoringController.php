<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Semester;
use App\Exports\NilaiAkhirExport;
use App\Pembelajaran;
use CustomHelper;
use App\Tahun_ajaran;
class MonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	public function index(){
		$user = auth()->user();
		$params = array(
			'waka'		=> $user->hasRole('waka'),
			'all_data'	=> Tahun_ajaran::with('semester')->where('periode_aktif', '=', 1)->orderBy('tahun_ajaran_id', 'desc')->get(),
			'query'		=> 'rekap_nilai',
			'title'		=> 'Rekapitulasi Hasil Penilaian',
		);
		return view('monitoring.rekap_nilai')->with($params);
	}
	public function unduh_nilai($pembelajaran_id){
		$pembelajaran = Pembelajaran::with('rombongan_belajar')->find($pembelajaran_id);
		$nama_file = 'Rekap Nilai '.$pembelajaran->nama_mata_pelajaran.' kelas '.$pembelajaran->rombongan_belajar->nama;
		$nama_file = CustomHelper::clean($nama_file);
		$nama_file = $nama_file.'.xlsx';
		return (new NilaiAkhirExport)->pembelajaran_id($pembelajaran_id)->download($nama_file);
	}
	public function analisis_nilai(){
		$params = array(
			'query'		=> 'analisis_nilai',
			'title'		=> 'Analisis Hasil Penilaian',
		);
		return view('monitoring.analisis_nilai')->with($params);
	}
	public function analisis_remedial(){
		$params = array(
			'query'		=> 'analisis_remedial',
			'title'		=> 'Analisis Pencapaian Remedial',
		);
		return view('monitoring.analisis_remedial')->with($params);
	}
	public function capaian_kompetensi(){
		$params = array(
			'query'		=> 'capaian_kompetensi',
			'title'		=> 'Analisis Pencapaian Kompetensi',
		);
		return view('monitoring.capaian_kompetensi')->with($params);
	}
	public function prestasi_individu(){
		$params = array(
			'query'		=> 'prestasi_individu',
			'title'		=> 'Monitoring Prestasi Individu Peserta Didik',
		);
		return view('monitoring.prestasi_individu')->with($params);
	}
}
