<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Semester;
use App\Exports\NilaiAkhirExport;
use App\Pembelajaran;
class MonitoringController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	public function index(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$params = array(
			'user' 		=> $user,
			'semester'	=> $semester,
			'query'		=> 'rekap_nilai',
			'title'		=> 'Rekapitulasi Hasil Penilaian',
		);
		return view('monitoring.rekap_nilai')->with($params);
	}
	public function unduh_nilai($pembelajaran_id){
		$pembelajaran = Pembelajaran::with('rombongan_belajar')->find($pembelajaran_id);
		$nama_file = 'Rekap Nilai '.$pembelajaran->nama_mata_pelajaran.' kelas '.$pembelajaran->rombongan_belajar->nama.'.xlsx';
		return (new NilaiAkhirExport)->pembelajaran_id($pembelajaran_id)->download($nama_file);
	}
	public function analisis_nilai(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$params = array(
			'user' 		=> $user,
			'semester'	=> $semester,
			'query'		=> 'analisis_nilai',
			'title'		=> 'Analisis Hasil Penilaian',
		);
		return view('monitoring.analisis_nilai')->with($params);
	}
	public function analisis_remedial(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$params = array(
			'user' 		=> $user,
			'semester'	=> $semester,
			'query'		=> 'analisis_remedial',
			'title'		=> 'Analisis Pencapaian Remedial',
		);
		return view('monitoring.analisis_remedial')->with($params);
	}
	public function capaian_kompetensi(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$params = array(
			'user' 		=> $user,
			'semester'	=> $semester,
			'query'		=> 'capaian_kompetensi',
			'title'		=> 'Analisis Pencapaian Kompetensi',
		);
		return view('monitoring.capaian_kompetensi')->with($params);
	}
	public function prestasi_individu(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$params = array(
			'user' 		=> $user,
			'semester'	=> $semester,
			'query'		=> 'prestasi_individu',
			'title'		=> 'Monitoring Prestasi Individu Peserta Didik',
		);
		return view('monitoring.prestasi_individu')->with($params);
	}
}
