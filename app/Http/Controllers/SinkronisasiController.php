<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Role_user;
use CustomHelper;
use ServerProvider;
use Ixudra\Curl\Facades\Curl;
use App\Sekolah;
use App\Mst_wilayah;
use App\Guru;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Jurusan_sp;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use App\Mata_pelajaran;
use App\Mata_pelajaran_kurikulum;
use Chumper\Zipper\Facades\Zipper;
use App\Kd_json;
use App\Kompetensi_dasar;
use Artisan;
use Illuminate\Support\Facades\Schema;
use App\Setting;
use File;
use App\Jurusan;
use App\Kurikulum;
use App\Anggota_rombel;
use App\Dudi;
use App\Rombongan_belajar;
use App\Pembelajaran;
use App\Ekstrakurikuler;
use App\Nilai_akhir;
use App\Nilai_rapor_dapodik;
use App\Mou;
use App\Semester;
class SinkronisasiController extends Controller
{
    public function __construct()
    {
		$this->middleware('auth');
    }
	public function index(){
		Storage::disk('public')->delete('proses_sekolah.json');
		Storage::disk('public')->delete('proses_guru.json');
		Storage::disk('public')->delete('proses_rombongan_belajar.json');
		Storage::disk('public')->delete('proses_siswa_aktif.json');
		Storage::disk('public')->delete('proses_siswa_keluar.json');
		Storage::disk('public')->delete('proses_pembelajaran.json');
		Storage::disk('public')->delete('proses_ekskul.json');
		Storage::disk('public')->delete('proses_anggota_ekskul.json');
		Storage::disk('public')->delete('proses_jurusan.json');
		Storage::disk('public')->delete('proses_kurikulum.json');
		Storage::disk('public')->delete('proses_mata_pelajaran.json');
		Storage::disk('public')->delete('proses_mapel_kur.json');
		Storage::disk('public')->delete('proses_dudi.json');
		Storage::disk('public')->delete('proses_anggota_ekskul_by_rombel.json');
		Storage::disk('public')->delete('proses_sinkron_sekolah.json');
		Storage::disk('public')->delete('proses_kompetensi_dasar.json');
		$json_files = Storage::disk('public')->files('kd');
		Storage::disk('public')->delete($json_files);
		$user = auth()->user();
		$sekolah = Sekolah::find(session('sekolah_id'));
		$data_sync = array(
			'username_dapo'		=> $user->email,
			'password_dapo'		=> trim($user->password_dapo),
			'tahun_ajaran_id'	=> substr(session('semester_id'),0,4),
			'semester_id'		=> session('semester_id'),
			'sekolah_id'		=> session('sekolah_id'),
			'npsn'				=> $sekolah->npsn
		);
		$host_server_direktorat = ServerProvider::url_server_direktorat('status');
		$host_server_direktorat = 'http://103.40.55.242/erapor_server/api/status';
		//dd($host_server_direktorat);
		try {
			$curl = Curl::to($host_server_direktorat)
			->withHeader('x-api-key:'.session('sekolah_id'))
			->withOption('USERPWD', "admin:1234")
			->returnResponseObject()
			->withResponseHeaders()
			->withData($data_sync)
			->withTimeout(0)
			->post();
			$wilayah = Mst_wilayah::orderBy('updated_at', 'desc')->first();
			$data_sync = array(
				'username_dapo'		=> $user->email,
				'password_dapo'		=> trim($user->password_dapo),
				'tahun_ajaran_id'	=> substr(session('semester_id'),0,4),
				'semester_id'		=> session('semester_id'),
				'sekolah_id'		=> session('sekolah_id'),
				'npsn'				=> $sekolah->npsn,
				'last_update'		=> date('Y-m-d H:i:s', strtotime($wilayah->updated_at)),
			);
			$curl_kd = Curl::to(config('erapor.url_server').'referensi')->returnResponseObject()->get();
			$response = json_decode($curl->content);
			if($curl->status == 200){
				$response = $response;
			} elseif($curl->status == 403 || $curl->status == 401) {
				$response->message = $response->message;
				$response->error = TRUE;
			} else {
				$response = new \StdClass();
				$response->error = TRUE;
				$response->message = 'Server tidak merespon';
			}
			if($curl_kd->status == 200){
				$response_referensi = json_decode($curl_kd->content);
				$response->ref_kd = $response_referensi->ref_kd;
				$response->wilayah = $response_referensi->wilayah;
			} else {
				$response->ref_kd = 0;
				$response->wilayah = 0;
			}
		} catch (\Exception $e) {
			$response = new \StdClass();
			$response->error = TRUE;
			$response->message = $e->getMessage();
		}
		$data['dapodik'] = $response;
		$data['erapor'] = (object) $this->data_erapor();
		return view('sinkronisasi.index', $data);
    }
	public function diterima_dikelas($id){
		$user = auth()->user();
		//$host_server_direktorat = ServerProvider::url_server_direktorat('diterima-dikelas');
		$host_server_direktorat = 'http://103.40.55.242/erapor_server/api/diterima-dikelas';
		$curl = Curl::to($host_server_direktorat)
		->withHeader('x-api-key:'.session('sekolah_id'))
		->withOption('USERPWD', "admin:1234")
		->returnResponseObject()
        ->withData(['peserta_didik_id' => $id, 'sekolah_id' => session('sekolah_id')])
		->withTimeout(0)
        ->post();
		if($curl->status == 200){
			$response = json_decode($curl->content);
			if($response->data){
				echo $response->data->nama;
			}	
		} else {
			echo 0;
		}
	}
	private function data_erapor(){
		$user = auth()->user();
		$sekolah = Sekolah::where('sekolah_id', session('sekolah_id'))->count();
		$pd_aktif = Anggota_rombel::whereHas('siswa')->whereHas('rombongan_belajar', function($query){
			$query->where('jenis_rombel', 1);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
		})->count();
		$pd_keluar = Anggota_rombel::whereHas('rombongan_belajar', function($query){
			$query->where('jenis_rombel', 1);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
		})->onlyTrashed()->count();
		$anggota_ekskul = Anggota_rombel::whereHas('siswa')->whereHas('rombongan_belajar', function($query){
			$query->where('jenis_rombel', 51);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
		})->count();
		$dudi_count = Mou::where('sekolah_id', session('sekolah_id'))->count();
		$jurusan_count = Jurusan::count();
		$kurikulum_count = Kurikulum::count();
		$mata_pelajaran_count = Mata_pelajaran::count();
		$mata_pelajaran_kurikulum_count = Mata_pelajaran_kurikulum::count();
		$kompetensi_dasar_count = Kompetensi_dasar::withTrashed()->count();
		$rombel = Rombongan_belajar::where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->where('jenis_rombel', 1)->count();
		$wilayah_count = Mst_wilayah::count();
		$data_erapor = array(
			'get_sekolah_sinkron' => Sekolah::find(session('sekolah_id')),
			'sekolah_erapor' => $sekolah,
			'sekolah_sinkron' => $sekolah,
			'guru_erapor' => Guru::where('sekolah_id', session('sekolah_id'))->count(),
			'guru_sinkron' => Guru::where('sekolah_id', session('sekolah_id'))->whereNotNull('guru_id_dapodik')->count(),
 			'rombel_erapor' => $rombel,
			'rombel_sinkron' => $rombel,
			'siswa_erapor' => $pd_aktif,
			'siswa_sinkron' => $pd_aktif,
			'siswa_keluar_erapor' => $pd_keluar,
			'siswa_keluar_sinkron' => $pd_keluar,
			'pembelajaran_erapor' => Pembelajaran::where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->count(),
			'pembelajaran_sinkron' => Pembelajaran::where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->whereNotNull('pembelajaran_id_dapodik')->count(),
			'ekskul_erapor' => Ekstrakurikuler::where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->count(),
			'ekskul_sinkron' => Ekstrakurikuler::where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->whereNotNull('id_kelas_ekskul')->count(),
			'anggota_ekskul_erapor' => $anggota_ekskul,
			'anggota_ekskul_sinkron' => $anggota_ekskul,
			'dudi_erapor' => $dudi_count,
			'dudi_sinkron' => $dudi_count,
			'jurusan_erapor' => $jurusan_count,
			'jurusan_sinkron' => $jurusan_count,
			'kurikulum_erapor' => $kurikulum_count,
			'kurikulum_sinkron' => $kurikulum_count,
			'mata_pelajaran_erapor' => $mata_pelajaran_count,
			'mata_pelajaran_sinkron' => $mata_pelajaran_count,
			'mata_pelajaran_kurikulum_erapor' => $mata_pelajaran_kurikulum_count,
			'mata_pelajaran_kurikulum_sinkron' => $mata_pelajaran_kurikulum_count,
			'wilayah_erapor' => $wilayah_count,
			'wilayah_sinkron' => $wilayah_count,
			'kompetensi_dasar_erapor' => $kompetensi_dasar_count,
			'kompetensi_dasar_sinkron' => $kompetensi_dasar_count,
		);
		return $data_erapor;
	}
	public function ref_kd(){
		$data['title'] = 'Sinkronisasi Kompetensi Dasar';
		$data['terproses']	= Kompetensi_dasar::count();
		return view('sinkronisasi.kompetensi_dasar', $data);
	}
	public function proses_artisan(){
		$response = Artisan::call('kd:start');
	}
	public function jumlah_kd(){
		echo 'Jumlah Ref.KD terproses : '.Kompetensi_dasar::count();
	}
	public function proses_kd($file){
		Kd_json::create(['nama_file' => $file]);
		$json = Storage::disk('public')->get('kd_json/kd_json/'.$file);
		$response = json_decode($json);
		foreach($response->data as $obj){
			$create_kd = Kompetensi_dasar::updateOrCreate(
				['aspek' => $obj->aspek, 'mata_pelajaran_id' => $obj->mata_pelajaran_id, 'kompetensi_dasar' => $obj->kompetensi_dasar, 'kurikulum_id' => $obj->kurikulum_id, 'kelas' => $obj->kelas],
				['id_kompetensi' => $obj->id_kompetensi, 'id_kompetensi_nas' => $obj->id_kompetensi_nas, 'kompetensi_dasar_alias' => $obj->kompetensi_dasar_alias, 'aktif' => $obj->aktif, 'created_at' => $obj->created_at, 'updated_at' => $obj->updated_at, 'deleted_at' => $obj->deleted_at, 'last_sync' => $obj->last_sync]
			);
		}
		$flash['success'] = 'Berhasil sinkonisasi sejumlah '.count($response->data).' referensi KD';
		return redirect()->route('sinkronisasi_ref_kd')->with($flash);
	}
	public function kirim_nilai(){
		$user = auth()->user();
		$sekolah = Sekolah::find($user->sekolah_id);
		try {
			$param = array(
				'status' 	=> TRUE,
				'user' 		=> $user,
				'sekolah' 	=> $sekolah,
				'rombongan_belajar' => [
					[
						'tingkat' => 10,
						'jumlah'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 10);
							$query->where('jenis_rombel', 1);
						})->count(),
						'terbuka'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 10);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 0);
						})->count(), 
						'terkunci' => Rombongan_belajar::where(function($query){
							$query->where('semester_id', session('semester_id'));
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('tingkat', 10);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'nilai' => Nilai_akhir::whereHas('pembelajaran.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 10);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'terkirim' => Nilai_rapor_dapodik::whereHas('matev_rapor.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat_pendidikan_id', 10);
							$query->where('jenis_rombel', 1);
							$query->where('soft_delete', 0);
						})->count(),
					],
					[
						'tingkat' => 11,
						'jumlah'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 11);
							$query->where('jenis_rombel', 1);
						})->count(), 
						'terbuka'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 11);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 0);
						})->count(), 
						'terkunci' => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 11);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'nilai' => Nilai_akhir::whereHas('pembelajaran.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 11);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'terkirim' => Nilai_rapor_dapodik::whereHas('matev_rapor.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat_pendidikan_id', 11);
							$query->where('jenis_rombel', 1);
							$query->where('soft_delete', 0);
						})->count(),
					],
					[
						'tingkat' => 12,
						'jumlah'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 12);
							$query->where('jenis_rombel', 1);
						})->count(), 
						'terbuka'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 12);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 0);
						})->count(), 
						'terkunci' => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 12);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'nilai' => Nilai_akhir::whereHas('pembelajaran.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 12);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'terkirim' => Nilai_rapor_dapodik::whereHas('matev_rapor.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat_pendidikan_id', 12);
							$query->where('jenis_rombel', 1);
							$query->where('soft_delete', 0);
						})->count(),
					],
					[
						'tingkat' => 13,
						'jumlah'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 13);
							$query->where('jenis_rombel', 1);
						})->count(), 
						'terbuka'  => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 13);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 0);
						})->count(), 
						'terkunci' => Rombongan_belajar::where(function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 13);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'nilai' => Nilai_akhir::whereHas('pembelajaran.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat', 13);
							$query->where('jenis_rombel', 1);
							$query->where('kunci_nilai', 1);
						})->count(),
						'terkirim' => Nilai_rapor_dapodik::whereHas('matev_rapor.rombongan_belajar', function($query){
							$query->where('sekolah_id', session('sekolah_id'));
							$query->where('semester_id', session('semester_id'));
							$query->where('tingkat_pendidikan_id', 13);
							$query->where('jenis_rombel', 1);
							$query->where('soft_delete', 0);
						})->count(),
					],
				],
			);
		} catch (\Exception $e) {
			$param = array(
				'status' 	=> FALSE,
				'output'	=> 'Tidak dapat terkoneksi dengan database eRaporSMK v.4.x.x',
			);
    	}
		return view('sinkronisasi.kirim_nilai')->with($param);
	}
	public function proses_kirim_nilai($tingkat, $sekolah_id, $semester_id){
		Artisan::call('kirim:nilai', ['tingkat' => $tingkat, 'sekolah_id' => $sekolah_id, 'semester_id' => $semester_id]);
	}
	public function kirim_data(){
		$url_server = config('erapor.url_server').'sinkronisasi/status';
		$user = auth()->user();
		$sekolah = Sekolah::find(session('sekolah_id'));
		$response = Curl::to($url_server)->returnResponseObject()->get();
		$param = array(
			'user' 		=> $user,
			'sekolah' 	=> $sekolah,
			'status_sync'	=> ($response->status == 200) ? json_decode($response->content) : json_decode(json_encode(['server' => 0])),
		);
		return view('sinkronisasi.kirim_data')->with($param);
	}
	public function proses_sync(){
		$session_id = session()->getId();
		$user = auth()->user();
		$last_sync = Setting::where('key', 'last_sync')->first();
		$last_sync = $last_sync->value;
		$last_sync_date = date('Y-m-d', strtotime($last_sync));
		$last_sync_time = date('H:i:s', strtotime($last_sync));
		$url_server = config('erapor.url_server').'sinkronisasi/proses';
		$table_sync = config('erapor.table_sync');
		$i=1;
		$total = 0;
		$result = 0;
		$withData = array(
			'semester_id' => session('semester_id'),
			'sekolah_id' => session('sekolah_id'),
			'table' => 'sekolah',
			'json' => CustomHelper::prepare_send(json_encode(Sekolah::find(session('sekolah_id')))),
		);
		$kirim_data = Curl::to($url_server.'')->returnResponseObject()->withData($withData)->post();
		foreach($table_sync as $sync){
			if(Schema::hasTable($sync)){
				$query = DB::table($sync);
				if($sync == 'ref.kompetensi_dasar'){
					$query->whereIn('user_id', function($q){
						$q->select('user_id')->from('users');
					});
				} elseif (Schema::hasColumn($sync, 'last_sync')) {
					$query->where('last_sync', '>=', $last_sync);
				}
				if (Schema::hasColumn($sync, 'semester_id')){
					$query->where('semester_id', session('semester_id'));
				}
				if (Schema::hasColumn($sync, 'sekolah_id')){
					$query->where('sekolah_id', session('sekolah_id'));
				}
				$result = $query->count();
				if($result){
					$total++;
				}
			}
		}
		$i=0;
		$limit = 10000;
		foreach($table_sync as $sync){
			if(Schema::hasTable($sync)){
				$query = DB::table($sync);
				if($sync == 'ref.kompetensi_dasar'){
					$query->whereIn('user_id', function($q){
						$q->select('user_id')->from('users');
					});
				} elseif (Schema::hasColumn($sync, 'last_sync')) {
					$query->where('last_sync', '>=', $last_sync);
				}
				if (Schema::hasColumn($sync, 'semester_id')){
					$query->where('semester_id', session('semester_id'));
				}
				if (Schema::hasColumn($sync, 'sekolah_id')){
					$query->where('sekolah_id', session('sekolah_id'));
				}
				$count = $query->count();
				if($count){
					if($count > $limit){
						for ($counter = 0; $counter <= $count; $counter += $limit) {
							$query = DB::table($sync);
							if($sync == 'ref.kompetensi_dasar'){
								$query->whereIn('user_id', function($q){
									$q->select('user_id')->from('users');
								});
							} elseif (Schema::hasColumn($sync, 'last_sync')) {
								$query->where('last_sync', '>=', $last_sync);
							}
							if (Schema::hasColumn($sync, 'semester_id')){
								$query->where('semester_id', session('semester_id'));
							}
							if (Schema::hasColumn($sync, 'sekolah_id')){
								$query->where('sekolah_id', session('sekolah_id'));
							}
							$query->offset($counter)->limit($limit);
							$result = $query->get();
							if($result){
								$withData = array(
									'semester_id' => session('semester_id'),
									'sekolah_id' => session('sekolah_id'),
									'table' => $sync,
									'json' => CustomHelper::prepare_send(json_encode($result)),
								);
								$kirim_data = Curl::to($url_server.'')->returnResponseObject()->withData($withData)->post();
								if($kirim_data->status == 200){
									$response = $kirim_data->content;
								} else {
									$response = 'Server error';
								}
								$percent = intval($i/ $total * 100);
								$arr_content['query'] = 'kirim';
								$arr_content['percent'] = $percent;
								$arr_content['message'] = "Mengirim data " .$sync;
								$arr_content['total'] = $total;
								$arr_content['no'] = $i;
								$arr_content['response'] = $kirim_data->content;
								Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
							}
						}
						$i++;
					} else {
						$query = DB::table($sync);
						if($sync == 'ref.kompetensi_dasar'){
							$query->whereIn('user_id', function($q){
								$q->select('user_id')->from('users');
							});
						} elseif (Schema::hasColumn($sync, 'last_sync')) {
							$query->where('last_sync', '>=', $last_sync);
						}
						if (Schema::hasColumn($sync, 'semester_id')){
							$query->where('semester_id', session('semester_id'));
						}
						if (Schema::hasColumn($sync, 'sekolah_id')){
							$query->where('sekolah_id', session('sekolah_id'));
						}
						$result = $query->get();
						if($result){
							$withData = array(
								'semester_id' => session('semester_id'),
								'sekolah_id' => session('sekolah_id'),
								'table' => $sync,
								'json' => CustomHelper::prepare_send(json_encode($result)),
							);
							$kirim_data = Curl::to($url_server.'')->returnResponseObject()->withData($withData)->post();
							if($kirim_data->status == 200){
								$response = $kirim_data->content;
							} else {
								$response = 'Server error';
							}
							$percent = intval($i/ $total * 100);
							$arr_content['query'] = 'kirim';
							$arr_content['percent'] = $percent;
							$arr_content['message'] = "Mengirim data " .$sync;
							$arr_content['total'] = $total;
							$arr_content['no'] = $i;
							$arr_content['response'] = $kirim_data->content;
							Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
							$i++;
						}
					}
				}
			}
		}
		$update_last_sync = Setting::where('key', 'last_sync')->update(['value' => date('Y-m-d H:i:s')]);
		//$update_last_sync = 1;
		if($update_last_sync){
			$record['title'] 	= 'Sukses';
			$record['text'] 	= 'Sinkronisasi Selesai';
			$record['type']		= 'success';
		} else {
			$record['value'] 	= 'Gagal';
			$record['text'] 	= 'Sinkronisasi Gagal! Silahkan dicoba beberapa saat lagi';
			$record['type']		= 'error';
		}
		$output['result'] = $record;
		echo json_encode($record);
	}
	public function proses_artisan_sync($server, $data, $aksi, $satuan = NULL){
		$user = auth()->user();
		$sekolah = Sekolah::find(session('sekolah_id'));
		$last_sync = Setting::where('key', 'last_sync')->first();
		$last_sync = $last_sync->value;
		$updated_at = $last_sync;
		if($aksi == 'jurusan'){
			$query = Jurusan::orderBy('last_sync', 'DESC')->first();
			if($query){
				$last_sync = $query->last_sync;
				$updated_at = $query->updated_at;
			}
		} elseif($aksi == 'kurikulum'){
			$query = Kurikulum::orderBy('last_sync', 'DESC')->first();
			if($query){
				$last_sync = $query->last_sync;
				$updated_at = date('Y-m-d H:i:s',$query->updated_at->getTimestamp());
			}
		} elseif($aksi == 'mata-pelajaran'){
			$query = Mata_pelajaran::orderBy('last_sync', 'DESC')->first();
			if($query){
				$last_sync = $query->last_sync;
				$updated_at = date('Y-m-d H:i:s',$query->updated_at->getTimestamp());
			}
		} elseif($aksi == 'mata-pelajaran-kurikulum'){
			$query = Mata_pelajaran_kurikulum::orderBy('last_sync', 'DESC')->first();
			if($query){
				$last_sync = $query->last_sync;
				$updated_at = date('Y-m-d H:i:s',$query->updated_at->getTimestamp());
			}
		} elseif($aksi == 'wilayah'){
			$wilayah = Mst_wilayah::orderBy('updated_at', 'desc')->first();
			$updated_at = date('Y-m-d H:i:s',strtotime('2016-12-31 00:00:01'));
		} elseif($aksi == 'count_kd'){
			$updated_at = Rombongan_belajar::select('kurikulum_id')->where(function($query){
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
			})->groupBy('kurikulum_id')->get()->toArray();
		}
		$host_server_direktorat = ServerProvider::url_server_direktorat($aksi);
		$host_server_direktorat = 'http://103.40.55.242/erapor_server/api/'.$aksi;
		$host_erapor_server = ServerProvider::url_server_erapor($aksi);
		$data_sync = array(
			'username_dapo'		=> $user->email,
			'password_dapo'		=> trim($user->password_dapo),
			'tahun_ajaran_id'	=> substr(session('semester_id'),0,4),
			'semester_id'		=> session('semester_id'),
			'sekolah_id'		=> session('sekolah_id'),
			'npsn'				=> $sekolah->npsn,
			'server'			=> ($server == 'erapor_server') ? $host_server_direktorat : $host_erapor_server,
			'aksi'				=> $aksi,
			'last_sync'			=> $last_sync,
			'updated_at'		=> $updated_at,
			'satuan'			=> $satuan
		);
		Artisan::call('sinkronisasi:ambildata',$data_sync);
	}
	public function hitung_data($data){
		$file = 'proses_'.$data.'.json';
		if(!Storage::disk('public')->exists($file)){
			$record['table'] = $data;
			$record['jumlah'] = 0;
			$record['inserted'] = 0;
			if($data != 'migrasi'){
				Storage::disk('public')->put($file, json_encode($record));
			}
		}
		Artisan::call('sinkronisasi:hitungdata', ['file' => 'proses_'.$data.'.json']);
		echo Artisan::output();
	}
	public function debug(){
		$user = auth()->user();
		$sekolah = Sekolah::find(session('sekolah_id'));
		$aksi = 'rombongan_belajar_sync';
		$host_server_direktorat = ServerProvider::url_server_direktorat($aksi);
		$host_server_direktorat = 'http://103.40.55.242/erapor_server/api/'.$aksi;
		$data_sync = array(
			'username_dapo'		=> $user->email,
			'password_dapo'		=> trim($user->password_dapo),
			'tahun_ajaran_id'	=> substr(session('semester_id'),0,4),
			'semester_id'		=> session('semester_id'),
			'sekolah_id'		=> session('sekolah_id'),
			'npsn'				=> $sekolah->npsn,
			'server'			=> $host_server_direktorat,
			'aksi'				=> $aksi,
			'updated_at'		=> date('Y-m-d H:i:s'),
		);
		Artisan::call('sinkronisasi:ambildata',$data_sync);
	}
	public function ambil_data(){
		$user = auth()->user();
		$sekolah = Sekolah::find(session('sekolah_id'));
		$last_sync = Setting::where('key', 'last_sync')->first();
		$last_sync = $last_sync->value;
		$url_server = ServerProvider::url_server_erapor('ambil_data');
		$data_sync = array(
			'sekolah_id'		=> session('sekolah_id'),
			'npsn'				=> $sekolah->npsn,
			'tahun_ajaran_id'	=> substr(session('semester_id'),0,4),
			'semester_id'		=> session('semester_id'),
			'last_sync'			=> $last_sync,
			'server'			=> $url_server,
			'table'				=> 'ref_sekolah',
			'next'				=> 'jurusan_sp',
		);
		$total = 1000;
		$i = 10;
		$percent = intval($i/ $total * 100);
		$arr_content['query'] = 'ambil';
		$arr_content['percent'] = $percent;
		$arr_content['message'] = "Mempersiapkan pengambilan data";
		$arr_content['total'] = $total;
		$arr_content['response'] = "";
		Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
		Artisan::call('sinkronisasi:ambildataerapor',$data_sync);
		$record['title'] 	= 'Sukses';
		$record['text'] 	= 'Sinkronisasi Selesai';
		$record['type']		= 'success';
		$output['result'] = $record;
		echo json_encode($record);
		Storage::disk('public')->delete('sinkronisasi.txt');
	}
	public function erapor_lama(){
		$user = auth()->user();
		$sekolah = Sekolah::find(session('sekolah_id'));
		Storage::disk('public')->delete('proses_migrasi.json');
		try {
        	DB::connection('erapor4')->getPdo();
			$param = array(
				'status' 	=> TRUE,
				'output'	=> 'Migrasi eRaporSMK v.4.x.x ke eRaporSMK v.5.0.0 siap dilakukan!',
				'sekolah' => $sekolah,
				'all_table'	=> [
					[
						'name' => 'jurusan_sp',
						'jumlah' => DB::connection('erapor4')->table('jurusan_sp')->where('sekolah_id', session('sekolah_id'))->count(),
					],
					[
						'name' => 'ref_guru', 
						'jumlah' => DB::connection('erapor4')->table('ref_guru')->where('sekolah_id', session('sekolah_id'))->count(),
					],
					[
						'name' => 'rombongan_belajar', 
						'jumlah' => DB::connection('erapor4')->table('rombongan_belajar')->where('sekolah_id', session('sekolah_id'))->count(),
					],
					[
						'name' => 'ekstrakurikuler', 
						'jumlah' => DB::connection('erapor4')->table('ekstrakurikuler')->where('ekstrakurikuler.sekolah_id', session('sekolah_id'))
						->join('ref_guru', 'ref_guru.guru_id', 'ekstrakurikuler.guru_id')
						->join('ref_semester', 'ref_semester.id', 'ekstrakurikuler.semester_id')
						->select(['ekstrakurikuler.rombongan_belajar_id', 'ekstrakurikuler.nama_ekskul', 'ekstrakurikuler.alamat_ekskul', 'ekstrakurikuler.id_kelas_ekskul', 'ref_guru.guru_id_dapodik', 'ref_semester.tahun', 'ref_semester.semester'])
						->count(),
					],
					[
						'name' => 'pembelajaran', 
						'jumlah' => DB::connection('erapor4')->table('pembelajaran')->where('sekolah_id', session('sekolah_id'))->count(),
					],
					[
						'name' => 'ref_siswa', 
						'jumlah' => DB::connection('erapor4')->table('ref_siswa')->where('sekolah_id', session('sekolah_id'))->count(),
					],
					[
						'name' => 'anggota_rombel', 
						'jumlah' => DB::connection('erapor4')->table('anggota_rombel')->select('anggota_rombel_id_dapodik')->where('anggota_rombel.sekolah_id', session('sekolah_id'))
						->join('rombongan_belajar', 'rombongan_belajar.rombongan_belajar_id', 'anggota_rombel.rombongan_belajar_id')
						->join('ref_semester', 'ref_semester.id', 'anggota_rombel.semester_id')->groupBy('anggota_rombel_id_dapodik')->get()->count(),
					],
					[
						'name' => 'teknik_penilaian', 
						'jumlah' => DB::connection('erapor4')->table('teknik_penilaian')->count(),
					],
					[
						'name' => 'bobot_keterampilan', 
						'jumlah' => DB::connection('erapor4')->table('bobot_keterampilan')->where('bobot_keterampilan.sekolah_id', session('sekolah_id'))
						->join('pembelajaran', function ($join) {
							$join->on('pembelajaran.semester_id', 'bobot_keterampilan.semester_id');
							$join->on('pembelajaran.mata_pelajaran_id', 'bobot_keterampilan.mata_pelajaran_id');
							$join->on('pembelajaran.rombongan_belajar_id', 'bobot_keterampilan.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'rencana_penilaian', 
						'jumlah' => DB::connection('erapor4')->table('rencana_penilaian')->where('rencana_penilaian.sekolah_id', session('sekolah_id'))
						->join('ref_semester', 'ref_semester.id', 'rencana_penilaian.semester_id')
						->join('pembelajaran', function ($join) {
							$join->on('pembelajaran.semester_id', 'rencana_penilaian.semester_id');
							$join->on('pembelajaran.mata_pelajaran_id', 'rencana_penilaian.mata_pelajaran_id');
							$join->on('pembelajaran.rombongan_belajar_id', 'rencana_penilaian.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'kd_nilai', 
						'jumlah' => DB::connection('erapor4')->table('kd_nilai')->where('kd_nilai.sekolah_id', session('sekolah_id'))
						->join('ref_kompetensi_dasar', 'ref_kompetensi_dasar.id', 'kd_nilai.kd_id')->count(),
					],
					[
						'name' => 'nilai', 
						'jumlah' => DB::connection('erapor4')->table('nilai')->where('nilai.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'nilai.semester_id');
							$join->on('anggota_rombel.siswa_id', 'nilai.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'nilai.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'nilai_akhir', 
						'jumlah' => DB::connection('erapor4')->table('nilai_akhir')->where('nilai_akhir.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'nilai_akhir.semester_id');
							$join->on('anggota_rombel.siswa_id', 'nilai_akhir.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'nilai_akhir.rombongan_belajar_id');
						})
						->join('pembelajaran', function ($join) {
							$join->on('pembelajaran.rombongan_belajar_id', 'nilai_akhir.rombongan_belajar_id');
							$join->on('pembelajaran.mata_pelajaran_id', 'nilai_akhir.mata_pelajaran_id');
						})->count(),
					],
					[
						'name' => 'nilai_rapor', 
						'jumlah' => DB::connection('erapor4')->table('nilai_rapor')->where('nilai_rapor.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'nilai_rapor.semester_id');
							$join->on('anggota_rombel.siswa_id', 'nilai_rapor.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'nilai_rapor.rombongan_belajar_id');
						})
						->join('pembelajaran', function ($join) {
							$join->on('pembelajaran.rombongan_belajar_id', 'nilai_rapor.rombongan_belajar_id');
							$join->on('pembelajaran.mata_pelajaran_id', 'nilai_rapor.mata_pelajaran_id');
						})->count(),
					],
					[
						'name' => 'absen', 
						'jumlah' => DB::connection('erapor4')->table('absen')->where('absen.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'absen.semester_id');
							$join->on('anggota_rombel.siswa_id', 'absen.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'absen.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'catatan_ppk', 
						'jumlah' => DB::connection('erapor4')->table('catatan_ppk')->where('catatan_ppk.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'catatan_ppk.semester_id');
							$join->on('anggota_rombel.siswa_id', 'catatan_ppk.siswa_id');
						})->count(),
					],
					[
						'name' => 'catatan_wali', 
						'jumlah' => DB::connection('erapor4')->table('catatan_wali')->where('catatan_wali.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'catatan_wali.semester_id');
							$join->on('anggota_rombel.siswa_id', 'catatan_wali.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'catatan_wali.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'deskripsi_mata_pelajaran', 
						'jumlah' => DB::connection('erapor4')->table('deskripsi_mata_pelajaran')->where('deskripsi_mata_pelajaran.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'deskripsi_mata_pelajaran.semester_id');
							$join->on('anggota_rombel.siswa_id', 'deskripsi_mata_pelajaran.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'deskripsi_mata_pelajaran.rombongan_belajar_id');
						})->join('pembelajaran', function ($join) {
							$join->on('pembelajaran.semester_id', 'deskripsi_mata_pelajaran.semester_id');
							$join->on('pembelajaran.mata_pelajaran_id', 'deskripsi_mata_pelajaran.mata_pelajaran_id');
							$join->on('pembelajaran.rombongan_belajar_id', 'deskripsi_mata_pelajaran.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'deskripsi_sikap', 
						'jumlah' => DB::connection('erapor4')->table('deskripsi_sikap')->where('deskripsi_sikap.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'deskripsi_sikap.semester_id');
							$join->on('anggota_rombel.siswa_id', 'deskripsi_sikap.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'deskripsi_sikap.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'kenaikan_kelas', 
						'jumlah' => DB::connection('erapor4')->table('kenaikan_kelas')->where('kenaikan_kelas.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', 'anggota_rombel.anggota_rombel_id', 'kenaikan_kelas.anggota_rombel_id')->count(),
					],
					[
						'name' => 'nilai_ekstrakurikuler', 
						'jumlah' => DB::connection('erapor4')->table('nilai_ekstrakurikuler')->where('nilai_ekstrakurikuler.sekolah_id', session('sekolah_id'))
						->join('ekstrakurikuler', 'ekstrakurikuler.ekstrakurikuler_id', 'nilai_ekstrakurikuler.ekstrakurikuler_id')
						->join('rombongan_belajar', 'ekstrakurikuler.rombongan_belajar_id', 'rombongan_belajar.rombongan_belajar_id')
						->join('anggota_rombel', 'rombongan_belajar.rombongan_belajar_id', 'anggota_rombel.rombongan_belajar_id')->count(),
					],
					[
						'name' => 'ref_sikap', 
						'jumlah' => DB::connection('erapor4')->table('ref_sikap')->count(),
					],
					[
						'name' => 'nilai_karakter', 
						'jumlah' => DB::connection('erapor4')->table('nilai_karakter')->where('sekolah_id', session('sekolah_id'))->count(),
					],
					[
						'name' => 'nilai_sikap', 
						'jumlah' => DB::connection('erapor4')->table('nilai_sikap')->where('nilai_sikap.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'nilai_sikap.semester_id');
							$join->on('anggota_rombel.siswa_id', 'nilai_sikap.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'nilai_sikap.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'prakerin', 
						'jumlah' => DB::connection('erapor4')->table('prakerin')->where('prakerin.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'prakerin.semester_id');
							$join->on('anggota_rombel.siswa_id', 'prakerin.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'prakerin.rombongan_belajar_id');
						})->count(),
					],
					[
						'name' => 'prestasi',
						'jumlah' => DB::connection('erapor4')->table('prestasi')->where('prestasi.sekolah_id', session('sekolah_id'))
						->join('anggota_rombel', function ($join) {
							$join->on('anggota_rombel.semester_id', 'prestasi.semester_id');
							$join->on('anggota_rombel.siswa_id', 'prestasi.siswa_id');
							$join->on('anggota_rombel.rombongan_belajar_id', 'prestasi.rombongan_belajar_id');
						})->count(),
					],
				],
			);
    	} catch (\Exception $e) {
			$param = array(
				'status' 	=> FALSE,
				'output'	=> 'Tidak dapat terkoneksi dengan database eRaporSMK v.4.x.x',
			);
    	}
		return view('sinkronisasi.erapor_lama')->with($param);
	}
	public function proses_erapor_lama($table){
		$user = auth()->user();
		Artisan::call('migrasi:start', ['sekolah_id' => session('sekolah_id'), 'query' => $table]);
	}
	public function anggota_by_rombel($rombongan_belajar_id){
		$server = 'erapor_server';
		$data = 'Anggota Ekskul';
		$aksi = 'anggota_ekskul_by_rombel';
		$user = auth()->user();
		$sekolah = Sekolah::find(session('sekolah_id'));
		$last_sync = Setting::where('key', 'last_sync')->first();
		$last_sync = $last_sync->value;
		$updated_at = $last_sync;
		$host_server_direktorat = ServerProvider::url_server_direktorat($aksi);
		$host_server_direktorat = 'http://103.40.55.242/erapor_server/api/'.$aksi;
		$host_erapor_server = ServerProvider::url_server_erapor($aksi);
		$data_sync = array(
			'username_dapo'		=> $user->email,
			'password_dapo'		=> trim($user->password_dapo),
			'tahun_ajaran_id'	=> substr(session('semester_id'),0,4),
			'semester_id'		=> session('semester_id'),
			'sekolah_id'		=> session('sekolah_id'),
			'npsn'				=> $sekolah->npsn,
			'server'			=> ($server == 'erapor_server') ? $host_server_direktorat : $host_erapor_server,
			'aksi'				=> $aksi,
			'last_sync'			=> $last_sync,
			'updated_at'		=> $updated_at,
			'satuan'			=> $rombongan_belajar_id
		);
		Artisan::call('sinkronisasi:ambildata',$data_sync);
	}
}
