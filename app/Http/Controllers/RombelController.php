<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use App\Providers\HelperServiceProvider;
use App\Rombongan_belajar;
use Illuminate\Support\Facades\DB;
use App\Jurusan;
use App\Kurikulum;
use App\Guru;
use App\Anggota_rombel;
use App\Pembelajaran;
use App\Kelompok;
class RombelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
		return view('rombel.list_rombel');
    }
	public function list_rombel(){
		$user = auth()->user();
		$semester = HelperServiceProvider::get_ta();
		$query = Rombongan_belajar::where('sekolah_id', '=', $user->sekolah_id)->where('semester_id', '=', $semester->semester_id)->where('jenis_rombel', '=', 1);
		//->join('role_user', 'role_user.user_id', '=', 'users.user_id');
		//->join('roles', 'roles.id', '=', 'role_user.role_id');
		return DataTables::of($query)
			->addColumn('wali', function ($item) {
				$get_wali = Guru::where('guru_id', '=', $item->guru_id)->first();
				$return  = $get_wali->nama;
				return $return;
			})
			->addColumn('jurusan', function ($item) {
				$get_jurusan = Jurusan::where('jurusan_id', $item->jurusan_id)->first();
				$return  = $get_jurusan->nama_jurusan;
				return $return;
			})
			->addColumn('kurikulum', function ($item) {
				$get_kurikulum = Kurikulum::where('kurikulum_id', $item->kurikulum_id)->first();
				$return  = $get_kurikulum->nama_kurikulum;
				return $return;
			})
			->addColumn('anggota', function ($item) {
				$return  = '<div class="text-center"><a href="'.url('rombel/anggota/'.$item->rombongan_belajar_id).'" class="btn btn-primary btn-sm toggle-modal"><i class="fa fa-search-plus"></i> Anggota Rombel</a></div>';
				return $return;
			})
			->addColumn('pembelajaran', function ($item) {
				$return  = '<div class="text-center"><a href="'.url('rombel/pembelajaran/'.$item->rombongan_belajar_id).'" class="btn btn-success btn-sm toggle-modal"><i class="fa fa-search-plus"></i> Pembelajaran</a></div>';
				return $return;
			})
            ->rawColumns(['wali', 'jurusan', 'kurikulum', 'anggota', 'pembelajaran'])
            ->make(true);  
	}
	public function anggota($rombongan_belajar_id){
		$all_anggota = Rombongan_belajar::where('rombongan_belajar_id', '=', $rombongan_belajar_id)->with(['anggota_rombel' => function ($query) {
			$query->with(['siswa' => function ($query) {
				$query->with(['kelas' => function($q){
					$q->where('jenis_rombel', 1);
				}]);
				$query->with('agama');
			}]);
		}])->first();
		$params = array(
			'all_anggota'	=> $all_anggota,
			'title' => ($all_anggota->jenis_rombel == 1) ? 'Anggota Rombel' : 'Anggota Ekstrakurikuler',
			'modal_s'	=> 'modal-lg',
		);
		return view('rombel.anggota')->with($params);
	}
	public function pembelajaran($rombongan_belajar_id){
		$all_pembelajaran = Rombongan_belajar::where('rombongan_belajar_id', '=', $rombongan_belajar_id)->with(['pembelajaran' => function ($query) {
			$query->with('guru')->with('pengajar')->orderBy('mata_pelajaran_id');
		}])->with('kurikulum')->first();
		$params = array(
			'all_pembelajaran'	=> $all_pembelajaran,
			'title' => 'Edit Pembelajaran',
			'modal_s'	=> 'modal-lg',
		);
		return view('rombel.pembelajaran')->with($params);
	}
	public function keluarkan($id){
		$find = Anggota_rombel::findOrFail($id);
		if($find){
			if($find->delete()){
				$status['icon'] = 'success';
				$status['text'] = 'Berhasil menghapus anggota rombel';
				$status['title'] = 'Sukses';
			} else {
				$status['icon'] = 'error';
				$status['text'] = 'Anggota rombel gagal dihapus';
				$status['title'] = 'Gagal';
			}
		} else {
			$status['icon'] = 'error';
			$status['text'] = 'Data anggota rombel tidak ditemukan';
			$status['title'] = 'Gagal';
		}
		echo json_encode($status);
	}
	public function pengajar(){
		$user = auth()->user();
		$pengajar = HelperServiceProvider::jenis_gtk('instruktur');
		$guru = HelperServiceProvider::jenis_gtk('guru');
		$jenis_gtk = array_merge($pengajar, $guru);
		$data_guru = Guru::where('sekolah_id', '=', $user->sekolah_id)->whereIn('jenis_ptk_id', $jenis_gtk)->orderBy('nama')->get();
		if($data_guru){
			foreach($data_guru as $guru){
				$status = array();
				$status['id'] = $guru->guru_id;
				$status['text'] = $guru->nama;
				$output[] = $status;
			}
		} else {
			$record['id'] 	= '';
			$record['text'] 	= 'Tidak ditemukan data pengajar';
			$output[] = $record;
		}
		echo json_encode($output);
	}
	public function kelompok($kurikulum_id){
		$get_kurikulum = Kurikulum::find($kurikulum_id);
		if (strpos($get_kurikulum->nama_kurikulum, 'REV') !== false) {
			$kurikulum = 2017;
		} elseif (strpos($get_kurikulum->nama_kurikulum, 'KTSP') !== false) {
			$kurikulum = 2006;
		} else {
			$kurikulum = 2013;
		}
		$get_kelompok = Kelompok::where('kurikulum', '=', $kurikulum)->orWhere('kurikulum', '=', 0)->get();
		if($get_kelompok){
			foreach($get_kelompok as $kelompok){
				$record= array();
				$record['id'] 	= $kelompok->kelompok_id;
				$record['text'] = $kelompok->nama_kelompok;
				$output[] = $record;
			}
		} else {
			$record['value'] 	= '';
			$record['text'] 	= 'Tidak ditemukan kelompok mata pelajaran';
			$output[] = $record;
		}
		echo json_encode($output);
	}
	public function tambah_alias(Request $request){
		$id = $request->input('name');
		$nama_mata_pelajaran = $request->input('value');
		$pembelajaran = Pembelajaran::findOrFail($id);
		if($pembelajaran){
			$pembelajaran->nama_mata_pelajaran = $nama_mata_pelajaran;
			$pembelajaran->last_sync = date('Y-m-d H:i:s');
			$pembelajaran->save();
		}
	}
	public function simpan_pembelajaran(Request $request){
		$pembelajaran_id 	= $request->input('pembelajaran_id');
		$guru_pengajar_id 	= $request->input('guru_pengajar_id');
		$kelompok_id		= $request->input('kelompok_id');
		$nomor_urut			= $request->input('nomor_urut');
		$nama_mapel_alias	= $request->input('nama_mapel_alias');
		if($guru_pengajar_id || $kelompok_id || $nomor_urut){
			$pembelajaran = Pembelajaran::findOrFail($pembelajaran_id);
			if($pembelajaran){
				$pembelajaran->guru_pengajar_id = $guru_pengajar_id;
				$pembelajaran->kelompok_id = $kelompok_id;
				$pembelajaran->no_urut = $nomor_urut;
				$pembelajaran->last_sync = date('Y-m-d H:i:s');
				if($pembelajaran->save()){
					$status['type'] = 'warning';
					$status['text'] = 'Berhasil memperbaharui pembelajaran '.$nama_mapel_alias;
					$status['title'] = 'Data Tersimpan!';
				} else {
					$status['type'] = 'error';
					$status['text'] = 'Gagal memperbaharui pembelajaran '.$nama_mapel_alias;
					$status['title'] = 'Data dilewati!';
				}
			}
		} else {
			$status['type'] = 'info';
			$status['text'] = 'Guru tidak dipilih untuk mata pelajaran '.$nama_mapel_alias;;
			$status['title'] = 'Data dilewati!';
		}
		echo json_encode($status);
	}
}
