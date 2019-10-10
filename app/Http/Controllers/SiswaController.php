<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Siswa;
Use App\Rombongan_belajar;
Use App\Anggota_rombel;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use App\Providers\HelperServiceProvider;
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
			'all_jurusan' => Jurusan_sp::where('sekolah_id', '=', $user->sekolah_id)->get(),
		);
		return view('siswa.list_siswa')->with($params);
    }
	public function keluar(){
		$user = auth()->user();
		$params = array(
			'status' => 'keluar',
			'title' => 'Peserta Didik Keluar',
			'all_jurusan' => Jurusan_sp::where('sekolah_id', '=', $user->sekolah_id)->get(),
		);
		return view('siswa.list_siswa')->with($params);
    }
	public function list_siswa(Request $request, $status){
		$user = auth()->user();
		$semester = HelperServiceProvider::get_ta();
		if($status == 'aktif'){
			$callback = function($query) use ($user, $semester) {
				$query->whereHas('rombongan_belajar', function($subquery) use ($user, $semester){
					$subquery->where('rombongan_belajar.jenis_rombel', '=', 1);
					$subquery->where('sekolah_id', '=', $user->sekolah_id);
					$subquery->where('semester_id', '=', $semester->semester_id);
				});
				$query->select(['anggota_rombel.*', 'anggota_rombel.deleted_at as terhapus']);
				$query->with(['rombongan_belajar' => function($subquery) use ($user, $semester){
					$subquery->where('rombongan_belajar.jenis_rombel', '=', 1);
					$subquery->where('sekolah_id', '=', $user->sekolah_id);
					$subquery->where('semester_id', '=', $semester->semester_id);
				}]);
				$query->where('sekolah_id', '=', $user->sekolah_id);
				$query->where('semester_id', '=', $semester->semester_id);
			};
			$query =  Siswa::whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback])->with('agama');
		} else {
			$callback = function($query) use ($user, $semester) {
				$query->whereHas('rombongan_belajar', function($subquery) use ($user, $semester){
					$subquery->where('rombongan_belajar.jenis_rombel', '=', 1);
					$subquery->where('sekolah_id', '=', $user->sekolah_id);
					$subquery->where('semester_id', '=', $semester->semester_id);
				});
				$query->select(['anggota_rombel.*', 'anggota_rombel.deleted_at as terhapus']);
				$query->with(['rombongan_belajar' => function($subquery) use ($user, $semester){
					$subquery->where('rombongan_belajar.jenis_rombel', '=', 1);
					$subquery->where('sekolah_id', '=', $user->sekolah_id);
					$subquery->where('semester_id', '=', $semester->semester_id);
				}]);
				$query->where('sekolah_id', '=', $user->sekolah_id);
				$query->where('semester_id', '=', $semester->semester_id);
				$query->onlyTrashed();
			};
			$query =  Siswa::whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback]);
		}
		return DataTables::of($query)
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
			})
			->addColumn('set_nama', function ($item) {
				if($item->photo){
					$foto = url('storage/images/'.$item->photo);
				} else {
					if($item->jenis_kelamin == 'L'){
						$foto = url('vendor/img/no_avatar.jpg');
					} else {
						$foto = url('vendor/img/no_avatar_f.jpg');
					}
				}
				$return  = '<img src="'.$foto.'" width="50" style="float:left; margin-right:10px;" />'.strtoupper($item->nama)."<br />".$item->nisn;
				return $return;
			})
			->addColumn('set_tempat_lahir', function ($item) {
				$return  = $item->tempat_lahir.', '.HelperServiceProvider::TanggalIndo(date('Y-m-d', strtotime($item->tanggal_lahir)));
				return $return;
			})
			->addColumn('set_agama', function ($item) {
				$return  = ($item->agama) ? $item->agama->nama : '-';
				return $return;
			})
            ->addColumn('rombel', function ($item) {
				$anggota = $item->anggota_rombel->first();
				$nama_rombel = ($anggota->rombongan_belajar) ? $anggota->rombongan_belajar->nama.'/'.$anggota->rombongan_belajar->tingkat : '-';
				return $nama_rombel;
			})
            ->addColumn('tgl_keluar', function ($item) {
				$anggota = $item->anggota_rombel->first();
				$return  = ($anggota->terhapus) ? HelperServiceProvider::TanggalIndo(date('Y-m-d', strtotime($anggota->terhapus))) : '-';
				//$return  = '-';
				return $return;
			})
            ->addColumn('actions', function ($item) {
				$links = '<div class="text-center"><a href="'.url('pd/view/'.$item->peserta_didik_id).'" class="btn btn-success btn-sm toggle-modal"><i class="fa fa-eye"></i> Detil</a></a>';
                return $links;

            })
            ->rawColumns(['set_nama', 'set_tempat_lahir', 'agama', 'rombel', 'tgl_keluar', 'actions'])
            ->make(true);  
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
		$update = Siswa::where('peserta_didik_id', '=', $request['peserta_didik_id'])->update($update_data);
		if($update){
			User::where('peserta_didik_id', '=', $request['peserta_didik_id'])->update(['email' => $request['email']]);
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
