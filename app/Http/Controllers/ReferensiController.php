<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mata_pelajaran_kurikulum;
use App\Rombongan_belajar;
use Yajra\Datatables\Datatables;
use App\Ekstrakurikuler;
use Illuminate\Support\Facades\Storage;
use App\Teknik_penilaian;
use App\Sikap;
use App\Kompetensi_dasar;
use App\Paket_ukk;
use App\Unit_ukk;
use App\Pembelajaran;
use Illuminate\Support\Str;
use App\Jurusan_sp;
use Illuminate\Support\Facades\Validator;
use Session;
use Artisan;
use CustomHelper;
use App\Kurikulum;
use App\Kd_nilai;
class ReferensiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
		return view('referensi.list_mata_pelajaran');
    }
	public function list_mata_pelajaran(){
		$user = auth()->user();
		$query = Mata_pelajaran_kurikulum::with('mata_pelajaran')->with('kurikulum')->whereIn('mata_pelajaran_kurikulum.kurikulum_id', function($query){
			$query->select('kurikulum_id')->from(with(new Rombongan_belajar)->getTable())->where('sekolah_id', session('sekolah_id'))->where('semester_id', session('semester_id'));
		})->orderBy('mata_pelajaran_kurikulum.kurikulum_id')->orderBy('mata_pelajaran_id')->orderBy('tingkat_pendidikan_id');
		return Datatables::of($query)->make(true);
	}
	public function ekskul(){
		Storage::disk('public')->delete('anggota_ekskul_by_rombel.json');
		return view('referensi.list_ekskul');
	}
	public function list_ekskul(){
		$user = auth()->user();
		$query = Ekstrakurikuler::with(['guru', 'rombongan_belajar'])->where('ekstrakurikuler.sekolah_id', session('sekolah_id'))
		->where('ekstrakurikuler.semester_id', session('semester_id'));
		return Datatables::of($query)
		->addColumn('anggota', function ($item) {
			$return  = '<div class="text-center"><a href="'.url('rombel/anggota/'.$item->rombongan_belajar->rombongan_belajar_id).'" class="btn btn-primary btn-sm toggle-modal"><i class="fa fa-eye"></i> Anggota Ekskul</a></div>';
			return $return;
		})
		->addColumn('sync_anggota', function ($item) {
			$return  = '<div class="text-center"><a href="'.url('sinkronisasi/anggota-by-rombel/'.$item->rombongan_belajar->rombel_id_dapodik).'" class="sync_anggota btn btn-danger btn-sm"><i class="fa fa-refresh"></i> Sinkron Anggota</a></div>';
			return $return;
		})
		 ->rawColumns(['anggota', 'sync_anggota'])
		->make(true);
	}
	public function metode(){
		/*$user = auth()->user();
		$query = Teknik_penilaian::where('sekolah_id', session('sekolah_id'));
		if(!$query->count()){
			$insert_teknik = array(
				array(
					'sekolah_id' 	=> session('sekolah_id'),
					'kompetensi_id'	=> 1,
					'nama'			=> 'Tes Tertulis',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> session('sekolah_id'),
					'kompetensi_id'	=> 1,
					'nama'			=> 'Tes Lisan',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> session('sekolah_id'),
					'kompetensi_id'	=> 1,
					'nama'			=> 'Penugasan',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> session('sekolah_id'),
					'kompetensi_id'	=> 2,
					'nama'			=> 'Portofolio',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> session('sekolah_id'),
					'kompetensi_id'	=> 2,
					'nama'			=> 'Kinerja',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
				array(
					'sekolah_id' 	=> session('sekolah_id'),
					'kompetensi_id'	=> 2,
					'nama'			=> 'Proyek',
					'last_sync'		=> date('Y-m-d H:i:s'),
				),
			);
			foreach($insert_teknik as $teknik){
				Teknik_penilaian::create($teknik);
			}
			return redirect(url('referensi/metode'));
		}*/
		return view('referensi.list_metode');
    }
	public function list_metode(){
		$user = auth()->user();
		$query = Teknik_penilaian::where('kompetensi_id', '<>', 3);
		return Datatables::of($query)
		->addColumn('kompetensi', function ($item) {
			$return  = ($item->kompetensi_id == 1) ? 'Pengetahuan' : 'Keterampilan';
			return $return;
		})
		->addColumn('tindakan', function ($item) {
			$return  = '<div class="text-center">';
			$return  .= '<a href="'.url('referensi/edit-metode/'.$item->teknik_penilaian_id).'" class="btn btn-warning btn-xs"><i class="fa fa-pencil"></i> Edit</a> ';
			$return  .= '<a href="'.url('referensi/hapus-metode/'.$item->teknik_penilaian_id).'" class="confirm btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</a>';
			$return  .= '</div>';
			return $return;
		})
		 ->rawColumns(['kompetensi', 'tindakan'])
		->make(true);
	}
	public function tambah_metode(){
		$data['metode'] = '';
		return view('referensi.add_metode', $data);
	}
	public function edit_metode($id){
		$data['metode'] = Teknik_penilaian::find($id);
		return view('referensi.add_metode', $data);
	}
	public function simpan_metode(Request $request){
		Artisan::call('migrate');
		$user = auth()->user();
		if($request['teknik_penilaian_id']){
			$metode = Teknik_penilaian::find($request['teknik_penilaian_id']);
			$metode->nama = $request['nama_metode'];
			$metode->save();
			Session::flash('success',"Data teknik penilaian berhasil diperbaharui");
		} else {
			Teknik_penilaian::firstOrCreate(
				[
					'kompetensi_id' => $request['kompetensi_id'],
					'nama'	=> $request['nama_metode'],
				],
				[
					'sekolah_id' => session('sekolah_id'),
					'last_sync'	=> date('Y-m-d H:i:s'),
				]
			);
			Session::flash('success',"Data teknik penilaian berhasil diproses");
		}
		return redirect('/referensi/metode');
	}
	public function hapus_metode($id){
		if(Teknik_penilaian::destroy($id)){
			$output['text'] = 'Berhasil menghapus data teknik penilaian';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Gagal menghapus data teknik penilaian';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function sikap(){
		$user = auth()->user();
		$query = Sikap::first();
		if(!$query){
			$insert_sikap = array(
				array(
					'butir_sikap'	=> 'Integritas',
					'last_sync'		=> date('Y-m-d H:i:s'),
					'sub_sikap'		=> array(
						array(
							'butir_sikap'	=> 'Kesetiaan',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Antikorupsi',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Keteladanan',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Keadilan',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Menghargai martabat manusia',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
					),
				),
				array(
					'butir_sikap'	=> 'Religius',
					'last_sync'		=> date('Y-m-d H:i:s'),
					'sub_sikap'		=> array(
						array(
							'butir_sikap'	=> 'Melindungi yang kecil dan tersisih',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Taat beribadah',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Menjalankan ajaran agama',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Menjauhi larangan agama',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
					),
				),
				array(
					'butir_sikap'	=> 'Nasionalis',
					'last_sync'		=> date('Y-m-d H:i:s'),
					'sub_sikap'		=> array(
						array(
							'butir_sikap'	=> 'Rela berkorban',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Taat hukum',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Unggul',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Disiplin',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Berprestasi',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Cinta damai',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
					),
				),
				array(
					'butir_sikap'	=> 'Mandiri',
					'last_sync'		=> date('Y-m-d H:i:s'),
					'sub_sikap'		=> array(
						array(
							'butir_sikap'	=> 'Tangguh',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Kerja keras',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Kreatif',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Keberanian',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Pembelajar',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Daya juang',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Berwawasan informasi dan teknologi',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
					),
				),
				array(
					'butir_sikap'	=> 'Gotong-royong',
					'last_sync'		=> date('Y-m-d H:i:s'),
					'sub_sikap'		=> array(
						array(
							'butir_sikap'	=> 'Musyawarah',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Tolong-menolong',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Kerelawanan',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Solidaritas',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
						array(
							'butir_sikap'	=> 'Antidiskriminasi',
							'last_sync'		=> date('Y-m-d H:i:s'),
						),
					),
				),
			);
			foreach($insert_sikap as $sikap){
				//dd($sikap);
				$induk = Sikap::create([
					'butir_sikap'	=> $sikap['butir_sikap'],
					'last_sync'		=> $sikap['last_sync'],
				]);
				foreach($sikap['sub_sikap'] as $sub_sikap){
					Sikap::create([
						'sikap_induk'	=> $induk->sikap_id,
						'butir_sikap'	=> $sub_sikap['butir_sikap'],
						'last_sync'		=> $sub_sikap['last_sync'],
					]);
				}
			}
			return redirect(url('referensi/sikap'));
		}
		$params = array(
			'all_sikap' => $query = Sikap::whereNull('sikap_induk')->with('sikap')->get()
		);
		return view('referensi.list_sikap')->with($params);
    }
	public function kd(){
		$user = auth()->user();
		$params = array(
			'all_pembelajaran' => Pembelajaran::select(['mata_pelajaran_id', 'nama_mata_pelajaran'])->where('sekolah_id', session('sekolah_id'))
			->where('semester_id', session('semester_id'))
			->where('guru_id', $user->guru_id)
			->orWhere('guru_pengajar_id', $user->guru_id)
			->orderBy('mata_pelajaran_id', 'asc')
			->groupBy('mata_pelajaran_id')
			->groupBy('nama_mata_pelajaran')
			->get(),
		);
		return view('referensi.list_kd')->with($params);
    }
	public function add_kd($kompetensi_id = NULL, $rombongan_belajar_id = NULL, $mata_pelajaran_id = NULL, $kelas = NULL){
		$rombongan_belajar = ($rombongan_belajar_id) ? Rombongan_belajar::find($rombongan_belajar_id) : '';
		$pembelajaran = ($mata_pelajaran_id) ? Pembelajaran::where('rombongan_belajar_id', $rombongan_belajar_id)->where('mata_pelajaran_id', $mata_pelajaran_id)->first() : '';
		$user = auth()->user();
		$params = array(
			'semester' => CustomHelper::get_ta(),
			'kompetensi_id'	=> $kompetensi_id,
			'kelas'	=> $kelas,
			'rombongan_belajar'	=> $rombongan_belajar,
			'pembelajaran'	=> $pembelajaran,
		);
		return view('referensi.add_kd')->with($params);
	}
	public function simpan_kd(Request $request){
		$user = auth()->user();
		$kelas_10 = ($request['kelas'] == 10) ? 1 : 0;
		$kelas_11 = ($request['kelas'] == 11) ? 1 : 0;
		$kelas_12 = ($request['kelas'] == 12) ? 1 : 0;
		$kelas_13 = ($request['kelas'] == 13) ? 1 : 0;
		$id_rombel = $request['rombel_id'];
		$rombongan_belajar = Rombongan_belajar::find($id_rombel);
		$get_kurikulum = Kurikulum::find($rombongan_belajar->kurikulum_id);
		if (strpos($get_kurikulum->nama_kurikulum, 'REV') !== false) {
			$kurikulum = 2017;
		} elseif (strpos($get_kurikulum->nama_kurikulum, 'KTSP') !== false) {
			$kurikulum = 2006;
		} else {
			$kurikulum = 2013;
		}
		$insert_kd = Kompetensi_dasar::firstOrCreate(
			[
				'mata_pelajaran_id' => $request['id_mapel'],
				'id_kompetensi'		=> $request['id_kompetensi'],
				'kompetensi_id'		=> $request['kompetensi_id'],
				'kelas_10'			=> $kelas_10,
				'kelas_11'			=> $kelas_11,
				'kelas_12'			=> $kelas_12,
				'kelas_13'			=> $kelas_13,
			],
			[
				'kompetensi_dasar_id'	=> Str::uuid(),
				'kompetensi_dasar'	=> $request['kompetensi_dasar'],
				'user_id' 			=> $user->user_id,
				'aktif'				=> 1,
				'kurikulum'			=> $kurikulum,
				'last_sync'			=> date('Y-m-d H:i:s'),
			]
		);
		if($insert_kd){
			Session::flash('success',"Data kompetensi dasar berhasil diproses");
		} else {
			Session::flash('error',"Data kompetensi dasar gagal diproses");
		}
		return redirect('/referensi/kd');
	}
	public function list_kd(Request $request){
		//dd($request);
		$user = auth()->user();
		$rombongan_belajar = Rombongan_belajar::with('kurikulum')->whereHas('pembelajaran', function($query) use ($user){
			$query->whereNotNull('kelompok_id');
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->where('guru_id', $user->guru_id);
			$query->orWhere('guru_pengajar_id', $user->guru_id);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->whereNotNull('kelompok_id');
		})->select('kurikulum_id')->groupBy('kurikulum_id')->get();
		$kurikulum_id = [];
		if($rombongan_belajar){
			foreach($rombongan_belajar as $rombel){
				if (strpos($rombel->kurikulum->nama_kurikulum, 'REV') !== false) {
					$kurikulum_id[] = 2017;
				} 
				if (strpos($rombel->kurikulum->nama_kurikulum, 'KTSP') !== false) {
					$kurikulum_id[] = 2006;
				}
				if (strpos($rombel->kurikulum->nama_kurikulum, 'KTSP') !== false && strpos($rombel->kurikulum->nama_kurikulum, 'REV') !== false) {
					$kurikulum_id[] = 2013;
				}
			}
		}
		$query = Kompetensi_dasar::with('mata_pelajaran')->whereHas('pembelajaran', function($query) use($user){
			$query->whereNotNull('kelompok_id');
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->where('guru_id', $user->guru_id);
			$query->orWhere('guru_pengajar_id', $user->guru_id);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->whereNotNull('kelompok_id');
		})->whereIn('kurikulum', $kurikulum_id)->orderBy('aktif', 'desc')->orderBy('kompetensi_id')->orderBy('ref.kompetensi_dasar.mata_pelajaran_id')->orderByRaw("cast(NULLIF(regexp_replace(id_kompetensi, '\D', '', 'g'), '') AS integer)")->orderBy('kelas_10')->orderBy('kelas_11')->orderBy('kelas_12')->orderBy('kelas_13');
		return Datatables::of($query)
		->filter(function ($query) use ($request) {
			if ($request->has('mata_pelajaran_id')) {
				$query->where('ref.kompetensi_dasar.mata_pelajaran_id', request('mata_pelajaran_id'));
			}
			if ($request->has('filter_kelas')) {
				$query->where('kelas_'.request('filter_kelas'), 1);
			}
			if ($request->has('filter_kompetensi')) {
				$query->where('kompetensi_id', request('filter_kompetensi'));
			}
			if (request()->has('search')) {
				$search = request('search')['value'];
				if($search){
					$query->where('id_kompetensi', 'ilike', '%'.$search.'%');
					$query->orWhere('kompetensi_dasar', 'ilike', '%'.$search.'%');
					$query->orWhere('kurikulum', 'ilike', '%'.$search.'%');
					$query->orWhereHas('mata_pelajaran', function($q) use ($search) { 
						$q->where('mata_pelajaran_id', 'ilike', '%'.$search.'%');
					});
				}
			}
		})
		->addColumn('isi_kd', function ($item) {
			$return  = ($item->kompetensi_dasar_alias) ? $item->kompetensi_dasar_alias : $item->kompetensi_dasar;
			return $return;
		})
		->addColumn('kelas', function ($item) {
			if($item->kelas_10){
				$kelas = '10';
			} elseif($item->kelas_11){
				$kelas = '11';
			} elseif($item->kelas_12){
				$kelas = '12';
			} elseif($item->kelas_13){
				$kelas = '13';
			}
			$return  = $kelas;
			return $return;
		})
		->addColumn('status', function ($item) {
			$return  = CustomHelper::status_label($item->aktif);
			return $return;
		})
		->addColumn('tindakan', function ($item) {
			if($item->aktif){
				$icon_aktif 	= 'fa-close';
				$title_aktif	= 'Non Aktifkan';
			} else {
            	$icon_aktif 	= 'fa-check';
				$title_aktif	= 'Aktifkan';
			}
			$return  = '<div class="text-center"><div class="btn-group">
							<button type="button" class="btn btn-default btn-sm">Aksi</button>
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu pull-right text-left" role="menu">
								 <li><a href="'.url('referensi/edit-kd/'.$item->kompetensi_dasar_id).'" class="toggle-modal tooltip-left" title="Tambah/Ubah Ringkasan Kompetensi"><i class="fa fa-pencil"></i>Ubah Ringkasan</a></li>
								 <li><a href="'.url('referensi/delete-kd/'.$item->kompetensi_dasar_id).'" class="confirm tooltip-left" title="Hapus Ringkasan Kompetensi"><i class="fa fa-power-off"></i>Hapus</a></li>
								 <li><a data-status="'.$item->aktif.'" href="'.url('referensi/toggle-aktif/'.$item->kompetensi_dasar_id).'" class="confirm_aktif tooltip-left" title="'.$title_aktif.'"><i class="fa '.$icon_aktif.'"></i>'.$title_aktif.'</a></li>
								 <li><a href="'.url('referensi/duplikat/'.$item->kompetensi_dasar_id).'" class="confirm tooltip-left" title="Hapus Data Ganda"><i class="fa fa-power-off"></i>Hapus Data Ganda</a></li>
                            </ul>
                        </div></div>';
			return $return;
		})
		->rawColumns(['isi_kd', 'kelas', 'status', 'tindakan'])
		->make(true);
	}
	public function edit_kd($id){
		$data['kd'] = Kompetensi_dasar::with('mata_pelajaran')->find($id);
		return view('referensi.edit_kd')->with($data);
	}
	public function update_kd(Request $request){
		$kd = Kompetensi_dasar::find($request['id']);
		$kd->kompetensi_dasar = $request['kompetensi_dasar'];
		$kd->kompetensi_dasar_alias = $request['kompetensi_dasar_alias'];
		if($kd->save()){
			$output['text'] = 'Berhasil memperbaharui data kompetensi dasar';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Gagal memperbaharui data kompetensi dasar';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function toggle_aktif($id){
		$kd = Kompetensi_dasar::find($id);
		if($kd->aktif){
			$kd->aktif = 0;
			$text = 'menonaktifkan';
		} else {
			$kd->aktif = 1;
			$text = 'mengaktifkan';
		}
		if($kd->save()){
			$output['text'] = 'Berhasil '.$text.' data kompetensi dasar';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Gagal '.$text.' data kompetensi dasar';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function delete_kd($id){
		$kd = Kompetensi_dasar::find($id);
		$kd->kompetensi_dasar_alias = NULL;
		if($kd->save()){
			$output['text'] = 'Berhasil menghapus ringkasan kompetensi dasar';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Gagal menghapus ringkasan kompetensi dasar';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function duplikat_kd($id){
		$dontDeleteThisRow = Kompetensi_dasar::find($id);
		$delete = Kompetensi_dasar::where(function($query) use ($dontDeleteThisRow){
			$query->where('id_kompetensi', $dontDeleteThisRow->id_kompetensi);
			$query->where('kompetensi_id', $dontDeleteThisRow->kompetensi_id);
			$query->where('mata_pelajaran_id', $dontDeleteThisRow->mata_pelajaran_id);
			$query->where('kelas_10', $dontDeleteThisRow->kelas_10);
			$query->where('kelas_11', $dontDeleteThisRow->kelas_11);
			$query->where('kelas_12', $dontDeleteThisRow->kelas_12);
			$query->where('kelas_13', $dontDeleteThisRow->kelas_13);
			$query->where('kurikulum', $dontDeleteThisRow->kurikulum);
			$query->where('kompetensi_dasar_id', '!=', $dontDeleteThisRow->kompetensi_dasar_id);
		})->delete();
		if($delete){
			$kd_nilai = Kd_nilai::whereHas('kompetensi_dasar', function($query) use ($dontDeleteThisRow){
				$query->where('id_kompetensi', $dontDeleteThisRow->id_kompetensi);
				$query->where('kompetensi_id', $dontDeleteThisRow->kompetensi_id);
				$query->where('mata_pelajaran_id', $dontDeleteThisRow->mata_pelajaran_id);
				$query->where('kelas_10', $dontDeleteThisRow->kelas_10);
				$query->where('kelas_11', $dontDeleteThisRow->kelas_11);
				$query->where('kelas_12', $dontDeleteThisRow->kelas_12);
				$query->where('kelas_13', $dontDeleteThisRow->kelas_13);
				$query->where('kurikulum', $dontDeleteThisRow->kurikulum);
				$query->where('kompetensi_dasar_id', '!=', $dontDeleteThisRow->kompetensi_dasar_id);
			})->update(['kompetensi_dasar_id' => $dontDeleteThisRow->kompetensi_dasar_id]);
			$output['text'] = 'Berhasil menghapus data ganda';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Data ganda tidak ditemukan';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function ukk(){
		return view('referensi.list_ukk');
    }
	public function list_ukk(){
		$user = auth()->user();
		$query = Paket_ukk::with('jurusan')->with('unit_ukk')->orderBy('jurusan_id', 'asc')->orderBy('kurikulum_id', 'asc')->orderBy('nomor_paket', 'asc');
		return Datatables::of($query)
		->addColumn('nama_jurusan', function ($item) {
			$return  = $item->jurusan->nama_jurusan;
			return $return;
		})
		->addColumn('jumlah_unit', function ($item) {
			$return  = ($item->unit_ukk) ? $item->unit_ukk->count() : 0;
			return '<div class="text-center">'.$return.'</div>';
		})
		->addColumn('status', function ($item) {
			$return  = CustomHelper::status_label($item->status);
			return $return;
		})
		->addColumn('tindakan', function ($item) {
			$ganti_status = ($item->status) ? 'Non Aktifkan' : 'Aktifkan';
			$power_status = ($item->status) ? 'fa-power-off' : 'fa-check';
			$return  = '<div class="text-center"><div class="btn-group">
							<button type="button" class="btn btn-default btn-sm">Aksi</button>
                            <button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu pull-right text-left" role="menu">
								<li><a href="'.url('referensi/tambah-unit-ukk/'.$item->paket_ukk_id).'"><i class="fa fa-plus"></i> Tambah Unit</a></li>
								<li><a class="toggle-modal" href="'.url('referensi/detil-unit-ukk/'.$item->paket_ukk_id).'"><i class="fa fa-search"></i> Detil Unit</a></li>
								<li><a class="confirm" href="'.url('referensi/status-ukk/'.$item->paket_ukk_id).'" data-status="'.$item->status.'"><i class="fa '.$power_status.'"></i>'.$ganti_status.'</a></li>
								<li><a href="'.url('referensi/edit-paket-ukk/'.$item->paket_ukk_id).'" class="toggle-modal"><i class="fa fa-pencil"></i>Ubah</a></li>
                            </ul>
                        </div></div>';
			return $return;
		})
		->rawColumns(['jumlah_unit', 'status', 'tindakan'])
		->make(true);
	}
	public function add_ukk(){
		$user = auth()->user();
		$params = array(
			'all_jurusan' => Jurusan_sp::where('sekolah_id', session('sekolah_id'))->get(),
		);
		return view('referensi.add_ukk')->with($params);
	}
	public function simpan_ukk(Request $request){
		$user = auth()->user();
		$query			= $request['query'];
		$nomor_paket	= $request['nomor_paket'];
		$nama_paket_id	= $request['nama_paket_id'];
		$nama_paket_en	= $request['nama_paket_en'];
		$status			= $request['status'];
		$jurusan_id		= $request['jurusan_id'];
		$kurikulum_id	= $request['kurikulum_id'];
		$kode_kompetensi = $request['kode_kompetensi'];
		//CustomHelper::test($_POST);
		//die();
		$insert=0;
		if($query == 'unit_ukk'){
			$paket_ukk_id 	= $request['paket_ukk_id'];
			$kode_unit		= $request['kode_unit'];
			$nama_unit		= $request['nama_unit'];
			$kode_unit	= array_filter($kode_unit);
			foreach($kode_unit as $key => $unit){
				$find = Unit_ukk::where('paket_ukk_id', $paket_ukk_id)->where('kode_unit', $unit)->first();
				if(!$find){
					$insert_data = array(
						'paket_ukk_id' 	=> $paket_ukk_id,
						'kode_unit'		=> $unit,
						'nama_unit'		=> $nama_unit[$key],
						'last_sync'		=> date('Y-m-d H:i:s'),
					);
					Unit_ukk::create($insert_data);
					$insert++;
				}
			}
			if($insert){
				$flash['success'] = 'Sukses menyimpan referensi unit kompetensi';
				return redirect('referensi/ukk')->with($flash);
			} else {
				$flash['error'] = 'Gagal menyimpan referensi unit kompetensi. kode_unit sudah ada';
				return redirect('referensi/tambah-unit-ukk/'.$paket_ukk_id)->with($flash);
			}
		} else {
			if($nomor_paket){
				$nomor_paket 	= array_filter($nomor_paket);
				foreach($nomor_paket as $key => $paket){
					//$nama = strtolower($nama_paket_id[$key]);
					$find = Paket_ukk::where('jurusan_id', $jurusan_id)->where('kurikulum_id', $kurikulum_id)->where('nomor_paket', $paket)->first();
					if(!$find){
						$insert_data = array(
							'jurusan_id'		=> $jurusan_id,
							'kurikulum_id'		=> $kurikulum_id,
							'kode_kompetensi'	=> $kode_kompetensi,
							'nomor_paket'		=> $paket,
							'nama_paket_id'		=> $nama_paket_id[$key],
							'nama_paket_en'		=> $nama_paket_en[$key],
							'status'			=> $status[$key],
							'last_sync'			=> date('Y-m-d H:i:s'),
						);
						Paket_ukk::create($insert_data);
						$insert++;
					}
				}
				if($insert){
					$flash['success'] = 'Sukses menyimpan referensi paket kompetensi';
				} else {
					$flash['error'] = 'Gagal menyimpan referensi paket kompetensi. Nomor paket sudah ada';
				}
				return redirect('referensi/ukk')->with($flash);
			} else {
				$flash['error'] = 'Gagal menyimpan referensi paket kompetensi. Periksa kembali semua isian';
				return redirect('referensi/tambah-ukk')->with($flash);
			}
		}
	}
	public function update_ukk(Request $request){
		$paket_ukk = Paket_ukk::find($request['paket_ukk_id']);
		$paket_ukk->nama_paket_id = $request['nama_paket_id'];
		$paket_ukk->nama_paket_en = $request['nama_paket_en'];
		$paket_ukk->status = $request['status'];
		if($paket_ukk->save()){
			$output['title'] = 'Sukses';
			$output['text'] = 'Berhasil memperbaharui data referensi UKK';
			$output['icon'] = 'success';
			$output['sukses'] = 1;
		} else {
			$output['title'] = 'Gagal';
			$output['text'] = 'Gagal memperbaharui data referensi UKK';
			$output['icon'] = 'error';
			$output['sukses'] = 0;
		}
		echo json_encode($output);
	}
	public function tambah_unit_ukk($paket_ukk_id){
		$user = auth()->user();
		$params = array(
			'paket_ukk' => Paket_ukk::with('jurusan')->with('unit_ukk')->find($paket_ukk_id),
		);
		return view('referensi.add_unit_ukk')->with($params);
	}
	public function status_ukk($id){
		$paket_ukk = Paket_ukk::find($id);
		if($paket_ukk->status == 1){
			$status = 0;
			$text = 'menonaktifkan';
		} else {
			$status = 1;
			$text = 'mengaktifkan';
		}
		$paket_ukk->status = $status;
		if($paket_ukk->save()){
			$output['text'] = "Berhasil $text referensi UKK";
			$output['icon'] = 'success';
		} else {
			$output['text'] = "Gagal $text referensi UKK";
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function detil_unit_ukk($id){
		$params = array(
			'paket_ukk' => Paket_ukk::with('jurusan')->with('unit_ukk')->find($id),
		);
		return view('referensi.detil_unit_ukk')->with($params);
	}
	public function edit_paket_ukk($id){
		$params = array(
			'paket_ukk' => Paket_ukk::with('jurusan')->with('kurikulum')->with('unit_ukk')->find($id),
		);
		return view('referensi.edit_paket_ukk')->with($params);
	}
}
