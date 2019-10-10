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
use App\Dudi;
class LaporanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
	public function index(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.catatan_akademik')->with($params);
		} else {
			/*$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('jenis_rombel', 1)->where('semester_id', $semester->semester_id)->first();
			$get_siswa = Anggota_rombel::with('siswa')->with('catatan_wali')->with(['nilai_rapor' => function($query){
				$query->with('pembelajaran');
				$query->limit(3);
			}])->where('rombongan_belajar_id', $rombongan_belajar->rombongan_belajar_id)->order()->get();
			*/
			$get_siswa = Anggota_rombel::with('siswa')->with('catatan_wali')->with(['nilai_rapor' => function($query){
				$query->with('pembelajaran');
				$query->limit(3);
			}])->whereHas('rombongan_belajar', function($query) use ($user, $semester){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', $semester->semester_id);
			})->order()->get();
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
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
		$semester = CustomHelper::get_ta();
		$callback = function($q) use ($user, $semester){
			if($user->hasRole('waka')){
				$q->where('sekolah_id', $user->sekolah_id);
				$q->where('semester_id', $semester->semester_id);
			} else {
				$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('semester_id', $semester->semester_id)->first();
				$q->where('rombongan_belajar_id', $rombongan_belajar->rombongan_belajar_id)->order();
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
		$semester = CustomHelper::get_ta();
		$get_siswa = Anggota_rombel::with('siswa')->whereHas('rombongan_belajar', function($query) use ($user, $semester){
			$query->where('guru_id', $user->guru_id);
			$query->where('jenis_rombel', 1);
			$query->where('semester_id', $semester->semester_id);
		})->order()->get();
		$all_sikap = Sikap::whereNull('sikap_induk')->with('sikap')->get();
		$params = array(
			'data'		=> NULL,
			'user' 		=> $user,
			'semester' 	=> $semester,
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
	public function kehadiran(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.kehadiran')->with($params);
		} else {
			$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('jenis_rombel', 1)->where('semester_id', $semester->semester_id)->first();
			$get_siswa = Anggota_rombel::with('siswa')->whereHas('rombongan_belajar', function($query) use ($user, $semester){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', $semester->semester_id);
			})->order()->get();
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
				'get_siswa'	=> $get_siswa,
				'rombongan_belajar_id'	=> $rombongan_belajar->rombongan_belajar_id,
			);
			return view('laporan.kehadiran')->with($params);
		}
	}
	public function simpan_kehadiran(Request $request){
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
					'sakit' 	=> $sakit[$key],
					'izin'		=> $izin[$key],
					'alpa'		=> $alpa[$key],
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
		return redirect('/laporan/kehadiran');
	}
	public function unduh_kehadiran($rombongan_belajar_id){
	}
	public function nilai_ekskul(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.nilai_ekskul')->with($params);
		} else {
			$callback = function($query) use ($semester){
				$query->where('semester_id', $semester->semester_id);
				$query->whereIn('rombongan_belajar_id', function($q){
					$q->select('rombongan_belajar_id')->from('rombongan_belajar')->where('jenis_rombel', 51);
				});
				$query->with(['kelas_ekskul' => function($q){
					$q->with(['guru'  => function($sq){
						$sq->with('gelar_depan');
						$sq->with('gelar_belakang');
					}]);
				}]);
				$query->with('nilai_ekskul');
			};
			$get_siswa = Anggota_rombel::with('siswa')->whereHas('anggota_ekskul', $callback)->with(['anggota_ekskul' => $callback])->whereHas('rombongan_belajar', function($query) use ($user, $semester){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', $semester->semester_id);
			})->order()->get();
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
				'get_siswa'	=> $get_siswa,
			);
			return view('laporan.nilai_ekskul')->with($params);
		}
	}
	public function pkl(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.pkl')->with($params);
		} else {
			$all_dudi = Dudi::with(['kecamatan' => function($query){
				$query->with('get_kabupaten');
			}])->where('sekolah_id', $user->sekolah_id)->orderBy('nama', 'asc')->get();
			$get_siswa = Anggota_rombel::with(['siswa', 'prakerin'])->whereHas('rombongan_belajar', function($query) use ($user, $semester){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', $semester->semester_id);
			})->order()->get();
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
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
		$insert=0;
		foreach($anggota_rombel_id as $key => $value){
			$new = Prakerin::UpdateOrCreate(
				['anggota_rombel_id' => $value],
				[
					'sekolah_id'=> $sekolah_id,
					'mitra_prakerin' 	=> $mitra_prakerin[$key],
					'lokasi_prakerin'		=> $lokasi_prakerin[$key],
					'lama_prakerin'		=> $lama_prakerin[$key],
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
		$semester = CustomHelper::get_ta();
		$get_siswa = Anggota_rombel::with(['siswa', 'prestasi'])->whereHas('rombongan_belajar', function($query) use ($user, $semester){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', $semester->semester_id);
			})->order()->get();
		$params = array(
			'user' 		=> $user,
			'semester' 	=> $semester,
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
		};
		$get_siswa = Anggota_rombel::with('siswa')->whereHas('rombongan_belajar', $callback)->with(['rombongan_belajar' => $callback])->whereHas('prestasi')->with('prestasi')->where('sekolah_id', $user->sekolah_id)->order()->get();
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
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.kenaikan')->with($params);
		} else {
			$cari_tingkat_akhir = Rombongan_belajar::where('sekolah_id', $user->sekolah_id)->where('semester_id', $semester->semester_id)->where('tingkat', 13)->first();
			$get_siswa = Anggota_rombel::with(['siswa', 'kenaikan', 'rombongan_belajar'])->whereHas('rombongan_belajar', function($query) use ($user, $semester){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', $semester->semester_id);
			})->order()->get();
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
				'get_siswa'	=> $get_siswa,
				'cari_tingkat_akhir'	=> $cari_tingkat_akhir,
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
				$new = Kenaikan_kelas::UpdateOrCreate(
					['anggota_rombel_id' => $value],
					[
						'sekolah_id'=> $sekolah_id,
						'status' 	=> $status[$key],
						'rombongan_belajar'		=> $rombongan_belajar[$key],
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
	public function rapor_pts(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.rapor_pts')->with($params);
		} else {
			$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('semester_id', $semester->semester_id)->first();
			$data_pembelajaran = Pembelajaran::with(['guru' => function($query){
				$query->with('gelar_depan');
				$query->with('gelar_belakang');
			}])->with(['rencana_penilaian' => function($query){
				$query->where('kompetensi_id', 1);
			}])->with('rapor_pts')->whereNotNull('kelompok_id')->whereNotNull('no_urut')->where('rombongan_belajar_id', $rombongan_belajar->rombongan_belajar_id)->orderBy('kelompok_id', 'asc')->get();
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
				'data_pembelajaran'	=> $data_pembelajaran,
			);
			return view('laporan.rapor_pts')->with($params);
		}
	}
	public function cetak_pts(Request $request){
		$rencana_penilaian = $request['rencana_penilaian'];
		$insert=0;
		foreach($rencana_penilaian as $pembelajaran_id => $rencana_penilaian_id){
			$insert_rapor_pts = array(
				'sekolah_id' => $request['sekolah_id'],
				'rencana_penilaian_id' => $rencana_penilaian_id,
				'last_sync'	=> date('Y-m-d H:i:s'),
			);
			$insert_data = Rapor_pts::updateOrCreate(
				[
					'rombongan_belajar_id' => $request['rombongan_belajar_id'],
					'pembelajaran_id' => $pembelajaran_id
				],
				$insert_rapor_pts
			);
			if($insert_data){
				$insert++;
			}
		}
		if($insert){
			Session::flash('success',"Data berhasil disimpan");
		} else {
			Session::flash('success',"Tidak ada data disimpan");
		}
		return redirect('/laporan/rapor-pts');
	}
	public function rapor_semester(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.rapor_semester')->with($params);
		} else {
			$callback = function($query) use ($user, $semester){
				$query->where('guru_id', $user->guru_id);
				$query->where('jenis_rombel', 1);
				$query->where('semester_id', $semester->semester_id);
			};
			$get_siswa = Anggota_rombel::with(['siswa', 'rombongan_belajar' => $callback])->whereHas('rombongan_belajar', $callback)->order()->get();
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
				'get_siswa'	=> $get_siswa,
			);
			return view('laporan.rapor_semester')->with($params);
		}
	}
	public function review_nilai($query, $id){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($query){
			$get_siswa = Anggota_rombel::with('siswa')->with(['rombongan_belajar' => function($query) use ($id){
				$query->with(['pembelajaran' => function($query) use ($id) {
					$callback = function($query) use ($id){
						$query->where('anggota_rombel_id', $id);
					};
					$query->with('kelompok');
					//$query->whereHas('nilai_akhir_pengetahuan', $callback);
					$query->with(['nilai_akhir_pengetahuan' => $callback]);
					//$query->whereHas('nilai_akhir_keterampilan', $callback);
					$query->with(['nilai_akhir_keterampilan' => $callback]);
					$query->whereNotNull('kelompok_id');
					$query->orderBy('kelompok_id', 'asc');
					$query->orderBy('no_urut', 'asc');
				}]);
				$query->with('semester');
				$query->with('jurusan');
				$query->with('kurikulum');
			}])->with(['sekolah' => function($q){
				$q->with('guru');
			}])->find($id);
		} else {
			$get_siswa = Anggota_rombel::with('siswa')->with('rombongan_belajar')->where('rombongan_belajar_id', $id)->order()->get();
		}
		$params = array(
			'user' 		=> $user,
			'semester' 	=> $semester,
			'get_siswa'	=> $get_siswa,
		);
		return view('laporan.review_nilai')->with($params);
	}
	public function legger(){
		$user = auth()->user();
		$semester = CustomHelper::get_ta();
		if($user->hasRole('waka')){
			$params = array(
				'user' 		=> $user,
				'semester' 	=> $semester,
			);
			return view('laporan.waka.legger')->with($params);
		} else {
			$rombongan_belajar = Rombongan_belajar::where('guru_id', $user->guru_id)->where('semester_id', $semester->semester_id)->first();
			$params = array(
				'rombongan_belajar' => $rombongan_belajar,
			);
			return view('laporan.legger')->with($params);
		}
	}
	public function unduh_legger_kd($id){
		/*
		$rombongan_belajar = Rombongan_belajar::with(['anggota_rombel' => function($query){
			$query->with('siswa');
			$query->order();
			$query->with(['nilai_kd_pengetahuan' => function($q){
				$q->with('kd_nilai');
				$q->orderBy('kd_id');
			}]);
			$query->with(['nilai_kd_keterampilan' => function($q){
				$q->with('kd_nilai');
				$q->orderBy('kd_id');
			}]);
		}])->with('jurusan')->with('sekolah')->with('semester')->with(['pembelajaran' => function($query){
			$query->with(['kd_nilai_p' => function($query){
				$query->select(['kd_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->orderBy('kompetensi_id', 'asc');
				$query->orderBy('kd_id', 'asc');
				$query->groupBy(['kd_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->with('kompetensi_dasar');
			}]);
			$query->with(['kd_nilai_k' => function($query){
				$query->select(['kd_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->orderBy('kompetensi_id', 'asc');
				$query->orderBy('kd_id', 'asc');
				$query->groupBy(['kd_id', 'pembelajaran_id', 'kompetensi_id']);
				$query->with('kompetensi_dasar');
			}]);
			$query->whereNotNull('kelompok_id');
			$query->orderBy('kelompok_id', 'asc');
			$query->orderBy('no_urut', 'asc');
		}])->find($id);
		$params = array(
			'rombongan_belajar' => $rombongan_belajar,
		);
		return view('laporan.legger_kd')->with($params);
		*/
		$rombongan_belajar = Rombongan_belajar::find($id);
		$nama_file = 'Leger Otentik Kelas '.$rombongan_belajar->nama.'.xlsx';
		return (new LeggerKDExport)->query($id)->download($nama_file);
	}
	public function unduh_legger_nilai_akhir($id){
		$rombongan_belajar = Rombongan_belajar::find($id);
		$nama_file = 'Leger Nilai Akhir Kelas '.$rombongan_belajar->nama.'.xlsx';
		return (new LeggerNilaiAkhirExport)->query($id)->download($nama_file);
	}
	public function unduh_legger_nilai_rapor($id){
		/*$get_siswa = Anggota_rombel::with('siswa')->where('rombongan_belajar_id', $id)->order()->get();
		$all_pembelajaran = Pembelajaran::where('rombongan_belajar_id', $id)->whereNotNull('kelompok_id')->orderBy('kelompok_id', 'asc')->orderBy('no_urut', 'asc')->get();
		$params = array(
			'get_siswa' => $get_siswa,
			'all_pembelajaran'	=> $all_pembelajaran,
		);
		return view('laporan.legger_nilai_rapor', $params);*/
		$rombongan_belajar = Rombongan_belajar::find($id);
		$nama_file = 'Leger Nilai Rapor Kelas '.$rombongan_belajar->nama.'.xlsx';
		return (new LeggerNilaiRaporExport)->query($id)->download($nama_file);
	}
}
