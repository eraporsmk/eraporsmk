<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rombongan_belajar;
use App\Pembelajaran;
use App\Siswa;
use CustomHelper;
use App\Teknik_penilaian;
use App\Kompetensi_dasar;
use App\Bobot_keterampilan;
use App\Rencana_penilaian;
use App\Kd_nilai;
use App\Anggota_rombel;
use App\Sikap;
use App\Nilai_sikap;
use App\Kurikulum;
use App\Paket_ukk;
use Illuminate\Support\Facades\DB;
use App\Ekstrakurikuler;
use App\Nilai_karakter;
use App\Catatan_ppk;
use App\Prestasi;
use App\Rencana_ukk;
use App\Dudi;
use App\Jurusan_sp;
use App\Nilai_us;
use App\Nilai_un;
use App\Kewirausahaan;
use App\Anggota_kewirausahaan;
use App\Budaya_kerja;
use App\Rencana_budaya_kerja;
use App\Aspek_budaya_kerja;
use App\Opsi_budaya_kerja;
use App\Catatan_budaya_kerja;
class AjaxController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
	}
	public function get_rombel_jurusan(Request $request){
		$user = auth()->user();
		$data_rombel = Rombongan_belajar::where(function($query) use ($request){
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->where('jenis_rombel', 1);
			$query->where('jurusan_sp_id', $request->jurusan_sp_id);
			$query->where('tingkat', 13);
			$query->orWhere('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->where('jenis_rombel', 1);
			$query->where('jurusan_sp_id', $request->jurusan_sp_id);
			$query->where('tingkat', 12);
		})->orderBy('nama')->get();
		if($data_rombel->count()){
			foreach($data_rombel as $rombel){
				$record= array();
				$record['value'] 	= $rombel->rombongan_belajar_id;
				$record['text'] 	= $rombel->nama;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan rombongan belajar di kelas terpilih';
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_rombel_filter(Request $request){
		$user = auth()->user();
		$jurusan_id = $request['jurusan_id'];
		$tingkat = $request['tingkat'];
		$data_rombel = Rombongan_belajar::where('sekolah_id', session('sekolah_id'))->where('tingkat', $tingkat)->where('semester_id', session('semester_id'))->where('jenis_rombel', 1)->where('jurusan_id', $jurusan_id)->orderBy('nama')->get();
		if($data_rombel->count()){
			foreach($data_rombel as $rombel){
				$record= array();
				$record['value'] 	= $rombel->rombongan_belajar_id;
				$record['text'] 	= $rombel->nama;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan rombongan belajar di kelas terpilih';
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
    public function get_rombel(Request $request){
		$user = auth()->user();
		$query = $request['query'];
		$tingkat = $request['kelas'];
		$semester_id = $request['semester_id'];
		if($query == 'rencana_penilaian' || $query == 'pengetahuan' || $query == 'keterampilan' || $query == 'remedial'){
			$data_rombel = Rombongan_belajar::whereHas('pembelajaran', function($query) use ($user, $semester_id){
				$query->where('pembelajaran.sekolah_id', session('sekolah_id'));
				$query->where('pembelajaran.semester_id', $semester_id);
				$query->where('pembelajaran.guru_id', $user->guru_id);
				$query->whereNotNull('kelompok_id');
				$query->whereNotNull('no_urut');
				$query->orWhere('pembelajaran.sekolah_id', session('sekolah_id'));
				$query->where('pembelajaran.semester_id', $semester_id);
				$query->where('pembelajaran.guru_pengajar_id', $user->guru_id);
				$query->whereNotNull('kelompok_id');
				$query->whereNotNull('no_urut');
			})
			->where('tingkat', $tingkat)
			->where('semester_id', $semester_id)
			->where('jenis_rombel', 1)
			->orderBy('nama')
			->orderBy('tingkat')
			->get();
		} else {
			if($user->hasRole('waka') || $query == 'sikap'){
				$data_rombel = Rombongan_belajar::where('sekolah_id', session('sekolah_id'))->where('tingkat', $tingkat)->where('semester_id', $semester_id)->where('jenis_rombel', 1)->orderBy('nama')->orderBy('tingkat')->get();
			} elseif($user->hasRole('guru')) {
				if($query == 'ukk'){
					$data_rombel = Rombongan_belajar::where('tingkat', $tingkat)
					->where('sekolah_id', session('sekolah_id'))
					->where('semester_id', $semester_id)
					->where('jenis_rombel', 1)
					->orderBy('nama')
					->orderBy('tingkat')
					->get();
				} else {
					$data_rombel = Rombongan_belajar::whereHas('pembelajaran', function($query) use ($user, $semester_id){
						$query->where('pembelajaran.sekolah_id', session('sekolah_id'));
						$query->where('pembelajaran.semester_id', $semester_id);
						$query->where('pembelajaran.guru_id', $user->guru_id);
						$query->whereNotNull('kelompok_id');
						$query->whereNotNull('no_urut');
						$query->orWhere('pembelajaran.sekolah_id', session('sekolah_id'));
						$query->where('pembelajaran.semester_id', $semester_id);
						$query->where('pembelajaran.guru_pengajar_id', $user->guru_id);
						$query->whereNotNull('kelompok_id');
						$query->whereNotNull('no_urut');
					})
					->where('tingkat', $tingkat)
					->where('semester_id', $semester_id)
					->where('jenis_rombel', 1)
					->orderBy('nama')
					->orderBy('tingkat')
					->get();
				}
			} else {
				$data_rombel = Rombongan_belajar::where(function($query) use ($tingkat, $semester_id){
					$query->where('sekolah_id', session('sekolah_id'));
					$query->where('tingkat', $tingkat);
					$query->where('semester_id', $semester_id);
				})->orderBy('nama')->orderBy('tingkat')->get();
			}
		}
		if($data_rombel->count()){
			foreach($data_rombel as $rombel){
				$record= array();
				$record['value'] 	= $rombel->rombongan_belajar_id;
				$record['text'] 	= $rombel->nama;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan rombongan belajar di kelas terpilih';
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_mapel(Request $request){
		$user = auth()->user();
		$rombongan_belajar_id = $request['rombel_id'];
		$semester_id = $request['semester_id'];
		$query = $request['query'];
		if($user->hasRole('waka')){
			if($query == 'rekap_nilai' || $query == 'analisis_nilai' || $query == 'analisis_remedial' || $query == 'capaian_kompetensi' || $query == 'prestasi_individu' || $query == 'nilai_us'){
				$all_mapel = Pembelajaran::where('rombongan_belajar_id', $rombongan_belajar_id)
				->whereNotNull('kelompok_id')
				->whereNotNull('no_urut')
				->orderBy('mata_pelajaran_id', 'asc')
				->get();
			} elseif($query == 'nilai_un'){
				$all_mapel = Pembelajaran::where('rombongan_belajar_id', $rombongan_belajar_id)
				->whereNotNull('kelompok_id')
				->whereNotNull('no_urut')
				->whereIn('mata_pelajaran_id', ['300110000', '401000000', '300210000'])
				->orWhere('rombongan_belajar_id', $rombongan_belajar_id)
				->where('kelompok_id', 10)
				->whereNotNull('no_urut')
				->orderBy('mata_pelajaran_id', 'asc')
				->get();
			} else {
				$all_mapel = Pembelajaran::where('rombongan_belajar_id', $rombongan_belajar_id)
				->where('guru_id', $user->guru_id)
				->whereNotNull('kelompok_id')
				->whereNotNull('no_urut')
				->orWhere('rombongan_belajar_id', $rombongan_belajar_id)
				->where('guru_pengajar_id', $user->guru_id)
				->whereNotNull('kelompok_id')
				->whereNotNull('no_urut')
				->orderBy('mata_pelajaran_id', 'asc')
				->get();
			}
		} else {
			$all_mapel = Pembelajaran::where('rombongan_belajar_id', $rombongan_belajar_id)
			->where('guru_id', $user->guru_id)
			->whereNotNull('kelompok_id')
			->whereNotNull('no_urut')
			->orWhere('rombongan_belajar_id', $rombongan_belajar_id)
			->where('guru_pengajar_id', $user->guru_id)
			->whereNotNull('kelompok_id')
			->whereNotNull('no_urut')
			->orderBy('mata_pelajaran_id', 'asc')
			->get();
		}
		if($all_mapel->count()){
			foreach($all_mapel as $mapel){
				$record= array();
				$record['value'] 	= $mapel->mata_pelajaran_id;
				$record['text'] 	= $mapel->nama_mata_pelajaran.' ('.$mapel->mata_pelajaran_id.')';
				$record['pembelajaran_id'] 	= $mapel->pembelajaran_id;
				$output['mapel'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan mata pelajaran di kelas terpilih';
			$record['pembelajaran_id'] 	= '';
			$output['mapel'][] = $record;
		}
		$rombel = Rombongan_belajar::with('kurikulum')->find($rombongan_belajar_id);
		if (strpos($rombel->kurikulum->nama_kurikulum, 'REV') !== false) {
			$kurikulum = 2017;
		} elseif (strpos($rombel->kurikulum->nama_kurikulum, 'KTSP') !== false) {
			$kurikulum = 2006;
		} elseif (strpos($rombel->kurikulum->nama_kurikulum, 'Pusat') !== false) {
			$kurikulum = 2021;
		} else {
			$kurikulum = 2013;
		}
		$output['kurikulum'] = $kurikulum;
		echo json_encode($output);
	}
	public function get_teknik(Request $request){
		$user = auth()->user();
		$kompetensi_id = $request['kompetensi_id'];
		$all_bentuk_penilaian = Teknik_penilaian::where('kompetensi_id', $kompetensi_id)->get();
		if($all_bentuk_penilaian->count()){
			foreach($all_bentuk_penilaian as $bentuk_penilaian){
				$record= array();
				$record['value'] 	= $bentuk_penilaian->teknik_penilaian_id;
				$record['text'] 	= $bentuk_penilaian->nama;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan teknik penilaian keterampilan';
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_kd(Request $request){
		$user = auth()->user();
		$kompetensi_id = $request['kompetensi_id'];
		$id_mapel = $request['id_mapel'];
		$kelas = $request['kelas'];
		$id_rombel = $request['rombel_id'];
		$metode_id = $request['teknik_penilaian'];
		$rombongan_belajar = Rombongan_belajar::find($id_rombel);
		$get_kurikulum = Kurikulum::find($rombongan_belajar->kurikulum_id);
		if (strpos($get_kurikulum->nama_kurikulum, 'REV') !== false) {
			$kurikulum = 2017;
		} elseif (strpos($get_kurikulum->nama_kurikulum, 'KTSP') !== false) {
			$kurikulum = 2006;
		} elseif (strpos($get_kurikulum->nama_kurikulum, 'Pusat') !== false) {
			$kurikulum = 2021;
		} else {
			$kurikulum = 2013;
		}
		$all_kd = Kompetensi_dasar::where('kompetensi_id', $kompetensi_id)->where('mata_pelajaran_id', $id_mapel)->where('kelas_'.$kelas, 1)->where('aktif', 1)->where('kurikulum', $kurikulum)->orderByRaw('length(id_kompetensi) asc')->orderBy('id_kompetensi')->get();
		$bobot = '';
		$bentuk_penilaian = '';
		$bentuk_penilaian = Teknik_penilaian::where('kompetensi_id', $kompetensi_id)->get();
		if($kompetensi_id == 1){
			//$bentuk_penilaian = Teknik_penilaian::where('kompetensi_id', $kompetensi_id)->get();
		} else {
			//$bentuk_penilaian = Teknik_penilaian::all();
			$pembelajaran = Pembelajaran::where('rombongan_belajar_id', $rombongan_belajar->rombongan_belajar_id)
			->where('mata_pelajaran_id', $id_mapel)
			->first();
			if($pembelajaran){
				$find_bobot_keterampilan = Bobot_keterampilan::where('pembelajaran_id', $pembelajaran->pembelajaran_id)->where('metode_id', $metode_id)->first();
				if($find_bobot_keterampilan){
					$bobot = $find_bobot_keterampilan->bobot;
				}
			}
		}
		$placeholder = 'UH/UTS/UAS dll...';
		if($kompetensi_id == 2){
			$placeholder = 'Kinerja/Proyek/Portofolio';
		} elseif($kompetensi_id == 3){
			$placeholder = 'UH/UTS/UAS/Kinerja/Proyek/Portofolio';
		}
		$params = array(
			'rombongan_belajar'	=> $rombongan_belajar,
			'all_kd' => $all_kd,
			'bobot' => $bobot,
			'kompetensi_id' => $kompetensi_id,
			'id_rombel'	=> $id_rombel,
			'id_mapel'	=> $id_mapel,
			'kelas'	=> $kelas,
			'placeholder' => $placeholder,
			'bentuk_penilaian' => $bentuk_penilaian,
		);
		return view('perencanaan.get_kd_'.$kompetensi_id)->with($params);
	}
	public function get_form_p5bk(Request $request){
		$params = [
			'data_siswa' => Anggota_rombel::with(['siswa', 'nilai_budaya_kerja' => function($query){
				$query->whereHas('aspek_budaya_kerja', function($query){
					$query->where('rencana_budaya_kerja_id', request()->rencana_budaya_kerja_id);
				});
			}])->where('rombongan_belajar_id', $request->rombel_id)->order()->get(),
			'rencana_p5bk' => Aspek_budaya_kerja::with(['budaya_kerja.elemen_budaya_kerja'])->where('rencana_budaya_kerja_id', $request->rencana_budaya_kerja_id)->get(),
			'opsi_budaya_kerja' => Opsi_budaya_kerja::all(),
		];
		return view('penilaian.penilaian_p5bk')->with($params);
	}
	public function get_bobot($pembelajaran_id, $metode_id){
		$find_bobot = Bobot_keterampilan::where('pembelajaran_id', $pembelajaran_id)->where('metode_id', $metode_id)->first();
		if($find_bobot){
			echo $find_bobot->bobot;
		}
	}
	public function get_rencana(Request $request){
		$user = auth()->user();
		$pembelajaran_id = $request['pembelajaran_id'];
		$kompetensi_id = $request['kompetensi_id'];
		$get_rencana = Rencana_penilaian::where(function($query){
			$query->where('pembelajaran_id', request()->pembelajaran_id);
			$query->where('kompetensi_id', request()->kompetensi_id);
		})->get();
		if($get_rencana->count()){
			foreach($get_rencana as $rencana){
				$record= array();
				$record['value'] 	= $rencana->rencana_penilaian_id;
				$record['text'] 	= $rencana->nama_penilaian;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan rencana penilaian di mata pelajaran terpilih';
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_kompetensi(Request $request){
		$get_kompetensi = array(
			array('id' => 1, 'nama' => 'Pengetahuan'),
			array('id' => 2, 'nama' => 'Keterampilan'),
			array('id' => 3, 'nama' => 'Pusat Keunggulan'),
		);
		foreach($get_kompetensi as $kompetensi){
			$record= array();
			$record['value'] 	= $kompetensi['id'];
			$record['text'] 	= $kompetensi['nama'];
			$output['aspek_penilaian'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_kd_analisis(Request $request){
		$callback = function($query) use ($request){
			$query->where('pembelajaran_id', $request['pembelajaran_id']);
			$query->where('kompetensi_id', $request['kompetensi_id']);
		};
		$all_kd_nilai = Kd_nilai::whereHas('rencana_penilaian', $callback)->with(['rencana_penilaian' => $callback])->orderBy('id_kompetensi', 'asc')->get();
		if($all_kd_nilai->count()){
			foreach($all_kd_nilai as $kd_nilai){
				$record= array();
				$record['value'] 	= $kd_nilai->kompetensi_dasar_id;
				$record['text'] 	= $kd_nilai->id_kompetensi;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan penilaian di mata pelajaran terpilih';
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_analisis_individu(Request $request){
		$pembelajaran_id = $request['pembelajaran_id'];
		$kompetensi_id = $request['kompetensi_id'];
		$nama_kompetensi = ($kompetensi_id == 1) ? 'pengetahuan' : 'keterampilan';
		$pembelajaran = Pembelajaran::with('rombongan_belajar')->with(['kd_nilai_p' => function($q) use ($request, $nama_kompetensi){
			$q->with('kompetensi_dasar');
			$q->select(['kd_nilai.kompetensi_dasar_id', 'rencana_penilaian.kompetensi_id', 'rencana_penilaian.nama_penilaian']);
			$q->groupBy(['kd_nilai.kompetensi_dasar_id', 'rencana_penilaian.kompetensi_id', 'rencana_penilaian.nama_penilaian', 'rencana_penilaian.pembelajaran_id']);
			$q->orderBy('kd_nilai.kompetensi_dasar_id', 'asc');
		},'kd_nilai_k' => function($q) use ($request, $nama_kompetensi){
			$q->with('kompetensi_dasar');
			$q->select(['kd_nilai.kompetensi_dasar_id', 'rencana_penilaian.kompetensi_id', 'rencana_penilaian.nama_penilaian']);
			$q->groupBy(['kd_nilai.kompetensi_dasar_id', 'rencana_penilaian.kompetensi_id', 'rencana_penilaian.nama_penilaian', 'rencana_penilaian.pembelajaran_id']);
			$q->orderBy('kd_nilai.kompetensi_dasar_id', 'asc');
		}, 'one_anggota_rombel' => function($query) use ($request){
			$query->with('siswa');
			$query->with(['nilai_kd_pengetahuan' => function($query) use ($request){
				$query->where('pembelajaran_id', $request['pembelajaran_id']);
			}]);
			$query->with(['nilai_kd_keterampilan' => function($query) use ($request){
				$query->where('pembelajaran_id', $request['pembelajaran_id']);
			}]);
			$query->where('anggota_rombel_id', $request['siswa_id']);
		}])->find($pembelajaran_id);
		$params = array(
			'pembelajaran'		=> $pembelajaran,
			'with'	=> 'nilai_kd_'.$nama_kompetensi,
		);
		return view('monitoring.result_prestasi_individu')->with($params);
	}
	public function get_capaian_kompetensi(Request $request){
		$pembelajaran_id = $request['pembelajaran_id'];
		$kompetensi_id = $request['kompetensi_id'];
		$nama_kompetensi = ($kompetensi_id == 1) ? 'pengetahuan' : 'keterampilan';
		$pembelajaran = Pembelajaran::with('rombongan_belajar')->with(['kd_nilai_capaian' => function($q) use ($request, $nama_kompetensi){
			$q->with('kompetensi_dasar');
			$q->where('rencana_penilaian.kompetensi_id', $request['kompetensi_id']);
			$q->where('kompetensi_dasar_id', $request['kd']);
		}, 'anggota_rombel' => function($query){
			$query->where('jenis_rombel', 1);
		}, 'anggota_rombel.siswa', 'anggota_rombel.nilai_kd_'.$nama_kompetensi => function($query) use ($request){
			$query->where('pembelajaran_id', $request['pembelajaran_id']);
		}])->find($pembelajaran_id);
		$params = array(
			'pembelajaran'		=> $pembelajaran,
			'with'	=> 'nilai_kd_'.$nama_kompetensi,
		);
		return view('monitoring.result_capaian_kompetensi')->with($params);
	}
	public function get_analisis_remedial(Request $request){
		$pembelajaran_id = $request['pembelajaran_id'];
		$kompetensi_id = $request['kompetensi_id'];
		$nama_kompetensi = ($kompetensi_id == 1) ? 'p' : 'k';
		$with = ($kompetensi_id == 1) ? 'v_nilai_akhir_p' : 'v_nilai_akhir_k';
		$pembelajaran = Pembelajaran::with(['kd_nilai' => function($q) use ($request, $nama_kompetensi){
			$q->with('kompetensi_dasar');
			$q->select(['kd_nilai.kompetensi_dasar_id', 'rencana_penilaian.kompetensi_id']);
			$q->where('rencana_penilaian.kompetensi_id', $request['kompetensi_id']);
			$q->groupBy(['kd_nilai.kompetensi_dasar_id', 'rencana_penilaian.kompetensi_id', 'rencana_penilaian.pembelajaran_id']);
			$q->orderBy('kd_nilai.kompetensi_dasar_id', 'asc');
		}, 'anggota_rombel', 'anggota_rombel.siswa', 'anggota_rombel.v_nilai_akhir_'.$nama_kompetensi => function($query) use ($request){
			$query->where('pembelajaran_id', $request['pembelajaran_id']);
		}, 'anggota_rombel.nilai_remedial' => function($query) use ($request){
			$query->where('pembelajaran_id', $request['pembelajaran_id']);
			$query->where('kompetensi_id', $request['kompetensi_id']);
		}])->find($pembelajaran_id);
		$params = array(
			'pembelajaran'	=> $pembelajaran,
			'with'			=> $with,
			'kompetensi_id'	=> $kompetensi_id,
		);
		return view('monitoring.result_analisis_remedial')->with($params);
	}
	public function get_remedial(Request $request){
		$kompetensi_id = $request['aspek_penilaian'];
		$pembelajaran_id = $request['pembelajaran_id'];
		$rombongan_belajar_id = $request['rombel_id'];
		$pembelajaran = Pembelajaran::with('rombongan_belajar')->find($pembelajaran_id);
		$get_mapel_agama = CustomHelper::filter_agama_siswa($pembelajaran_id, $rombongan_belajar_id);
		if($kompetensi_id == 1){
			$with_1 = 'nilai_kd_pengetahuan';
			$with_2 = 'v_nilai_akhir_p';
		} elseif($kompetensi_id == 2){
			$with_1 = 'nilai_kd_keterampilan';
			$with_2 = 'v_nilai_akhir_k';
		} else {
			$with_1 = 'nilai_kd_pk';
			$with_2 = 'v_nilai_akhir_pk';
		}
		//$with_1 = ($kompetensi_id == 1) ? 'nilai_kd_pengetahuan' : 'nilai_kd_keterampilan';
		//$with_2 = ($kompetensi_id == 1) ? 'v_nilai_akhir_p' : 'v_nilai_akhir_k';
		if($get_mapel_agama){
			$callback = function($query) use ($get_mapel_agama) {
				$query->where('agama_id', $get_mapel_agama);
			};
			$all_siswa = Anggota_rombel::whereHas('siswa', $callback)->with(['siswa' => $callback])->with(['nilai_remedial' => function($q) use ($kompetensi_id, $pembelajaran_id){
				$q->where('kompetensi_id', $kompetensi_id);
				$q->where('pembelajaran_id', $pembelajaran_id);
			}])->with([$with_1 => function($q) use ($kompetensi_id, $pembelajaran_id){
				$callback = function($sq) use ($kompetensi_id, $pembelajaran_id){
					$sq->wherehas('rencana_penilaian', function($query) use ($kompetensi_id, $pembelajaran_id){
						$query->where('kompetensi_id', $kompetensi_id);
						$query->where('pembelajaran_id', $pembelajaran_id);
					});
				};
				$q->with(['kd_nilai'], $callback);
				$q->orderBy('kompetensi_dasar_id');
			}])->with([$with_2 => function($q) use ($kompetensi_id, $pembelajaran_id){
				$q->where('kompetensi_id', $kompetensi_id);
				$q->where('pembelajaran_id', $pembelajaran_id);
			}])->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		} else {
			$all_siswa = Anggota_rombel::with('siswa')->with(['nilai_remedial' => function($q) use ($kompetensi_id, $pembelajaran_id){
				$q->where('kompetensi_id', $kompetensi_id);
				$q->where('pembelajaran_id', $pembelajaran_id);
			}])->with([$with_1 => function($q) use ($kompetensi_id, $pembelajaran_id){
				$callback = function($sq) use ($kompetensi_id, $pembelajaran_id){
					$sq->wherehas('rencana_penilaian', function($query) use ($kompetensi_id, $pembelajaran_id){
						$query->where('kompetensi_id', $kompetensi_id);
						$query->where('pembelajaran_id', $pembelajaran_id);
					});
					$sq->select(['kompetensi_dasar_id', 'id_kompetensi']);
					$sq->groupBy(['kompetensi_dasar_id', 'id_kompetensi']);
					$sq->orderBy('id_kompetensi');
				};
				$q->wherehas('kd_nilai', $callback);
				$q->orderBy('kompetensi_dasar_id');
			}])->with([$with_2 => function($q) use ($kompetensi_id, $pembelajaran_id){
				$q->where('kompetensi_id', $kompetensi_id);
				$q->where('pembelajaran_id', $pembelajaran_id);
			}])->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		}
		$callback = function($q) use ($kompetensi_id, $pembelajaran_id){
			$q->with('pembelajaran');
			$q->where('kompetensi_id', $kompetensi_id);
			$q->where('pembelajaran_id', $pembelajaran_id);
			/*if($kompetensi_id == 1){
				$q->orWhere('kompetensi_id', 3);
				$q->where('pembelajaran_id', $pembelajaran_id);
			}*/
		};
		$all_kd = Kd_nilai::whereHas('rencana_penilaian', $callback)->with(['rencana_penilaian' => $callback, 'kompetensi_dasar'])->select(['kompetensi_dasar_id', 'id_kompetensi'])->groupBy(['kompetensi_dasar_id', 'id_kompetensi'])->orderBy('id_kompetensi')->get();
		//$all_kd = Kd_nilai::whereHas('rencana_penilaian', $callback)->with(['rencana_penilaian' => $callback, 'kompetensi_dasar'])->orderBy('id_kompetensi')->get();
		$params = array(
			'kkm'	=> CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm),
			'pembelajaran_id' => $pembelajaran->pembelajaran_id,
			'rombongan_belajar' => $pembelajaran->rombongan_belajar,
			'kompetensi_id'	=> $kompetensi_id,
			'all_siswa' => $all_siswa,
			'all_kd'	=> $all_kd,
			'with_1'	=> $with_1,
			'with_2'	=> $with_2,
		);
		
		return view('penilaian.penilaian_remedial')->with($params);
	}
	public function get_siswa(Request $request){
		$rombongan_belajar_id = $request['rombel_id'];
		$get_siswa = Anggota_rombel::with('siswa')->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		if($get_siswa->count()){
			foreach($get_siswa as $siswa){
				$record= array();
				$record['value'] 	= $siswa->anggota_rombel_id;
				$record['text'] 	= strtoupper($siswa->siswa->nama);
				$output['siswa'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan siswa di rombongan belajar terpilih';
			$output['siswa'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_anggota_ekskul(Request $request){
		/*
		->with(['rombongan_belajar' => function($query) use ($ekskul){
			$query->where('semester_id', $ekskul->semester_id);
			$query->where('jenis_rombel', 1);
		}])
		*/
		$rombongan_belajar_id = $request['kelas_ekskul'];
		$ekskul = Ekstrakurikuler::where('rombongan_belajar_id', $rombongan_belajar_id)->first();
		$get_siswa = Anggota_rombel::with('nilai_ekskul')->with(['siswa' => function($query) use ($request){
			$query->with(['kelas' => function($q) use ($request){
				$q->where('rombongan_belajar.semester_id', $request->semester_id);
				$q->where('jenis_rombel', 1);
			}]);
		}])->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		$all_rombel = Rombongan_belajar::whereIn('rombongan_belajar_id',function($query) use ($rombongan_belajar_id) {
			$query->select('rombongan_belajar_id')->from('anggota_rombel');
			$query->whereIn('peserta_didik_id',function($q) use ($rombongan_belajar_id) {
				$q->select('peserta_didik_id')->from('peserta_didik');
				$q->whereIn('peserta_didik_id',function($sq) use ($rombongan_belajar_id) {
					$sq->select('peserta_didik_id')->from('anggota_rombel');
					$sq->where('rombongan_belajar_id', $rombongan_belajar_id);
				});
			});
		})->where('semester_id', $request->semester_id)->where('jenis_rombel', 1)->orderBy('tingkat', 'ASC')->orderBy('kurikulum_id', 'ASC')->get();
		$params = array(
			'ekskul' 		=> $ekskul,
			'all_siswa' 	=> $get_siswa,
			'all_rombel'	=> $all_rombel,
		);
		return view('penilaian.penilaian_ekskul')->with($params);
	}
	public function filter_rombel_ekskul(Request $request){
		$rombongan_belajar_id = $request['rombel_reguler'];
		$rombongan_belajar_id_ekskul = $request['kelas_ekskul'];
		$callback = function($query) use ($rombongan_belajar_id){
			$query->with(['kelas' => function($q){
				$q->where('jenis_rombel', 1);
			}]);
			if($rombongan_belajar_id){
				$query->whereIn('peserta_didik_id', function($q) use ($rombongan_belajar_id){
					$q->select('peserta_didik_id')->from('anggota_rombel')->where('rombongan_belajar_id', $rombongan_belajar_id);
				});
			}
		};
		$get_siswa = Anggota_rombel::with('nilai_ekskul')->whereHas('siswa', $callback)->with(['siswa' => $callback])->where('rombongan_belajar_id', $rombongan_belajar_id_ekskul)->order()->get();
		$params = array(
			'all_siswa' 	=> $get_siswa,
			'rombongan_belajar_id'	=> $rombongan_belajar_id,
			'rombongan_belajar_id_ekskul'	=> $rombongan_belajar_id_ekskul
		);
		return view('penilaian.filter_rombel_ekskul')->with($params);
	}
	public function get_sikap(Request $request){
		$anggota_rombel_id = $request['siswa_id'];
		$guru_id = $request['guru_id'];
		$nilai_sikap = Nilai_sikap::with('ref_sikap')->where('anggota_rombel_id', $anggota_rombel_id)->where('guru_id', $guru_id)->get();
		$all_sikap = Sikap::whereHas('sikap')->with('sikap')->orderBy('sikap_id')->get();
		$params = array(
			'nilai_sikap' => $nilai_sikap,
			'all_sikap' => $all_sikap,
			'guru_id'	=> $guru_id,
		);
		return view('penilaian.penilaian_sikap')->with($params);
	}
	public function get_kd_nilai(Request $request){
		$user = auth()->user();
		$kompetensi_id = $request['kompetensi_id'];
		$rencana_penilaian_id = $request['rencana_id'];
		$rombongan_belajar_id = $request['rombel_id'];
		$pembelajaran_id = $request['pembelajaran_id'];
		$rencana_penilaian_id = $request['rencana_id'];
		$rombongan_belajar = Rombongan_belajar::find($rombongan_belajar_id);
		$all_kd_nilai = Rencana_penilaian::with(['kd_nilai','kd_nilai.kompetensi_dasar'])->find($rencana_penilaian_id);
		$get_mapel_agama = CustomHelper::filter_agama_siswa($pembelajaran_id, $rombongan_belajar_id);
		if($get_mapel_agama){
			$callback = function($query) use ($get_mapel_agama) {
				$query->where('agama_id', $get_mapel_agama);
			};
			$all_anggota = Anggota_rombel::whereHas('siswa', $callback)->with(['siswa' => $callback])->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		} else {
			$all_anggota = Anggota_rombel::with('siswa')->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		}
		$all_bobot = Rencana_penilaian::where('pembelajaran_id', $pembelajaran_id)->where('kompetensi_id', $kompetensi_id)->sum('bobot');
		$params = array(
			'rombongan_belajar'		=> $rombongan_belajar,
			'all_kd_nilai' 			=> $all_kd_nilai,
			'all_anggota' 			=> $all_anggota,
			'all_bobot'				=> $all_bobot,
			'kompetensi_id'			=> $kompetensi_id,
			'bobot'					=> $all_kd_nilai->bobot,
			'rencana_penilaian_id'	=> $rencana_penilaian_id,
		);
		return view('penilaian.get_kd_nilai')->with($params);
	}
	public function get_rekap_nilai(Request $request){
		$semester_id = $request->semester_id;
		$pembelajaran_id = $request->pembelajaran_id;
		$agama_id = CustomHelper::filter_agama_siswa($pembelajaran_id, $request->rombel_id);
		$pembelajaran = Pembelajaran::with(['rombongan_belajar.anggota_rombel' => function($query) use ($agama_id){
			$query->order();
			$callback = function($sq) use ($agama_id) {
				$sq->where('agama_id', $agama_id);
			};
		}, 'rombongan_belajar.anggota_rombel.nilai_akhir_pengetahuan' => function($query) use ($pembelajaran_id){
			$query->where('pembelajaran_id', $pembelajaran_id);
		}, 'rombongan_belajar.anggota_rombel.nilai_akhir_keterampilan' => function($query) use ($pembelajaran_id){
			$query->where('pembelajaran_id', $pembelajaran_id);
		}])->find($pembelajaran_id);
		$params['pembelajaran'] = $pembelajaran;
		$params['rasio_p'] = ($pembelajaran->rasio_p) ? $pembelajaran->rasio_p : 50;
		$params['rasio_k'] = ($pembelajaran->rasio_k) ? $pembelajaran->rasio_k : 50;
		return view('monitoring.result_rekap_nilai')->with($params);
	}
	public function get_analisis_nilai(Request $request){
		$kompetensi_id = $request['kompetensi_id'];
		$nilai_kd = ($kompetensi_id == 1) ? 'pembelajaran.anggota_rombel.nilai_kd_pengetahuan' : 'pembelajaran.anggota_rombel.nilai_kd_keterampilan';
		$rencana_penilaian = Rencana_penilaian::with(['pembelajaran', 'pembelajaran.rombongan_belajar', 'pembelajaran.anggota_rombel', $nilai_kd])->find($request['rencana_penilaian']);
		$params = array(
			'kompetensi_id'		=> $kompetensi_id,
			'rencana_penilaian'	=> $rencana_penilaian,
		);
		return view('monitoring.result_analisis_nilai')->with($params);
	}
	public function get_kurikulum(Request $request){
		$jurusan_id = $request['jurusan_id'];
		$get_kurikulum = Kurikulum::where('jurusan_id', $jurusan_id)->get();
		if($get_kurikulum){
			foreach($get_kurikulum as $kurikulum){
				$record= array();
				$record['value'] 	= $kurikulum->kurikulum_id;
				$record['text'] 	= $kurikulum->nama_kurikulum;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan kurikulum dibawah jurusan terpilih ('.$jurusan_id.')';
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_paket_tersimpan(Request $request){
		$kode_kompetensi = $request['kode_kompetensi'];
		$paket_ukk = Paket_ukk::where('kode_kompetensi', $kode_kompetensi);
		$params = array(
			'paket_ukk'		=> $paket_ukk,
		);
		return view('referensi.get_paket_tersimpan')->with($params);
	}
	public function get_paket_by_jurusan(Request $request){
		$jurusan_id = $request['jurusan_id'];
		$jurusan_sp = Jurusan_sp::where('jurusan_id', $jurusan_id)->first();
		$get_paket_ukk = Paket_ukk::where('jurusan_id', $jurusan_id)->where('status', 1)->get();
		if($get_paket_ukk->count()){
			foreach($get_paket_ukk as $paket_ukk){
				$record= array();
				$record['value'] 	= $paket_ukk->paket_ukk_id;
				$record['text'] 	= $paket_ukk->nama_paket_id;
				$output['result'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan paket UKK pada kompetensi keahlian '.$jurusan_sp->nama_jurusan_sp;
			$output['result'][] = $record;
		}
		echo json_encode($output);
	}
	public function get_ppk(Request $request){
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$find_ppk = Catatan_ppk::where('anggota_rombel_id', $anggota_rombel_id)->first();
		$ref_sikap = $request['sikap_id'];
		foreach($ref_sikap as $sikap){
			$all_nilai_sikap = Nilai_sikap::with('ref_sikap')->with(['guru' => function($query){
				$query->with('gelar_depan');
				$query->with('gelar_belakang');
			}])->where('anggota_rombel_id', $anggota_rombel_id)->where('sikap_id', $sikap)->get();
			if($all_nilai_sikap->count()){
				foreach($all_nilai_sikap as $nilai_sikap){
					$opsi_sikap = ($nilai_sikap->opsi_sikap == 1) ? 'positif' : 'negatif';
					$a[$nilai_sikap->sikap_id][CustomHelper::nama_guru($nilai_sikap->guru->gelar_depan, $nilai_sikap->guru->nama, $nilai_sikap->guru->gelar_belakang)][] = $nilai_sikap->ref_sikap->butir_sikap.' = '.$nilai_sikap->uraian_sikap.' ('.$opsi_sikap.')';
				}
			} else {
				$a= array();
				$output['sugesti_'.$sikap] = 'Nilai sikap belum di entri';
			}
			if($find_ppk){
				$get_nilai_karakter = Nilai_karakter::where('catatan_ppk_id', $find_ppk->catatan_ppk_id)->where('sikap_id', $sikap)->first();
				$output['capaian'] = ($find_ppk->exists) ? $find_ppk->capaian : NULL;
			} else {
				$output['capaian'] = NULL;
				$get_nilai_karakter = NULL;
			}
			$output['sikap_id_'.$sikap] = ($get_nilai_karakter) ? $get_nilai_karakter->deskripsi : '';
			if(isset($a[$sikap])){
				$nama_guru = '';
				foreach($a[$sikap] as $b=>$c){
					$nama_guru .= '<b>'.$b.'</b><br />';
					$nama_guru .= '<ul style="margin-bottom:0px;">';
					foreach($c as $d => $e){
						$nama_guru .= '<li>'.$e.'</li>';
					}
					$nama_guru .= '</ul>';
					$output['sugesti_'.$sikap] = $nama_guru;
				}
			}
		
		}
		echo json_encode($output);
	}
	public function get_prestasi(Request $request){
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$params = array(
			'all_prestasi'		=> Anggota_rombel::whereHas('prestasi')->with('prestasi')->find($anggota_rombel_id),
		);
		return view('laporan.prestasi_result')->with($params);
	}
	public function get_jurusan(Request $request){
		$rombel = Rombongan_belajar::find($request['rombel_id']);
		echo $rombel->jurusan_id;
	}
	public function get_siswa_ukk(Request $request){
		//$data_siswa = Anggota_rombel::with('siswa')->whereHas('nilai_ukk', $callback)->with(['nilai_ukk' => $callback])->order()->get();
		$internal = $request['internal'];
		$eksternal = $request['eksternal'];
		$paket_ukk_id = $request['paket_ukk_id'];
		$rombongan_belajar_id = $request['rombel_id'];
		$semester_id = $request['semester_id'];
		$rencana_ukk = Rencana_ukk::with('paket_ukk')->where('internal', $internal)->where('eksternal', $eksternal)->where('paket_ukk_id', $paket_ukk_id)->where('semester_id', $semester_id)->first(); 
		$data_siswa = Anggota_rombel::with('siswa')->with(['nilai_ukk' => function($query) use ($rencana_ukk){
			$query->whereHas('rencana_ukk', function($query) use ($rencana_ukk){
				if($rencana_ukk){
					$query->where('rencana_ukk_id',$rencana_ukk->rencana_ukk_id);
				}
			});
			/*$query->whereIn('rencana_ukk_id', function($q) use ($request){
				$q->select('rencana_ukk_id')->from('rencana_ukk')->where('paket_ukk_id', $request['paket_id']);
			});*/
		}])->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		$params = array(
			'data_siswa'	=> $data_siswa,
			'rencana_ukk'	=> $rencana_ukk,
			'rombongan_belajar_id'	=> $rombongan_belajar_id,
		);
		return view('perencanaan.siswa_ukk')->with($params);
	}
	public function get_siswa_nilai_ukk(Request $request){
		$rencana_ukk_id = $request['rencana_ukk_id'];
		$callback = function($query) use ($request){
			$query->whereIn('rencana_ukk_id', function($q) use ($request){
				$q->select('rencana_ukk_id')->from('rencana_ukk')->where('rencana_ukk_id', $request['rencana_ukk_id']);
			});
		};
		$data_siswa = Anggota_rombel::with('siswa')->whereHas('nilai_ukk', $callback)->with(['nilai_ukk' => $callback])->order()->get();
		$params = array(
			'data_siswa'	=> $data_siswa,
		);
		return view('penilaian.siswa_ukk')->with($params);
	}
	public function get_catatan_akademik(Request $request){
		$guru_id = $request['guru_id'];
		$rombongan_belajar = Rombongan_belajar::find($request['rombel_id']);
		$get_siswa = Anggota_rombel::with('siswa')->with('catatan_wali')->with(['nilai_rapor' => function($query){
			$query->with('pembelajaran');
			$query->limit(3);
		}])->where('rombongan_belajar_id', $request['rombel_id'])->order()->get();
		$params = array(
			'get_siswa'	=> $get_siswa,
			'open'	=> ($guru_id == $rombongan_belajar->guru_id) ? 1 : 0,
		);
		return view('laporan.waka.catatan_akademik_result')->with($params);
	}
	public function get_ketidakhadiran(Request $request){
		$guru_id = $request['guru_id'];
		$rombongan_belajar = Rombongan_belajar::find($request['rombel_id']);
		$get_siswa = Anggota_rombel::with('siswa')->with('kehadiran')->where('rombongan_belajar_id', $request['rombel_id'])->order()->get();
		$params = array(
			'get_siswa'	=> $get_siswa,
			'open'	=> ($guru_id == $rombongan_belajar->guru_id) ? 1 : 0,
			'rombongan_belajar_id' => $request['rombel_id'],
		);
		return view('laporan.waka.kehadiran_result')->with($params);
	}
	public function get_kenaikan(Request $request){
		$guru_id = $request['guru_id'];
		$cari_tingkat_akhir = Rombongan_belajar::where('sekolah_id', $request['sekolah_id'])->where('semester_id', $request['semester_id'])->where('tingkat', 13)->first();
		$rombongan_belajar = Rombongan_belajar::find($request['rombel_id']);
		$get_siswa = Anggota_rombel::with('siswa')->with('kenaikan')->where('rombongan_belajar_id', $request['rombel_id'])->order()->get();
		$rombel_4_tahun = (config('global.rombel_4_tahun')) ? unserialize(config('global.rombel_4_tahun')) : [];
		$params = array(
			'get_siswa'	=> $get_siswa,
			'open'	=> ($guru_id == $rombongan_belajar->guru_id) ? 1 : 0,
			'rombongan_belajar_id' => $request['rombel_id'],
			'cari_tingkat_akhir'	=> $cari_tingkat_akhir,
			'rombel_4_tahun' => $rombel_4_tahun,
		);
		return view('laporan.waka.kenaikan_result')->with($params);
	}
	public function get_nilai_ekskul(Request $request){
		$user = auth()->user();
		$guru_id = $request['guru_id'];
		$rombongan_belajar = Rombongan_belajar::find($request['rombel_id']);
		$callback = function($query) use ($request){
			$query->where('semester_id', $request['semester_id']);
			$query->whereIn('rombongan_belajar_id', function($q){
				$q->select('rombongan_belajar_id')->from('rombongan_belajar')->where('jenis_rombel', 51);
			});
			$query->with(['kelas_ekskul' => function($q){
				$q->with(['wali'  => function($sq){
					$sq->with('gelar_depan');
					$sq->with('gelar_belakang');
				}]);
			}]);
			$query->with(['nilai_ekskul' => function($query){
				$query->with('ekstrakurikuler');
			}]);
		};
			$get_siswa = Anggota_rombel::with('siswa')->whereHas('anggota_ekskul', $callback)->with(['anggota_ekskul' => $callback])->where('rombongan_belajar_id', $request['rombel_id'])->order()->get();
		$params = array(
			'get_siswa'	=> $get_siswa,
			'open'	=> ($guru_id == $rombongan_belajar->guru_id) ? 1 : 0,
			'user'	=> $user,
			'rombongan_belajar_id' => $request['rombel_id'],
		);
		return view('laporan.waka.nilai_ekskul_result')->with($params);
	}
	public function get_pkl(Request $request){
		$guru_id = $request['guru_id'];
		$rombongan_belajar = Rombongan_belajar::find($request['rombel_id']);
		$all_dudi = Dudi::with(['kecamatan' => function($query) use ($request){
			$query->with('get_kabupaten');
		}])->where('sekolah_id', $request['sekolah_id'])->orderBy('nama', 'asc')->get();
		$get_siswa = Anggota_rombel::with('siswa')->with('prakerin')->where('rombongan_belajar_id', $request['rombel_id'])->order()->get();
		$params = array(
			'get_siswa'	=> $get_siswa,
			'all_dudi'	=> $all_dudi,
			'open'	=> ($guru_id == $rombongan_belajar->guru_id) ? 1 : 0,
			'rombongan_belajar_id' => $request['rombel_id'],
		);
		return view('laporan.waka.pkl_result')->with($params);
	}
	public function get_rapor_uts(Request $request){
		$data_pembelajaran = Pembelajaran::with(['guru' => function($query){
			$query->with('gelar_depan');
			$query->with('gelar_belakang');
		}])->with(['rencana_penilaian' => function($query){
			$query->where('kompetensi_id', 1);
		}])->with('rapor_pts')->whereNotNull('kelompok_id')->whereNotNull('no_urut')->where('rombongan_belajar_id', $request['rombel_id'])->orderBy('kelompok_id', 'asc')->get();
		$params = array(
			'data_pembelajaran'	=> $data_pembelajaran,
			'rombongan_belajar_id' => $request['rombel_id'],
		);
		return view('laporan.waka.rapor_pts_result')->with($params);
	}
	public function get_rapor_semester(Request $request){
		$get_siswa = Anggota_rombel::with('siswa')->with('rombongan_belajar')->where('rombongan_belajar_id', $request['rombel_id'])->order()->get();
		$params = array(
			'get_siswa'	=> $get_siswa,
			'rombongan_belajar_id' => $request['rombel_id'],
		);
		return view('laporan.waka.rapor_semester_result')->with($params);
	}
	public function get_legger(Request $request){
		$rombongan_belajar = Rombongan_belajar::find($request['rombel_id']);
		$params = array(
			'rombongan_belajar' => $rombongan_belajar,
		);
		return view('laporan.waka.legger_result')->with($params);
	}
	public function get_next_rombel(Request $request){
		$rombongan_belajar_id = $request->rombongan_belajar_id;
		$now = Rombongan_belajar::find($rombongan_belajar_id);
		$all_rombel = Rombongan_belajar::where(function($query) use ($now){
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->where('tingkat', ($now->tingkat + 1));
		})->get();
		if($all_rombel->count()){
            foreach($all_rombel as $rombel){
                $record[$rombel->rombongan_belajar_id] 	= $rombel->nama;   
            }
	    $record[$rombongan_belajar_id] = 'Entry manual';		
	    $output = $record;
        } else {
            $record[$rombongan_belajar_id] = 'Entry manual';
            $output= $record;
        }
		$response = [
            'rombongan_belajar' => (object) $output,
        ];
        return response()->json($response);
	}
	public function get_single_rombel(Request $request){
		if($request->rombongan_belajar_id != 'a'){
			$response = Rombongan_belajar::select('nama', 'rombongan_belajar_id')->find($request->rombongan_belajar_id);
		} else {
			$response = [
				'nama' => NULL
			];
		}
		return response()->json($response);
	}
	public function get_nilai_us(Request $request){
		$pembelajaran = Pembelajaran::find($request->pembelajaran_id);
		$get_mapel_agama = CustomHelper::filter_agama_siswa($request->pembelajaran_id, $request->rombel_id);
		$callback = function($query) use ($get_mapel_agama) {
			if($get_mapel_agama){
				$query->where('agama_id', $get_mapel_agama);
			}
		};
		$get_siswa = Anggota_rombel::whereHas('siswa', $callback)->with(['siswa' => $callback])->with(['nilai_us' => function($query) use ($request){
			$query->where('pembelajaran_id', $request->pembelajaran_id);
		}])->where('rombongan_belajar_id', $request->rombel_id)->order()->get();
		return view('laporan.waka.nilai_us_result', compact('get_siswa', 'pembelajaran'));
	}
	public function get_nilai_un(Request $request){
		$pembelajaran = Pembelajaran::find($request->pembelajaran_id);
		$get_mapel_agama = CustomHelper::filter_agama_siswa($request->pembelajaran_id, $request->rombel_id);
		$callback = function($query) use ($get_mapel_agama) {
			if($get_mapel_agama){
				$query->where('agama_id', $get_mapel_agama);
			}
		};
		$get_siswa = Anggota_rombel::whereHas('siswa', $callback)->with(['siswa' => $callback])->with(['nilai_un' => function($query) use ($request){
			$query->where('pembelajaran_id', $request->pembelajaran_id);
		}])->where('rombongan_belajar_id', $request->rombel_id)->order()->get();
		return view('laporan.waka.nilai_un_result', compact('get_siswa', 'pembelajaran'));
	}
	public function get_wirausaha(Request $request){
		$kewirausahaan = Kewirausahaan::where('anggota_rombel_id', $request->anggota_rombel_id)->get();
		return view('laporan.kewirausahaan.result', compact('kewirausahaan'));
	}
	public function get_anggota_wirausaha(Request $request){
		$anggota_rombel = Anggota_rombel::find($request->anggota_rombel_id);
		$get_siswa = Anggota_rombel::with('siswa')->where('rombongan_belajar_id', $anggota_rombel->rombongan_belajar_id)->order()->get();
		if($get_siswa->count()){
			foreach($get_siswa as $siswa){
				if($siswa->anggota_rombel_id != $request->anggota_rombel_id){
					$record= array();
					$record['value'] 	= $siswa->anggota_rombel_id;
					$record['text'] 	= strtoupper($siswa->siswa->nama);
					$output['results'][] = $record;
				}
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan siswa di rombongan belajar terpilih';
			$output['results'][] = $record;
		}
		/*$anggota_wirausaha = Anggota_kewirausahaan::where('anggota_rombel_id', $request->anggota_rombel_id)->get();
		if($anggota_wirausaha->count()){
			foreach($anggota_wirausaha as $anggota){
				$record= array();
				$record['value'] 	= $anggota->anggota_rombel_id;
				$record['text'] 	= $anggota->kewirausahaan_id;
				$output['results'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan anggota kewirausahaan di kelas terpilih';
			$output['results'][] = $record;
		}*/
		return response()->json($output);
	}
	public function get_deskripsi_pk(Request $request){
		$kompetensi_id = $request['kompetensi_id'];
		$pembelajaran_id = $request['pembelajaran_id'];
		$rombongan_belajar_id = $request['rombel_id'];
		$pembelajaran = Pembelajaran::with('rombongan_belajar')->find($pembelajaran_id);
		$get_mapel_agama = CustomHelper::filter_agama_siswa($pembelajaran_id, $rombongan_belajar_id);
		if($get_mapel_agama){
			$callback = function($query) use ($get_mapel_agama) {
				$query->where('agama_id', $get_mapel_agama);
			};
			$all_siswa = Anggota_rombel::whereHas('siswa', $callback)->with(['siswa' => $callback])->with([
				'nilai_rapor_pk' => function($q) use ($pembelajaran_id){
					$q->where('pembelajaran_id', $pembelajaran_id);
				},
				'nilai_kd_pk' => function($q) use ($pembelajaran_id){
					$q->where('pembelajaran_id', $pembelajaran_id);
				},
				'deskripsi_mata_pelajaran' => function($q) use ($pembelajaran_id){
					$q->where('pembelajaran_id', $pembelajaran_id);
				},
			])->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		} else {
			$all_siswa = Anggota_rombel::with('siswa')->with([
				'nilai_rapor_pk' => function($q) use ($pembelajaran_id){
					$q->where('pembelajaran_id', $pembelajaran_id);
				},
				'nilai_kd_pk' => function($q) use ($pembelajaran_id){
					$q->where('pembelajaran_id', $pembelajaran_id);
				},
				'deskripsi_mata_pelajaran' => function($q) use ($pembelajaran_id){
					$q->where('pembelajaran_id', $pembelajaran_id);
				},
			])->where('rombongan_belajar_id', $rombongan_belajar_id)->order()->get();
		}
		$params = array(
			'kkm'	=> CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm),
			'pembelajaran_id' => $pembelajaran->pembelajaran_id,
			'rombongan_belajar' => $pembelajaran->rombongan_belajar,
			'kompetensi_id'	=> $kompetensi_id,
			'pembelajaran' => $pembelajaran,
			'all_siswa' => $all_siswa,
		);
		
		return view('penilaian.capaian_kompetensi')->with($params);
	}
	public function get_rencana_budaya_kerja(Request $request){
		$params = [
			'budaya_kerja' => Budaya_kerja::all(),
		];
		return view('perencanaan.form_budaya_kerja')->with($params);
	}
	public function get_rencana_p5bk(Request $request){
		$data = Rencana_budaya_kerja::where('rombongan_belajar_id', $request->rombel_id)->get();
		if($data->count()){
			foreach($data as $d){
				$record= array();
				$record['value'] 	= $d->rencana_budaya_kerja_id;
				$record['text'] 	= $d->nama;
				$output['results'][] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan Rencana P5bk di rombongan belajar terpilih';
			$output['results'][] = $record;
		}
		echo json_encode($output);
	}
	public function catatan_p5bk($anggota_rombel_id){
		$params = [
			'anggota_rombel_id' => $anggota_rombel_id,
			'title' => 'Tambah/Perbaharui Catatan Kegiatan P5BK',
			'catatan' => Catatan_budaya_kerja::where('anggota_rombel_id', $anggota_rombel_id)->first(),
		];
		return view('laporan.catatan_p5bk')->with($params);
	}
	public function simpan_catatan_p5bk(Request $request){
		$insert = Catatan_budaya_kerja::updateOrCreate(
			[
				'anggota_rombel_id' => request()->anggota_rombel_id,
			],
			[
				'sekolah_id' => session('sekolah_id'),
				'catatan' => request()->catatan,
				'last_sync' => now(),
			]
		);
		if($insert){
			$output['text'] = 'Berhasil menyimpan catatan kegiatan P5BK';
			$output['icon'] = 'success';
			$output['title'] = 'Berhasil';
		} else {
			$output['text'] = 'Tidak ada catatan kegiatan P5BK diproses';
			$output['icon'] = 'error';
			$output['title'] = 'Gagal';
		}
		return response()->json($output);
	}
}
