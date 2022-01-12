<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\User;
use App\Guru;
use App\Sekolah;
use Illuminate\Support\Facades\DB;
use App\Pembelajaran;
use App\Rombongan_belajar;
use Alert;
use App\NilaiAkhirKeterampilan;
use App\NilaiAkhirPengetahuan;
use App\NilaiAkhirPk;
use App\Nilai_akhir;
use App\Semester;
use App\Anggota_rombel;
use CustomHelper;
use App\Rencana_penilaian;
use App\Kd_nilai;
use App\Nilai_rapor;
use Yajra\Datatables\Datatables;
use App\Jurusan_sp;
use App\Remedial;
use Session;
use App\Setting;
use App\NilaiPkByKd;
use Illuminate\Support\Facades\Storage;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
		$user = auth()->user();
		$guru = 0;
		$siswa = 0;
		$rencana_penilaian = 0;
		$penilaian = 0;
		$pembelajaran = '';
		$rombongan_belajar = '';
		$all_jurusan = '';
		if($user->hasRole('admin')){
			$guru 	= Guru::where('sekolah_id', session('sekolah_id'))->whereNotNull('guru_id_dapodik')->count();
			$siswa 	= Anggota_rombel::whereHas('siswa')->whereHas('rombongan_belajar', function($query){
				$query->where('jenis_rombel', 1);
			})->where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'))->count();
			$rencana_penilaian = Rencana_penilaian::whereHas('pembelajaran', function($query){
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
			})->count();
			$penilaian = Kd_nilai::whereHas('rencana_penilaian', function ($q){
				$q->whereHas('pembelajaran', function($query){
					$query->where('sekolah_id', session('sekolah_id'));
					$query->where('semester_id', session('semester_id'));
				});
			})->count();
		}
		if($user->hasRole('guru')){
			$pembelajaran = Pembelajaran::whereHas('rombongan_belajar')->with('mata_pelajaran')->with(['rombongan_belajar' => function($query){
				$query->withCount('anggota_rombel')->with(['wali' => function($query){
					$query->with('gelar_depan');
					$query->with('gelar_belakang');
				}])->orderBy('tingkat', 'asc');
			}])
			->withCount('rencana_pengetahuan')
			->withCount('rencana_keterampilan')
			->withCount('rencana_pk')
			->withCount('nilai_akhir_pengetahuan')
			->withCount('nilai_akhir_keterampilan')
			->withCount('nilai_akhir_pk')
			->withCount('rencana_pengetahuan_dinilai')
			->withCount('rencana_keterampilan_dinilai')
			->withCount('rencana_pk_dinilai')
			->where('sekolah_id', $user->sekolah_id)
			->where('semester_id', session('semester_id'))
			->where('guru_id', $user->guru_id)
			->whereNotNull('kelompok_id')
			->whereNotNull('no_urut')
			->orWhere('guru_pengajar_id', $user->guru_id)
			->where('sekolah_id', $user->sekolah_id)
			->where('semester_id', session('semester_id'))
			->whereNotNull('kelompok_id')
			->whereNotNull('no_urut')
			->orderBy('mata_pelajaran_id', 'asc')
			->get();
			if($user->hasRole('wali')){
				$rombongan_belajar = Rombongan_belajar::with(['pembelajaran' => function($query){
					$callback = function($q){
						$q->with('gelar_depan');
						$q->with('gelar_belakang');
					};
					$query->with(['guru' => $callback]);
					$query->with(['pengajar' => $callback]);
					$query->withCount('rencana_pengetahuan');
					$query->withCount('rencana_keterampilan');
					$query->withCount('rencana_pk');
					$query->withCount('nilai_akhir_pengetahuan');
					$query->withCount('nilai_akhir_keterampilan');
					$query->withCount('nilai_akhir_pk');
					$query->withCount('rencana_pengetahuan_dinilai');
					$query->withCount('rencana_keterampilan_dinilai');
					$query->withCount('rencana_pk_dinilai');
					$query->whereNotNull('kelompok_id');
					$query->whereNotNull('no_urut');
				}])->where('sekolah_id', $user->sekolah_id)
				->where('jenis_rombel', 1)
				->where('semester_id', session('semester_id'))
				->where('guru_id', $user->guru_id)->first();
			}
			if($user->hasRole('waka')){
				$all_jurusan = Jurusan_sp::where('sekolah_id', $user->sekolah_id)->get();
			}
		}
		if($user->hasRole('siswa')){
			$user = User::wherehas('siswa.anggota_rombel', function($query){
				$query->whereHas('rombongan_belajar', function($query){
					$query->where('jenis_rombel', 1);
					$query->where('semester_id', session('semester_id'));
				});
			})->with(['siswa', 'siswa.anggota_rombel' => function($query){
				$query->with(['rombongan_belajar' => function($query){
					$query->where('jenis_rombel', 1);
					$query->with(['pembelajaran', 'pembelajaran.nilai_rapor', 'pembelajaran.pengajar', 'pembelajaran.pengajar.gelar_depan', 'pembelajaran.pengajar.gelar_belakang', 'pembelajaran.guru', 'pembelajaran.guru.gelar_depan', 'pembelajaran.guru.gelar_belakang']);
				}, 'rombongan_belajar.wali', 'rombongan_belajar.wali.gelar_depan', 'rombongan_belajar.wali.gelar_belakang']);
				$query->where('semester_id', session('semester_id'));
			}])->find($user->user_id);
		}
		$status_penilaian = CustomHelper::status_penilaian(session('sekolah_id'), session('semester_id'));
		$params = array(
			'user' 				=> $user,
			'pengguna'			=> $user,
			'guru' 				=> $guru,
			'siswa' 			=> $siswa,
			'rencana_penilaian' => ($rencana_penilaian) ? $rencana_penilaian : 0,
			'penilaian' 		=> ($penilaian) ? $penilaian : 0,
			'all_pembelajaran'	=> $pembelajaran,
			'rombongan_belajar'	=> $rombongan_belajar,
			'all_jurusan'		=> $all_jurusan,
			'status_penilaian'	=> $status_penilaian,
		);
		return view('home')->with($params);
    }
	public function proses(Request $request){
        $this->validate($request,[
           'name' => 'required|min:1|max:100',
           'description' => 'required',
        ]);
		$role = Role::create([
            'name' => $request['name'],
            'display_name' => $request['name'],
            'description' => $request['description'],
        ]);
		return redirect()->route('home')->with('success', "The role <strong>$role->name</strong> has successfully been created.");
    }
	public function kunci_nilai($rombongan_belajar_id, $status){
		$rombel = Rombongan_belajar::find($rombongan_belajar_id);
		$rombel->kunci_nilai = ($status) ? 0 : 1;
		if($rombel->save()){
			$with = 'success';
			$text = ($status) ? 'berhasil di aktifkan' : 'berhasil di nonaktifkan';
		} else {
			$text = ($status) ? 'gagal di aktifkan' : 'gagal di nonaktifkan';
			$with = 'error';
		}
		return redirect()->route('home')->with($with, 'Status Penilaian di Rombongan Belajar '.$rombel->nama.' '. $text);
	}
	public function togglePenilaian(Request $request){
		$user = auth()->user();
		$status = $request->status;
		$kunci_nilai = ($status) ? 0 : 1;
		$text = ($status) ? 'Status Penilaian di aktifkan' : 'Status Penilaian berhasil di nonaktifkan';
		Rombongan_belajar::where('sekolah_id', $user->sekolah_id)->where('semester_id', $user->periode_aktif)->update(['kunci_nilai' => $kunci_nilai]);
		$status_penilaian = CustomHelper::status_penilaian(session('sekolah_id'), session('semester_id'));
		$status_penilaian->status = $kunci_nilai;
		/*$status_penilaian = Setting::updateOrCreate(
			['key' => 'status_penilaian'],
			['value' => $kunci_nilai]
		);*/
		if($status_penilaian->save()){
			$output = [
				'title' => 'Berhasil',
				'icon' => 'success',
				'success' => TRUE,
				'message' => $text,
			];
		} else {
			$output = [
				'title' => 'Berhasil',
				'icon' => 'error',
				'success' => FALSE,
				'message' => $text,
			];
		}
		return response()->json($output);
	}
	public function generate_nilai($pembelajaran_id, $kompetensi_id){
		$user = auth()->user();
		if($kompetensi_id == 1){
			$rencana_penilaian = 'rencana_pengetahuan';
		} elseif($kompetensi_id == 2){
			$rencana_penilaian = 'rencana_keterampilan';
		} else {
			$rencana_penilaian = 'rencana_pk';
		}
		$pembelajaran = Pembelajaran::with([$rencana_penilaian, $rencana_penilaian.'.kd_nilai', $rencana_penilaian.'.kd_nilai.nilai'])->find($pembelajaran_id);
		$kkm = CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm);
		$result = array();
		$bobot = 0;
		foreach($pembelajaran->{$rencana_penilaian} as $rencana){
			$kd_nilai_count = 0;
			$bobot += $rencana->bobot;
			foreach($rencana->kd_nilai as $kd_nilai){
				$kd_nilai_count++;
				foreach($kd_nilai->nilai as $nilai){
					$record = array();
					$record['pembelajaran_id'] = $pembelajaran->pembelajaran_id;
					$record['anggota_rombel_id'] = $nilai->anggota_rombel_id;
					$record['kompetensi_id'] = $rencana->kompetensi_id;
					$record['rasio_p'] = ($pembelajaran->rasio_p) ? $pembelajaran->rasio_p : 50;
					$record['rasio_k'] = ($pembelajaran->rasio_k) ? $pembelajaran->rasio_k : 50;
					$result[$nilai->anggota_rombel_id][$kd_nilai->kompetensi_dasar_id] = $record;
					$result[$nilai->anggota_rombel_id][$kd_nilai->kompetensi_dasar_id] = $record;
				}
			}
		}
		$a=0;
		$b=0;
		foreach($result as $key => $value){
			foreach($value as $k=>$v){
				$pembelajaran_id = $v['pembelajaran_id'];
				$rasio_p = $v['rasio_p'];
				$rasio_k = $v['rasio_k'];
			}
			$get_nilai_remedial = Remedial::where('pembelajaran_id', $pembelajaran_id)->where('anggota_rombel_id', $key)->where('kompetensi_id', $kompetensi_id)->first();
			$nilai_akhir = NULL;
			if($get_nilai_remedial){
				$nilai_akhir = $get_nilai_remedial->rerata_remedial;
			} else {
				if($kompetensi_id == 1){
					$query = NilaiAkhirPengetahuan::where('pembelajaran_id', $pembelajaran_id)->where('anggota_rombel_id', $key)->where('kompetensi_id', $kompetensi_id)->first();
				} elseif($kompetensi_id == 2){
					$query = NilaiAkhirKeterampilan::where('pembelajaran_id', $pembelajaran_id)->where('anggota_rombel_id', $key)->where('kompetensi_id', $kompetensi_id)->first();
				} else {
					$query = NilaiAkhirPk::where('pembelajaran_id', $pembelajaran_id)->where('anggota_rombel_id', $key)->where('kompetensi_id', $kompetensi_id)->first();
				}
				if($kompetensi_id == 3){
					$nilai_akhir = $query->nilai_akhir;
				} else {
					$nilai_akhir = $query->nilai_akhir;
				}
			}
			if($nilai_akhir != NULL){
				$find_nilai_akhir = Nilai_akhir::where('pembelajaran_id', $pembelajaran_id)->where('anggota_rombel_id', $key)->where('kompetensi_id', $kompetensi_id)->first();
				if($find_nilai_akhir){
					$a++;
					$find_nilai_akhir->nilai = $nilai_akhir;
					$find_nilai_akhir->last_sync = date('Y-m-d H:i:s');
					$find_nilai_akhir->save();
				} else {
					$b++;
					$insert_nilai_akhir = array(
						'sekolah_id'		=> $user->sekolah_id,
						'pembelajaran_id'	=> $pembelajaran_id,
						'anggota_rombel_id'	=> $key,
						'kompetensi_id'		=> $kompetensi_id,
						'nilai'				=> $nilai_akhir,
						'last_sync'		=> date('Y-m-d H:i:s'),
					);
					Nilai_akhir::create($insert_nilai_akhir);
				}
				$find_nilai_rapor = Nilai_rapor::where('anggota_rombel_id', $key)->where('pembelajaran_id', $pembelajaran_id)->first();
				$insert_rapor = array(
					'anggota_rombel_id'	=> $key,
					'pembelajaran_id'	=> $pembelajaran_id,
					'sekolah_id' 		=> $user->sekolah_id,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				$insert_rapor['total_nilai'] = $nilai_akhir;
				if($kompetensi_id == 1){
					$insert_rapor['nilai_p'] = $nilai_akhir;
					$insert_rapor['rasio_p'] = $rasio_p;
				} else {
					$insert_rapor['nilai_k'] = $nilai_akhir;
					$insert_rapor['rasio_k'] = $rasio_k;
				}
				if($find_nilai_rapor){
					if($kompetensi_id == 1){
						$find_nilai_rapor->nilai_p = $nilai_akhir;
						$find_nilai_rapor->rasio_p = $rasio_p;
						$find_nilai_rapor->total_nilai = ($find_nilai_rapor->nilai_p + $nilai_akhir) - $kkm;
						$find_nilai_rapor->save();
					} else {
						$find_nilai_rapor->nilai_k = $nilai_akhir;
						$find_nilai_rapor->rasio_k = $rasio_k;
						$find_nilai_rapor->total_nilai = ($find_nilai_rapor->nilai_k + $nilai_akhir) - $kkm;
						$find_nilai_rapor->save();
					}
				} else {
					Nilai_rapor::create($insert_rapor);
				}
			}
		}
		$status['icon'] = 'success';
		$status['text'] = "$b siswa berhasil disimpan. $a siswa berhasil diperbaharui";
		$status['insert'] = $b;
		$status['update'] = $a;
		$status['title'] = 'Generate Nilai Selesai!';
		echo json_encode($status);
	}
	public function progres_perencanaan_dan_penilaian(Request $request){
		$user = auth()->user();
		$callback = function($q){
			$q->with('gelar_depan');
			$q->with('gelar_belakang');
		};
		$query = Pembelajaran::select(['pembelajaran.*', 'rombongan_belajar.nama as nama_rombel'])->with(['guru' => $callback])
		->with(['pengajar' => $callback],'rombongan_belajar')
		->withCount('rencana_pengetahuan')
		->withCount('rencana_keterampilan')
		->withCount('rencana_pk')
		->withCount('nilai_akhir_pengetahuan')
		->withCount('nilai_akhir_keterampilan')
		->withCount('nilai_akhir_pk')
		->withCount('rencana_pengetahuan_dinilai')
		->withCount('rencana_keterampilan_dinilai')
		->withCount('rencana_pk_dinilai')
		->whereNotNull('kelompok_id')
		->whereNotNull('no_urut')
		->where('pembelajaran.sekolah_id', $user->sekolah_id)
		->where('pembelajaran.semester_id', session('semester_id'))
		->join('rombongan_belajar', 'pembelajaran.rombongan_belajar_id', 'rombongan_belajar.rombongan_belajar_id');
		return Datatables::of($query)
		->orderColumn('nama_rombel', 'nama_mata_pelajaran $1')
		->filter(function ($query) {
			if (request()->has('filter_jurusan')) {
				$query->whereIn('rombongan_belajar.rombongan_belajar_id',function($q) {
					$q->select('rombongan_belajar.rombongan_belajar_id')->from('rombongan_belajar')->where('jurusan_id', request('filter_jurusan'));
				});
			}
			if (request()->has('filter_tingkat')) {
				$query->whereIn('rombongan_belajar.rombongan_belajar_id',function($q) {
					$q->select('rombongan_belajar.rombongan_belajar_id')->from('rombongan_belajar')->where('tingkat', request('filter_tingkat'));
				});
			}
			if (request()->has('filter_rombel')) {
				$query->whereIn('rombongan_belajar.rombongan_belajar_id',function($q) {
					$q->select('rombongan_belajar.rombongan_belajar_id')->from('rombongan_belajar')->where('rombongan_belajar.rombongan_belajar_id', request('filter_rombel'));
				});
			}
		}, true)
		->addColumn('guru_mapel', function ($item) {
			$return  = (($item->pengajar) ? CustomHelper::nama_guru($item->pengajar->gelar_depan, $item->pengajar->nama, $item->pengajar->gelar_belakang) : (($item->guru) ? CustomHelper::nama_guru($item->guru->gelar_depan, $item->guru->nama, $item->guru->gelar_belakang) : '-'));
			return $return;
		})
		->addColumn('guru_pengajar', function ($item) {
			$return  = ($item->pengajar) ? CustomHelper::nama_guru($item->pengajar->gelar_depan, $item->pengajar->nama, $item->pengajar->gelar_belakang) : '-';
			return $return;
		})
		->addColumn('skm', function ($item) {
			$return  = CustomHelper::get_kkm($item->kelompok_id, $item->kkm);
			return '<div class="text-center">'.$return.'</div>';
		})
		->addColumn('jumlah_rencana_p', function ($item) {
			$return  = $item->rencana_pengetahuan_count ?: $item->rencana_pk_count;
			return '<div class="text-center">'.$return.'</div>';
		})
		->addColumn('jumlah_rencana_k', function ($item) {
			$return  = $item->rencana_keterampilan_count;
			return '<div class="text-center">'.$return.'</div>';
		})
		->addColumn('jumlah_nilai_p', function ($item) {
			$return  = $item->rencana_pengetahuan_dinilai_count ?: $item->rencana_pk_dinilai_count;
			return '<div class="text-center">'.$return.'</div>';
		})
		->addColumn('jumlah_nilai_k', function ($item) {
			$return  = $item->rencana_keterampilan_dinilai_count;
			return '<div class="text-center">'.$return.'</div>';
		})
		->addColumn('generate_p', function ($item) {
			$count  = $item->nilai_akhir_pengetahuan_count;
			if($count){
				$class = 'danger';
				$text = 'Perbaharui';
			} else {
				$text = 'Proses';
				$class = 'success';
			}
			if($item->rencana_pengetahuan_dinilai_count){
				$return  = '<a href="'.url('/generate-nilai/'.$item->pembelajaran_id.'/1').'" class="generate_nilai btn btn-sm btn-'.$class.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text.'</a>';
			} elseif($item->rencana_pk_dinilai_count){
				$return  = '<a href="'.url('/generate-nilai/'.$item->pembelajaran_id.'/3').'" class="generate_nilai btn btn-sm btn-'.$class.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text.'</a>';
			} else {
				$return  = '-';
			}
			return '<div class="text-center">'.$return.'</div>';
		})
		->addColumn('generate_k', function ($item) {
			$count  = $item->nilai_akhir_keterampilan_count;
			if($count){
				$class = 'danger';
				$text = 'Perbaharui';
			} else {
				$text = 'Proses';
				$class = 'success';
			}
			if($item->rencana_keterampilan_dinilai_count){
				$return  = '<a href="'.url('/generate-nilai/'.$item->pembelajaran_id.'/2').'" class="generate_nilai btn btn-sm btn-'.$class.' btn_generate btn-sm"><i class="fa fa-check-square-o"></i> '.$text.'</a>';
			} else {
				$return  = '-';
			}
			return '<div class="text-center">'.$return.'</div>';
		})
		->rawColumns(['guru_mapel', 'skm', 'jumlah_rencana_p', 'jumlah_rencana_k', 'jumlah_nilai_p', 'jumlah_nilai_k', 'generate_p', 'generate_k'])
		->make(true);
	}
	public function detil_nilai($pembelajaran_id){
		$user = auth()->user();
		$pembelajaran = Pembelajaran::with(['kd_nilai_p' => function($query){
			$query->select(['kompetensi_dasar_id', 'rencana_penilaian.pembelajaran_id']);
			$query->groupBy(['kompetensi_dasar_id', 'rencana_penilaian.pembelajaran_id']);
			$query->with('kompetensi_dasar');
			$query->with('nilai_kd_pengetahuan');
		}, 'kd_nilai_k' => function($query){
			$query->select(['kompetensi_dasar_id', 'rencana_penilaian.pembelajaran_id']);
			$query->groupBy(['kompetensi_dasar_id', 'rencana_penilaian.pembelajaran_id']);
			$query->with('kompetensi_dasar');
			$query->with('nilai_kd_keterampilan');
		}])->find($pembelajaran_id);
		$params = array(
			'user' 				=> $user,
			'pembelajaran'		=> $pembelajaran,
		);
        return view('monitoring.detil_nilai')->with($params);
	}
}
