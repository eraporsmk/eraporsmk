<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Siswa;
Use App\Rombongan_belajar;
Use App\Anggota_rombel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use CustomHelper;
use Illuminate\Support\Facades\DB;
use App\Jurusan_sp;
use Illuminate\Support\Str;
use App\Pekerjaan;
use App\User;
class SiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
		$user = auth()->user();
		$params = array(
			'status' => 'aktif',
			'title' => 'Peserta Didik Aktif',
			'all_jurusan' => Jurusan_sp::where('sekolah_id', session('sekolah_id'))->get(),
		);
		return view('siswa.list_siswa')->with($params);
    }
	public function keluar(){
		$user = auth()->user();
		$params = array(
			'status' => 'keluar',
			'title' => 'Peserta Didik Keluar',
			'all_jurusan' => Jurusan_sp::where('sekolah_id', session('sekolah_id'))->get(),
		);
		return view('siswa.list_siswa')->with($params);
	}
	public function password_siswa(Request $request){
		$user = auth()->user();
		$callback = function($query) use ($user) {
			$query->whereHas('anggota_rombel', function($query) use ($user){
				$query->whereHas('rombongan_belajar', function($subquery) use ($user){
					$subquery->where('rombongan_belajar.jenis_rombel', 1);
					$subquery->where('sekolah_id', session('sekolah_id'));
					$subquery->where('semester_id', session('semester_id'));
					$subquery->where('guru_id', $user->guru_id);
				});
			});
		};
		$query =  User::with('siswa')->whereHas('siswa', $callback)->orderBy('name')->get();
		$params = ['all_siswa' => $query];
		return view('siswa.list_password_siswa')->with($params);
	}
	public function list_siswa(Request $request, $status){
		$user = auth()->user();
		if($status == 'aktif'){
			$callback = function($query) use ($user) {
				$query->whereHas('rombongan_belajar', function($subquery) use ($user){
					$subquery->where('rombongan_belajar.jenis_rombel', 1);
					$subquery->where('sekolah_id', session('sekolah_id'));
					$subquery->where('semester_id', session('semester_id'));
				});
				$query->select(['anggota_rombel.*', 'anggota_rombel.deleted_at as terhapus']);
				$query->with(['rombongan_belajar' => function($subquery) use ($user){
					$subquery->where('rombongan_belajar.jenis_rombel', 1);
					$subquery->where('sekolah_id', session('sekolah_id'));
					$subquery->where('semester_id', session('semester_id'));
				}]);
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
			};
			$query =  Siswa::whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback, 'agama']);
		} else {
			$callback = function($query) use ($user) {
				$query->whereHas('rombongan_belajar', function($subquery) use ($user){
					$subquery->where('rombongan_belajar.jenis_rombel', 1);
					$subquery->where('sekolah_id', session('sekolah_id'));
					$subquery->where('semester_id', session('semester_id'));
				});
				$query->select(['anggota_rombel.*', 'anggota_rombel.deleted_at as terhapus']);
				$query->with(['rombongan_belajar' => function($subquery) use ($user){
					$subquery->where('rombongan_belajar.jenis_rombel', 1);
					$subquery->where('sekolah_id', session('sekolah_id'));
					$subquery->where('semester_id', session('semester_id'));
				}]);
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
				$query->onlyTrashed();
			};
			$query =  Siswa::whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback, 'agama'])->onlyTrashed();
		}
		$dt = DataTables::of($query)
			->filter(function ($query) {
				if (request()->has('filter_jurusan')) {
					$query->whereHas('anggota_rombel.rombongan_belajar', function($subquery){
						$subquery->where('jurusan_sp_id', request('filter_jurusan'));
					});
				}
				if (request()->has('filter_kelas')) {
					$query->whereHas('anggota_rombel.rombongan_belajar', function($subquery){
						$subquery->where('jurusan_sp_id', request('filter_jurusan'));
						$subquery->where('tingkat', request('filter_kelas'));
					});
				}
				if (request()->has('filter_rombel')) {
					$query->whereHas('anggota_rombel', function($subquery){
						$subquery->where('rombongan_belajar_id', request('filter_rombel'));
					});
				}
				if (request()->has('search')) {
					$search = request('search')['value'];
					if($search){
						$query->where('nama', 'ilike', '%'.$search.'%');
					}
				}
			})
			->addColumn('set_nama', function ($item) {
				if($item->photo){
					$foto = asset('storage/images/'.$item->photo);
				} else {
					if($item->jenis_kelamin == 'L'){
						$foto = asset('vendor/img/no_avatar.jpg');
					} else {
						$foto = asset('vendor/img/no_avatar_f.jpg');
					}
				}
				$return  = '<img src="'.$foto.'" width="50" style="float:left; margin-right:10px;" />'.strtoupper($item->nama)."<br />".$item->nisn;
				return $return;
			})
			->addColumn('set_tempat_lahir', function ($item) {
				$return  = $item->tempat_lahir.', '.CustomHelper::TanggalIndo(date('Y-m-d', strtotime($item->tanggal_lahir)));
				return $return;
			})
			->addColumn('set_agama', function ($item) {
				$return  = ($item->agama) ? $item->agama->nama : '-';
				return $return;
			})
            ->addColumn('rombel', function ($item) {
				$nama_rombel = '-';
				if($item->anggota_rombel){
					$nama_rombel = $item->anggota_rombel->rombongan_belajar->nama;
				}
				return $nama_rombel;
			})
            ->addColumn('tgl_keluar', function ($item) {
				//$anggota = $item->anggota_rombel->first();
				$return  = ($item->anggota_rombel) ? ($item->anggota_rombel->terhapus) ? CustomHelper::TanggalIndo(date('Y-m-d', strtotime($item->anggota_rombel->terhapus))) : '-' : '';
				//$return  = '-';
				return $return;
			})
            ->addColumn('actions', function ($item) {
				$links = '<div class="text-center"><a href="'.url('pd/view/'.$item->peserta_didik_id).'" class="btn btn-success btn-sm toggle-modal"><i class="fa fa-eye"></i> Detil</a></a>';
                return $links;

            });
		if ($request->has('filter_kelas')) {
			$dt->addColumn('rombongan_belajar', function () {
				$tingkat = request('filter_kelas');
				$data_rombel = Rombongan_belajar::where(function($query) {
					$query->where('jurusan_sp_id', request('filter_jurusan'));
					$query->where('tingkat', request('filter_kelas'));
					$query->where('semester_id', session('semester_id'));
					$query->where('sekolah_id', session('sekolah_id'));
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
					$record['text'] 	= 'Tidak ditemukan rombongan belajar di kelas terpilih '.$tingkat;
					$output['result'][] = $record;
				}
				return $output;
			});
		} else {
			$dt->addColumn('rombongan_belajar', function () {
				$record['value'] 	= '';
				$record['text'] 	= 'Tidak ditemukan rombongan belajar di kelas terpilih';
				$output['result'][] = $record;
				return $output;
			});
		}
		$dt->rawColumns(['rombongan_belajar', 'set_nama', 'set_tempat_lahir', 'agama', 'rombel', 'tgl_keluar', 'actions']);
        //->make(true); 
		return $dt->make(true); 
	}
	public function view($siswa_id){
		/*$a = Siswa::find($siswa_id);
		$a->delete();
		return redirect()->route('pd_aktif');*/
		$data['siswa'] = Siswa::withTrashed()->with('pekerjaan_ayah')->with('pekerjaan_ibu')->with('pekerjaan_wali')->with('agama')->find($siswa_id);
		$data['title'] = 'Detil Peserta Didik';
		$data['pekerjaan'] = Pekerjaan::all();
		$data['modal_s'] = 'modal_s';
		return view('siswa.view', $data);
	}
	public function update_data(Request $request){
		$update_data = array(
			'status' 			=> $request['status'],
			'anak_ke' 			=> $request['anak_ke'],
			'sekolah_asal' 		=> $request['sekolah_asal'],
			'diterima_kelas' 	=> $request['diterima_kelas'],
			'email' 			=> $request['email'],
			'nama_wali' 		=> $request['nama_wali'],
			'alamat_wali' 		=> $request['alamat_wali'],
			'telp_wali' 		=> $request['telp_wali'],
			'kerja_wali' 		=> $request['kerja_wali'],
		);
		$update = Siswa::where('peserta_didik_id', $request['peserta_didik_id'])->update($update_data);
		if($update){
			User::where('peserta_didik_id', $request['peserta_didik_id'])->update(['email' => $request['email']]);
			$output['title'] = 'Sukses';
			$output['text'] = 'Berhasil memperbaharui data peserta didik';
			$output['icon'] = 'success';
			$output['sukses'] = 1;
		} else {
			$output['title'] = 'Gagal';
			$output['text'] = 'Gagal memperbaharui data peserta didik';
			$output['icon'] = 'error';
			$output['sukses'] = 0;
		}
		echo json_encode($output);
	}
}
