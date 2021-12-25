<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CustomHelper;
use App\Anggota_rombel;
use App\Catatan_wali;
use Session;
use Illuminate\Support\Facades\DB;
use App\Catatan_ppk;
use Yajra\Datatables\Datatables;
use App\Sikap;
use App\Nilai_karakter;
use App\Absensi;
use App\Prakerin;
use App\Prestasi;
use App\Kenaikan_kelas;
use App\Pembelajaran;
use App\Rombongan_belajar;
use App\Rencana_penilaian;
use App\Rapor_pts;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LeggerKDExport;
use App\Exports\LeggerNilaiAkhirExport;
use App\Exports\LeggerNilaiRaporExport;
use App\Exports\AbsensiExport;
use App\Dudi;
use App\Tahun_ajaran;
use App\Nilai_us;
use App\Nilai_un;
use App\Jurusan_sp;
use App\Kewirausahaan;
use App\Anggota_kewirausahaan;
use App\Exports\LaporanExport;
use App\Imports\LaporanImport;
use App\Rombel4_tahun;
use App\Rencana_budaya_kerja;
use App\Opsi_budaya_kerja;
use App\Budaya_kerja;
use Validator;
class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	public function index(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			return view('laporan.waka.catatan_akademik');
		} else {
			$get_siswa = Anggota_rombel::with('siswa')->with('catatan_wali')->with(['nilai_rapor' => function($query){
				$query->with('pembelajaran');
				$query->limit(3);
			}])->whereHas('rombongan_belajar', function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			})->order()->get();
			$params = array(
				'get_siswa'	=> $get_siswa,
			);
			return view('laporan.catatan_akademik')->with($params);
		}
	}
	public function simpan_catatan_akademik(Request $request){
		$sekolah_id = $request['sekolah_id'];
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$uraian_deskripsi = $request['uraian_deskripsi'];
		$uraian_deskripsi = array_filter($uraian_deskripsi);
		$insert=0;
		foreach($uraian_deskripsi as $key => $value){
			$new = Catatan_wali::UpdateOrCreate(
				['anggota_rombel_id' => $anggota_rombel_id[$key]],
				[
					'sekolah_id' => $sekolah_id,
					'uraian_deskripsi' => $value,
					'last_sync'	=> date('Y-m-d H:i:s'),
				]
			);
			if($new){
				$insert++;
			}
		}
		if($insert){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		return redirect('/laporan/catatan-akademik');
	}
	public function nilai_karakter(){
		$user = auth()->user();
		$params = array(
			'content_header_right'	=> ($user->hasRole('wali')) ? '<a href="'.url('laporan/tambah-nilai-karakter').'" class="btn btn-success pull-right">Tambah Data</a>' : '',
		);
		return view('laporan.nilai_karakter_list')->with($params);
	}
	public function simpan_nilai_karakter(Request $request){
		$insert_catatan_ppk = array(
			'sekolah_id' => $request['sekolah_id'],
			'capaian' => $request['capaian'],
			'last_sync'	=> date('Y-m-d H:i:s'),
		);
		$catatan_ppk = Catatan_ppk::updateOrCreate(
			['anggota_rombel_id' => $request['anggota_rombel_id']],
			$insert_catatan_ppk
		);
		$all_deskripsi = $request['deskripsi'];
		foreach($all_deskripsi as $key => $deskripsi){
			$insert_nilai_karakter = array(
				'sekolah_id' => $request['sekolah_id'],
				'deskripsi' => $deskripsi,
				'last_sync'	=> date('Y-m-d H:i:s'),
			);
			$catatan_ppk = Nilai_karakter::updateOrCreate(
				['catatan_ppk_id' => $catatan_ppk->catatan_ppk_id, 'sikap_id' => $key],
				$insert_nilai_karakter
			);
		}
		if($catatan_ppk){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		return redirect('/laporan/nilai-karakter');
	}
	public function list_nilai_karakter(){
		$user = auth()->user();
		$callback = function($q) use ($user){
			if($user->hasRole('waka')){
				$q->where('sekolah_id', session('sekolah_id'));
				$q->where('semester_id', session('semester_id'));
			} else {
				//$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('semester_id', session('semester_id'))->first();
				$q->whereHas('rombongan_belajar', function($query) use ($user){
					$query->where('guru_id', $user->guru_id);
					$query->where('jenis_rombel', 1);
					$query->where('semester_id', session('semester_id'));
				});
			}
		};
		$query = Catatan_ppk::whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback])->with('anggota_rombel.siswa')->with('anggota_rombel.rombongan_belajar');
		return DataTables::of($query)
			->addColumn('nama_siswa', function ($item) {
				$return  = strtoupper($item->anggota_rombel->siswa->nama);
				return $return;
			})
			->addColumn('actions', function ($item) use ($user){
				$links = '<div class="text-center">';
				$links .= '<div class="btn-group">';
				$links .= '<button type="button" class="btn btn-default btn-sm">Aksi</button>';
				$links .= '<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="true">';
				$links .= '<span class="caret"></span>';
				$links .= '<span class="sr-only">Toggle Dropdown</span>';
				$links .= '</button>';
				$links .= '<ul class="dropdown-menu pull-right text-left" role="menu">';
				$links .= '<li><a class="toggle-modal" href="'.url('laporan/detil-nilai-karakter/'.$item->catatan_ppk_id).'"><i class="fa fa-eye"></i> Detil</a></li>';
				if(!$user->hasRole('waka')){
					$links .= '<li><a href="'.url('laporan/hapus-nilai-karakter/'.$item->catatan_ppk_id).'" class="confirm"><i class="fa fa-trash"></i> Hapus</a></li>';
				}
				$links .= '</ul>';
				$links .= '</div>';
				$links .= '</div>';
                return $links;

            })
            ->rawColumns(['actions', 'set_nama', 'set_tempat_lahir'])
            ->make(true);  
	}
	public function tambah_nilai_karakter(){
		$user = auth()->user();
		$get_siswa = Anggota_rombel::with('siswa')->whereHas('rombongan_belajar', function($query) use ($user){
			$query->where('guru_id', $user->guru_id);
			$query->where('jenis_rombel', 1);
			$query->where('semester_id', session('semester_id'));
		})->order()->get();
		$all_sikap = Sikap::whereNull('sikap_induk')->with('sikap')->get();
		$params = array(
			'data'		=> NULL,
			'get_siswa'	=> $get_siswa,
			'all_sikap'	=> $all_sikap,
		);
		return view('laporan.nilai_karakter_tambah')->with($params);
	}
	public function detil_karakter($id){
		$params['data'] = Catatan_ppk::with(['siswa', 'nilai_karakter', 'nilai_karakter.sikap'])->find($id);
		return view('laporan.nilai_karakter_detil')->with($params);
	}
	public function delete_karakter($id){
		$delete_nilai_karakter = Nilai_karakter::where('catatan_ppk_id', $id)->delete();
		if($delete_nilai_karakter){
			Catatan_ppk::destroy($id);
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		if($delete_nilai_karakter){
			Catatan_ppk::destroy($id);
			$output['title'] = 'Sukses';
			$output['text'] = 'Berhasil menghapus nilai karakter';
			$output['icon'] = 'success';
			$output['sukses'] = 1;
		} else {
			$output['title'] = 'Gagal';
			$output['text'] = 'Gagal menghapus nilai karakter';
			$output['icon'] = 'error';
			$output['sukses'] = 0;
		}
		echo json_encode($output);
	}
	public function ketidakhadiran(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			return view('laporan.waka.kehadiran');
		} else {
			$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('jenis_rombel', 1)->where('semester_id', session('semester_id'))->first();
			$get_siswa = Anggota_rombel::with('siswa')->whereHas('rombongan_belajar', function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			})->order()->get();
			$params = array(
				'get_siswa'	=> $get_siswa,
				'rombongan_belajar_id'	=> $rombongan_belajar->rombongan_belajar_id,
			);
			return view('laporan.kehadiran')->with($params);
		}
	}
	public function simpan_ketidakhadiran(Request $request){
		$sekolah_id = $request['sekolah_id'];
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$sakit = $request['sakit'];
		$izin = $request['izin'];
		$alpa = $request['alpa'];
		$insert=0;
		foreach($anggota_rombel_id as $key => $value){
			$new = Absensi::UpdateOrCreate(
				['anggota_rombel_id' => $value],
				[
					'sekolah_id'=> $sekolah_id,
					'sakit' 	=> ($sakit[$key]) ? $sakit[$key] : 0,
					'izin'		=> ($izin[$key]) ? $izin[$key] : 0,
					'alpa'		=> ($alpa[$key]) ? $alpa[$key] : 0,
					'last_sync'	=> date('Y-m-d H:i:s'),
				]
			);
			if($new){
				$insert++;
			}
		}
		if($insert){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		//return redirect('/laporan/kehadiran');
		return redirect()->route('laporan.ketidakhadiran');
	}
	public function unduh_kehadiran($id){
		$rombongan_belajar = Rombongan_belajar::find($id);
		$nama_file = 'Rekap Absensi '.$rombongan_belajar->nama;
		$nama_file = CustomHelper::clean($nama_file);
		$nama_file = $nama_file.'.xlsx';
		return (new AbsensiExport)->query($id)->download($nama_file);
	}
	public function nilai_ekskul(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			return view('laporan.waka.nilai_ekskul');
		} else {
			$callback = function($query){
				$query->where('semester_id', session('semester_id'));
				$query->whereIn('rombongan_belajar_id', function($q){
					$q->select('rombongan_belajar_id')->from('rombongan_belajar')->where('jenis_rombel', 51);
				});
				$query->with(['kelas_ekskul' => function($q){
					$q->with(['wali'  => function($sq){
						$sq->with('gelar_depan');
						$sq->with('gelar_belakang');
					}]);
				}]);
				$query->with('nilai_ekskul');
			};
			$get_siswa = Anggota_rombel::with('siswa')->whereHas('anggota_ekskul', $callback)->with(['anggota_ekskul' => $callback])->whereHas('rombongan_belajar', function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			})->order()->get();
			$params = array(
				'get_siswa'	=> $get_siswa,
			);
			return view('laporan.nilai_ekskul')->with($params);
		}
	}
	public function pkl(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			return view('laporan.waka.pkl');
		} else {
			$all_dudi = Dudi::with(['kecamatan' => function($query){
				$query->with('get_kabupaten');
			}])->where('sekolah_id', session('sekolah_id'))->orderBy('nama', 'asc')->get();
			$get_siswa = Anggota_rombel::with(['siswa', 'prakerin'])->whereHas('rombongan_belajar', function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			})->order()->get();
			$params = array(
				'get_siswa'	=> $get_siswa,
				'all_dudi'	=> $all_dudi,
			);
			return view('laporan.pkl')->with($params);
		}
	}
	public function simpan_pkl(Request $request){
		$sekolah_id = $request['sekolah_id'];
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$mitra_prakerin = $request['mitra_prakerin'];
		$lokasi_prakerin = $request['lokasi_prakerin'];
		$lama_prakerin = $request['lama_prakerin'];
		$keterangan_prakerin = $request['keterangan_prakerin'];
		$skala = $request['skala'];
		$bidang_usaha = $request['bidang_usaha'];
		$keterangan_prakerin = $request['keterangan_prakerin'];
		$insert=0;
		foreach($anggota_rombel_id as $key => $value){
			$new = Prakerin::UpdateOrCreate(
				['anggota_rombel_id' => $value],
				[
					'sekolah_id'=> $sekolah_id,
					'mitra_prakerin' 	=> $mitra_prakerin[$key],
					'lokasi_prakerin'		=> $lokasi_prakerin[$key],
					'lama_prakerin'		=> $lama_prakerin[$key],
					'skala'		=> $skala[$key],
					//'bidang_usaha'		=> $bidang_usaha[$key],
					'keterangan_prakerin'		=> $keterangan_prakerin[$key],
					'last_sync'	=> date('Y-m-d H:i:s'),
				]
			);
			if($new){
				$insert++;
			}
		}
		if($insert){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		return redirect('/laporan/pkl');
	}
	public function prestasi(){
		return view('laporan.prestasi_list');
	}
	public function tambah_prestasi(){
		$user = auth()->user();
		$get_siswa = Anggota_rombel::with(['siswa', 'prestasi'])->whereHas('rombongan_belajar', function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			})->order()->get();
		$params = array(
			'get_siswa'	=> $get_siswa,
		);
		return view('laporan.prestasi')->with($params);
	}
	public function list_prestasi(){
		$user = auth()->user();
		$callback = function($query) use ($user){
			if($user->hasRole('wali') && !$user->hasRole('waka')){
				$query->where('guru_id', $user->guru_id);
			}
			$query->where('jenis_rombel', 1);
			$query->where('semester_id', session('semester_id'));
		};
		$get_siswa = Anggota_rombel::with('siswa')->whereHas('rombongan_belajar', $callback)->with(['rombongan_belajar' => $callback])->whereHas('prestasi')->with('prestasi')->where('sekolah_id', session('sekolah_id'))->order()->get();
		return DataTables::of($get_siswa)
		->addColumn('set_nama', function ($item) {
			$return  = strtoupper($item->siswa->nama);
			return $return;
		})
		->addColumn('set_kelas', function ($item) {
			$return  = $item->rombongan_belajar->nama;
			return $return;
		})
		->addColumn('jenis_prestasi', function ($item) {
			$links = '';
			foreach($item->prestasi as $prestasi){
				$links .= $prestasi->jenis_prestasi.'<br />';
			}
			return $links;
		})
		->addColumn('keterangan', function ($item) {
			$links = '';
			foreach($item->prestasi as $prestasi){
				$links .= $prestasi->keterangan_prestasi.'<br />';
			}
			return $links;
		})
		->rawColumns(['set_nama', 'set_kelas', 'jenis_prestasi', 'keterangan'])
		->make(true);  
	}
	public function edit_prestasi($id){
		$data['prestasi'] = Prestasi::find($id);
		return view('laporan.prestasi_edit')->with($data);
	}
	public function delete_prestasi($id){
		$find = Prestasi::findOrFail($id);
		if($find){
			if($find->delete()){
				$status['icon'] = 'success';
				$status['text'] = 'Berhasil menghapus nilai prestasi';
				$status['title'] = 'Sukses';
			} else {
				$status['icon'] = 'error';
				$status['text'] = 'Gagal menghapus nilai prestasi';
				$status['title'] = 'Gagal';
			}
		} else {
			$status['icon'] = 'error';
			$status['text'] = 'Nilai prestasi tidak ditemukan';
			$status['title'] = 'Gagal';
		}
		echo json_encode($status);
	}
	public function simpan_prestasi(Request $request){
		$sekolah_id = $request['sekolah_id'];
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$jenis_prestasi = $request['jenis_prestasi'];
		$keterangan_prestasi = $request['keterangan_prestasi'];
		$new = Prestasi::create(
			[
				'anggota_rombel_id' => $anggota_rombel_id,
				'jenis_prestasi' 		=> $jenis_prestasi,
				'sekolah_id'			=> $sekolah_id,
				'keterangan_prestasi'	=> $keterangan_prestasi,
				'last_sync'	=> date('Y-m-d H:i:s'),
			]
		);
		if($new){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		return redirect('/laporan/prestasi');
	}
	public function update_prestasi(Request $request){
		$update = Prestasi::find($request['prestasi_id']);
		$update->jenis_prestasi = $request['jenis_prestasi'];
		$update->keterangan_prestasi = $request['keterangan_prestasi'];
		$update->last_sync	= date('Y-m-d H:i:s');
		if($update->save()){
			$output['title'] = 'Sukses';
			$output['text'] = 'Berhasil memperbaharui nilai prestasi';
			$output['icon'] = 'success';
			$output['sukses'] = 1;
		} else {
			$output['title'] = 'Gagal';
			$output['text'] = 'Gagal memperbaharui nilai prestasi';
			$output['icon'] = 'error';
			$output['sukses'] = 0;
		}
		echo json_encode($output);
	}
	public function kenaikan(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			return view('laporan.waka.kenaikan');
		} else {
			$cari_tingkat_akhir = Rombongan_belajar::where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->where('tingkat', 13)->first();
			$get_siswa = Anggota_rombel::with(['siswa', 'kenaikan', 'rombongan_belajar'])->whereHas('rombongan_belajar', function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			})->order()->get();
			//$rombel_4_tahun = (config('global.rombel_4_tahun')) ? unserialize(config('global.rombel_4_tahun')) : [];
			$rombel_4_tahun = Rombel4_tahun::select('rombongan_belajar_id')->where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->get()->keyBy('rombongan_belajar_id')->keys()->toArray();
			$params = array(
				'get_siswa'	=> $get_siswa,
				'cari_tingkat_akhir'	=> $cari_tingkat_akhir,
				'rombel_4_tahun' => $rombel_4_tahun,
			);
			return view('laporan.kenaikan')->with($params);
		}
	}
	public function simpan_kenaikan(Request $request){
		$sekolah_id = $request['sekolah_id'];
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$status = $request['status'];
		$rombongan_belajar = $request['rombongan_belajar'];
		$insert=0;
		foreach($anggota_rombel_id as $key => $value){
			if($status[$key]){
				/*
				*/
				//$get_rombel = Anggota_rombel::with('rombongan_belajar')->find($value);
				//$rombongan_belajar_id = $get_rombel->rombongan_belajar->rombongan_belajar_id;
				if($status[$key] == 3 || $status[$key] == 4){
					$get_rombel = Anggota_rombel::with('rombongan_belajar')->find($value);
					$rombongan_belajar_id = $get_rombel->rombongan_belajar->rombongan_belajar_id;
				} else {
					$rombongan_belajar_id = $rombongan_belajar[$key];
				}
				$new = Kenaikan_kelas::UpdateOrCreate(
					['anggota_rombel_id' => $value],
					[
						'sekolah_id'=> $sekolah_id,
						'status' 	=> $status[$key],
						'rombongan_belajar_id'		=> $rombongan_belajar_id,
						'nama_kelas' => $request->nama_kelas[$key],
						'last_sync'	=> date('Y-m-d H:i:s'),
					]
				);
				if($new){
					$insert++;
				}
			}
		}
		if($insert){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		return redirect('/laporan/kenaikan');
	}
	public function rapor_uts(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			return view('laporan.waka.rapor_pts');
		} else {
			//$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('semester_id', session('semester_id'))->first();
			$data_pembelajaran = Pembelajaran::with(['guru' => function($query){
				$query->with('gelar_depan');
				$query->with('gelar_belakang');
			}])->with(['rencana_penilaian' => function($query){
				$query->where('kompetensi_id', 1);
			}])->with('rapor_pts')->whereNotNull('kelompok_id')->whereNotNull('no_urut')->whereHas('rombongan_belajar', function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			})->orderBy('kelompok_id', 'asc')->get();
			$params = array(
				'data_pembelajaran'	=> $data_pembelajaran,
			);
			return view('laporan.rapor_pts')->with($params);
		}
	}
	public function cetak_uts(Request $request){
		$rencana_penilaian = $request->rencana_penilaian;
		$insert=0;
		if($rencana_penilaian){
			foreach($rencana_penilaian as $pembelajaran_id => $rencana_penilaian_id){
				$pembelajaran_id_array[] = $pembelajaran_id;
				foreach($rencana_penilaian_id as $ren_id){
					$rencana_penilaian_id_array[] = $ren_id;
					$insert_rapor_pts = array(
						'sekolah_id' => $request->sekolah_id,
						'last_sync'	=> date('Y-m-d H:i:s'),
					);
					$insert_data = Rapor_pts::updateOrCreate(
						[
							'rombongan_belajar_id' => $request->rombongan_belajar_id,
							'pembelajaran_id' => $pembelajaran_id,
							'rencana_penilaian_id' => $ren_id
						],
						$insert_rapor_pts
					);
					if($insert_data){
						$insert++;
					}
				}
			}
			Rapor_pts::where(function($query) use ($request, $pembelajaran_id_array, $rencana_penilaian_id_array){
				$query->where('rombongan_belajar_id', $request->rombongan_belajar_id);
				$query->whereIn('pembelajaran_id', $pembelajaran_id_array);
				$query->whereNotIn('rencana_penilaian_id', $rencana_penilaian_id_array);
			})->delete();
		}
		if($insert){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('error',"Tidak ada data disimpan");
		}
		return redirect('/laporan/rapor-uts');
	}
	public function rapor_semester(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			return view('laporan.waka.rapor_semester');
		} else {
			$callback = function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', session('semester_id'));
			};
			$get_siswa = Anggota_rombel::with(['siswa', 'rombongan_belajar' => $callback])->whereHas('rombongan_belajar', $callback)->order()->get();
			$params = array(
				'get_siswa'	=> $get_siswa,
			);
			return view('laporan.rapor_semester')->with($params);
		}
	}
	public function review_nilai($query, $id){
		$user = auth()->user();
		if($query){
			$get_siswa = Anggota_rombel::with([
				'siswa', 
				'rombongan_belajar' => function($query) use ($id){
					$query->with(['pembelajaran' => function($query) use ($id) {
						$callback = function($query) use ($id){
							$query->where('anggota_rombel_id', $id);
						};
						//$query->with('kelompok');
						//$query->whereHas('nilai_akhir_pengetahuan', $callback);
						$query->with([
							'kelompok', 
							'nilai_akhir_pengetahuan' => $callback, 
							'nilai_akhir_keterampilan' => $callback, 
							'nilai_akhir_pk' => $callback,
							'deskripsi_mata_pelajaran' => $callback,
						]);
						//$query->whereHas('nilai_akhir_keterampilan', $callback);
						//$query->with(['nilai_akhir_keterampilan' => $callback]);
						$query->whereNotNull('kelompok_id');
						$query->orderBy('kelompok_id', 'asc');
						$query->orderBy('no_urut', 'asc');
					}]);
					$query->with('semester');
					$query->with('jurusan');
					$query->with('kurikulum');
				},
				'sekolah' => function($q){
					$q->with('guru');
				},
			])->find($id);
		} else {
			$get_siswa = Anggota_rombel::with(['siswa', 'rombongan_belajar'])->where('rombongan_belajar_id', $id)->order()->get();
		}
		$params = array(
			'get_siswa'	=> $get_siswa,
		);
		return view('laporan.review_nilai')->with($params);
	}
	public function legger(){
		$user = auth()->user();
		if($user->hasRole('waka')){
			$params = array(
				'all_data' => Tahun_ajaran::with('semester')->where('periode_aktif', '=', 1)->orderBy('tahun_ajaran_id', 'desc')->get(),
			);
			return view('laporan.waka.legger')->with($params);
		} else {
			$rombongan_belajar = Rombongan_belajar::where(function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('semester_id', session('semester_id'));
				$query->where('jenis_rombel', 1);
			})->first();
			$params = array(
				'rombongan_belajar' => $rombongan_belajar,
			);
			return view('laporan.legger')->with($params);
		}
	}
	public function unduh_legger_kd($id){
		$rombongan_belajar = Rombongan_belajar::find($id);
		$nama_file = 'Leger Otentik Kelas '.$rombongan_belajar->nama;
		$nama_file = CustomHelper::clean($nama_file);
		$nama_file = $nama_file.'.xlsx';
		return (new LeggerKDExport)->query($id)->download($nama_file);
	}
	public function unduh_legger_nilai_akhir($id){
		$rombongan_belajar = Rombongan_belajar::find($id);
		$nama_file = 'Leger Nilai Akhir Kelas '.$rombongan_belajar->nama;
		$nama_file = CustomHelper::clean($nama_file);
		$nama_file = $nama_file.'.xlsx';
		return (new LeggerNilaiAkhirExport)->query($id)->download($nama_file);
	}
	public function unduh_legger_nilai_rapor($id){
		$rombongan_belajar = Rombongan_belajar::find($id);
		$nama_file = 'Leger Nilai Rapor Kelas '.$rombongan_belajar->nama;
		$nama_file = CustomHelper::clean($nama_file);
		$nama_file = $nama_file.'.xlsx';
		return (new LeggerNilaiRaporExport)->query($id)->download($nama_file);
	}
	public function nilai_us(Request $request){
		if ($request->isMethod('post')) {
			$messages = [
				'nilai.*.required' => 'Nilai tidak boleh kosong',
				'nilai.*.integer' => 'Nilai harus berupa angka',
				'nilai.*.min' => 'Minimal nilai 0 (nol)',
				'nilai.*.max' => 'Maksimal nilai 100 (seratus)',
			];
			$validator = Validator::make(request()->all(), [
				'nilai.*' => 'required|integer|min:0|max:100',
			],
			$messages
			)->validate();
			foreach($request->nilai as $anggota_rombel_id => $nilai){
				Nilai_us::updateOrCreate(
					[
						'sekolah_id' => $request->sekolah_id,
						'anggota_rombel_id' => $anggota_rombel_id,
						'pembelajaran_id' => $request->pembelajaran_id,
					],
					[
						'nilai' => $nilai,
						'last_sync' => date('Y-m-d H:i:s'),
					]
				);
			}
			return redirect()->back()->with(['success' => 'Nilai US/USBN berhasil disimpan']);
		}
		$user = auth()->user();
		if($user->hasRole('waka')){
			$jurusan_sp = Jurusan_sp::whereHas('rombongan_belajar', function($query){
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
				$query->where('tingkat', 13);
			})->get();
			if(!$jurusan_sp->count()){
				$jurusan_sp = Jurusan_sp::whereHas('rombongan_belajar', function($query){
					$query->where('sekolah_id', session('sekolah_id'));
					$query->where('semester_id', session('semester_id'));
					$query->where('tingkat', 12);
				})->get();
			}
			return view('laporan.waka.nilai_us', compact('jurusan_sp'));
		} else {
			$rombongan_belajar = Rombongan_belajar::with(['pembelajaran' => function($query){
				$query->whereNotNull('kelompok_id');
				$query->whereNotNull('no_urut');
			}])->where(function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('semester_id', session('semester_id'));
				$query->where('jenis_rombel', 1);
				$query->where('tingkat', 13);
				$query->orWhere('tingkat', 12);
				$query->where('guru_id', $user->guru_id);
				$query->where('semester_id', session('semester_id'));
				$query->where('jenis_rombel', 1);
			})->first();
			return view('laporan.nilai_us', compact('rombongan_belajar'));
		}
	}
	public function nilai_un(Request $request){
		if ($request->isMethod('post')) {
			$messages = [
				'nilai.*.required' => 'Nilai tidak boleh kosong',
				'nilai.*.integer' => 'Nilai harus berupa angka',
				'nilai.*.min' => 'Minimal nilai 0 (nol)',
				'nilai.*.max' => 'Maksimal nilai 100 (seratus)',
			];
			$validator = Validator::make(request()->all(), [
				'nilai.*' => 'required|integer|min:0|max:100',
			],
			$messages
			)->validate();
			foreach($request->nilai as $anggota_rombel_id => $nilai){
				Nilai_un::updateOrCreate(
					[
						'sekolah_id' => $request->sekolah_id,
						'anggota_rombel_id' => $anggota_rombel_id,
						'pembelajaran_id' => $request->pembelajaran_id,
					],
					[
						'nilai' => $nilai,
						'last_sync' => date('Y-m-d H:i:s'),
					]
				);
			}
			return redirect()->back()->with(['success' => 'Nilai UN berhasil disimpan']);
		}
		$user = auth()->user();
		if($user->hasRole('waka')){
			$jurusan_sp = Jurusan_sp::whereHas('rombongan_belajar', function($query){
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
				$query->where('tingkat', 13);
			})->get();
			if(!$jurusan_sp->count()){
				$jurusan_sp = Jurusan_sp::whereHas('rombongan_belajar', function($query){
					$query->where('sekolah_id', session('sekolah_id'));
					$query->where('semester_id', session('semester_id'));
					$query->where('tingkat', 12);
				})->get();
			}
			return view('laporan.waka.nilai_un', compact('jurusan_sp'));
		} else {
			$rombongan_belajar = Rombongan_belajar::with(['pembelajaran' => function($query){
				$query->whereNotNull('kelompok_id');
				$query->whereNotNull('no_urut');
				$query->whereIn('mata_pelajaran_id', ['300110000', '401000000', '300210000']);
				$query->orWhere('kelompok_id', 10);
				$query->whereNotNull('no_urut');
			}])->where(function($query) use ($user){
				$query->where('guru_id', $user->guru_id);
				$query->where('semester_id', session('semester_id'));
				$query->where('jenis_rombel', 1);
				$query->where('tingkat', 13);
				$query->orWhere('tingkat', 12);
				$query->where('guru_id', $user->guru_id);
				$query->where('semester_id', session('semester_id'));
				$query->where('jenis_rombel', 1);
			})->first();
			return view('laporan.nilai_un', compact('rombongan_belajar'));
		}
	}
	public function unduh_template(Request $request){
		$query = $request->route('query');
		if($query == 'nilai_us'){
			$kompetensi = 'US/USBN';
		} else {
			$kompetensi = 'UN';
		}
		$pembelajaran = Pembelajaran::find($request->route('id'));
		$nama_mapel = CustomHelper::clean($pembelajaran->nama_mata_pelajaran);
		$nama_file = 'Format Nilai '.$kompetensi.' eRaporSMK '.$nama_mapel.' '.$pembelajaran->rombongan_belajar->nama;
		$nama_file = CustomHelper::clean($nama_file);
		$nama_file = $nama_file.'.xlsx';
		return (new LaporanExport)->query($query, $pembelajaran->rombongan_belajar_id, $request->route('id'))->download($nama_file);
	}
	public function import_excel(Request $request){
		$validator = Validator::make($request->all(), [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
		if ($validator->passes()) {
			$file = $request->file('file');
			//$nama_file = rand().$file->getClientOriginalName();
			//$file->move('excel',$nama_file);
			$Import = new LaporanImport();
			$rows = Excel::import($Import, $file);
			//if(File::exists(public_path('/excel/'.$nama_file))) {
				//File::delete(public_path('/excel/'.$nama_file));
			//}
			return response()->json($Import);
		}
		return response()->json(['error'=>$validator->errors()->all()]);
	}
	public function tambah_kewirausahaan(Request $request){
		if ($request->isMethod('post')) {
			$messages = [
				'anggota_rombel_id.required' => 'Peserta didik tidak boleh kosong',
				'pola.required' => 'Pola Kewirausahaan tidak boleh kosong',
				'jenis.required' => 'Jenis Kewirausahaan tidak boleh kosong',
				'nama_produk.required' => 'Nama produk tidak boleh kosong',
			];
			$validator = Validator::make(request()->all(), [
				'anggota_rombel_id' => 'required',
				'pola' => 'required',
				'jenis' => 'required',
				'nama_produk' => 'required',
			],
			$messages
			)->validate();
			$insert = Kewirausahaan::create([
				'sekolah_id' => $request->sekolah_id,
				'anggota_rombel_id' => $request->anggota_rombel_id,
				'pola' => $request->pola,
				'jenis' => $request->jenis,
				'nama_produk' => $request->nama_produk,
				'last_sync' => date('Y-m-d H:i:s'),
			]);
			if($request->anggota_wirausaha){
				foreach($request->anggota_wirausaha as $anggota_wirausaha){
					Anggota_kewirausahaan::create([
							'kewirausahaan_id' => $insert->kewirausahaan_id,
							'anggota_rombel_id' => $anggota_wirausaha,
							'last_sync' => date('Y-m-d H:i:s'),
					]);
				}
			}
			return redirect()->route('laporan.kewirausahaan')->with(['success' => 'Data Kewirausahaan berhasil disimpan']);
		}
		$user = auth()->user();
		$get_siswa = Anggota_rombel::with('siswa')->with('catatan_wali')->with(['nilai_rapor' => function($query){
			$query->with('pembelajaran');
			$query->limit(3);
		}])->whereHas('rombongan_belajar', function($query) use ($user){
			$query->where('guru_id', $user->guru_id);
			$query->where('jenis_rombel', 1);
			$query->where('semester_id', session('semester_id'));
		})->order()->get();
		return view('laporan.kewirausahaan.tambah', compact('user', 'get_siswa'));
	}
	public function kewirausahaan(Request $request){
		$user = auth()->user();
		return view('laporan.kewirausahaan.index', compact('user'));
	}
	public function list_kewirausahaan(){
		$user = auth()->user();
		$callback = function($q) use ($user){
			if($user->hasRole('waka')){
				$q->where('kewirausahaan.sekolah_id', session('sekolah_id'));
				$q->where('semester_id', session('semester_id'));
			} else {
				//$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('semester_id', session('semester_id'))->first();
				$q->whereHas('rombongan_belajar', function($query) use ($user){
					$query->where('guru_id', $user->guru_id);
					$query->where('jenis_rombel', 1);
					$query->where('semester_id', session('semester_id'));
				});
				$q->with('siswa');
			}
		};
		$query = Kewirausahaan::query()->whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback, 'anggota.anggota_rombel' => $callback]);
		return DataTables::of($query)
			->addColumn('nama_siswa', function ($item) {
				$return  = ($item->anggota->count()) ? $item->nama_anggota() : strtoupper($item->anggota_rombel->siswa->nama);
				return $return;
			})
			->addColumn('actions', function ($item) use ($user){
				$links = '<div class="text-center">';
				if(!$user->hasRole('waka')){
					$links .= '<a href="'.url('laporan/hapus-kewirausahaan/'.$item->kewirausahaan_id).'" class="btn btn-danger btn-sm confirm"><i class="fa fa-trash"></i> Hapus</a>';
				} else {
					$links .= '-';
				}
				$links .= '</div>';
                return $links;

            })
            ->rawColumns(['nama_siswa', 'actions'])
            ->make(true);  
	}
	public function hapus_kewirausahaan($id){
		$delete = Kewirausahaan::find($id);
		if($delete->delete()){
			$output = [
				'title' => 'Berhasil',
				'text' => 'Data Kewirausahaan berhasil dihapus',
				'icon' => 'success',
			];
		} else {
			$output = [
				'title' => 'Gagal',
				'text' => 'Data Kewirausahaan gagal dihapus',
				'icon' => 'error',
			];
		}
		return response()->json($output);
	}
	public function budaya_kerja(Request $request){
		$user = auth()->user();
		$get_siswa = Anggota_rombel::with('siswa')->with('nilai_budaya_kerja')->whereHas('rombongan_belajar', function($query) use ($user){
			$query->where('guru_id', $user->guru_id);
			$query->where('jenis_rombel', 1);
			$query->where('semester_id', session('semester_id'));
		})->order()->get();
		$params = array(
			'get_siswa'	=> $get_siswa,
		);
		return view('laporan.p5bk')->with($params);
	}
	public function review_p5bk($anggota_rombel_id){
		$get_siswa = Anggota_rombel::with([
			'siswa', 
			'nilai_budaya_kerja',
			'rombongan_belajar.sekolah',
			'catatan_budaya_kerja',
		])->find($anggota_rombel_id);
		$params = array(
			'get_siswa'	=> $get_siswa,
			'rencana_budaya_kerja' => Rencana_budaya_kerja::where('rombongan_belajar_id', $get_siswa->rombongan_belajar_id)->with(['aspek_budaya_kerja' => function($query) use ($anggota_rombel_id){
				$query->with([
					'budaya_kerja.elemen_budaya_kerja.nilai_budaya_kerja' => function($query) use ($anggota_rombel_id){
						$query->where('anggota_rombel_id', $anggota_rombel_id);
					},
				]);
			}])->get(),
			'opsi_budaya_kerja' => Opsi_budaya_kerja::orderBy('opsi_id')->get(),
			'budaya_kerja' => Budaya_kerja::orderBy('budaya_kerja_id')->get(),
		);
		return view('laporan.review_p5bk')->with($params);
	}
}