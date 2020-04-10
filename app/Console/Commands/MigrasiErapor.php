<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Sekolah;
use App\User;
use App\Role;
use App\Role_user;
use App\Providers\HelperServiceProvider;
use App\Mst_wilayah;
use App\Guru;
use App\Gelar_ptk;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
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
use Illuminate\Support\Facades\Storage;
use App\Rencana_penilaian;
use App\Sikap;
use App\Teknik_penilaian;
use App\Kd_nilai;
use App\Kompetensi_dasar;
use App\Kd;
use App\Nilai;
use App\Nilai_akhir;
use App\Nilai_rapor;
use App\Bobot_keterampilan;
use App\Absensi;
use App\Catatan_ppk;
use App\Catatan_wali;
use App\Deskripsi_mata_pelajaran;
use App\Deskripsi_sikap;
use App\Kenaikan_kelas;
use App\Nilai_ekstrakurikuler;
use App\Nilai_karakter;
use App\Nilai_sikap;
use App\Prakerin;
use App\Prestasi;
use App\Migrasi;
class MigrasiErapor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrasi:start {sekolah_id} {query}';

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
		$sekolah_id = $arguments['sekolah_id'];
		$query = $arguments['query'];
		self::{$query}($sekolah_id);
		//jurusan_sp
		//ref_guru
		//rombongan_belajar
		//ekskul
		//pembelajaran
		//ref_siswa
		//anggota_rombel
		//dudi
		//mou
		//teknik_penilaian
		//bobot_keterampilan
		//rencana_penilaian
		//kd_nilai
		//nilai
		//absen
		//catatan_ppk
		//catatan_wali
		//deskripsi_mata_pelajaran
		//deskripsi_sikap
		//kenaikan_kelas
		//nilai_ekstrakurikuler
		//nilai_karakter
		//nilai_sikap
		//prakerin
		//prestasi
	}
	public function start_migrasi(){
		$result['status'] = 1;
		$result['progress'] = 0;
		$result['table']	= 'jurusan_sp';
		echo json_encode($result);
	}
	public function jurusan_sp($sekolah_id){
		$i=0;
		$percent = intval(1/ 26 * 100);
        $erapor = DB::connection('erapor4')->table('jurusan_sp')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'jurusan_sp';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $j_sp){
			$insert_jur_sp = array(
				'sekolah_id'		=> $sekolah_id,
				'jurusan_id'		=> $j_sp->jurusan_id,
				'nama_jurusan_sp'	=> $j_sp->nama_jurusan_sp,
				'last_sync'			=> date('Y-m-d H:i:s'),
			);
			$create_data = Jurusan_sp::updateOrCreate(
				['jurusan_sp_id_dapodik' => $j_sp->jurusan_sp_id],
				$insert_jur_sp
			);
			if($create_data){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'jurusan_sp'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Jurusan_sp::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'ref_guru';
		echo json_encode($result);
	}
	public function ref_guru($sekolah_id){
		$i=0;
		$percent = intval(2/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('ref_guru')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'ref_guru';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $guru){
			$insert_guru = array(
				'nama' 					=> $guru->nama, 
				'tanggal_lahir' 		=> $guru->tanggal_lahir,
				'sekolah_id'			=> $sekolah_id,
				'nuptk' 				=> $guru->nuptk,
				'guru_id_dapodik' 		=> $guru->guru_id_dapodik,
				'nip' 					=> $guru->nip,
				'nik' 					=> $guru->nik,
				'jenis_kelamin' 		=> $guru->jenis_kelamin,
				'tempat_lahir' 			=> $guru->tempat_lahir,
				'status_kepegawaian_id'	=> $guru->status_kepegawaian_id,
				'jenis_ptk_id' 			=> $guru->jenis_ptk_id,
				'agama_id' 				=> $guru->agama_id,
				'kode_wilayah' 			=> '999999  ',
				'alamat' 				=> $guru->alamat,
				'rt' 					=> ($guru->rt) ? $guru->rt : 0,
				'rw' 					=> ($guru->rw) ? $guru->rw : 0,
				'desa_kelurahan' 		=> $guru->desa_kelurahan,
				'kecamatan' 			=> $guru->kecamatan,
				'kode_pos'				=> ($guru->kode_pos) ? $guru->kode_pos : 0,
				'no_hp'					=> ($guru->no_hp) ? $guru->no_hp : 0,
				'email' 				=> $guru->email,
				'last_sync'				=> date('Y-m-d H:i:s'),
			);
			$create_guru = Guru::updateOrCreate(
				['guru_id_migrasi'		=> $guru->guru_id],
				$insert_guru
			);
			if($create_guru){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
			$insert_user = array(
				'name' => $guru->nama,
				'nuptk'	=> $guru->nuptk,
				'password' => Hash::make(12345678),
				'last_sync'	=> date('Y-m-d H:i:s'),
				'sekolah_id'	=> $sekolah_id,
				'password_dapo'	=> md5(12345678),
				'guru_id'	=> $create_guru->guru_id
			);
			$create_user = User::updateOrCreate(
				['email' => $guru->email],
				$insert_user
			);
			$adminRole = Role::where('name', 'guru')->first();
			$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
			if(!$CheckadminRole){
				$create_user->attachRole($adminRole);
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'ref_guru'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Guru::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'rombongan_belajar';
		echo json_encode($result);
	}
	public function rombongan_belajar($sekolah_id){
		$i=0;
		$percent = intval(3/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('rombongan_belajar')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'rombongan_belajar';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $rombel){
			if($rombel->rombel_id_dapodik){
				$semester = DB::connection('erapor4')->table('ref_semester')->find($rombel->semester_id);
				$tahun = substr($semester->tahun, 0,4); // returns "d"
				$semester_id = $tahun.$semester->semester;
				$get_jurusan_id = Jurusan_sp::where('jurusan_id', '=', $rombel->jurusan_id)->first();
				$get_wali = Guru::where('guru_id_dapodik', '=', $rombel->guru_id_dapodik)->first();
				$get_user = User::where('guru_id', '=', $get_wali->guru_id)->first();
				$insert_rombel = array(
					'sekolah_id' 			=> $sekolah_id,
					'jurusan_id' 			=> $rombel->jurusan_id,
					'jurusan_sp_id' 		=> $get_jurusan_id->jurusan_sp_id,
					'kurikulum_id' 			=> $rombel->kurikulum_id,
					'nama' 					=> $rombel->nama,
					'guru_id' 				=> $get_wali->guru_id,
					'tingkat' 				=> $rombel->tingkat,
					'ptk_id' 				=> $rombel->guru_id_dapodik,
					'jenis_rombel'			=> 1,
					'last_sync'				=> date('Y-m-d H:i:s'),
					'rombel_id_dapodik' 	=> $rombel->rombel_id_dapodik, 
					'rombongan_belajar_id_migrasi' 			=> $rombel->rombongan_belajar_id,
				);
				$create_data = Rombongan_belajar::updateOrCreate(
					['rombel_id_dapodik' => $rombel->rombel_id_dapodik, 'semester_id' => $semester_id],
					$insert_rombel
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
				$adminRole = Role::where('name', 'wali')->first();
				$CheckadminRole = DB::table('role_user')->where('user_id', $get_user->user_id)->where('role_id', $adminRole->id)->first();
				if(!$CheckadminRole){
					$get_user->attachRole($adminRole);
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'rombongan_belajar'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Rombongan_belajar::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'ekskul';
		echo json_encode($result);
    }
	public function ekskul($sekolah_id){
		$i=0;
		$percent = intval(4/ 26 * 100);
		$query = DB::connection('erapor4')->table('ekstrakurikuler')->where('ekstrakurikuler.sekolah_id', $sekolah_id);
		$query->join('ref_guru', 'ref_guru.guru_id', '=', 'ekstrakurikuler.guru_id');
		$query->join('ref_semester', 'ref_semester.id', '=', 'ekstrakurikuler.semester_id');
		$query->select(['ekstrakurikuler.rombongan_belajar_id', 'ekstrakurikuler.nama_ekskul', 'ekstrakurikuler.alamat_ekskul', 'ekstrakurikuler.id_kelas_ekskul', 'ref_guru.guru_id_dapodik', 'ref_semester.tahun', 'ref_semester.semester']);
		$erapor = $query->get();
		$record['table'] = 'ekskul';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$tahun = substr($data->tahun, 0,4); // returns "d"
			$semester_id = $tahun.$data->semester;
			$get_wali = Guru::where('guru_id_dapodik', $data->guru_id_dapodik)->first();
			if($get_wali){
				$get_user = User::where('guru_id', '=', $get_wali->guru_id)->first();
				$insert_rombel = array(
					'sekolah_id' 			=> $sekolah_id,
					'nama' 					=> $data->nama_ekskul,
					'kurikulum_id'			=> 99,
					'guru_id' 				=> $get_wali->guru_id,
					'tingkat' 				=> 0,
					'ptk_id' 				=> $data->guru_id_dapodik,
					'jenis_rombel'			=> 51,
					'last_sync'				=> date('Y-m-d H:i:s'),
				);
				$create_rombel = Rombongan_belajar::updateOrCreate(
					['rombel_id_dapodik' => $data->rombongan_belajar_id, 'semester_id' => $semester_id],
					$insert_rombel
				);
				$insert_ekskul = array(
					'sekolah_id'	=> $sekolah_id,
					'guru_id' => $get_wali->guru_id,
					'nama_ekskul' => $data->nama_ekskul,
					'is_dapodik' => 1,
					'rombongan_belajar_id'	=> $create_rombel->rombongan_belajar_id,
					'alamat_ekskul' => $data->alamat_ekskul, 
					'last_sync'	=> date('Y-m-d H:i:s'),
				);
				$create_ekskul = Ekstrakurikuler::updateOrCreate(
					['id_kelas_ekskul' => $data->id_kelas_ekskul, 'semester_id' => $semester_id],
					$insert_ekskul
				);
				if($create_ekskul){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				}
				$adminRole = Role::where('name', 'pembina_ekskul')->first();
				if(!$adminRole){
					$insert_adminRole = DB::table('roles')->insert([
						'name' => 'pembina_ekskul',
						'display_name' => 'Pembina Ekstrakurikuler',
						'description' => 'Pembina Ekstrakurikuler',
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
					$CheckadminRole = DB::table('role_user')->where('user_id', $get_user->user_id)->where('role_id', $adminRole->id)->first();
					if(!$CheckadminRole){
						$get_user->attachRole($adminRole);
					}
				} else {
					$CheckadminRole = DB::table('role_user')->where('user_id', $get_user->user_id)->where('role_id', $adminRole->id)->first();
					if(!$CheckadminRole){
						$get_user->attachRole($adminRole);
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'ekstrakurikuler'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Ekstrakurikuler::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'pembelajaran';
		echo json_encode($result);
	}
	public function pembelajaran($sekolah_id){
		$i=0;
		$percent = intval(5/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('pembelajaran')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'pembelajaran';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$semester = DB::connection('erapor4')->table('ref_semester')->find($data->semester_id);
			$tahun = substr($semester->tahun, 0,4); // returns "d"
			$semester_id = $tahun.$semester->semester;
			$rombongan_belajar = Rombongan_belajar::where('rombongan_belajar_id_migrasi', '=', $data->rombongan_belajar_id)->first();
			$get_guru = Guru::where('guru_id_migrasi', '=', $data->guru_id)->first();
			$insert_pembelajaran = array(
				'sekolah_id'				=> $sekolah_id,
				'rombongan_belajar_id'		=> $rombongan_belajar->rombongan_belajar_id,
				'guru_id'					=> $get_guru->guru_id,
				'mata_pelajaran_id'			=> $data->mata_pelajaran_id,
				'nama_mata_pelajaran'		=> $data->nama_mata_pelajaran,
				'kelompok_id'				=> $data->kelompok_id,
				'no_urut'					=> $data->no_urut,
				'kkm'						=> $data->kkm,
				'is_dapodik'				=> $data->is_dapodik,
				'last_sync'					=> date('Y-m-d H:i:s'),
				'pembelajaran_id_migrasi'	=> $data->pembelajaran_id,
				'semester_id' 				=> $semester_id
			);
			$create_data = Pembelajaran::updateOrCreate(
				['pembelajaran_id_dapodik' => $data->pembelajaran_id_dapodik],
				$insert_pembelajaran
			);
			if($create_data){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'pembelajaran'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Pembelajaran::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'ref_siswa';
		echo json_encode($result);
	}
	public function ref_siswa($sekolah_id){
		$i=0;
		$percent = intval(6/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('ref_siswa')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'ref_siswa';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$insert_siswa = array(
				'sekolah_id'		=> $sekolah_id,
				'nama' 				=> $data->nama,
				'no_induk' 			=> $data->no_induk,
				'nisn' 				=> $data->nisn,
				'jenis_kelamin' 	=> $data->jenis_kelamin,
				'tempat_lahir' 		=> $data->tempat_lahir,
				'tanggal_lahir' 	=> $data->tanggal_lahir,
				'agama_id' 			=> $data->agama_id,
				'status' 			=> $data->status,
				'anak_ke' 			=> $data->anak_ke,
				'alamat' 			=> $data->alamat,
				'rt' 				=> $data->rt,
				'rw' 				=> $data->rw,
				'desa_kelurahan' 	=> $data->desa_kelurahan,
				'kecamatan' 		=> $data->kecamatan,
				'kode_wilayah' 		=> '999999  ',
				'kode_pos' 			=> $data->kode_pos,
				'no_telp' 			=> $data->no_telp,
				'sekolah_asal' 		=> $data->sekolah_asal,
				'diterima_kelas' 	=> $data->diterima_kelas,
				'diterima' 			=> $data->diterima,
				'kode_wilayah' 		=> ($data->kode_wilayah) ? $data->kode_wilayah : '010101AA',
				'email' 			=> $data->email,
				'nama_ayah' 		=> $data->nama_ayah,
				'nama_ibu' 			=> $data->nama_ibu,
				'kerja_ayah' 		=> ($data->kerja_ayah) ? $data->kerja_ayah : 1,
				'kerja_ibu' 		=> ($data->kerja_ibu) ? $data->kerja_ibu : 1,
				'nama_wali' 		=> $data->nama_wali,
				'alamat_wali' 		=> $data->alamat_wali,
				'telp_wali' 		=> $data->telp_wali,
				'kerja_wali' 		=> ($data->kerja_wali) ? $data->kerja_wali : 1,
				'active' 			=> $data->active,
				'last_sync'			=> date('Y-m-d H:i:s'),
				'peserta_didik_id_migrasi'	=> $data->siswa_id,
			);
			$create_siswa = Siswa::updateOrCreate(
				['peserta_didik_id_dapodik' => $data->siswa_id_dapodik],
				$insert_siswa
			);
			if($create_siswa){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
			$insert_user = array(
				'name' => $data->nama,
				'nisn'	=> $data->nisn,
				'password' => Hash::make(12345678),
				'last_sync'	=> date('Y-m-d H:i:s'),
				'sekolah_id'	=> $sekolah_id,
				'password_dapo'	=> md5(12345678),
				'peserta_didik_id'	=> $create_siswa->peserta_didik_id
			);
			$create_user = User::updateOrCreate(
				['email' => $data->email],
				$insert_user
			);
			$adminRole = Role::where('name', 'siswa')->first();
			$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
			if(!$CheckadminRole){
				$create_user->attachRole($adminRole);
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'ref_siswa'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Siswa::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'anggota_rombel';
		echo json_encode($result);
	}
	public function anggota_rombel($sekolah_id){
		$i=0;
		$percent = intval(7/ 26 * 100);
		//$erapor = DB::connection('erapor4')->table('anggota_rombel')->where('sekolah_id', $sekolah_id)->get();
		$query = DB::connection('erapor4')->table('anggota_rombel')->where('anggota_rombel.sekolah_id', $sekolah_id);
		$query->join('rombongan_belajar', 'rombongan_belajar.rombongan_belajar_id', '=', 'anggota_rombel.rombongan_belajar_id');
		$query->join('ref_semester', 'ref_semester.id', '=', 'anggota_rombel.semester_id');
		//$query->join('ref_semester', 'ref_semester.id', '=', 'ekstrakurikuler.semester_id');
		//$query->select(['ekstrakurikuler.rombongan_belajar_id', 'ekstrakurikuler.nama_ekskul', 'ekstrakurikuler.alamat_ekskul', 'ekstrakurikuler.id_kelas_ekskul', 'ref_guru.guru_id_dapodik', 'ref_semester.tahun', 'ref_semester.semester']);
		$erapor = $query->get();
		$record['table'] = 'anggota_rombel';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$tahun = substr($data->tahun, 0,4); // returns "d"
			$semester_id = $tahun.$data->semester;
			$find_rombel = Rombongan_belajar::where('rombongan_belajar_id_migrasi', '=', $data->rombongan_belajar_id)->first();
			$find_siswa = Siswa::where('peserta_didik_id_migrasi', '=', $data->siswa_id)->first();
			if($find_rombel){
				$insert_anggota_rombel = array(
					'sekolah_id'				=> $sekolah_id,
					'rombongan_belajar_id' 		=> $find_rombel->rombongan_belajar_id, 
					'peserta_didik_id' 			=> $find_siswa->peserta_didik_id,
					'anggota_rombel_id_migrasi'	=> $data->anggota_rombel_id,
					'last_sync'					=> date('Y-m-d H:i:s'),
				);
				$create_anggota_rombel = Anggota_rombel::updateOrCreate(
					['anggota_rombel_id_dapodik' => $data->anggota_rombel_id_dapodik, 'semester_id' => $semester_id],
					$insert_anggota_rombel
				);
				if($create_anggota_rombel){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			} else {
				$find_rombel_two = Rombongan_belajar::where('rombel_id_dapodik', '=', $data->rombel_id_dapodik)->first();
				if($find_rombel_two){
					$insert_anggota_rombel = array(
						'sekolah_id'				=> $sekolah_id,
						'rombongan_belajar_id' 		=> $find_rombel_two->rombongan_belajar_id, 
						'peserta_didik_id' 			=> $find_siswa->peserta_didik_id,
						'last_sync'					=> date('Y-m-d H:i:s'),
					);
					$create_anggota_rombel = Anggota_rombel::updateOrCreate(
						['anggota_rombel_id_dapodik' => $data->anggota_rombel_id_dapodik, 'semester_id' => $semester_id],
						$insert_anggota_rombel
					);
					if($create_anggota_rombel){
						$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
						$record['inserted'] = number_format($i,0,',','.');
						Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
						$i++;
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'anggota_rombel'],
			['jumlah_asal'	=> $i, 'jumlah_masuk' => Anggota_rombel::count()]
		);
		$result['jumlah'] = $i;
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'teknik_penilaian';
		echo json_encode($result);
	}
	/*public function dudi($sekolah_id){
		$i=0;
		$percent = intval(8/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('dudi')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'dudi';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$insert_dudi = array(
				'sekolah_id'		=> $data->sekolah_id,
				'nama'				=> $data->nama,
				'bidang_usaha_id'	=> $data->bidang_usaha_id,
				'nama_bidang_usaha'	=> $data->nama_bidang_usaha,
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
			if($create_dudi){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'dudi'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Dudi::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'mou';
		echo json_encode($result);
	}
	public function mou($sekolah_id){
		$i=0;
		$percent = intval(9/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('mou')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'mou';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_dudi = Dudi::where('dudi_id_dapodik', '=', $data->dudi_id)->first();
			$insert_mou = array(
				'id_jns_ks'			=> $data->id_jns_ks,
				'dudi_id'			=> $find_dudi->dudi_id,
				'dudi_id_dapodik'	=> $data->dudi_id,
				'sekolah_id'		=> $data->sekolah_id,
				'nomor_mou'			=> $data->nomor_mou,
				'judul_mou'			=> $data->judul_mou,
				'tanggal_mulai'		=> $data->tanggal_mulai,
				'tanggal_selesai'	=> $data->tanggal_selesai,
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
			if($create_mou){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'mou'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Mou::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'teknik_penilaian';
		echo json_encode($result);
	}*/
	public function teknik_penilaian($sekolah_id){
		$query = Teknik_penilaian::where('sekolah_id', '=', $sekolah_id);
		if(!$query->count()){
			$insert_teknik = array(
				array(
					'sekolah_id' 	=> $sekolah_id,
					'kompetensi_id'	=> 1,
					'nama'			=> 'Tes Tertulis',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> $sekolah_id,
					'kompetensi_id'	=> 1,
					'nama'			=> 'Tes Lisan',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> $sekolah_id,
					'kompetensi_id'	=> 1,
					'nama'			=> 'Penugasan',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> $sekolah_id,
					'kompetensi_id'	=> 2,
					'nama'			=> 'Portofolio',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> $sekolah_id,
					'kompetensi_id'	=> 2,
					'nama'			=> 'Kinerja',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> $sekolah_id,
					'kompetensi_id'	=> 2,
					'nama'			=> 'Proyek',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
			);
			foreach($insert_teknik as $teknik){
				Teknik_penilaian::create($teknik);
			}
		}
		$i=0;
		$percent = intval(8/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('teknik_penilaian')->get();
		$record['table'] = 'teknik_penilaian';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$insert_teknik = array(
				'teknik_penilaian_id_migrasi'	=> $data->teknik_penilaian_id,
				'sekolah_id'					=> $sekolah_id,
				'bobot'							=> $data->bobot,
				'last_sync'						=> date('Y-m-d H:i:s'),
			);
			$create_data = Teknik_penilaian::updateOrCreate(
				['nama' => $data->nama, 'kompetensi_id' => $data->kompetensi_id],
				$insert_teknik
			);
			if($create_data){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'teknik_penilaian'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => $i]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'bobot_keterampilan';
		echo json_encode($result);
	}
	public function bobot_keterampilan($sekolah_id){
		$i=0;
		$percent = intval(9/ 26 * 100);
		$query = DB::connection('erapor4')->table('bobot_keterampilan')->where('bobot_keterampilan.sekolah_id', $sekolah_id);
		$query->join('pembelajaran', function ($join) {
            $join->on('pembelajaran.semester_id', '=', 'bobot_keterampilan.semester_id');
			$join->on('pembelajaran.mata_pelajaran_id', '=', 'bobot_keterampilan.mata_pelajaran_id');
			$join->on('pembelajaran.rombongan_belajar_id', '=', 'bobot_keterampilan.rombongan_belajar_id');
        });
		$erapor = $query->get();
		$record['table'] = 'bobot_keterampilan';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_pembelajaran = Pembelajaran::where('pembelajaran_id_migrasi', '=', $data->pembelajaran_id)->first();
			if($find_pembelajaran){
				$find_metode = Teknik_penilaian::where('teknik_penilaian_id_migrasi', '=', $data->teknik_penilaian_id)->first();
				if($find_metode){
					$i++;
					$insert_bobot = array(
						'sekolah_id'		=> $sekolah_id,
						'bobot'				=> $data->bobot,
						'last_sync'			=> date('Y-m-d H:i:s'),
					);
					$create_data = Bobot_keterampilan::updateOrCreate(
						['pembelajaran_id' => $find_pembelajaran->pembelajaran_id, 'metode_id' => $find_metode->teknik_penilaian_id],
						$insert_bobot
					);
					if($create_data){
						$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
						$record['inserted'] = number_format($i,0,',','.');
						Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
						$i++;
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'bobot_keterampilan'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Bobot_keterampilan::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'rencana_penilaian';
		echo json_encode($result);
	}
	public function rencana_penilaian($sekolah_id){
		$i=0;
		$percent = intval(10/ 26 * 100);
		$query = DB::connection('erapor4')->table('rencana_penilaian')->where('rencana_penilaian.sekolah_id', $sekolah_id);
		$query->join('ref_semester', 'ref_semester.id', '=', 'rencana_penilaian.semester_id');
		$query->join('pembelajaran', function ($join) {
            $join->on('pembelajaran.semester_id', '=', 'rencana_penilaian.semester_id');
			$join->on('pembelajaran.mata_pelajaran_id', '=', 'rencana_penilaian.mata_pelajaran_id');
			$join->on('pembelajaran.rombongan_belajar_id', '=', 'rencana_penilaian.rombongan_belajar_id');
        });
		$erapor = $query->get();
		$record['table'] = 'rencana_penilaian';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$tahun = substr($data->tahun, 0,4); // returns "d"
			$semester_id = $tahun.$data->semester;
			$find_pembelajaran = Pembelajaran::where('pembelajaran_id_migrasi', '=', $data->pembelajaran_id)->first();
			$find_metode = Teknik_penilaian::where('teknik_penilaian_id_migrasi', '=', $data->metode_id)->first();
			if($find_pembelajaran && $find_metode){
				$insert_rencana_penilaian = array(
					'sekolah_id'					=> $sekolah_id,
					'pembelajaran_id'				=> $find_pembelajaran->pembelajaran_id,
					'kompetensi_id'					=> $data->kompetensi_id,
					'nama_penilaian'				=> $data->nama_penilaian,
					'metode_id'						=> $find_metode->teknik_penilaian_id,
					'bobot'							=> $data->bobot,
					'keterangan'					=> $data->keterangan,
					'last_sync'						=> date('Y-m-d H:i:s'),
				);
				$create_data = Rencana_penilaian::updateOrCreate(
					['rencana_penilaian_id_migrasi' => $data->rencana_penilaian_id],
					$insert_rencana_penilaian
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'rencana_penilaian'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Rencana_penilaian::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'kd_nilai';
		echo json_encode($result);
	}
	public function kd_nilai($sekolah_id){
		$i=0;
		$percent = intval(11/ 26 * 100);
		$limit = 10000;
		//$count = DB::connection('erapor4')->table('kd_nilai')->where('sekolah_id', $sekolah_id)->count();
		$query = DB::connection('erapor4')->table('kd_nilai')->where('kd_nilai.sekolah_id', $sekolah_id);
		$query->join('ref_kompetensi_dasar', 'ref_kompetensi_dasar.id', '=', 'kd_nilai.kd_id');
		$count = $query->count();
		$record['table'] = 'kd_nilai';
		$record['jumlah'] = number_format($count,0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		for ($counter = 0; $counter <= $count; $counter += $limit) {
			$query = DB::connection('erapor4')->table('kd_nilai')->where('kd_nilai.sekolah_id', $sekolah_id);
			$query->join('ref_kompetensi_dasar', 'ref_kompetensi_dasar.id', '=', 'kd_nilai.kd_id');
			$query->offset($counter);
			$query->limit($limit);
			$erapor = $query->get();
			foreach($erapor as $data){
				$find_rencana = Rencana_penilaian::with(['pembelajaran' => function($query){
					$query->with(['rombongan_belajar' => function($query){
						$query->with('kurikulum');
					}]);
				}])->where('rencana_penilaian_id_migrasi', '=', $data->rencana_penilaian_id)->first();
				if($find_rencana){
					$nama_kurikulum = $find_rencana->pembelajaran->rombongan_belajar->kurikulum->nama_kurikulum;
					if (strpos($nama_kurikulum, 'REV') !== false) {
						$kurikulum = 2017;
					} elseif (strpos($nama_kurikulum, '2013') !== false) {
						$kurikulum = 2013;
					} else {
						$kurikulum = 2006;
					}
					$find_kd = Kompetensi_dasar::where('id_kompetensi', '=', $data->id_kompetensi)->where('kompetensi_id', '=', ($data->aspek == 'P') ? 1 : 2)->where('mata_pelajaran_id', '=', $data->mata_pelajaran_id)->where('kelas_'.$data->kelas, '=', 1)->first();
					if($find_kd){
						if($find_kd->kurikulum == $kurikulum){
							$kd_id = $find_kd->kompetensi_dasar_id;
							$id_kompetensi = $find_kd->id_kompetensi;
						} else {
							$insert_kd = array(
								'id_kompetensi' 			=> $find_kd->id_kompetensi,
								'kompetensi_id'				=> $find_kd->kompetensi_id,
								'mata_pelajaran_id'			=> $find_kd->mata_pelajaran_id,
								'kelas_10'					=> $find_kd->kelas_10,
								'kelas_11'					=> $find_kd->kelas_11,
								'kelas_12'					=> $find_kd->kelas_12,
								'kelas_13'					=> $find_kd->kelas_13,
								'id_kompetensi_nas'			=> $find_kd->id_kompetensi_nas,
								'kompetensi_dasar'			=> $find_kd->kompetensi_dasar,
								'kompetensi_dasar_alias'	=> $find_kd->kompetensi_dasar_alias,
								'user_id'					=> $sekolah_id,
								'aktif'						=> $find_kd->aktif,
								'kurikulum'					=> $kurikulum,
								'last_sync'					=> date('Y-m-d H:i:s'),
							);
							$create_kd = Kompetensi_dasar::create($insert_kd);
							$kd_id = $create_kd->kompetensi_dasar_id;
							$id_kompetensi = $create_kd->id_kompetensi;
						}
					} else {
						$insert_kd = array(
							'id_kompetensi' 			=> $data->id_kompetensi,
							'kompetensi_id'				=> ($data->aspek == 'P') ? 1 : 2,
							'mata_pelajaran_id'			=> $data->mata_pelajaran_id,
							'kelas_10'					=> ($data->kelas == 10) ? 1 : 0,
							'kelas_11'					=> ($data->kelas == 11) ? 1 : 0,
							'kelas_12'					=> ($data->kelas == 12) ? 1 : 0,
							'kelas_13'					=> ($data->kelas == 13) ? 1 : 0,
							'id_kompetensi_nas'			=> $data->id_kompetensi_nas,
							'kompetensi_dasar'			=> $data->kompetensi_dasar,
							'kompetensi_dasar_alias'	=> $data->kompetensi_dasar_alias,
							'user_id'					=> $sekolah_id,
							'aktif'						=> $data->aktif,
							'kurikulum'					=> $kurikulum,
							'last_sync'					=> date('Y-m-d H:i:s'),
						);
						$create_kd = Kompetensi_dasar::create($insert_kd);
						$kd_id = $create_kd->kompetensi_dasar_id;
						$id_kompetensi = $create_kd->id_kompetensi;
					}
					$insert_kd_nilai = array(
						'sekolah_id'					=> $sekolah_id,
						'rencana_penilaian_id'			=> $find_rencana->rencana_penilaian_id,
						'kompetensi_dasar_id'			=> $kd_id,
						'id_kompetensi'					=> $id_kompetensi,
						'last_sync'						=> date('Y-m-d H:i:s'),
					);
					$create_data = Kd_nilai::updateOrCreate(
						['kd_nilai_id_migrasi' => $data->kd_nilai_id],
						$insert_kd_nilai
					);
					if($create_data){
						$record['progress'] = $percent + (intval($i/ $count * 100) / 100);
						$record['inserted'] = number_format($i,0,',','.');
						Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
						$i++;
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'kd_nilai'],
			['jumlah_asal'	=> $count, 'jumlah_masuk' => Kd_nilai::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'nilai';
		echo json_encode($result);
	}
	public function nilai($sekolah_id){
		$i=0;
		$percent = intval(12/ 26 * 100);
		$limit = 10000;
		$query = DB::connection('erapor4')->table('nilai')->where('nilai.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'nilai.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'nilai.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'nilai.rombongan_belajar_id');
		});
		$count = $query->count();
		$record['table'] = 'nilai';
		$record['jumlah'] = number_format($count,0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		for ($counter = 0; $counter <= $count; $counter += $limit) {
			$query = DB::connection('erapor4')->table('nilai')->where('nilai.sekolah_id', $sekolah_id);
			$query->join('anggota_rombel', function ($join) {
				$join->on('anggota_rombel.semester_id', '=', 'nilai.semester_id');
				$join->on('anggota_rombel.siswa_id', '=', 'nilai.siswa_id');
				$join->on('anggota_rombel.rombongan_belajar_id', '=', 'nilai.rombongan_belajar_id');
			});
			$query->offset($counter);
			$query->limit($limit);
			$erapor = $query->get();
			foreach($erapor as $data){
				$find_kd_nilai = Kd_nilai::where('kd_nilai_id_migrasi', '=', $data->kd_nilai_id)->first();
				if($find_kd_nilai){
					$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
					if($find_anggota_rombel){
						$insert_nilai = array(
							'sekolah_id'		=> $sekolah_id,
							'kd_nilai_id'		=> $find_kd_nilai->kd_nilai_id,
							'nilai'				=> $data->nilai,
							'rerata'			=> $data->rerata_jadi,
							'last_sync'			=> date('Y-m-d H:i:s'),
						);
						$create_data = Nilai::updateOrCreate(
							['kd_nilai_id' => $find_kd_nilai->kd_nilai_id, 'anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id, 'kompetensi_id' => $data->kompetensi_id],
							$insert_nilai
						);
						if($create_data){
							$record['progress'] = $percent + (intval($i/ $count * 100) / 100);
							$record['inserted'] = number_format($i,0,',','.');
							Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
							$i++;
						}
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'nilai'],
			['jumlah_asal'	=> $count, 'jumlah_masuk' => Nilai::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'nilai_akhir';
		echo json_encode($result);
	}
	public function nilai_akhir($sekolah_id){
		$i=0;
		$percent = intval(13/ 26 * 100);
		$limit = 10000;
		$erapor = DB::connection('erapor4')->table('nilai_akhir')->where('nilai_akhir.sekolah_id', $sekolah_id)
		->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'nilai_akhir.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'nilai_akhir.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'nilai_akhir.rombongan_belajar_id');
		})
		->join('pembelajaran', function ($join) {
			$join->on('pembelajaran.rombongan_belajar_id', '=', 'nilai_akhir.rombongan_belajar_id');
			$join->on('pembelajaran.mata_pelajaran_id', '=', 'nilai_akhir.mata_pelajaran_id');
		})->get();
		$count = $erapor->count();
		$record['table'] = 'nilai_akhir';
		$record['jumlah'] = number_format($count,0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_migrasi', '=', $data->anggota_rombel_id)->first();
			$find_pembelajaran = Pembelajaran::where('pembelajaran_id_migrasi', '=', $data->pembelajaran_id)->first();
			if($find_anggota_rombel && $find_pembelajaran){
				$insert_nilai_akhir = array(
					'sekolah_id'		=> $sekolah_id,
					'nilai'				=> $data->nilai,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$create_data = Nilai_akhir::updateOrCreate(
					['pembelajaran_id' => $find_pembelajaran->pembelajaran_id, 'anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id, 'kompetensi_id' => $data->kompetensi_id],
					$insert_nilai_akhir
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $count * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'nilai_akhir'],
			['jumlah_asal'	=> $count, 'jumlah_masuk' => Nilai_akhir::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'nilai_rapor';
		echo json_encode($result);
	}
	public function nilai_rapor($sekolah_id){
		$i=0;
		$percent = intval(14/ 26 * 100);
		$limit = 10000;
		$erapor = DB::connection('erapor4')->table('nilai_rapor')->where('nilai_rapor.sekolah_id', $sekolah_id)
		->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'nilai_rapor.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'nilai_rapor.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'nilai_rapor.rombongan_belajar_id');
		})
		->join('pembelajaran', function ($join) {
			$join->on('pembelajaran.rombongan_belajar_id', '=', 'nilai_rapor.rombongan_belajar_id');
			$join->on('pembelajaran.mata_pelajaran_id', '=', 'nilai_rapor.mata_pelajaran_id');
		})->get();
		$count = $erapor->count();
		$record['table'] = 'nilai_rapor';
		$record['jumlah'] = number_format($count,0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_migrasi', '=', $data->anggota_rombel_id)->first();
			$find_pembelajaran = Pembelajaran::where('pembelajaran_id_migrasi', '=', $data->pembelajaran_id)->first();
			if($find_anggota_rombel && $find_pembelajaran){
				$insert_nilai_rapor = array(
					'sekolah_id'		=> $sekolah_id,
					'nilai_p'			=> $data->nilai_p,
					'nilai_k'			=> $data->nilai_k,
					'rasio_p'			=> $data->rasio_p,
					'rasio_k'			=> $data->rasio_k,
					'total_nilai'		=> ($data->nilai_p + $data->nilai_k),
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$create_data = Nilai_rapor::updateOrCreate(
					['pembelajaran_id' => $find_pembelajaran->pembelajaran_id, 'anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id],
					$insert_nilai_rapor
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $count * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'nilai_rapor'],
			['jumlah_asal'	=> $count, 'jumlah_masuk' => Nilai_rapor::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'absen';
		echo json_encode($result);
	}
	public function absen($sekolah_id){
		$i=0;
		$percent = intval(15/ 26 * 100);
		$query = DB::connection('erapor4')->table('absen')->where('absen.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'absen.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'absen.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'absen.rombongan_belajar_id');
		});
		$erapor = $query->get();
		$record['table'] = 'absen';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				$insert_absen = array(
					'sekolah_id'	=> $sekolah_id,
					'sakit'			=> $data->sakit,
					'izin'			=> $data->izin,
					'alpa'			=> $data->alpa,
					'last_sync'		=> date('Y-m-d H:i:s'),
				);
				$create_data = Absensi::updateOrCreate(
					['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id],
					$insert_absen
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'absen'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Absensi::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'catatan_ppk';
		echo json_encode($result);
	}
	public function catatan_ppk($sekolah_id){
		$i=0;
		$percent = intval(16/ 26 * 100);
		$query = DB::connection('erapor4')->table('catatan_ppk')->where('catatan_ppk.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'catatan_ppk.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'catatan_ppk.siswa_id');
		});
		$erapor = $query->get();
		$count = $erapor->count();
		$record['table'] = 'catatan_ppk';
		$record['jumlah'] = number_format($count,0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$capaian = strip_tags($data->capaian);
			$capaian = preg_replace('/\s+/', ' ', $capaian);
			$capaian = str_replace('&nbsp;', '', $capaian);
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				$insert_catatan_ppk = array(
					'sekolah_id'				=> $sekolah_id,
					'capaian'					=> $capaian,
					'last_sync'					=> date('Y-m-d H:i:s'),
					'catatan_ppk_id_migrasi'	=> $data->catatan_ppk_id,
				);
				$create_data = Catatan_ppk::updateOrCreate(
					['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id],
					$insert_catatan_ppk
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $count * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'catatan_ppk'],
			['jumlah_asal'	=> $count, 'jumlah_masuk' => Catatan_ppk::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'catatan_wali';
		echo json_encode($result);
	}
	public function catatan_wali($sekolah_id){
		$i=0;
		$percent = intval(17/ 26 * 100);
		$query = DB::connection('erapor4')->table('catatan_wali')->where('catatan_wali.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'catatan_wali.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'catatan_wali.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'catatan_wali.rombongan_belajar_id');
		});
		$erapor = $query->get();
		$record['table'] = 'catatan_wali';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				$insert_catatan_wali = array(
					'sekolah_id'		=> $sekolah_id,
					'uraian_deskripsi'	=> strip_tags($data->uraian_deskripsi),
					'last_sync'		=> date('Y-m-d H:i:s'),
				);
				$create_data = Catatan_wali::updateOrCreate(
					['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id],
					$insert_catatan_wali
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'catatan_wali'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Catatan_wali::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'deskripsi_mata_pelajaran';
		echo json_encode($result);
	}
	public function deskripsi_mata_pelajaran($sekolah_id){
		$i=0;
		$percent = intval(18/ 26 * 100);
		$query = DB::connection('erapor4')->table('deskripsi_mata_pelajaran')->where('deskripsi_mata_pelajaran.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'deskripsi_mata_pelajaran.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'deskripsi_mata_pelajaran.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'deskripsi_mata_pelajaran.rombongan_belajar_id');
		});
		$query->join('pembelajaran', function ($join) {
			$join->on('pembelajaran.semester_id', '=', 'deskripsi_mata_pelajaran.semester_id');
			$join->on('pembelajaran.mata_pelajaran_id', '=', 'deskripsi_mata_pelajaran.mata_pelajaran_id');
			$join->on('pembelajaran.rombongan_belajar_id', '=', 'deskripsi_mata_pelajaran.rombongan_belajar_id');
		});
		$erapor = $query->get();
		$record['table'] = 'deskripsi_mata_pelajaran';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				$find_pembelajaran = Pembelajaran::where('pembelajaran_id_dapodik', '=', $data->pembelajaran_id_dapodik)->first();
				if($find_pembelajaran){
					$insert_deskripsi_mata_pelajaran = array(
						'sekolah_id'			=> $sekolah_id,
						'deskripsi_pengetahuan'	=> trim($data->deskripsi_pengetahuan),
						'deskripsi_keterampilan'=> trim($data->deskripsi_keterampilan),
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					$create_data = Deskripsi_mata_pelajaran::updateOrCreate(
						['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id, 'pembelajaran_id' => $find_pembelajaran->pembelajaran_id],
						$insert_deskripsi_mata_pelajaran
					);
					if($create_data){
						$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
						$record['inserted'] = number_format($i,0,',','.');
						Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
						$i++;
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'deskripsi_mata_pelajaran'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Deskripsi_mata_pelajaran::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'deskripsi_sikap';
		echo json_encode($result);
	}
	public function deskripsi_sikap($sekolah_id){
		$i=0;
		$percent = intval(19/ 26 * 100);
		$query = DB::connection('erapor4')->table('deskripsi_sikap')->where('deskripsi_sikap.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'deskripsi_sikap.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'deskripsi_sikap.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'deskripsi_sikap.rombongan_belajar_id');
		});
		$erapor = $query->get();
		$record['table'] = 'deskripsi_sikap';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				$insert_deskripsi_sikap = array(
					'sekolah_id'					=> $sekolah_id,
					'uraian_deskripsi_spiritual'	=> trim($data->uraian_deskripsi_spiritual),
					'uraian_deskripsi_sosial'		=> trim($data->uraian_deskripsi_sosial),
					'predikat_spiritual'			=> trim($data->predikat_spiritual),
					'predikat_sosial'				=> trim($data->predikat_sosial),
					'last_sync'						=> date('Y-m-d H:i:s'),
				);
				$create_data = Deskripsi_sikap::updateOrCreate(
					['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id],
					$insert_deskripsi_sikap
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'deskripsi_sikap'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Deskripsi_sikap::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'kenaikan_kelas';
		echo json_encode($result);
	}
	public function kenaikan_kelas($sekolah_id){
		$i=0;
		$percent = intval(20/ 26 * 100);
		$query = DB::connection('erapor4')->table('kenaikan_kelas')->where('kenaikan_kelas.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', 'anggota_rombel.anggota_rombel_id', '=', 'kenaikan_kelas.anggota_rombel_id');
		$erapor = $query->get();
		$record['table'] = 'kenaikan_kelas';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				if($data->ke_kelas){
					$find_rombongan_belajar = Rombongan_belajar::where('rombongan_belajar_id_migrasi', '=', $data->ke_kelas)->first();
					$rombongan_belajar_id = $find_rombongan_belajar->rombongan_belajar_id;
					$insert_kenaikan_kelas = array(
						'sekolah_id'			=> $sekolah_id,
						'rombongan_belajar_id'	=> $rombongan_belajar_id,
						'status'				=> $data->status,
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					$create_data = Kenaikan_kelas::updateOrCreate(
						['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id],
						$insert_kenaikan_kelas
					);
					if($create_data){
						$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
						$record['inserted'] = number_format($i,0,',','.');
						Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
						$i++;
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'kenaikan_kelas'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Kenaikan_kelas::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'nilai_ekstrakurikuler';
		echo json_encode($result);
	}
	public function nilai_ekstrakurikuler($sekolah_id){
		$i=0;
		$percent = intval(21/ 26 * 100);
		$query = DB::connection('erapor4')->table('nilai_ekstrakurikuler')->where('nilai_ekstrakurikuler.sekolah_id', $sekolah_id);
		$query->join('ekstrakurikuler', 'ekstrakurikuler.ekstrakurikuler_id', '=', 'nilai_ekstrakurikuler.ekstrakurikuler_id');
		$query->join('rombongan_belajar', 'ekstrakurikuler.rombongan_belajar_id', '=', 'rombongan_belajar.rombongan_belajar_id');
		$query->join('anggota_rombel', 'rombongan_belajar.rombongan_belajar_id', '=', 'anggota_rombel.rombongan_belajar_id');
		$query->select(['nilai_ekstrakurikuler.nilai', 'nilai_ekstrakurikuler.deskripsi_ekskul', 'anggota_rombel.rombongan_belajar_id', 'anggota_rombel.anggota_rombel_id', 'anggota_rombel.anggota_rombel_id_dapodik', 'ekstrakurikuler.nama_ekskul', 'ekstrakurikuler.id_kelas_ekskul']);
        $erapor = $query->get();
		$record['table'] = 'nilai_ekstrakurikuler';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_ekskul = Ekstrakurikuler::with(['anggota_rombel_satuan' => function($query) use ($data){
				$query->where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik);
			}])->where('id_kelas_ekskul', '=', $data->id_kelas_ekskul)->first();
			if($find_ekskul->anggota_rombel_satuan){
				$insert_nilai_ekstrakurikuler = array(
					'sekolah_id'			=> $sekolah_id,
					'nilai'					=> ($data->nilai) ? $data->nilai : NULL,
					'deskripsi_ekskul'		=> ($data->deskripsi_ekskul) ? $data->deskripsi_ekskul : NULL,
					'last_sync'				=> date('Y-m-d H:i:s'),
				);
				$create_data = Nilai_ekstrakurikuler::updateOrCreate(
					['anggota_rombel_id' => $find_ekskul->anggota_rombel_satuan->anggota_rombel_id, 'ekstrakurikuler_id' => $find_ekskul->ekstrakurikuler_id],
					$insert_nilai_ekstrakurikuler
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $count * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'nilai_ekstrakurikuler'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Nilai_ekstrakurikuler::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'ref_sikap';
		echo json_encode($result);
	}
	public function ref_sikap($sekolah_id){
		$i=0;
		$percent = intval(22/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('ref_sikap')->get();
		$record['table'] = 'ref_sikap';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$insert_sikap = array(
				'sikap_id_migrasi' => $data->sikap_id,
				'last_sync'	=> date('Y-m-d H:i:s'),
			);
			$create_data = Sikap::updateOrCreate(
				['butir_sikap' => $data->butir_sikap],
				$insert_sikap
			);
			if($create_data){
				$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
				$record['inserted'] = number_format($i,0,',','.');
				Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
				$i++;
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'ref_sikap'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => $i]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'nilai_karakter';
		echo json_encode($result);
	}
	public function nilai_karakter($sekolah_id){
		$i=0;
		$percent = intval(23/ 26 * 100);
		$erapor = DB::connection('erapor4')->table('nilai_karakter')->where('sekolah_id', $sekolah_id)->get();
		$record['table'] = 'nilai_karakter';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_sikap = Sikap::where('sikap_id_migrasi', '=', $data->sikap_id)->first();
			if($find_sikap){
				$find_catatan_ppk = Catatan_ppk::where('catatan_ppk_id_migrasi', '=', $data->catatan_ppk_id)->first();
				if($find_catatan_ppk){
					$insert_nilai_karakter = array(
						'sekolah_id'			=> $sekolah_id,
						'deskripsi'				=> ($data->deskripsi) ? $data->deskripsi : NULL,
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					$create_data = Nilai_karakter::updateOrCreate(
						['catatan_ppk_id' => $find_catatan_ppk->catatan_ppk_id, 'sikap_id'	=> $find_sikap->sikap_id],
						$insert_nilai_karakter
					);
					if($create_data){
						$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
						$record['inserted'] = number_format($i,0,',','.');
						Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
						$i++;
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'nilai_karakter'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Nilai_karakter::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'nilai_sikap';
		echo json_encode($result);
	}
	public function nilai_sikap($sekolah_id){
		$i=0;
		$percent = intval(24/ 26 * 100);
		$query = DB::connection('erapor4')->table('nilai_sikap')->where('nilai_sikap.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'nilai_sikap.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'nilai_sikap.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'nilai_sikap.rombongan_belajar_id');
		});
		$erapor = $query->get();
		$record['table'] = 'nilai_sikap';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel && $data->butir_sikap){
				$find_sikap = Sikap::where('sikap_id_migrasi', '=', $data->butir_sikap)->first();
				if($find_sikap){
					$find_guru = Guru::where('guru_id_migrasi', '=', $data->guru_id)->first();
					if($find_guru){
						$insert_nilai_sikap = array(
							'sekolah_id'			=> $sekolah_id,
							'guru_id'				=> $find_guru->guru_id,
							'tanggal_sikap'			=> $data->tanggal_sikap,
							'uraian_sikap'			=> $data->uraian_sikap,
							'last_sync'				=> date('Y-m-d H:i:s'),
							'anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id, 'sikap_id'	=> $find_sikap->sikap_id, 'opsi_sikap' => $data->opsi_sikap
						);
						$create_data = Nilai_sikap::updateOrCreate(
							['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id, 'sikap_id'	=> $find_sikap->sikap_id, 'opsi_sikap' => $data->opsi_sikap],
							$insert_nilai_sikap
						);
						if($create_data){
							$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
							$record['inserted'] = number_format($i,0,',','.');
							Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
							$i++;
						}
					}
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'nilai_sikap'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Nilai_sikap::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'prakerin';
		echo json_encode($result);
	}
	public function prakerin($sekolah_id){
		$i=0;
		$percent = intval(25/ 26 * 100);
		$query = DB::connection('erapor4')->table('prakerin')->where('prakerin.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'prakerin.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'prakerin.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'prakerin.rombongan_belajar_id');
		});
		$erapor = $query->get();
		$record['table'] = 'prakerin';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				$insert_prakerin = array(
					'sekolah_id'			=> $sekolah_id,
					'mitra_prakerin'		=> $data->mitra_prakerin,
					'lokasi_prakerin'		=> $data->lokasi_prakerin,
					'lama_prakerin'			=> $data->lama_prakerin,
					'keterangan_prakerin'	=> $data->keterangan_prakerin,
					'last_sync'				=> date('Y-m-d H:i:s'),
				);
				$create_data = Prakerin::updateOrCreate(
					['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id],
					$insert_prakerin
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'prakerin'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Prakerin::count()]
		);
		$result['status'] = 1;
		$result['progress'] = $percent;
		$result['table']	= 'prestasi';
		echo json_encode($result);
	}
	public function prestasi($sekolah_id){
		$i=0;
		$percent = intval(26/ 26 * 100);
		//$erapor = DB::connection('erapor4')->table('prestasi')->where('sekolah_id', $sekolah_id)->get();
		$query = DB::connection('erapor4')->table('prestasi')->where('prestasi.sekolah_id', $sekolah_id);
		$query->join('anggota_rombel', function ($join) {
			$join->on('anggota_rombel.semester_id', '=', 'prestasi.semester_id');
			$join->on('anggota_rombel.siswa_id', '=', 'prestasi.siswa_id');
			$join->on('anggota_rombel.rombongan_belajar_id', '=', 'prestasi.rombongan_belajar_id');
		});
		$erapor = $query->get();
		$record['table'] = 'prestasi';
		$record['jumlah'] = number_format($erapor->count(),0,',','.');
		$record['inserted'] = $i;
		Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
		foreach($erapor as $data){
			$find_anggota_rombel = Anggota_rombel::where('anggota_rombel_id_dapodik', '=', $data->anggota_rombel_id_dapodik)->first();
			if($find_anggota_rombel){
				$insert_prestasi = array(
					'sekolah_id'			=> $sekolah_id,
					'keterangan_prestasi'	=> $data->keterangan_prestasi,
					'last_sync'				=> date('Y-m-d H:i:s'),
				);
				$create_data = Prestasi::updateOrCreate(
					['anggota_rombel_id' => $find_anggota_rombel->anggota_rombel_id, 'jenis_prestasi' => $data->jenis_prestasi],
					$insert_prestasi
				);
				if($create_data){
					$record['progress'] = $percent + (intval($i/ $erapor->count() * 100) / 100);
					$record['inserted'] = number_format($i,0,',','.');
					Storage::disk('public')->put('proses_migrasi.json', json_encode($record));
					$i++;
				}
			}
		}
		Migrasi::updateOrCreate(
			['nama_table' => 'prestasi'],
			['jumlah_asal'	=> $erapor->count(), 'jumlah_masuk' => Prestasi::count()]
		);
		$result['status'] = 0;
		$result['progress'] = $percent;
		$result['table']	= '';
		echo json_encode($result);
	}
}
