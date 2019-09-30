<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Providers\HelperServiceProvider;
use Yajra\Datatables\Datatables;
use App\Semester;
use App\Pembelajaran;
use App\Rencana_penilaian;
use App\Kd_nilai;
use App\Bobot_keterampilan;
use App\Kurikulum;
use App\Kompetensi_dasar;
use CustomHelper;
use App\Guru;
use Illuminate\Support\Facades\Validator;
use App\Rencana_ukk;
use App\Nilai_ukk;
use App\Anggota_rombel;
class PerencanaanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$pembelajaran = Pembelajaran::with('mata_pelajaran')->select('mata_pelajaran_id','rasio_p','rasio_k')
		->where('sekolah_id', '=', $user->sekolah_id)
		->where('semester_id', '=', $semester->semester_id)
		->where('guru_id', '=', $user->guru_id)
		->whereNotNull('kelompok_id')
		->whereNotNull('no_urut')
		->orWhere('guru_pengajar_id', '=', $user->guru_id)
		->where('sekolah_id', '=', $user->sekolah_id)
		->where('semester_id', '=', $semester->semester_id)
		->whereNotNull('kelompok_id')
		->whereNotNull('no_urut')
		->orderBy('mata_pelajaran_id', 'asc')
		->groupBy('mata_pelajaran_id')
		->groupBy('rasio_p')
		->groupBy('rasio_k')
		->get();
		$params = array(
			'all_pembelajaran' => $pembelajaran,
			'semester'	=> $semester,
		);
		return view('perencanaan.rasio')->with($params);
    }
	public function pengetahuan(){
		return view('perencanaan.pengetahuan');
    }
	public function keterampilan(){
		return view('perencanaan.keterampilan');
    }
	public function list_rencana(Request $request, $kompetensi_id){
		$callback = function($query) {
			$user = auth()->user();
			$semester = Semester::where('periode_aktif', 1)->first();
			$query->where('sekolah_id', '=', $user->sekolah_id);
			$query->where('guru_id', '=', $user->guru_id);
			$query->where('semester_id', '=', $semester->semester_id);
			$query->orWhere('guru_pengajar_id', '=', $user->guru_id);
			$query->where('sekolah_id', '=', $user->sekolah_id);
			$query->where('semester_id', '=', $semester->semester_id);
		};
		$query = Rencana_penilaian::whereHas('pembelajaran', $callback)->with(['pembelajaran' => $callback, 'pembelajaran.rombongan_belajar'])->where('kompetensi_id', '=', $kompetensi_id)
		->withCount('kd_nilai')->with('teknik_penilaian');
		return DataTables::of($query)
		->addColumn('nama_mata_pelajaran', function ($item) {
			$return  = ($item->pembelajaran) ? $item->pembelajaran->nama_mata_pelajaran. ' ('.$item->pembelajaran->mata_pelajaran_id.')' : '-';
			return $return;
		})
		->addColumn('kelas', function ($item) {
			$return  = ($item->pembelajaran) ? $item->pembelajaran->rombongan_belajar->nama : '-';
			return $return;
		})
		->addColumn('metode', function ($item) {
			$return  = ($item->teknik_penilaian) ? $item->teknik_penilaian->nama : '-';
			return $return;
		})
		->addColumn('actions', function ($item) use($kompetensi_id) {
			$nilai = 0;
			$admin_akses = '';
			if($nilai){
				$admin_akses .= '<li><a href="'.url('perencanaan/delete/'.$kompetensi_id.'/'.$item->rencana_penilaian_id).'" class="confirm"><i class="fa fa-power-off"></i> Hapus</a></li>';
			} else {
				$admin_akses .= '<li><a href="'.url('perencanaan/edit/'.$kompetensi_id.'/'.$item->rencana_penilaian_id).'"><i class="fa fa-pencil"></i> Edit</a></li>';
				$admin_akses .= '<li><a href="'.url('perencanaan/delete/'.$kompetensi_id.'/'.$item->rencana_penilaian_id).'" class="confirm"><i class="fa fa-power-off"></i> Hapus</a></li>';
			}
			$return  = '<div class="text-center"><div class="btn-group">
							<button type="button" class="btn btn-default btn-sm">Aksi</button>
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu pull-right text-left" role="menu">
								 '.$admin_akses.'
                            </ul>
                        </div></div>';
			return $return;
		})
		->rawColumns(['nama_mata_pelajaran', 'kelas', 'metode', 'actions'])
		->make(true);
	}
	public function edit_rencana($kompetensi_id, $rencana_id){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$rencana = Rencana_penilaian::with('rombongan_belajar')->with('pembelajaran')->with('kd_nilai')->with('metode')->find($rencana_id);
		$get_kurikulum = Kurikulum::find($rencana->rombongan_belajar->kurikulum_id);
		if (strpos($get_kurikulum->nama_kurikulum, 'REV') !== false) {
			$kurikulum = 2017;
		} elseif (strpos($get_kurikulum->nama_kurikulum, 'KTSP') !== false) {
			$kurikulum = 2006;
		} else {
			$kurikulum = 2013;
		}
		$all_kd = Kompetensi_dasar::where('kompetensi_id', '=', $kompetensi_id)->where('mata_pelajaran_id', '=', $rencana->pembelajaran->mata_pelajaran_id)->where('kelas_'.$rencana->rombongan_belajar->tingkat, '=', 1)->where('aktif', '=', 1)->where('kurikulum', '=', $kurikulum)->orderBy('id_kompetensi', 'ASC')->get();
		$param = array(
			'semester' => $semester,
			'user'	=> $user,
			'rencana'	=> $rencana,
			'all_kd'	=> $all_kd,
		);
		if($kompetensi_id == 1){
			return view('perencanaan.edit_pengetahuan')->with($param);
		} else {
			return view('perencanaan.edit_keterampilan')->with($param);
		}
	}
	public function delete($kompetensi_id, $rencana_id){
		if(Rencana_penilaian::destroy($rencana_id)){
			$flash['success'] = 'Berhasil menghapus rencana penilaian';
		} else {
			$flash['error'] = 'Gagal menghapus rencana penilaian';
		}
		$nama_perencanaan = ($kompetensi_id == 1) ? 'pengetahuan' : 'keterampilan';
		return redirect()->route('perencanaan_'.$nama_perencanaan)->with($flash);
	}
	public function bobot(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$callback = function($query) use ($user, $semester){
			$query->where('pembelajaran.sekolah_id', '=', $user->sekolah_id);
			$query->where('pembelajaran.semester_id', '=', $semester->semester_id);
			$query->where('pembelajaran.guru_id', '=', $user->guru_id);
			$query->whereNotNull('kelompok_id');
			$query->whereNotNull('no_urut');
			$query->orWhere('pembelajaran.sekolah_id', '=', $user->sekolah_id);
			$query->where('pembelajaran.semester_id', '=', $semester->semester_id);
			$query->where('pembelajaran.guru_pengajar_id', '=', $user->guru_id);
			$query->whereNotNull('kelompok_id');
			$query->whereNotNull('no_urut');
		};
		$all_bobot = Bobot_keterampilan::whereHas('pembelajaran', $callback)->with('metode')->with('pembelajaran', 'pembelajaran.rombongan_belajar')->get();
		$params = array(
			'all_bobot' => $all_bobot,
		);
		return view('perencanaan.bobot')->with($params);
    }
	public function simpan_bobot(Request $request){
		$all_bobot = $request['bobot'];
		$sukses = 0;
		foreach($all_bobot as $key => $value){
			$bobot = Bobot_keterampilan::find($key);
			$bobot->bobot = $value;
			if($bobot->save()){
				$sukses++;
				Rencana_penilaian::where('metode_id', $bobot->metode_id)->update(['bobot' => $value]);
			}
		}
		if($sukses){
			$flash['success'] = 'Berhasil menyimpan bobot keterampilan';
		} else {
			$flash['error'] = 'Gagal menyimpan bobot keterampilan';
		}
		return redirect()->route('list_bobot')->with($flash);
	}
	public function ukk(){
		return view('perencanaan.ukk');
    }
	public function simpan_rasio(Request $request){
		$user = auth()->user();
		$semester_id= $request['semester_id'];
		$mata_pelajaran_id = $request['mata_pelajaran_id'];
		$rasio_p = $request['rasio_p'];
		$rasio_k = $request['rasio_k'];
		$sukses = 0;
		$gagal = 0;
		$numeric = 0;
		$flash = array();
		if(is_array($rasio_p)){
			foreach($rasio_p  as $key => $value){
				if(is_numeric($value) && is_numeric($rasio_k[$key])){
					$jumlah_rasio = ($value + $rasio_k[$key]);
					if($jumlah_rasio != 100){
						$gagal++;
					} else {
						$mapel_id = $mata_pelajaran_id[$key];
						$find_pembelajaran_guru_id = Pembelajaran::where('semester_id', '=', $semester_id)->where('guru_id', '=', $user->guru_id)->where('mata_pelajaran_id', '=', $mapel_id)->get();
						$update_rasio = array(
							'rasio_p'	=> $value,
							'rasio_k'	=> $rasio_k[$key]
						);
						if($find_pembelajaran_guru_id){
							foreach($find_pembelajaran_guru_id as $p_guru_id){
								$p_guru_id->rasio_p = $value;
								$p_guru_id->rasio_k = $rasio_k[$key];
								if($p_guru_id->save()){
									$sukses++;
								}
							}
						}
						$find_pembelajaran_guru_pengajar_id = Pembelajaran::where('semester_id', '=', $semester_id)->where('guru_pengajar_id', '=', $user->guru_id)->where('mata_pelajaran_id', '=', $mapel_id)->get();
						if($find_pembelajaran_guru_pengajar_id){
							foreach($find_pembelajaran_guru_pengajar_id as $p_guru_pengajar_id){
								$p_guru_pengajar_id->rasio_p = $value;
								$p_guru_pengajar_id->rasio_k = $rasio_k[$key];
								if($p_guru_pengajar_id->save()){
									$sukses++;
								}
							}
						}
					}
				} else {
					$numeric++;
				}
			}
		}
		if($sukses){
			$flash['success'] = 'Berhasil menyimpan rasio nilai akhir';
		}
		if($gagal){
			$flash['error'] = 'Gagal menyimpan rasio nilai akhir. Akumulasi rasio harus sama dengan 100 (seratus)';
		}
		if($numeric){
			$flash['error'] = 'Gagal menyimpan rasio nilai akhir. Isian rasio harus berupa angka';
		}
		return redirect()->route('rasio')->with($flash);
    }
	public function tambah_pengetahuan(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$param = array(
			'semester' => $semester,
			'user'	=> $user,
		);
		return view('perencanaan.tambah_pengetahuan')->with($param);
    }
	public function tambah_keterampilan(){
		$user = auth()->user();
		$semester = Semester::where('periode_aktif', 1)->first();
		$param = array(
			'semester' => $semester,
			'user'	=> $user,
		);
		return view('perencanaan.tambah_keterampilan')->with($param);
    }
	public function simpan_perencanaan(Request $request){
		$user = auth()->user();
		$rencana_penilaian_id 		= $request['rencana_penilaian_id'];
		$kompetensi_id 		= $request['kompetensi_id'];
		$nama_penilaian		= $request['nama_penilaian'];
		$pembelajaran_id	= $request['pembelajaran_id'];
		$teknik_penilaian	= $request['teknik_penilaian'];
		$set_bobot			= $request['bobot'];
		$bobot_value		= $request['bobot_value'];
		$bobot				= ($set_bobot) ? $set_bobot : $bobot_value;
		$keterangan_penilaian= $request['keterangan_penilaian'];
		$insert = 0;
		$gagal = 0;
		if($rencana_penilaian_id){
			$id_kompetensi = $request['id_kompetensi'];
			$rencana = Rencana_penilaian::find($rencana_penilaian_id);
			$rencana->nama_penilaian = $nama_penilaian;
			if($kompetensi_id == 1){
				$rencana->bobot = $set_bobot;
			}
			$rencana->keterangan = $keterangan_penilaian;
			$nama_perencanaan = ($kompetensi_id == 2) ? 'Keterampilan' : 'Pengetahuan';
			if($rencana->save()){
				if($request['kd']){
					Kd_nilai::where('rencana_penilaian_id', $rencana_penilaian_id)->whereNotIn('kd_id', $request['kd'])->delete();
					foreach($request['kd'] as $key => $kd){
						Kd_nilai::firstOrCreate(
							['rencana_penilaian_id' => $rencana_penilaian_id, 'kd_id' => $kd],
							['id_kompetensi' => $id_kompetensi[$key], 'last_sync' => date('Y-m-d H:i:s'), 'sekolah_id' => $user->sekolah_id]
						);
					}
				}
				$flash['success'] = 'Data perencanaan '.$nama_perencanaan.' berhasil diperbaharui';
			} else {
				$flash['error'] = 'Data perencanaan '.$nama_perencanaan.' gagal diperbaharui';
			}
			return redirect()->route('perencanaan_'.strtolower($nama_perencanaan))->with($flash);
		}
		$nama_penilaian = array_filter($nama_penilaian);
		if($nama_penilaian){
			if($kompetensi_id == 2){
				$rules['teknik_penilaian'] = 'required';
				$rules['bobot'] = 'required|integer';
				$customMessages = [
					'required' => 'Isian :attribute tidak boleh kosong.',
					'integer' => 'Isian :attribute harus berupa angka.'
				];
				$this->validate($request, $rules, $customMessages);
				$find_bobot_keterampilan = Bobot_keterampilan::where('pembelajaran_id', '=', $pembelajaran_id)->where('metode_id', '=', $teknik_penilaian)->first();
				if(!$find_bobot_keterampilan){
					$insert_bobot_keterampilan = array(
						'sekolah_id'			=> $user->sekolah_id,
						'pembelajaran_id'		=> $pembelajaran_id,
						'metode_id'				=> $teknik_penilaian,
						'bobot'					=> $bobot,
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					Bobot_keterampilan::create($insert_bobot_keterampilan);
				}
				$nama_perencanaan = 'Keterampilan';
				foreach($nama_penilaian as $k=>$v) {
					$i = $k + 1;
					$kds		= $request['kd_'.$i];
					$insert_rencana = array(
						'sekolah_id'			=> $user->sekolah_id,
						'pembelajaran_id'		=> $pembelajaran_id,
						'kompetensi_id'			=> $kompetensi_id,
						'nama_penilaian'		=> $nama_penilaian[$k],
						'metode_id'				=> $teknik_penilaian,
						'bobot'					=> $bobot,
						'keterangan'			=> $keterangan_penilaian[$k],
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					$rencana_penilaian = Rencana_penilaian::create($insert_rencana);
					if($rencana_penilaian->exists){
						foreach($kds as $kd){
							$get_post_kd = explode("|", $kd);
							$insert_kd_nilai = array(
								'sekolah_id'			=> $user->sekolah_id,
								'rencana_penilaian_id' 	=> $rencana_penilaian->rencana_penilaian_id,
								'id_kompetensi' 		=> $get_post_kd[0],
								'kd_id' 				=> $get_post_kd[1],
								'last_sync'				=> date('Y-m-d H:i:s'),
							);
							if(Kd_nilai::create($insert_kd_nilai)){
								$insert++;
							}
						}
					}
				}
			} else {
				foreach($nama_penilaian as $k=>$v) {
					$rules['nama_penilaian.'.$k] = 'required';
					$rules['bentuk_penilaian.'.$k] = 'required';
					$rules['bobot_penilaian.'.$k] = 'required|integer';
				}
				$customMessages = [
					'required' => 'Isian :attribute tidak boleh kosong.',
					'integer' => 'Isian :attribute harus berupa angka.'
				];
				$this->validate($request, $rules, $customMessages);
				$bobot_penilaian = $request['bobot_penilaian'];
				$bentuk_penilaian = $request['bentuk_penilaian'];
				foreach($nama_penilaian as $k=>$v) {
					$i = $k + 1;
					$kds		= $request['kd_'.$i];
					$insert_rencana = array(
						'sekolah_id'			=> $user->sekolah_id,
						'pembelajaran_id'		=> $pembelajaran_id,
						'kompetensi_id'			=> $kompetensi_id,
						'nama_penilaian'		=> $nama_penilaian[$k],
						'metode_id'				=> $bentuk_penilaian[$k],
						'bobot'					=> $bobot_penilaian[$k],
						'keterangan'			=> $keterangan_penilaian[$k],
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					$rencana_penilaian = Rencana_penilaian::create($insert_rencana);
					if($rencana_penilaian->exists){
						foreach($kds as $kd){
							$get_post_kd = explode("|", $kd);
							$insert_kd_nilai = array(
								'sekolah_id'			=> $user->sekolah_id,
								'rencana_penilaian_id' 	=> $rencana_penilaian->rencana_penilaian_id,
								'id_kompetensi' 		=> $get_post_kd[0],
								'kd_id' 				=> $get_post_kd[1],
								'last_sync'				=> date('Y-m-d H:i:s'),
							);
							if(Kd_nilai::create($insert_kd_nilai)){
								$insert++;
							}
						}
					}
				}
				$nama_perencanaan = 'Pengetahuan';
			}
		} else {
			$flash['error'] = 'Tidak ada data perencanaan disimpan';
		}
		if($insert){
			$flash['success'] = 'Data perencanaan '.$nama_perencanaan.' berhasil disimpan';
		} else {
			$flash['error'] = 'Data perencanaan '.$nama_perencanaan.' gagal disimpan';
		}
		return redirect()->route('perencanaan_'.strtolower($nama_perencanaan))->with($flash);
    }
	public function tambah_ukk(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		$get_internal = CustomHelper::jenis_gtk('guru');
		$get_eksternal = CustomHelper::jenis_gtk('asesor');
		$callback = function($query){
			$query->whereRoleIs('internal');
		};
		$internal = Guru::where('sekolah_id', '=', $user->sekolah_id)->whereIn('jenis_ptk_id', $get_internal)->whereHas('pengguna', $callback)->with(['pengguna' => $callback])->get();
		$eksternal = Guru::where('sekolah_id', '=', $user->sekolah_id)->whereHas('dudi')->with('dudi')->whereIn('jenis_ptk_id', $get_eksternal)->get();
		$params = array(
			'user' => $user,
			'semester'	=> $semester,
			'internal'	=> $internal,
			'eksternal'	=> $eksternal,
		);
		return view('perencanaan.tambah_ukk')->with($params);
    }
	public function simpan_ukk(Request $request){
		$validator = Validator::make($request->all(), [
			'rombel_id' => 'required',
			'internal' => 'required',
			'eksternal' => 'required',
			'tanggal_sertifikat' => 'required',
		]);
		$siswa_dipilih	= $request['siswa_dipilih'];
		$insert = Rencana_ukk::firstOrCreate(
			[
			'semester_id'			=> $request['semester_id'],
			'paket_ukk_id'			=> $request['paket_ukk_id'],
			'internal'				=> $request['internal'],
			'eksternal'				=> $request['eksternal'],
			],
			[
			'sekolah_id' 			=> $request['sekolah_id'],
			'tanggal_sertifikat'	=> $request['tanggal_sertifikat'],
			'last_sync' 			=> date('Y-m-d H:i:s'), 
			]
		);
		if($insert){
			if($siswa_dipilih){
				foreach($siswa_dipilih as $key => $value){
					Nilai_ukk::firstOrCreate(
						[
						'rencana_ukk_id'		=> $insert->rencana_ukk_id,
						'anggota_rombel_id'		=> $request['anggota_rombel_id'][$key],
						'peserta_didik_id'		=> $request['peserta_didik_id'][$key],
						],
						[
						'sekolah_id' 			=> $request['sekolah_id'],
						'nilai'					=> 0,
						'last_sync' 			=> date('Y-m-d H:i:s'), 
						]
					);
				}
			}
			$flash['success'] = 'Data perencanaan penilaian UKK berhasil disimpan';
		} else {
			$flash['error'] = 'Data perencanaan penilaian UKK gagal disimpan';
		}
		return redirect()->route('perencanaan_ukk')->with($flash);
    }
	public function list_ukk(Request $request){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		$query = Rencana_ukk::with('guru_eksternal')->with('guru_internal')->with('paket_ukk')->where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id);
		if(!$user->hasRole('kaprog')){
			$query->where('internal', '=', $user->guru_id);
		}
		return DataTables::of($query)
		->addColumn('internal', function ($item) {
			$return  = ($item->guru_internal) ? CustomHelper::nama_guru($item->guru_internal->gelar_depan, $item->guru_internal->nama, $item->guru_internal->gelar_belakang) : '-';
			return $return;
		})
		->addColumn('eksternal', function ($item) {
			if($item->guru_eksternal->dudi){
				$return  = CustomHelper::nama_guru($item->guru_eksternal->gelar_depan, $item->guru_eksternal->nama, $item->guru_eksternal->gelar_belakang).' ('.$item->guru_eksternal->dudi->nama.')';
			} else {
				$return  = $item->guru_eksternal->nama;
			}
			return $return;
		})
		->addColumn('paket_ukk', function ($item) {
			$return  = ($item->paket_ukk) ? $item->paket_ukk->nama_paket_id : '-';
			return $return;
		})
		->addColumn('actions', function ($item) {
			$return  = '<div class="text-center"><div class="btn-group">
							<button type="button" class="btn btn-default btn-sm">Aksi</button>
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu pull-right text-left" role="menu">
								 <li><a href="'.url('perencanaan/view-ukk/'.$item->rencana_ukk_id).'" class="toggle-modal"><i class="fa fa-pencil"></i> Detil</a></li>
								 <li><a href="'.url('perencanaan/delete-ukk/'.$item->rencana_ukk_id).'" class="confirm"><i class="fa fa-power-off"></i> Hapus</a></li>
                            </ul>
                        </div></div>';
			return $return;
		})
		->rawColumns(['internal', 'eksternal', 'paket_ukk', 'actions'])
		->make(true);
	}
	public function view_ukk($rencana_ukk_id){
		$callback = function($query) use ($rencana_ukk_id){
			$query->where('rencana_ukk_id', $rencana_ukk_id);
		};
		$data_siswa = Anggota_rombel::with(['rombongan_belajar' => function($query){
			$query->where('jenis_rombel', 1);
		}])->with('siswa')->whereHas('nilai_ukk', $callback)->with(['nilai_ukk' => $callback])->get();
		$rencana_ukk = Rencana_ukk::with(['paket_ukk' => function($query){
			$query->with('jurusan');
			$query->with('kurikulum');
		}, 'guru_internal' => function($query){
			$query->with('gelar_depan');
			$query->with('gelar_belakang');
		}, 'guru_eksternal' => function($query){
			$query->with('gelar_depan');
			$query->with('gelar_belakang');
		}])->find($rencana_ukk_id); 
		$params = array(
			'data_siswa'	=> $data_siswa,
			'rencana_ukk'	=> $rencana_ukk,
		);
		return view('perencanaan.detil_ukk')->with($params);
	}
	public function delete_ukk($rencana_ukk_id){
		$delete = Rencana_ukk::destroy($rencana_ukk_id);
		if($delete){
			$output['text'] = 'Berhasil menghapus perencanaan penilaian UKK';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Gagal menghapus perencanaan penilaian UKK';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
}
