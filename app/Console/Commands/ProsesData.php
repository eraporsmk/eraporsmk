<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Sekolah;
use App\User;
use App\Role;
use App\Role_user;
use CustomHelper;
use App\Mst_wilayah;
use App\Guru;
use App\Gelar;
use App\Gelar_ptk;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Jurusan_sp;
use App\Mata_pelajaran;
use App\Mata_pelajaran_kurikulum;
use Ixudra\Curl\Facades\Curl;
use App\Rombongan_belajar;
use App\Siswa;
Use App\Anggota_rombel;
use App\Ekstrakurikuler;
use App\Dudi;
use App\Mou;
use App\Pembelajaran;
use App\Jurusan;
use App\Kurikulum;
use App\Akt_pd;
use App\Anggota_akt_pd;
use App\Bimbing_pd;
use App\Kompetensi_dasar;
use Illuminate\Support\Facades\Storage;
class ProsesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinkronisasi:prosesdata {response*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
		$arguments = $this->arguments();
		$function = $arguments['response']['query'];
		if($function == 'count_kd'){
			self::{$function}($arguments['response']['data'], $arguments['response']['count'], $arguments['response']['page']);
		} else {
			self::{$function}($arguments['response']['data']);
		}
	}
	private function sekolah($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : $response->sekolah_id;
		$sekolah = Sekolah::find($sekolah_id);
		if(!$user){
			$query = $response;
		} else {
			$query = CustomHelper::array_to_object($response);
		}
		
		if(isset($query->kepala_sekolah)){
			$data = $query->kepala_sekolah;
		} else {
			if($query->ptk_terdaftar){
				$data = $query->ptk_terdaftar->ptk;
			} else {
				$data = $query->tugas_tambahan->ptk;
			}
		}
		$random = Str::random(6);
		$data->email = ($data->email) ? $data->email : strtolower($random).'@erapor-smk.net';
		if($user){
			$data->email = ($data->email != $user->email) ? $data->email : strtolower($random).'@erapor-smk.net';
		}
		$data->email = ($data->email != $sekolah->email) ? $data->email : strtolower($random).'@erapor-smk.net';
		$data->email = strtolower($data->email);
		$data->nuptk = ($data->nuptk) ? $data->nuptk : mt_rand();
		$insert_guru = array(
			'sekolah_id' 			=> $sekolah_id,
			'nama' 					=> $data->nama,
			'nuptk' 				=> $data->nuptk,
			'nip' 					=> $data->nip,
			'nik' 					=> $data->nik,
			'jenis_kelamin' 		=> $data->jenis_kelamin,
			'tempat_lahir' 			=> $data->tempat_lahir,
			'tanggal_lahir' 		=> $data->tanggal_lahir,
			'status_kepegawaian_id'	=> $data->status_kepegawaian_id,
			'jenis_ptk_id' 			=> $data->jenis_ptk_id,
			'agama_id' 				=> $data->agama_id,
			'alamat' 				=> $data->alamat_jalan,
			'rt' 					=> $data->rt,
			'rw' 					=> $data->rw,
			'desa_kelurahan' 		=> $data->desa_kelurahan,
			'kecamatan' 			=> $data->wilayah->nama,
			'kode_wilayah'			=> $data->kode_wilayah,
			'kode_pos'				=> ($data->kode_pos) ? $data->kode_pos : 0,
			'no_hp'					=> ($data->no_hp) ? $data->no_hp : 0,
			'email' 				=> $data->email,
			'is_dapodik'			=> 1,
			'last_sync'				=> date('Y-m-d H:i:s'),
		);
		$create_guru = Guru::updateOrCreate(
			['guru_id_dapodik' => $data->ptk_id],
			$insert_guru
		);
		foreach($query->jurusan_sp as $jurusan_sp){
			$insert_jur_sp = array(
				'sekolah_id'	=> $sekolah_id,
				'jurusan_id'	=> $jurusan_sp->jurusan_id,
				'nama_jurusan_sp'	=> $jurusan_sp->nama_jurusan_sp,
				'last_sync'	=> date('Y-m-d H:i:s'),
			);
			Jurusan_sp::updateOrCreate(
				['jurusan_sp_id_dapodik' => $jurusan_sp->jurusan_sp_id],
				$insert_jur_sp
			);
		}
		if($query->wilayah->id_level_wilayah == 4){
			if($query->wilayah->parrent_recursive){
				$kecamatan = $query->wilayah->parrent_recursive->nama;
				if($query->wilayah->parrent_recursive->parrent_recursive){
					$kabupaten = $query->wilayah->parrent_recursive->parrent_recursive->nama;
				} else {
					$get_kabupaten = Mst_wilayah::find($kecamatan->mst_kode_wilayah);
					$kabupaten = $get_kabupaten->nama;
				}
				if($query->wilayah->parrent_recursive->parrent_recursive->parrent_recursive){
					$provinsi = $query->wilayah->parrent_recursive->parrent_recursive->parrent_recursive->nama;
				} else {
					$get_provinsi = Mst_wilayah::find($kabupaten->mst_kode_wilayah);
					$provinsi = $get_provinsi->nama;
				}
			}
		} else {
			$kecamatan = $query->wilayah->nama;
			if($query->wilayah->parrent_recursive){
				$kabupaten = $query->wilayah->parrent_recursive->nama;
				if($query->wilayah->parrent_recursive->parrent_recursive){
					$provinsi = $query->wilayah->parrent_recursive->parrent_recursive->nama;
				} else {
					$get_provinsi = Mst_wilayah::find($kabupaten->mst_kode_wilayah);
					$provinsi = $get_provinsi->nama;
				}
			} else {
				$get_kabupaten = Mst_wilayah::find($kecamatan->mst_kode_wilayah);
				$kabupaten = $get_kabupaten->nama;
			}
		}
		$sekolah->npsn = $query->npsn;
		$sekolah->nama = $query->nama;
		$sekolah->nss = $query->nss;
		$sekolah->alamat = $query->alamat_jalan;
		$sekolah->desa_kelurahan = $query->desa_kelurahan;
		$sekolah->kecamatan = $kecamatan;
		$sekolah->kode_wilayah = $query->kode_wilayah;
		$sekolah->kabupaten = $kabupaten;
		$sekolah->provinsi = $provinsi;
		$sekolah->kode_pos = $query->kode_pos;
		$sekolah->lintang = $query->lintang;
		$sekolah->bujur = $query->bujur;
		$sekolah->no_telp = $query->nomor_telepon;
		$sekolah->no_fax = $query->nomor_fax;
		$sekolah->email = $query->email;
		$sekolah->website = $query->website;
		$sekolah->status_sekolah = $query->status_sekolah;
		$sekolah->last_sync = date('Y-m-d H:i:s');
		$sekolah->guru_id = $create_guru->guru_id;
		$sekolah->sinkron = 1;
		$sekolah->save();
	}
	private function save_jurusan_sp($sekolah_id, $jurusan_sp){
		$insert_jur_sp = array(
			'sekolah_id'	=> $sekolah_id,
			'jurusan_id'	=> $jurusan_sp->jurusan_id,
			'nama_jurusan_sp'	=> $jurusan_sp->nama_jurusan_sp,
			'last_sync'	=> date('Y-m-d H:i:s'),
		);
		Jurusan_sp::updateOrCreate(
			['jurusan_sp_id_dapodik' => $jurusan_sp->jurusan_sp_id],
			$insert_jur_sp
		);
	}
	private function guru($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$sekolah = Sekolah::find($sekolah_id);
		if(!$user){
			$dapodik = $response;
		} else {
			$dapodik = CustomHelper::array_to_object($response);
		}
		$jumlah = count((array)$dapodik);
		$i=1;
		$record['table'] = 'guru';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_guru.json', json_encode($record));
		
		foreach($dapodik as $data){
			if(!$sekolah_id){
				$sekolah_id = $data->ptk_terdaftar->sekolah_id;
			}
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_guru.json', json_encode($record));
			$data->nuptk = str_replace(' ','',$data->nuptk);
			$data->nuptk = str_replace('-','',$data->nuptk);
			$data->nuptk = str_replace(' ','',$data->nuptk);
			$random = Str::random(6);
			$data->nuptk = ($data->nuptk) ? $data->nuptk : mt_rand();
			$data->email = ($data->email) ? $data->email : strtolower($random).'@erapor-smk.net';
			if($user){
				$data->email = ($data->email != $user->email) ? $data->email : strtolower($random).'@erapor-smk.net';
			}
			if($sekolah){
				$data->email = ($data->email != $sekolah->email) ? $data->email : strtolower($random).'@erapor-smk.net';
			}
			$data->email = strtolower($data->email);
			$insert_guru = array(
				'sekolah_id'			=> $sekolah_id,
				'nama' 					=> $data->nama,
				'nuptk' 				=> $data->nuptk,
				'nip' 					=> $data->nip,
				'nik' 					=> $data->nik,
				'jenis_kelamin' 		=> $data->jenis_kelamin,
				'tempat_lahir' 			=> $data->tempat_lahir,
				'tanggal_lahir' 		=> $data->tanggal_lahir,
				'status_kepegawaian_id'	=> $data->status_kepegawaian_id,
				'jenis_ptk_id' 			=> $data->jenis_ptk_id,
				'agama_id' 				=> $data->agama_id,
				'alamat' 				=> $data->alamat_jalan,
				'rt' 					=> ($data->rt) ? $data->rt : 0,
				'rw' 					=> ($data->rw) ? $data->rw : 0,
				'desa_kelurahan' 		=> $data->desa_kelurahan,
				'kecamatan' 			=> $data->wilayah->nama,
				'kode_wilayah'			=> $data->kode_wilayah,
				'kode_pos'				=> ($data->kode_pos) ? $data->kode_pos : 0,
				'no_hp'					=> ($data->no_hp) ? $data->no_hp : 0,
				'email' 				=> $data->email,
				'last_sync'				=> date('Y-m-d H:i:s'),
			);
			$create_guru = Guru::updateOrCreate(
				['guru_id_dapodik' => $data->ptk_id],
				$insert_guru
			);
			$data_sync = array(
				'ptk_id'	=> $data->ptk_id,
			);
			$find_gelar = $data->rwy_pend_formal;
			if($find_gelar){
				foreach($find_gelar as $gelar){
					if($gelar->gelar_akademik_id){
						$get_gelar = Gelar::where('gelar_akademik_id', $gelar->gelar_akademik_id)->first();
						if($get_gelar){
							$find_gelar_ptk = Gelar_ptk::where([
								['ptk_id', $data->ptk_id], 
								['gelar_akademik_id', $gelar->gelar_akademik_id]
							])->first();
							if($find_gelar_ptk){
								$find_gelar_ptk->delete();
							}
							Gelar_ptk::create(array('gelar_akademik_id' => $gelar->gelar_akademik_id, 'sekolah_id' => $sekolah_id, 'ptk_id' => $data->ptk_id, 'guru_id' => $create_guru->guru_id, 'last_sync' => date('Y-m-d H:i:s')));
							}
						}
				}
			}
			$i++;
		}
	}
	private function rombongan_belajar($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$sekolah = Sekolah::find($sekolah_id);
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$i=1;
		$record['table'] = 'rombongan belajar';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_rombongan_belajar.json', json_encode($record));
		$rombongan_belajar_id = [];
		foreach($dapodik as $data){
			Storage::disk('public')->put('proses_rombongan_belajar.json', json_encode($data));
			if(!$sekolah_id){
				$sekolah_id = $data->sekolah_id;
			}
			$rombongan_belajar_id[] = $data->rombongan_belajar_id;
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_rombongan_belajar.json', json_encode($record));
			$this->save_jurusan_sp($sekolah_id, $data->jurusan_sp);
			$get_jurusan_id = Jurusan_sp::where('jurusan_sp_id_dapodik', $data->jurusan_sp->jurusan_sp_id)->first();
			$get_wali = Guru::where('guru_id_dapodik', $data->ptk_id)->first();
			$insert_rombel = array(
				'sekolah_id' 			=> $sekolah_id,
				'jurusan_id' 			=> $data->jurusan_sp->jurusan_id,
				'jurusan_sp_id' 		=> $get_jurusan_id->jurusan_sp_id,
				'kurikulum_id' 			=> $data->kurikulum_id,
				'nama' 					=> $data->nama,
				'guru_id' 				=> $get_wali->guru_id,
				'tingkat' 				=> $data->tingkat_pendidikan_id,
				'ptk_id' 				=> $data->ptk_id,
				'jenis_rombel'			=> $data->jenis_rombel,
				'last_sync'				=> date('Y-m-d H:i:s'),
			);
			Rombongan_belajar::updateOrCreate(
				['rombel_id_dapodik' => $data->rombongan_belajar_id, 'semester_id' => $data->semester_id],
				$insert_rombel
			);
			$i++;
		}
		if($rombongan_belajar_id){
			Rombongan_belajar::where('jenis_rombel', 1)->where('sekolah_id', $sekolah_id)->where('semester_id', $data->semester_id)->whereNotIn('rombel_id_dapodik', $rombongan_belajar_id)->delete();
		}
	}
	private function siswa_aktif($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$sekolah = Sekolah::find($sekolah_id);
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$i=1;
		$record['table'] = 'peserta didik aktif';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_siswa_aktif.json', json_encode($record));
		$anggota_rombel_id = [];
		$semester_id = NULL;
		foreach($dapodik as $data){
			//Storage::disk('public')->put('proses_siswa_aktif.json', json_encode($data));
			if(isset($data->anggota_rombel_id)){
				$anggota_rombel_id[] = $data->anggota_rombel_id;
				$semester_id = session('semester_id');
			} else {
				$semester_id = $data->anggota_rombel->rombongan_belajar->semester_id;
				$anggota_rombel_id[] = $data->anggota_rombel->anggota_rombel_id;
			}
			if(!$sekolah_id){
				$sekolah_id = $data->anggota_rombel->rombongan_belajar->sekolah_id;
			}
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_siswa_aktif.json', json_encode($record));
			$random = Str::random(6);
			$data->nisn = ($data->nisn) ? $data->nisn : mt_rand();
			$data->email = ($data->email) ? $data->email : strtolower($random).'@erapor-smk.net';
			if($sekolah){
				$data->email = ($data->email != $sekolah->email) ? $data->email : strtolower($random).'@erapor-smk.net';
			}
			$data->email = strtolower($data->email);
			$kecamatan = Mst_wilayah::find($data->kode_wilayah);
			if(!isset($data->nipd)){
				$data->nipd = $data->registrasi_peserta_didik->nipd;
				$data->sekolah_asal = $data->registrasi_peserta_didik->sekolah_asal;
				$data->tanggal_masuk_sekolah = $data->registrasi_peserta_didik->tanggal_masuk_sekolah;
			}
			$insert_siswa = array(
				'sekolah_id'		=> $sekolah_id,
				'nama' 				=> $data->nama,
				'no_induk' 			=> ($data->nipd) ? $data->nipd : 0,
				'nisn' 				=> $data->nisn,
				'jenis_kelamin' 	=> ($data->jenis_kelamin) ? $data->jenis_kelamin : 0,
				'tempat_lahir' 		=> ($data->tempat_lahir) ? $data->tempat_lahir : 0,
				'tanggal_lahir' 	=> $data->tanggal_lahir,
				'agama_id' 			=> ($data->agama_id) ? $data->agama_id : 0,
				'status' 			=> 'Anak Kandung',
				'anak_ke' 			=> ($data->anak_keberapa) ? $data->anak_keberapa : 0,
				'alamat' 			=> ($data->alamat_jalan) ? $data->alamat_jalan : 0,
				'rt' 				=> ($data->rt) ? $data->rt : 0,
				'rw' 				=> ($data->rw) ? $data->rw : 0,
				'desa_kelurahan' 	=> ($data->desa_kelurahan) ? $data->desa_kelurahan : 0,
				'kecamatan' 		=> ($kecamatan) ? $kecamatan->nama : 0,
				'kode_pos' 			=> ($data->kode_pos) ? $data->kode_pos : 0,
				'no_telp' 			=> ($data->nomor_telepon_seluler) ? $data->nomor_telepon_seluler : 0,
				'sekolah_asal' 		=> ($data->sekolah_asal) ? $data->sekolah_asal : 0,
				'diterima' 			=> ($data->tanggal_masuk_sekolah) ? $data->tanggal_masuk_sekolah : 0,
				'kode_wilayah' 		=> $data->kode_wilayah,
				'email' 			=> $data->email,
				'nama_ayah' 		=> ($data->nama_ayah) ? $data->nama_ayah : 0,
				'nama_ibu' 			=> ($data->nama_ibu_kandung) ? $data->nama_ibu_kandung : 0,
				'kerja_ayah' 		=> ($data->pekerjaan_id_ayah) ? $data->pekerjaan_id_ayah : 1,
				'kerja_ibu' 		=> ($data->pekerjaan_id_ibu) ? $data->pekerjaan_id_ibu : 1,
				'nama_wali' 		=> ($data->nama_wali) ? $data->nama_wali : 0,
				'alamat_wali' 		=> ($data->alamat_jalan) ? $data->alamat_jalan : 0,
				'telp_wali' 		=> ($data->nomor_telepon_seluler) ? $data->nomor_telepon_seluler : 0,
				'kerja_wali' 		=> ($data->pekerjaan_id_wali) ? $data->pekerjaan_id_wali : 1,
				'active' 			=> 1,
				'last_sync'			=> date('Y-m-d H:i:s'),
			);
			$create_siswa = Siswa::updateOrCreate(
				['peserta_didik_id_dapodik' => $data->peserta_didik_id],
				$insert_siswa
			);
			if(isset($data->rombongan_belajar_id)){
				$find_rombel = Rombongan_belajar::where('rombel_id_dapodik', $data->rombongan_belajar_id)->where('semester_id', $semester_id)->first();
			} else {
				$find_rombel = Rombongan_belajar::where('rombel_id_dapodik', $data->anggota_rombel->rombongan_belajar_id)->where('semester_id', $semester_id)->first();
			}
			if($find_rombel){
				$insert_anggota_rombel = array(
					'sekolah_id'				=> $sekolah_id,
					'rombongan_belajar_id' 		=> $find_rombel->rombongan_belajar_id, 
					'peserta_didik_id' 			=> $create_siswa->peserta_didik_id,
					'last_sync'					=> date('Y-m-d H:i:s'),
				);
				if(isset($data->anggota_rombel_id)){
					$create_anggota_rombel = Anggota_rombel::updateOrCreate(
						['anggota_rombel_id_dapodik' => $data->anggota_rombel_id, 'semester_id' => $semester_id],
						$insert_anggota_rombel
					);
				} else {
					$create_anggota_rombel = Anggota_rombel::updateOrCreate(
						['anggota_rombel_id_dapodik' => $data->anggota_rombel->anggota_rombel_id, 'semester_id' => $semester_id],
						$insert_anggota_rombel
					);
				}
			}
			$i++;
		}
		if($anggota_rombel_id){
			Anggota_rombel::whereHas('rombongan_belajar', function($query){
				$query->where('jenis_rombel', 1);
			})->where('sekolah_id', $sekolah_id)->where('semester_id', $semester_id)->whereNotIn('anggota_rombel_id_dapodik', $anggota_rombel_id)->delete();
		}
	}
	private function siswa_keluar($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$sekolah = Sekolah::find($sekolah_id);
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$i=1;
		$record['table'] = 'peserta didik keluar';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_siswa_keluar.json', json_encode($record));
		
		foreach($dapodik as $data){
			//Storage::disk('public')->put('proses_siswa_keluar.json', json_encode($data));
			if(isset($data->anggota_rombel_id)){
				$semester_id = session('semester_id');
				$anggota_rombel_id = $data->anggota_rombel_id;
			} else {
				$semester_id = $data->anggota_rombel->rombongan_belajar->semester_id;
				$anggota_rombel_id = $data->anggota_rombel->anggota_rombel_id;
			}
			if(!$sekolah_id){
				$sekolah_id = $data->anggota_rombel->rombongan_belajar->sekolah_id;
			}
			if(!isset($data->nipd)){
				if($data->registrasi_peserta_didik){
					$data->nipd = $data->registrasi_peserta_didik->nipd;
					$data->sekolah_asal = $data->registrasi_peserta_didik->sekolah_asal;
					$data->tanggal_masuk_sekolah = $data->registrasi_peserta_didik->tanggal_masuk_sekolah;
				} else {
					$data->nipd = 0;
					$data->sekolah_asal = NULL;
					$data->tanggal_masuk_sekolah = NULL;
				}
			}
			$record['inserted'] = $i;
			//Storage::disk('public')->put('proses_siswa_keluar.json', json_encode($record));
			$find_siswa = Siswa::where('peserta_didik_id_dapodik', $data->peserta_didik_id)->onlyTrashed()->first();
			if($find_siswa){
				$find_anggota_rombel = Anggota_rombel::where('peserta_didik_id' , $find_siswa->peserta_didik_id)->where('semester_id', $semester_id)->onlyTrashed()->first();
				if(!$find_anggota_rombel){
					$find_rombel = Rombongan_belajar::where('rombel_id_dapodik', $data->rombongan_belajar_id)->where('semester_id', $semester_id)->first();
					if($find_rombel){
						$insert_anggota_rombel = array(
							'sekolah_id'				=> $sekolah_id,
							'rombongan_belajar_id' 		=> $find_rombel->rombongan_belajar_id, 
							'peserta_didik_id' 			=> $find_siswa->peserta_didik_id,
							'last_sync'					=> date('Y-m-d H:i:s'),
						);
						$create_anggota_rombel = Anggota_rombel::updateOrCreate(
							['anggota_rombel_id_dapodik' => $anggota_rombel_id, 'semester_id' => $semester_id],
							$insert_anggota_rombel
						);
						$create_anggota_rombel->delete();
					}
				}
			} else {
				$kecamatan = Mst_wilayah::find($data->kode_wilayah);
				$random = Str::random(6);
				$data->nisn = ($data->nisn) ? $data->nisn : mt_rand();
				$data->email = ($data->email) ? $data->email : strtolower($random).'@erapor-smk.net';
				if($sekolah){
					$data->email = ($data->email != $sekolah->email) ? $data->email : strtolower($random).'@erapor-smk.net';
				}
				$data->email = strtolower($data->email);
				$nipd = 0;
				if(isset($data->nipd)){
					$nipd = ($data->nipd) ? $data->nipd : 0;
				}
				$insert_siswa = array(
					'sekolah_id'		=> $sekolah_id,
					'nama' 				=> $data->nama,
					'no_induk' 			=> $nipd,
					'nisn' 				=> $data->nisn,
					'jenis_kelamin' 	=> ($data->jenis_kelamin) ? $data->jenis_kelamin : 0,
					'tempat_lahir' 		=> ($data->tempat_lahir) ? $data->tempat_lahir : 0,
					'tanggal_lahir' 	=> $data->tanggal_lahir,
					'agama_id' 			=> ($data->agama_id) ? $data->agama_id : 0,
					'status' 			=> 'Anak Kandung',
					'anak_ke' 			=> ($data->anak_keberapa) ? $data->anak_keberapa : 0,
					'alamat' 			=> ($data->alamat_jalan) ? $data->alamat_jalan : 0,
					'rt' 				=> ($data->rt) ? $data->rt : 0,
					'rw' 				=> ($data->rw) ? $data->rw : 0,
					'desa_kelurahan' 	=> ($data->desa_kelurahan) ? $data->desa_kelurahan : 0,
					'kecamatan' 		=> ($kecamatan) ? $kecamatan->nama : 0,
					'kode_pos' 			=> ($data->kode_pos) ? $data->kode_pos : 0,
					'no_telp' 			=> ($data->nomor_telepon_seluler) ? $data->nomor_telepon_seluler : 0,
					'sekolah_asal' 		=> ($data->sekolah_asal) ? $data->sekolah_asal : 0,
					'diterima_kelas' 	=> 0,
					'diterima' 			=> ($data->tanggal_masuk_sekolah) ? $data->tanggal_masuk_sekolah : date('Y-m-d'),
					'kode_wilayah' 		=> $data->kode_wilayah,
					'email' 			=> $data->email,
					'nama_ayah' 		=> ($data->nama_ayah) ? $data->nama_ayah : 0,
					'nama_ibu' 			=> ($data->nama_ibu_kandung) ? $data->nama_ibu_kandung : 0,
					'kerja_ayah' 		=> ($data->pekerjaan_id_ayah) ? $data->pekerjaan_id_ayah : 1,
					'kerja_ibu' 		=> ($data->pekerjaan_id_ibu) ? $data->pekerjaan_id_ibu : 1,
					'nama_wali' 		=> ($data->nama_wali) ? $data->nama_wali : 0,
					'alamat_wali' 		=> ($data->alamat_jalan) ? $data->alamat_jalan : 0,
					'telp_wali' 		=> ($data->nomor_telepon_seluler) ? $data->nomor_telepon_seluler : 0,
					'kerja_wali' 		=> ($data->pekerjaan_id_wali) ? $data->pekerjaan_id_wali : 1,
					'active' 			=> 0,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$create_siswa = Siswa::updateOrCreate(
					['peserta_didik_id_dapodik' => $data->peserta_didik_id],
					$insert_siswa
				);
				$find_anggota_rombel = Anggota_rombel::where('peserta_didik_id' , $create_siswa->peserta_didik_id)->where('semester_id', $semester_id)->first();
				if(!$find_anggota_rombel){
					if(isset($data->rombongan_belajar_id)){
						$find_rombel = Rombongan_belajar::where('rombel_id_dapodik', $data->rombongan_belajar_id)->where('semester_id', $semester_id)->first();
					} else {
						$find_rombel = Rombongan_belajar::where('rombel_id_dapodik', $data->anggota_rombel->rombongan_belajar_id)->where('semester_id', $semester_id)->first();
					}
					if($find_rombel){
						$insert_anggota_rombel = array(
							'sekolah_id'				=> $sekolah_id,
							'rombongan_belajar_id' 		=> $find_rombel->rombongan_belajar_id, 
							'peserta_didik_id' 			=> $create_siswa->peserta_didik_id,
							'last_sync'					=> date('Y-m-d H:i:s'),
						);
						$create_anggota_rombel = Anggota_rombel::updateOrCreate(
							['anggota_rombel_id_dapodik' => $anggota_rombel_id, 'semester_id' => $semester_id],
							$insert_anggota_rombel
						);
						$create_anggota_rombel->delete();
					}
				}
				$create_siswa->delete();
			}
			$i++;
		}
	}
	private function pembelajaran($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$i=1;
		$record['table'] = 'pembelajaran';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_pembelajaran.json', json_encode($record));
		$pembelajaran_id = [];
		foreach($dapodik as $data){
			Storage::disk('public')->put('proses_pembelajaran.json', json_encode($data));
			$pembelajaran_id[] = $data->pembelajaran_id;
			if(!$sekolah_id){
				$sekolah_id = $data->ptk_terdaftar->sekolah_id;
			}
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_pembelajaran.json', json_encode($record));
			$rombongan_belajar = Rombongan_belajar::where('rombel_id_dapodik', $data->rombongan_belajar_id)->first();
			$get_guru = Guru::where('guru_id_dapodik', $data->ptk_terdaftar->ptk_id)->first();
			$insert_pembelajaran = array(
				'sekolah_id'				=> $sekolah_id,
				'rombongan_belajar_id'		=> $rombongan_belajar->rombongan_belajar_id,
				'guru_id'					=> $get_guru->guru_id,
				'mata_pelajaran_id'			=> $data->mata_pelajaran_id,
				'nama_mata_pelajaran'		=> $data->nama_mata_pelajaran,
				'kkm'						=> 0,
				'is_dapodik'				=> 1,
				'last_sync'					=> date('Y-m-d H:i:s'),
			);
			Pembelajaran::updateOrCreate(
				['pembelajaran_id_dapodik' => $data->pembelajaran_id, 'semester_id' => $data->semester_id],
				$insert_pembelajaran
			);
			$i++;
		}
		if($pembelajaran_id){
			Pembelajaran::where('sekolah_id', $sekolah_id)->where('semester_id', $data->semester_id)->whereNotIn('pembelajaran_id_dapodik', $pembelajaran_id)->delete();
		}
	}
	private function ekskul($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$sekolah = Sekolah::find($sekolah_id);
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$i=1;
		$record['table'] = 'ekstrakurikuler';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_ekskul.json', json_encode($record));
		$ekskul_id = [];
		$semester_id = NULL;
		foreach($dapodik as $data){
			$id_kelas_ekskul = isset($data->ID_kelas_ekskul) ? $data->ID_kelas_ekskul : $data->id_kelas_ekskul;
			$ekskul_id[] = $id_kelas_ekskul;
			if(!$sekolah_id){
				$sekolah_id = $data->rombongan_belajar->sekolah_id;
			}
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_ekskul.json', json_encode($record));
			$guru_id_dapodik = isset($data->rombongan_belajar->wali_kelas->ptk_id) ? $data->rombongan_belajar->wali_kelas->ptk_id : $data->rombongan_belajar->ptk_id;
			$get_wali = Guru::where('guru_id_dapodik', $guru_id_dapodik)->first();
			$semester_id = isset($data->semester_id) ? $data->semester_id : $data->rombongan_belajar->semester_id;
			if($get_wali){
				$insert_rombel = array(
					'sekolah_id' 			=> $sekolah_id,
					'kurikulum_id' 			=> $data->rombongan_belajar->kurikulum_id,
					'nama' 					=> $data->rombongan_belajar->nama,
					'guru_id' 				=> $get_wali->guru_id,
					'tingkat' 				=> $data->rombongan_belajar->tingkat_pendidikan_id,
					'ptk_id' 				=> $guru_id_dapodik,
					'rombel_id_dapodik'		=> $data->rombongan_belajar_id,
					'jenis_rombel'			=> $data->rombongan_belajar->jenis_rombel,
					'last_sync'				=> date('Y-m-d H:i:s'),
				);
				$create_rombel = Rombongan_belajar::updateOrCreate(
					['rombel_id_dapodik' => $data->rombongan_belajar_id, 'semester_id' => $semester_id],
					$insert_rombel
				);
				$insert_ekskul = array(
					'sekolah_id'	=> $sekolah_id,
					'guru_id' => $get_wali->guru_id,
					'nama_ekskul' => $data->nm_ekskul,
					'is_dapodik' => 1,
					'rombongan_belajar_id'	=> $create_rombel->rombongan_belajar_id,
					'alamat_ekskul' => $data->rombongan_belajar->ruang->nm_ruang, 
					'last_sync'	=> date('Y-m-d H:i:s'),
				);
				Ekstrakurikuler::updateOrCreate(
					['id_kelas_ekskul' => $id_kelas_ekskul, 'semester_id' => $semester_id],
					$insert_ekskul
				);
			}
			$i++;
		}
		if($ekskul_id){
			Ekstrakurikuler::where('sekolah_id', $sekolah_id)->where('semester_id', $semester_id)->whereNotIn('id_kelas_ekskul', $ekskul_id)->delete();
		}
	}
	private function anggota_ekskul($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$sekolah = Sekolah::find($sekolah_id);
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$i=1;
		$record['table'] = 'anggota ekstrakurikuler';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_anggota_ekskul.json', json_encode($record));
		
		foreach($dapodik as $data){
			if(!$sekolah_id){
				$sekolah_id = $data->rombongan_belajar->sekolah_id;
			}
			$semester_id = isset($data->semester_id) ? $data->semester_id : $data->rombongan_belajar->semester_id;
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_anggota_ekskul.json', json_encode($record));
			$find_rombel = Rombongan_belajar::where('rombel_id_dapodik', $data->rombongan_belajar_id)->where('semester_id', $semester_id)->first();
			if($find_rombel){
				$find_siswa = Siswa::where('peserta_didik_id_dapodik', $data->peserta_didik_id)->first();
				if($find_siswa){
					$insert_anggota_ekskul = array(
						'sekolah_id'				=> $sekolah_id,
						'semester_id' 				=> $semester_id, 
						'rombongan_belajar_id' 		=> $find_rombel->rombongan_belajar_id, 
						'peserta_didik_id' 			=> $find_siswa->peserta_didik_id,
						'anggota_rombel_id_dapodik'	=> $data->anggota_rombel_id,
						'last_sync'			=> date('Y-m-d H:i:s'),
					);
					$create_anggota_rombel = Anggota_rombel::updateOrCreate(
						['anggota_rombel_id_dapodik' => $data->anggota_rombel_id, 'semester_id' => $semester_id],
						$insert_anggota_ekskul
					);
					$i++;
				}
			}
		}
	}
	private function dudi($response){
		$user = auth()->user();
		$sekolah_id = ($user) ? $user->sekolah_id : NULL;
		$sekolah = Sekolah::find($sekolah_id);
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$i=1;
		$record['table'] = 'relasi dunia usaha dan industri (DUDI)';
		$record['jumlah'] = $jumlah;
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_dudi.json', json_encode($record));
		
		foreach($dapodik as $data){
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_dudi.json', json_encode($record));
			if(!$sekolah_id){
				$sekolah_id = $data->mou->sekolah_id;
			}
			if(isset($data->id_jns_ks)){
				$insert_dudi = array(
					'sekolah_id'		=> $sekolah_id,
					'nama'				=> $data->nama_dudi,
					'bidang_usaha_id'	=> $data->dudi->bidang_usaha_id,
					'nama_bidang_usaha'	=> $data->nama_bidang_usaha,
					'alamat_jalan'		=> $data->dudi->alamat_jalan,
					'rt'				=> $data->dudi->rt,
					'rw'				=> $data->dudi->rw,
					'nama_dusun'		=> $data->dudi->nama_dusun,
					'desa_kelurahan'	=> $data->dudi->desa_kelurahan,
					'kode_wilayah'		=> $data->dudi->kode_wilayah,
					'kode_pos'			=> $data->dudi->kode_pos,
					'lintang'			=> $data->dudi->lintang,
					'bujur'				=> $data->dudi->bujur,
					'nomor_telepon'		=> $data->dudi->nomor_telepon,
					'nomor_fax'			=> $data->dudi->nomor_fax,
					'email'				=> $data->dudi->email,
					'website'			=> $data->dudi->website,
					'npwp'				=> $data->dudi->npwp,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$create_dudi = Dudi::updateOrCreate(
					['dudi_id_dapodik' => $data->dudi_id],
					$insert_dudi
				);	
				$insert_mou = array(
					'id_jns_ks'			=> $data->id_jns_ks,
					'dudi_id'			=> $create_dudi->dudi_id,
					'dudi_id_dapodik'	=> $data->dudi_id,
					'sekolah_id'		=> $sekolah_id,
					'nomor_mou'			=> $data->nomor_mou,
					'judul_mou'			=> $data->judul_mou,
					'tanggal_mulai'		=> $data->tanggal_mulai,
					'tanggal_selesai'	=> ($data->tanggal_selesai) ? $data->tanggal_selesai : date('Y-m-d'),
					'nama_dudi'			=> $data->nama_dudi,
					'npwp_dudi'			=> $data->npwp_dudi,
					'nama_bidang_usaha'	=> $data->nama_bidang_usaha,
					'telp_kantor'		=> $data->telp_kantor,
					'fax'				=> $data->fax,
					'contact_person'	=> $data->contact_person,
					'telp_cp'			=> $data->telp_cp,
					'jabatan_cp'		=> $data->jabatan_cp,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$create_mou = Mou::updateOrCreate(
					['mou_id_dapodik' => $data->mou_id],
					$insert_mou
				);
			} else {
				$insert_dudi = array(
					'sekolah_id'		=> $sekolah_id,
					'nama'				=> $data->nama,
					'bidang_usaha_id'	=> $data->bidang_usaha_id,
					'nama_bidang_usaha'	=> $data->mou->nama_bidang_usaha,
					'alamat_jalan'		=> $data->alamat_jalan,
					'rt'				=> $data->rt,
					'rw'				=> $data->rw,
					'nama_dusun'		=> $data->nama_dusun,
					'desa_kelurahan'	=> $data->desa_kelurahan,
					'kode_wilayah'		=> $data->kode_wilayah,
					'kode_pos'			=> $data->kode_pos,
					'lintang'			=> $data->lintang,
					'bujur'				=> $data->bujur,
					'nomor_telepon'		=> $data->nomor_telepon,
					'nomor_fax'			=> $data->nomor_fax,
					'email'				=> $data->email,
					'website'			=> $data->website,
					'npwp'				=> $data->npwp,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$create_dudi = Dudi::updateOrCreate(
					['dudi_id_dapodik' => $data->dudi_id],
					$insert_dudi
				);	
				$insert_mou = array(
					'id_jns_ks'			=> $data->mou->id_jns_ks,
					'dudi_id'			=> $create_dudi->dudi_id,
					'dudi_id_dapodik'	=> $data->mou->dudi_id,
					'sekolah_id'		=> $sekolah_id,
					'nomor_mou'			=> $data->mou->nomor_mou,
					'judul_mou'			=> $data->mou->judul_mou,
					'tanggal_mulai'		=> $data->mou->tanggal_mulai,
					'tanggal_selesai'	=> ($data->mou->tanggal_selesai) ? $data->mou->tanggal_selesai : date('Y-m-d'),
					'nama_dudi'			=> $data->mou->nama_dudi,
					'npwp_dudi'			=> $data->mou->npwp_dudi,
					'nama_bidang_usaha'	=> $data->mou->nama_bidang_usaha,
					'telp_kantor'		=> $data->mou->telp_kantor,
					'fax'				=> $data->mou->fax,
					'contact_person'	=> $data->mou->contact_person,
					'telp_cp'			=> $data->mou->telp_cp,
					'jabatan_cp'		=> $data->mou->jabatan_cp,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$create_mou = Mou::updateOrCreate(
					['mou_id_dapodik' => $data->mou->mou_id],
					$insert_mou
				);
			}
			if(!isset($data->akt_pd)){
				$data->akt_pd = $data->mou->akt_pd;
			}
			if($data->akt_pd){
				foreach($data->akt_pd as $akt_pd){
					$insert_akt_pd = array(
						'sekolah_id'	=> $sekolah_id,
						'mou_id'		=> $create_mou->mou_id,
						'id_jns_akt_pd'	=> $akt_pd->id_jns_akt_pd,
						'judul_akt_pd'	=> $akt_pd->judul_akt_pd,
						'sk_tugas'		=> ($akt_pd->sk_tugas) ? $akt_pd->sk_tugas : '-',
						'tgl_sk_tugas'	=> $akt_pd->tgl_sk_tugas,
						'ket_akt'		=> $akt_pd->ket_akt,
						'a_komunal'		=> $akt_pd->a_komunal,
						'last_sync'		=> date('Y-m-d H:i:s'),
					);
					$create_akt_pd = Akt_pd::updateOrCreate(
						['akt_pd_id_dapodik' => $akt_pd->id_akt_pd],
						$insert_akt_pd
					);
					if($akt_pd->anggota_akt_pd){
						foreach($akt_pd->anggota_akt_pd as $anggota_akt_pd){
							$find_siswa = Siswa::where('peserta_didik_id_dapodik', $anggota_akt_pd->registrasi_peserta_didik->peserta_didik_id)->first();
							if($find_siswa){
								$insert_anggota_akt_pd = array(
									'sekolah_id'		=> $sekolah_id,
									'akt_pd_id'			=> $create_akt_pd->akt_pd_id,
									'peserta_didik_id'	=> $find_siswa->peserta_didik_id,
									'nm_pd'				=> $anggota_akt_pd->nm_pd,
									'nipd'				=> $anggota_akt_pd->nipd,
									'jns_peran_pd'		=> $anggota_akt_pd->jns_peran_pd,
									'last_sync'			=> date('Y-m-d H:i:s'),
								);
								$create_anggota_akt_pd = Anggota_akt_pd::updateOrCreate(
									['id_ang_akt_pd' => $anggota_akt_pd->id_ang_akt_pd],
									$insert_anggota_akt_pd
								);
							}
						}
					}
					if($akt_pd->bimbing_pd){
						foreach($akt_pd->bimbing_pd as $bimbing_pd){
							$find_guru = Guru::where('guru_id_dapodik', $bimbing_pd->ptk_id)->first();
							if($find_guru){
									$insert_bimbing_pd = array(
									'sekolah_id'		=> $sekolah_id,
									'akt_pd_id'			=> $create_akt_pd->akt_pd_id,
									'guru_id'			=> $find_guru->guru_id,
									'ptk_id'			=> $bimbing_pd->ptk_id,
									'urutan_pembimbing'	=> $bimbing_pd->urutan_pembimbing,
									'last_sync'			=> date('Y-m-d H:i:s'),
								);
								$create_bimbing_pd = Bimbing_pd::updateOrCreate(
									['id_bimb_pd' => $bimbing_pd->id_bimb_pd],
									$insert_bimbing_pd
								);
							}
						}
					}
				}
			}
			$i++;
		}
	}
	private function jurusan($response){
		$user = auth()->user();
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$count_erapor = Jurusan::count();
		$count_server = count($response);
		$record['table'] = 'referensi jurusan';
		$record['jumlah'] = $count_server;
		$record['inserted'] = $count_erapor;
		Storage::disk('public')->put('proses_jurusan.json', json_encode($record));
		
		$i=1;
		foreach($dapodik as $data){
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_jurusan.json', json_encode($record));
			$data->created_at = date('Y-m-d H:i:s', strtotime($data->create_date));
			$data->updated_at = date('Y-m-d H:i:s', strtotime($data->last_update));
			$data->deleted_at = ($data->expired_date) ? date('Y-m-d H:i:s', strtotime($data->expired_date)) : NULL;
			$insert_jurusan = array(
				'nama_jurusan'			=> $data->nama_jurusan,
				'untuk_sma'				=> $data->untuk_sma,
				'untuk_smk'				=> $data->untuk_smk,
				'untuk_pt'				=> $data->untuk_pt,
				'untuk_slb'				=> $data->untuk_slb,
				'untuk_smklb'			=> $data->untuk_smklb,
				'jenjang_pendidikan_id'	=> $data->jenjang_pendidikan_id,
				'jurusan_induk' 		=> $data->jurusan_induk,
				'level_bidang_id'		=> $data->level_bidang_id,
				'created_at'			=> $data->created_at,
				'updated_at'			=> $data->updated_at,
				'deleted_at'			=> $data->deleted_at,
				'last_sync'				=> $data->last_sync,
			);
			Jurusan::updateOrCreate(
				['jurusan_id' => $data->jurusan_id],
				$insert_jurusan
			);
			$i++;
		}
	}
	private function kurikulum($response){
		$user = auth()->user();
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$count_erapor = Kurikulum::count();
		$count_server = $jumlah = count($response);
		$record['table'] = 'referensi kurikulum';
		$record['jumlah'] = $count_server;
		$record['inserted'] = $count_erapor;
		Storage::disk('public')->put('proses_kurikulum.json', json_encode($record));
		
		$i=1;
		foreach($dapodik as $data){
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_kurikulum.json', json_encode($record));
			$data->created_at = date('Y-m-d H:i:s', strtotime($data->create_date));
			$data->updated_at = date('Y-m-d H:i:s', strtotime($data->last_update));
			$data->deleted_at = ($data->expired_date) ? date('Y-m-d H:i:s', strtotime($data->expired_date)) : NULL;
			$insert_kurikulum = array(
				'nama_kurikulum'			=> $data->nama_kurikulum,
				'mulai_berlaku'				=> $data->mulai_berlaku,
				'sistem_sks'				=> $data->sistem_sks,
				'total_sks'					=> $data->total_sks,
				'jenjang_pendidikan_id'		=> $data->jenjang_pendidikan_id,
				'jurusan_id'				=> $data->jurusan_id,
				'created_at'				=> $data->created_at,
				'updated_at'				=> $data->updated_at,
				'deleted_at'				=> $data->deleted_at,
				'last_sync'					=> $data->last_sync,
			);
			Kurikulum::updateOrCreate(
				['kurikulum_id' => $data->kurikulum_id],
				$insert_kurikulum
			);
			$i++;
		}
	}
	private function mata_pelajaran($response){
		$user = auth()->user();
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$count_erapor = Mata_pelajaran::count();
		$count_server = $jumlah;
		$record['table'] = 'referensi mata pelajaran';
		$record['jumlah'] = $count_server;
		$record['inserted'] = $count_erapor;
		Storage::disk('public')->put('proses_mata_pelajaran.json', json_encode($record));
		
		$i=1;
		foreach($dapodik as $data){
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_mata_pelajaran.json', json_encode($record));
			$data->created_at = date('Y-m-d H:i:s', strtotime($data->create_date));
			$data->updated_at = date('Y-m-d H:i:s', strtotime($data->last_update));
			$data->deleted_at = ($data->expired_date) ? date('Y-m-d H:i:s', strtotime($data->expired_date)) : NULL;
			$insert_mata_pelajaran = array(
				'jurusan_id' 				=> $data->jurusan_id,
				'nama'						=> $data->nama,
				'pilihan_sekolah'			=> $data->pilihan_sekolah,
				'pilihan_kepengawasan'		=> $data->pilihan_kepengawasan,
				'pilihan_buku'				=> $data->pilihan_buku,
				'pilihan_evaluasi'			=> $data->pilihan_evaluasi,
				'created_at'				=> $data->created_at,
				'updated_at'				=> $data->updated_at,
				'deleted_at'				=> $data->deleted_at,
				'last_sync'					=> $data->last_sync,
			);
			Mata_pelajaran::updateOrCreate(
				['mata_pelajaran_id' => $data->mata_pelajaran_id],
				$insert_mata_pelajaran
			);
			$i++;
		}
	}
	private function mapel_kur($response){
		$user = auth()->user();
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$count_erapor = Mata_pelajaran_kurikulum::count();
		$count_server = $jumlah;
		$record['table'] = 'referensi mata pelajaran kurikulum';
		$record['jumlah'] = $count_server;
		$record['inserted'] = $count_erapor;
		Storage::disk('public')->put('proses_mapel_kur.json', json_encode($record));
		$i=1;
		foreach($dapodik as $data){
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_mapel_kur.json', json_encode($record));
			$data->created_at = date('Y-m-d H:i:s', strtotime($data->create_date));
			$data->updated_at = date('Y-m-d H:i:s', strtotime($data->last_update));
			$data->deleted_at = ($data->expired_date) ? date('Y-m-d H:i:s', strtotime($data->expired_date)) : NULL;
			$insert_mata_pelajaran_kurikulum = array(
				'jumlah_jam'			=> $data->jumlah_jam,
				'jumlah_jam_maksimum'	=> $data->jumlah_jam_maksimum,
				'wajib'					=> $data->wajib,
				'sks'					=> $data->sks,
				'a_peminatan'			=> $data->a_peminatan,
				'area_kompetensi'		=> $data->area_kompetensi,
				'gmp_id'				=> $data->gmp_id,
				'created_at'			=> $data->created_at,
				'updated_at'			=> $data->updated_at,
				'deleted_at'			=> $data->deleted_at,
				'last_sync'				=> $data->last_sync
			);
			Mata_pelajaran_kurikulum::updateOrCreate(
				['kurikulum_id' => $data->kurikulum_id, 'mata_pelajaran_id' => $data->mata_pelajaran_id, 'tingkat_pendidikan_id' => $data->tingkat_pendidikan_id],
				$insert_mata_pelajaran_kurikulum
			);
			$i++;
		}
	}
	private function wilayah($response){
		$user = auth()->user();
		$jumlah = count($response);
		$dapodik = CustomHelper::array_to_object($response);
		$count_erapor = Mst_wilayah::count();
		$count_server = $jumlah;
		$record['table'] = 'referensi wilayah';
		$record['jumlah'] = $count_server;
		$record['inserted'] = $count_erapor;
		Storage::disk('public')->put('proses_wilayah.json', json_encode($record));
		$i=1;
		foreach($dapodik as $data){
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_wilayah.json', json_encode($record));
			$find_induk = Mst_wilayah::find($data->mst_kode_wilayah);
			if($find_induk){
				if(!isset($data->created_at)){
					$data->created_at = date('Y-m-d H:i:s', strtotime($data->create_date));
					$data->updated_at = date('Y-m-d H:i:s', strtotime($data->last_update));
					$data->deleted_at = ($data->expired_date) ? date('Y-m-d H:i:s', strtotime($data->expired_date)) : NULL;
				}
				$insert_wilayah = array(
					'nama'				=> $data->nama,
					'id_level_wilayah'	=> $data->id_level_wilayah,
					'mst_kode_wilayah'	=> $data->mst_kode_wilayah,
					'negara_id'			=> $data->negara_id,
					'asal_wilayah'		=> $data->asal_wilayah,
					'kode_bps'			=> NULL,
					'kode_dagri'		=> NULL,
					'kode_keu'			=> NULL,
					'created_at'		=> $data->created_at,
					'updated_at'		=> $data->updated_at,
					'deleted_at'		=> $data->deleted_at,
					'last_sync'			=> $data->last_sync
				);
				Mst_wilayah::updateOrCreate(
					['kode_wilayah' => $data->kode_wilayah],
					$insert_wilayah
				);
				$i++;
			}
		}
	}
	private function count_kd($response, $count, $page){
		$user = auth()->user();
		$jumlah = $count;
		$dapodik = CustomHelper::array_to_object($response);
		$count_erapor = Kompetensi_dasar::count();
		$count_server = $jumlah;
		$record['table'] = 'referensi kompetensi_dasar';
		$record['jumlah'] = $count_server;
		$record['inserted'] = $count_erapor;
		Storage::disk('public')->put('proses_kompetensi_dasar.json', json_encode($record));
		$i=($page) ? $page * 500 : 1;
		$record['page'] = $page;
		foreach($dapodik as $data){
			$record['inserted'] = $i;
			Storage::disk('public')->put('proses_kompetensi_dasar.json', json_encode($record));
			$find = Kompetensi_dasar::find($data->kompetensi_dasar_id);
			if(!$find){
				Kompetensi_dasar::updateOrCreate(
					[
						'kompetensi_dasar_id' 		=> $data->kompetensi_dasar_id,
					],
					[
						'id_kompetensi'				=> $data->id_kompetensi,
						'kompetensi_id'				=> $data->kompetensi_id,
						'mata_pelajaran_id'			=> $data->mata_pelajaran_id,
						'kelas_10'					=> $data->kelas_10,
						'kelas_11'					=> $data->kelas_11,
						'kelas_12'					=> $data->kelas_12,
						'kelas_13'					=> $data->kelas_13,
						'aktif'						=> $data->aktif,
						'kurikulum'					=> $data->kurikulum,
						'id_kompetensi_nas'			=> $data->id_kompetensi_nas,
						'kompetensi_dasar'			=> trim($data->kompetensi_dasar),
						'kompetensi_dasar_alias'	=> $data->kompetensi_dasar_alias,
						'created_at'				=> $data->created_at,
						'updated_at'				=> $data->updated_at,
						'deleted_at'				=> $data->deleted_at,
						'last_sync'					=> $data->last_sync,
					]
				);
			}
			$i++;
		}
	}
	private function kelengkapan_data($response){
		Storage::disk('public')->put('kelengkapan_data.json', json_encode($response));
		$dapodik = CustomHelper::array_to_object($response);
		foreach($dapodik as $data){
			$find_siswa = Siswa::where('peserta_didik_id_dapodik', $data->peserta_didik_id)->first();
			if($find_siswa){
				$find_siswa->diterima_kelas = $data->rombongan_belajar->nama;
				$find_siswa->save();
			}
		}
	}
	private function registrasi($response){
		$dapodik = CustomHelper::array_to_object($response);
		$insert_sekolah = array(
			'npsn' 					=> $dapodik->sekolah->npsn,
			'nss' 					=> $dapodik->sekolah->nss,
			'nama' 					=> $dapodik->sekolah->nama,
			'alamat' 				=> $dapodik->sekolah->alamat_jalan,
			'desa_kelurahan'		=> $dapodik->sekolah->desa_kelurahan,
			'kode_wilayah'			=> $dapodik->sekolah->kode_wilayah,
			'kecamatan' 			=> $dapodik->sekolah->wilayah->parrent_recursive->nama,
			'kabupaten' 			=> $dapodik->sekolah->wilayah->parrent_recursive->parrent_recursive->nama,
			'provinsi' 				=> $dapodik->sekolah->wilayah->parrent_recursive->parrent_recursive->parrent_recursive->nama,
			'kode_pos' 				=> $dapodik->sekolah->kode_pos,
			'lintang' 				=> 0,//$dapodik->lintang,
			'bujur' 				=> 0,//$dapodik->bujur,
			'no_telp' 				=> $dapodik->sekolah->nomor_telepon,
			'no_fax' 				=> $dapodik->sekolah->nomor_fax,
			'email' 				=> $dapodik->sekolah->email,
			'website' 				=> $dapodik->sekolah->website,
			'status_sekolah'		=> $dapodik->sekolah->status_sekolah,
			'last_sync'				=> date('Y-m-d H:i:s'),
		);
		$sekolah = Sekolah::updateOrCreate(
			['sekolah_id' => $dapodik->sekolah->sekolah_id],
			$insert_sekolah
		);
		foreach($dapodik->pengguna as $pengguna){
			$user = User::updateOrCreate(
				['name' => $pengguna->name, 'email' => $pengguna->email],
				['password' => Hash::make($pengguna->password), 'last_sync' => date('Y-m-d H:i:s'), 'sekolah_id' => $pengguna->sekolah_id, 'password_dapo'	=> $pengguna->password_dapo]
			);
			$adminRole = Role::where('name', 'admin')->first();
			$user = User::where('email', $pengguna->email)->first();
			$CheckadminRole = DB::table('role_user')->where('user_id', $user->user_id)->first();
			if(!$CheckadminRole){
				$user->attachRole($adminRole);
			}
		}
	}
}
