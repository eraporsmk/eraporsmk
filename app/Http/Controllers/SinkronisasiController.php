<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Role_user;
use CustomHelper;
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
		Storage::disk('public')->delete('sinkronisasi.txt');
		$user = auth()->user();
		$sekolah = Sekolah::find($user->sekolah_id);
		$semester = CustomHelper::get_ta();
		$data_sync = array(
			'username_dapo'		=> $user->email,
			'password_dapo'		=> trim($user->password_dapo),
			'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
			'semester_id'		=> $semester->semester_id,
			'sekolah_id'		=> $user->sekolah_id,
			'npsn'				=> $sekolah->npsn
		);
		$host_server_direktorat = CustomHelper::url_server_direktorat('status');
		$curl = Curl::to($host_server_direktorat)
		->withHeader('x-api-key:'.$user->sekolah_id)
		->withOption('USERPWD', "admin:1234")
		->returnResponseObject()
        ->withData($data_sync)
        ->post();
		$host_erapor_server = CustomHelper::url_server_erapor('count_kd');
		$response_dashboard = Curl::to($host_erapor_server)
		->returnResponseObject()
        ->withData($data_sync)
        ->post();
		$response = json_decode($curl->content);
		//echo $host_server_direktorat;
		//dd($curl);
		if($curl->status == 200){
			$response = $response;
		} elseif($curl->status == 403 || $curl->status == 401) {
			$response->message = $response->error;
			$response->error = TRUE;
		} else {
			$response->error = TRUE;
			$response->message = 'Server tidak merespon';
		}
		if($response_dashboard->status == 200){
			$response_dashboard = json_decode($response_dashboard->content);
			$response->ref_kd = $response_dashboard->dapodik;
		} else {
			$response->ref_kd = 0;
		}
		$data['semester'] = $semester;
		$data['dapodik'] = $response;
		$data['erapor'] = (object) $this->data_erapor();
		return view('sinkronisasi.index', $data);
    }
	private function data_erapor(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		$sekolah = Sekolah::where('sekolah_id', '=', $user->sekolah_id)->count();
		$pd_aktif = Anggota_rombel::whereHas('siswa')->whereHas('rombongan_belajar', function($query) use ($user, $semester){
			$query->where('jenis_rombel', '=', 1);
			$query->where('sekolah_id', '=', $user->sekolah_id);
			$query->where('semester_id', '=', $semester->semester_id);
		})->count();
		$pd_keluar = Anggota_rombel::whereHas('siswa')->whereHas('rombongan_belajar', function($query) use ($user, $semester){
			$query->where('jenis_rombel', '=', 1);
			$query->where('sekolah_id', '=', $user->sekolah_id);
			$query->where('semester_id', '=', $semester->semester_id);
		})->onlyTrashed()->count();
		$anggota_ekskul = Anggota_rombel::whereHas('siswa')->whereHas('rombongan_belajar', function($query) use ($user, $semester){
			$query->where('jenis_rombel', '=', 51);
			$query->where('sekolah_id', '=', $user->sekolah_id);
			$query->where('semester_id', '=', $semester->semester_id);
		})->count();
		$dudi_count = Dudi::where('sekolah_id', '=', $user->sekolah_id)->count();
		$jurusan_count = Jurusan::count();
		$kurikulum_count = Kurikulum::count();
		$mata_pelajaran_count = Mata_pelajaran::count();
		$mata_pelajaran_kurikulum_count = Mata_pelajaran_kurikulum::count();
		$data_erapor = array(
			'get_sekolah_sinkron' => Sekolah::find($user->sekolah_id),
			'sekolah_erapor' => $sekolah,
			'sekolah_sinkron' => $sekolah,
			'guru_erapor' => Guru::where('sekolah_id', '=', $user->sekolah_id)->count(),
			'guru_sinkron' => Guru::where('sekolah_id', '=', $user->sekolah_id)->whereNotNull('guru_id_dapodik')->count(),
 			'rombel_erapor' => Rombongan_belajar::where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id)->where('jenis_rombel', '=', 1)->count(),
			'rombel_sinkron' => Rombongan_belajar::where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id)->where('jenis_rombel', '=', 1)->count(),
			'siswa_erapor' => $pd_aktif,
			'siswa_sinkron' => $pd_aktif,
			'siswa_keluar_erapor' => $pd_keluar,
			'siswa_keluar_sinkron' => $pd_keluar,
			'pembelajaran_erapor' => Pembelajaran::where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id)->count(),
			'pembelajaran_sinkron' => Pembelajaran::where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id)->whereNotNull('pembelajaran_id_dapodik')->count(),
			'ekskul_erapor' => Ekstrakurikuler::where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id)->count(),
			'ekskul_sinkron' => Ekstrakurikuler::where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id)->whereNotNull('id_kelas_ekskul')->count(),
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
			'kompetensi_dasar_erapor' => Kompetensi_dasar::count(),
			'kompetensi_dasar_sinkron' => Kompetensi_dasar::whereNull('user_id')->count(),
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
	public function kirim_data(){
		$url_server = CustomHelper::url_server_erapor('status');
		$user = auth()->user();
		$sekolah = Sekolah::find($user->sekolah_id);
		$semester = CustomHelper::get_ta();
		$response = Curl::to($url_server)->get();
		$param = array(
			'user' 		=> $user,
			'sekolah' 	=> $sekolah,
			'semester' 	=> $semester,
			'status_sync'	=> json_decode($response),
		);
		return view('sinkronisasi.kirim_data')->with($param);
	}
	public function kirim_nilai(){
		$user = auth()->user();
		$sekolah = Sekolah::find($user->sekolah_id);
		$semester = CustomHelper::get_ta();
		$param = array(
			'user' 		=> $user,
			'sekolah' 	=> $sekolah,
			'semester' 	=> $semester,
		);
		return view('sinkronisasi.kirim_nilai')->with($param);
	}
	public function proses_sync(){
		$session_id = session()->getId();
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		$last_sync = Setting::where('key', '=', 'last_sync')->first();
		$last_sync = $last_sync->value;
		$last_sync_date = date('Y-m-d', strtotime($last_sync));
		$last_sync_time = date('H:i:s', strtotime($last_sync));
		$url_server = CustomHelper::url_server_erapor('proses_laravel');
		$table_sync = CustomHelper::table_sync();
		$i=1;
		$total = 0;
		$result = 0;
		foreach($table_sync as $sync){
			if(Schema::hasTable($sync)){
				$query = DB::table($sync);
				if($sync == 'ref.kompetensi_dasar'){
					$query->whereNotNull('user_id');
				} elseif (Schema::hasColumn($sync, 'last_sync')) {
					$query->where('last_sync', '>=', $last_sync);
					//$query->whereDate('last_sync', '>=', $last_sync_date);
					//$query->whereTime('last_sync', '>=', $last_sync_time);
				}
				if (Schema::hasColumn($sync, 'semester_id')){
					$query->where('semester_id', '=', $semester->semester_id);
				}
				if (Schema::hasColumn($sync, 'sekolah_id')){
					$query->where('sekolah_id', '=', $user->sekolah_id);
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
				if($sync == 'ref_kompetensi_dasar'){
					$query->whereNotNull('user_id');
				} elseif (Schema::hasColumn($sync, 'last_sync')) {
					$query->where('last_sync', '>=', $last_sync);
					//$query->whereDate('last_sync', '>=', $last_sync_date);
					//$query->whereTime('last_sync', '>=', $last_sync_time);
				}
				if (Schema::hasColumn($sync, 'semester_id')){
					$query->where('semester_id', '=', $semester->semester_id);
				}
				if (Schema::hasColumn($sync, 'sekolah_id')){
					$query->where('sekolah_id', '=', $user->sekolah_id);
				}
				$count = $query->count();
				if($count){
					if($count > $limit){
						for ($counter = 0; $counter <= $count; $counter += $limit) {
							$query = DB::table($sync);
							if($sync == 'ref_kompetensi_dasar'){
								$query->whereNotNull('user_id');
							} elseif (Schema::hasColumn($sync, 'last_sync')) {
								$query->where('last_sync', '>=', $last_sync);
								//$query->whereDate('last_sync', '>=', $last_sync_date);
								//$query->whereTime('last_sync', '>=', $last_sync_time);
							}
							if (Schema::hasColumn($sync, 'semester_id')){
								$query->where('semester_id', '=', $semester->semester_id);
							}
							if (Schema::hasColumn($sync, 'sekolah_id')){
								$query->where('sekolah_id', '=', $user->sekolah_id);
							}
							$query->offset($counter)->limit($limit);
							$result = $query->get();
							if($result){
								$withData = array(
									'semester_id' => $semester->semester_id,
									'sekolah_id' => $user->sekolah_id,
									'table' => $sync,
									'json' => CustomHelper::prepare_send(json_encode($result)),
								);
								$kirim_data = Curl::to($url_server)->withData($withData)->post();
								$percent = intval($i/ $total * 100);
								$arr_content['query'] = 'kirim';
								$arr_content['percent'] = $percent;
								$arr_content['message'] = "Mengirim data " .$sync;
								$arr_content['total'] = $total;
								$arr_content['no'] = $i;
								$arr_content['response'] = $kirim_data;
								Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
							}
						}
						$i++;
					} else {
						$query = DB::table($sync);
						if($sync == 'ref_kompetensi_dasar'){
							$query->whereNotNull('user_id');
						} elseif (Schema::hasColumn($sync, 'last_sync')) {
							$query->where('last_sync', '>=', $last_sync);
							//$query->whereDate('last_sync', '>=', $last_sync_date);
							//$query->whereTime('last_sync', '>=', $last_sync_time);
						}
						if (Schema::hasColumn($sync, 'semester_id')){
							$query->where('semester_id', '=', $semester->semester_id);
						}
						if (Schema::hasColumn($sync, 'sekolah_id')){
							$query->where('sekolah_id', '=', $user->sekolah_id);
						}
						$result = $query->get();
						if($result){
							$withData = array(
								'semester_id' => $semester->semester_id,
								'sekolah_id' => $user->sekolah_id,
								'table' => $sync,
								'json' => CustomHelper::prepare_send(json_encode($result)),
							);
							$kirim_data = Curl::to($url_server)->withData($withData)->post();
							$percent = intval($i/ $total * 100);
							$arr_content['query'] = 'kirim';
							$arr_content['percent'] = $percent;
							$arr_content['message'] = "Mengirim data " .$sync;
							$arr_content['total'] = $total;
							$arr_content['no'] = $i;
							$arr_content['response'] = $kirim_data;
							Storage::disk('public')->put("sinkronisasi.txt", json_encode($arr_content));
							$i++;
						}
					}
				}
			}
		}
		$update_last_sync = Setting::where('key', '=', 'last_sync')->update(['value' => date('Y-m-d H:i:s')]);
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
		$sekolah = Sekolah::find($user->sekolah_id);
		$semester = CustomHelper::get_ta();
		$last_sync = Setting::where('key', '=', 'last_sync')->first();
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
		}
		$host_server_direktorat = CustomHelper::url_server_direktorat($aksi);
		$host_erapor_server = CustomHelper::url_server_erapor($aksi);
		$data_sync = array(
			'username_dapo'		=> $user->email,
			'password_dapo'		=> trim($user->password_dapo),
			'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
			'semester_id'		=> $semester->semester_id,
			'sekolah_id'		=> $user->sekolah_id,
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
		$sekolah = Sekolah::find($user->sekolah_id);
		$semester = CustomHelper::get_ta();
		$aksi = 'rombongan_belajar_sync';
		$host_server_direktorat = CustomHelper::url_server_direktorat($aksi);
		$data_sync = array(
			'username_dapo'		=> $user->email,
			'password_dapo'		=> trim($user->password_dapo),
			'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
			'semester_id'		=> $semester->semester_id,
			'sekolah_id'		=> $user->sekolah_id,
			'npsn'				=> $sekolah->npsn,
			'server'			=> $host_server_direktorat,
			'aksi'				=> $aksi,
			'updated_at'		=> date('Y-m-d H:i:s'),
		);
		Artisan::call('sinkronisasi:ambildata',$data_sync);
	}
	public function ambil_data(){
		$user = auth()->user();
		$sekolah = Sekolah::find($user->sekolah_id);
		$semester = CustomHelper::get_ta();
		$last_sync = Setting::where('key', '=', 'last_sync')->first();
		$last_sync = $last_sync->value;
		$url_server = CustomHelper::url_server_erapor('ambil_data');
		$data_sync = array(
			'sekolah_id'		=> $user->sekolah_id,
			'npsn'				=> $sekolah->npsn,
			'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
			'semester_id'		=> $semester->semester_id,
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
		$sekolah = Sekolah::find($user->sekolah_id);
		$semester = CustomHelper::get_ta();
		Storage::disk('public')->delete('proses_migrasi.json');
		try {
        	DB::connection('erapor')->getPdo();
			$param = array(
				'status' 	=> TRUE,
				'output'	=> 'Migrasi eRaporSMK v.4.x.x ke eRaporSMK v.5.0.0 siap dilakukan!',
				'sekolah' => $sekolah,
				'all_table'	=> array(
					'jurusan_sp', 'ref_guru', 'rombongan_belajar', 'ekstrakurikuler', 'pembelajaran', 'ref_siswa', 'anggota_rombel', 'dudi', 'mou', 'teknik_penilaian', 'bobot_keterampilan', 'rencana_penilaian', 'kd_nilai', 'nilai', 'absen', 'catatan_ppk', 'catatan_wali', 'deskripsi_mata_pelajaran', 'deskripsi_sikap', 'kenaikan_kelas', 'nilai_ekstrakurikuler', 'nilai_karakter', 'nilai_sikap', 'prakerin', 'prestasi'),
			);
    	} catch (\Exception $e) {
        	//die("Could not connect to the database. Please check your configuration. error:" . $e );
			$param = array(
				'status' 	=> FALSE,
				'output'	=> 'Tidak dapat terkoneksi dengan database eRaporSMK v.4.x.x',
			);
    	}
		return view('sinkronisasi.erapor_lama')->with($param);
	}
	public function proses_erapor_lama($table){
		$user = auth()->user();
		Artisan::call('migrasi:start', ['sekolah_id' => $user->sekolah_id, 'query' => $table]);
	}
}
