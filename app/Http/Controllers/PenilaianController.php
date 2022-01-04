<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Semester;
use CustomHelper;
use App\Nilai;
use App\Rencana_penilaian;
use App\Exports\NilaiExport;
use App\Imports\NilaiImport;
use Maatwebsite\Excel\Facades\Excel;
use Validator;
use File;
use App\Nilai_sikap;
use Yajra\Datatables\Datatables;
use App\Remedial;
use App\Sikap;
use App\Ekstrakurikuler;
use App\Nilai_ekstrakurikuler;
use App\Rencana_ukk;
use App\Nilai_ukk;
use Session;
use App\Guru;
use App\Bimbing_pd;
use App\Deskripsi_mata_pelajaran;
use App\Nilai_budaya_kerja;
class PenilaianController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function index($kompetensi_id){
		$user = auth()->user();
		if($kompetensi_id == 'ekskul'){
			$params = array(
				'user' => $user,
				'title'	=> 'Ekstrakurikuler',
				'all_ekskul' => Ekstrakurikuler::where('guru_id', $user->guru_id)->where('semester_id', session('semester_id'))->get(),
			);
			return view('penilaian.ekstrakurikuler')->with($params);
		} elseif($kompetensi_id == 'ukk'){
			$callback = function($query){
				$query->where('status', 1);
			};
			$params = array(
				'user' => $user,
				'title'	=> 'UKK',
				'all_rencana_ukk' => Rencana_ukk::whereHas('paket_ukk', $callback)->with(['paket_ukk' => $callback])->where('internal', $user->guru_id)->where('semester_id', session('semester_id'))->get(),
			);
			return view('penilaian.penilaian_ukk')->with($params);
		} elseif($kompetensi_id == 'prakerin'){
			$params = array(
				'user' => $user,
				'title'	=> 'Prakerin',
				'all_bimbing_pd' => Bimbing_pd::whereHas('akt_pd')->with(['akt_pd.dudi', 'akt_pd.anggota_akt_pd.siswa'])->where('guru_id', $user->guru_id)->get(),
				//whereHas('paket_ukk', $callback)->with(['paket_ukk' => $callback])->where('internal', $user->guru_id)->where('semester_id', session('semester_id'))->get(),
			);
			return view('penilaian.penilaian_prakerin')->with($params);
		} else {
			if($kompetensi_id == 'pengetahuan'){
				$set_kompetensi_id = 1;
			} elseif($kompetensi_id == 'keterampilan'){
				$set_kompetensi_id = 2;
			} else {
				$set_kompetensi_id = 3;
			}
			$params = array(
				'user' => $user,
				'title'	=> ucwords(str_replace('-', ' ', $kompetensi_id)),
				'kompetensi_id'	=> $set_kompetensi_id,
				'query'	=> $kompetensi_id,
			);
			return view('penilaian.form_penilaian')->with($params);
		}
    }
	public function reset_remedial(Request $request){
		$pembelajaran_id = $request->pembelajaran_id;
		$delete = Remedial::where('pembelajaran_id', $pembelajaran_id)->delete();
		if($delete){
			$output = [
				'title'	=> 'Berhasil',
				'icon'	=> 'success',
				'text'	=> 'Remedial berhasil di reset',
			];
		} else {
			$output = [
				'title'	=> 'Gagal',
				'icon'	=> 'error',
				'text'	=> 'Remedial gagal di reset',
			];
		}
		return response()->json($output);
	}
	public function reset_capaian_kompetensi(Request $request){
		$delete = Deskripsi_mata_pelajaran::where('pembelajaran_id', $request->pembelajaran_id)->delete();
		if($delete){
			$output = [
				'title'	=> 'Berhasil',
				'icon'	=> 'success',
				'text'	=> 'Capaian Kompetensi berhasil di reset',
			];
		} else {
			$output = [
				'title'	=> 'Gagal',
				'icon'	=> 'error',
				'text'	=> 'Capaian Kompetensi gagal di reset',
			];
		}
		return response()->json($output);
	}
	public function list_sikap (){
		$params = array(
			'title'	=> 'Data Penilaian Sikap',
		);
		return view('penilaian.list_sikap')->with($params);
    }
	public function get_list_sikap(Request $request){
		$user = auth()->user();
		$guru = Guru::find($user->guru_id);
		$callback = function($query) use ($user){
			$query->whereHas('siswa');
			$query->whereHas('rombongan_belajar');
			$query->with('rombongan_belajar');
			$query->with('siswa');
			$query->where('sekolah_id', '=', $user->sekolah_id);
			$query->where('semester_id', '=', session('semester_id'));
		};
		if($guru->jenis_ptk_id == 5 || $user->hasRole('waka')){
			$query = Nilai_sikap::with('ref_sikap')->whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback]);
		} else {
			$query = Nilai_sikap::with('ref_sikap')->whereHas('anggota_rombel', $callback)->with(['anggota_rombel' => $callback])->where('guru_id', '=', $user->guru_id);
		}
		return Datatables::of($query)
		->addColumn('nama_siswa', function ($item) {
			$return  = $item->anggota_rombel->siswa->nama;
			return $return;
		})
		->addColumn('nama_rombel', function ($item) {
			$return  = $item->anggota_rombel->rombongan_belajar->nama.'/'.$item->anggota_rombel->rombongan_belajar->tingkat;
			return $return;
		})
		->addColumn('get_butir_sikap', function ($item) {
			$return  = $item->ref_sikap->butir_sikap;
			return $return;
		})
		->addColumn('get_opsi_sikap', function ($item) {
			$return  = ($item->opsi_sikap == 1) ? 'Positif' : 'Negatif';
			return $return;
		})
		->rawColumns(['nama_siswa', 'nama_rombel', 'get_butir_sikap', 'get_opsi_sikap'])
		->make(true);
	}
	public function simpan_nilai_ekskul(Request $request){
		$sekolah_id = $request['sekolah_id'];
		$ekstrakurikuler_id = $request['ekstrakurikuler_id'];
		$anggota_rombel_id = $request['anggota_rombel_id'];
		$all_nilai = $request['nilai'];
		$deskripsi_ekskul = $request['deskripsi_ekskul'];
		$insert=0;
		foreach($all_nilai as $key => $value){
			if($value){
				$nilai_ekskul = Nilai_ekstrakurikuler::updateOrCreate(
					[
						'anggota_rombel_id' => $anggota_rombel_id[$key],
						'ekstrakurikuler_id' 	=> $ekstrakurikuler_id,
					],
					[
						'sekolah_id' 			=> $sekolah_id, 
						'nilai'					=> $value,
						'deskripsi_ekskul'		=> $deskripsi_ekskul[$key],
						'last_sync'				=> date('Y-m-d H:i:s'),
					]
				);
				if($nilai_ekskul){
					$insert++;
				}
			} else {
				Nilai_ekstrakurikuler::where('anggota_rombel_id', $anggota_rombel_id[$key])->where('ekstrakurikuler_id', $ekstrakurikuler_id)->delete();
			}
		}
		if($insert){
			$output['text'] = 'Berhasil memproses nilai eksktrakurikuler';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Tidak ada nilai diproses';
			$output['icon'] = 'error';
		}
		$output['redirect'] = 'ekskul';
		echo json_encode($output);
	}
	public function simpan_nilai(Request $request){
		$user = auth()->user();
		$query = $request['query'];
		$siswa_id = $request['siswa_id'];
		$jumlah_kd = $request['jumlah_kd'];
		$bobot = $request['bobot_kd'];
		$total_bobot = $request['all_bobot'];
		$kompetensi_id = $request['kompetensi_id'];
		$kds = $request['kd'];
		$bobot = ($bobot > 0) ? $bobot : 1;
		$tanggal_sikap = $request['tanggal_sikap'];
		$sikap_id = $request['sikap_id'];
		$opsi_sikap = $request['opsi_sikap'];
		$uraian_sikap = $request['uraian_sikap'];
		$guru_id = $request['guru_id'];
		$output['jumlah_form'] = (is_array($siswa_id)) ? count($siswa_id) : 0;
		$redirect = ($kompetensi_id == 1 || $kompetensi_id == 3) ? 'pengetahuan' : 'keterampilan';
		$insert = 0;
		$update = 0;
		if($query == 'sikap'){
			$tanggal_sikap = date('Y-m-d', strtotime($tanggal_sikap));
			$insert_sikap = array(
				'sekolah_id'		=> $user->sekolah_id,
				'guru_id'			=> $guru_id,
				'anggota_rombel_id'	=> $siswa_id,
				'tanggal_sikap' 	=> $tanggal_sikap,
				'sikap_id'			=> $sikap_id,
				'opsi_sikap'		=> $opsi_sikap,
				'uraian_sikap'		=> $uraian_sikap,
				'last_sync'			=> date('Y-m-d H:i:s'),
			);
			$create_nilai_sikap = Nilai_sikap::create($insert_sikap);
			if($create_nilai_sikap){
				$insert=1;
			}
			$redirect = '/list-sikap';
			$text = 'Data tidak disimpan. Periksa kembali semua isian';
		} elseif($query == 'remedial'){
			$redirect = '/remedial';
			$insert=1;
			$nilai_remedial = $request['nilai_remedial'];
			$pembelajaran_id = $request['pembelajaran_id'];
			$kompetensi_id = $request['aspek_penilaian'];
			$rerata_akhir = $request['rerata_akhir'];
			$rerata_remedial = $request['rerata_remedial'];
			foreach($nilai_remedial as $anggota_rombel_id => $nilai){
				$a = $this->check_100($nilai, $redirect);
				if($a){
					$output['title'] = 'Gagal';
					$output['text'] = $a;
					$output['icon'] = 'error';
					$output['redirect'] = '';
					echo json_encode($output);
					exit;
				}
				$nilai_filter = array_filter($nilai);
				if($rerata_remedial[$anggota_rombel_id]){
					$insert_remedial = array(
						'sekolah_id' 		=> $user->sekolah_id,
						'nilai'				=> serialize($nilai_filter),
						'rerata_akhir'		=> $rerata_akhir[$anggota_rombel_id],
						'rerata_remedial'	=> $rerata_remedial[$anggota_rombel_id],
						'last_sync'			=> date('Y-m-d H:i:s'),
					);
					Remedial::updateOrCreate(
						['anggota_rombel_id' => $anggota_rombel_id, 'pembelajaran_id' => $pembelajaran_id, 'kompetensi_id' => $kompetensi_id],
						$insert_remedial
					);
				}
			}
		} elseif($query == 'capaian-kompetensi'){
			$insert = 0;
			foreach(request()->siswa_id as $anggota_rombel_id){
				if(request()->nilai_akhir[$anggota_rombel_id]){
					DB::table('deskripsi_mata_pelajaran')->where('anggota_rombel_id', $anggota_rombel_id)->where('pembelajaran_id', request()->pembelajaran_id)->delete();
					$insert++;
					foreach(request()->deskripsi_pengetahuan[$anggota_rombel_id] as $kompetensi_dasar_id => $deskripsi_pengetahuan){
						Deskripsi_mata_pelajaran::updateOrCreate(
							[
								'sekolah_id' => session('sekolah_id'),
								'anggota_rombel_id' => $anggota_rombel_id,
								'pembelajaran_id' => request()->pembelajaran_id,
								'kompetensi_dasar_id' => $kompetensi_dasar_id,
							],
							[
								'deskripsi_pengetahuan' => $deskripsi_pengetahuan,
								'last_sync' => now(),
							]
						);
					}
				}
			}
			$redirect = '/capaian-kompetensi';
			$text = 'Tidak ada Capaian Kompetensi disimpan. Pastikan nilai akhir telah di Generate!!!';
		} elseif($query == 'projek-profil-pelajar-pancasila-dan-budaya-kerja'){
			$insert = 0;
			foreach($request->nilai as $anggota_rombel_id => $value){
				foreach($value as $aspek_budaya_kerja_id => $elemen){
					foreach($elemen as $elemen_id => $opsi_id){
						if($opsi_id){
							Nilai_budaya_kerja::updateOrCreate(
								[
									'anggota_rombel_id' => $anggota_rombel_id,
									'aspek_budaya_kerja_id' => $aspek_budaya_kerja_id,
									'elemen_id' => $elemen_id,
								],
								[
									'sekolah_id' => session('sekolah_id'),
									'opsi_id' => $opsi_id,
									'last_sync' => now(),
								]
							);
							$insert++;
						}
					}
				}
			}
			$redirect = '/projek-profil-pelajar-pancasila-dan-budaya-kerja';
		} else {
			foreach($siswa_id as $k=>$siswa){
				foreach($kds as $key=>$kd) {
					$a = $this->check_100($kd, $redirect);
					if($a){
						$output['title'] = 'Gagal';
						$output['text'] = $a;
						$output['icon'] = 'error';
						$output['redirect'] = '';
						echo json_encode($output);
						exit;
					}
				}
			}
			foreach($siswa_id as $k=>$siswa){
				$hitung = 0;
				foreach($kds as $kd){
					$hitung += $kd[$k];
				}
				$hasil = $hitung/$jumlah_kd;
				$rerata_nilai = $hasil*$bobot;//($hasil*$bobot)/$total_bobot;
				$rerata_jadi = ($total_bobot) ? number_format($rerata_nilai/$total_bobot,2) : 0;
				$rerata_stor[] = number_format($hitung/$jumlah_kd,0);
				$record['value'] 	= number_format($hitung/$jumlah_kd,0);
				//=F6*(C4/(C4+G4+J4+M4))
				$record['rerata_text'] 	= 'x '.$bobot.' / '.$total_bobot.' =';
				$record['rerata_jadi'] 	= $rerata_jadi;
				$output['rerata'][] = $record;
			}
			foreach($siswa_id as $k=>$siswa){
				foreach($kds as $key=>$kd) {
					$nilai = ($kd[$k]) ? $kd[$k] : 0;
					//if($nilai){
						$get_nilai = Nilai::where('kd_nilai_id', '=', $key)->where('anggota_rombel_id', '=', $siswa)->first();
						if($get_nilai){
							$update++;
							$get_nilai->nilai = $nilai;
							$get_nilai->rerata = $rerata_stor[$k];
							$get_nilai->kompetensi_id = $kompetensi_id;
							$get_nilai->last_sync = date('Y-m-d H:i:s');
							$get_nilai->save();
						} else {
							$insert++;
							$insert_nilai = array(
								'sekolah_id'		=> $user->sekolah_id,
								'kd_nilai_id'		=> $key,
								'anggota_rombel_id'	=> $siswa,
								'kompetensi_id'		=> $kompetensi_id,
								'nilai'				=> $nilai,
								'rerata'			=> $rerata_stor[$k],
								'last_sync'			=> date('Y-m-d H:i:s')
							);
							Nilai::create($insert_nilai);
						}
					//}
				}
			}
			if($kompetensi_id == 1){
				$redirect = '/pengetahuan';
			} elseif($kompetensi_id == 2){
				$redirect = '/keterampilan';
			} else {
				$redirect = '/pusat-keunggulan';
			}
			$text = 'Tidak ada nilai disimpan. Periksa kembali isian nilai KD';
		}
		$output['rumus'] = '';
		if($insert || $update){
			$output['title'] = 'Berhasil';
			$output['text'] = 'Nilai berhasil disimpan';
			$output['icon'] = 'success';
			$output['redirect'] = $redirect;
		} else {
			$output['title'] = 'Gagal';
			$output['text'] = $text;
			$output['icon'] = 'error';
			$output['redirect'] = '';
		}
		echo json_encode($output);
	}
	static function check_100($array, $redirect){
		$return = '';
		foreach ($array as $a) {
			if($a){
				if (is_numeric($a)) {
					if($a < 0){
						$return = 'Tambah data nilai '.$redirect.' gagal. Nilai tidak boleh minus';
					} elseif($a > 100){
						$return = 'Tambah data nilai '.$redirect.' gagal. Nilai harus tidak lebih besar dari 100';
					}
				} else {
					$return = 'Tambah data nilai '.$redirect.' gagal. Nilai harus berupa angka';
				}
			}
		}
		return $return;
	}
	public function exportToExcel($id){
		$rencana_penilaian = Rencana_penilaian::with(['pembelajaran.rombongan_belajar'])->where('rencana_penilaian_id', '=', $id)->first();
		$get_mapel_agama = CustomHelper::filter_agama_siswa($rencana_penilaian->pembelajaran_id, $rencana_penilaian->pembelajaran->rombongan_belajar_id);
		$kompetensi = ($rencana_penilaian->kompetensi_id == 1) ? 'Pengetahuan' : 'Keterampilan';
		$nama_mapel = CustomHelper::clean($rencana_penilaian->pembelajaran->nama_mata_pelajaran);
		$nama_file = 'Format Nilai '.$kompetensi.' eRaporSMK '.$nama_mapel.' '.$rencana_penilaian->pembelajaran->rombongan_belajar->nama;
		$nama_file = CustomHelper::clean($nama_file);
		$nama_file = $nama_file.'.xlsx';
		return (new NilaiExport)->query($id, $get_mapel_agama)->download($nama_file);
	}
	public function import_excel(Request $request){
		$file = $request->file('file');
		//dd($file->getClientOriginalExtension());
		$messages = [
            'file.required' => 'File tidak boleh kosong',
            'file.mimes' => 'File harus berekstensi .XLS/.XLSX',
        ];
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xls,xlsx'
         ],
        $messages
        )->validate();
		$file = $request->file('file');
		$Import = new NilaiImport();
		$rows = Excel::import($Import, $file);
		return response()->json($Import);
	}
	public function delete_remedial($remedial_id){
		$find = Remedial::find($remedial_id);
		$delete = $find->delete();
		if($delete){
			$output['title'] = 'Nilai remedial berhasil dihapus';
			$output['icon'] = 'success';
		} else {
			$output['title'] = 'Nilai remedial gagal dihapus';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function edit_sikap($id){
		$params = array(
			'nilai_sikap'	=> Nilai_sikap::find($id),
			'all_sikap' => Sikap::whereHas('sikap')->with('sikap')->orderBy('sikap_id')->get(),
		);
		return view('penilaian.edit_sikap')->with($params);
	}
	public function delete_sikap($id){
		if(Nilai_sikap::destroy($id)){
			$output['title'] = 'Nilai sikap berhasil dihapus';
			$output['icon'] = 'success';
		} else {
			$output['title'] = 'Nilai sikap gagal dihapus';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function update_sikap(Request $request){
		$nilai_sikap = Nilai_sikap::find($request['nilai_sikap_id_edit']);
		$nilai_sikap->sikap_id = $request['sikap_id_edit'];
		$nilai_sikap->opsi_sikap = $request['opsi_sikap_edit'];
		$nilai_sikap->uraian_sikap = $request['uraian_sikap_edit'];
		if($nilai_sikap->save()){
			$output['title'] = 'Nilai sikap berhasil diperbaharui';
			$output['icon'] = 'success';
		} else {
			$output['title'] = 'Nilai sikap gagal diperbaharui';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function simpan_nilai_ukk(Request $request){
		$sekolah_id			= $request['sekolah_id'];
		$rencana_ukk_id		= $request['rencana_ukk_id'];
		$peserta_didik_id	= $request['peserta_didik_id'];
		$anggota_rombel_id	= $request['anggota_rombel_id'];
		$nilai 				= $request['nilai'];
		$nilai				= array_filter($nilai);
		$insert = 0;
		if($nilai){
			foreach($nilai as $key => $value){
				$nilai_ukk = Nilai_ukk::updateOrCreate(
					[
						'rencana_ukk_id'	=> $rencana_ukk_id,
						'anggota_rombel_id' => $anggota_rombel_id[$key]
					],
					[
						'sekolah_id' 			=> $sekolah_id, 
						'nilai'					=> $value,
						'last_sync'				=> date('Y-m-d H:i:s'),
					]
				);
				if($nilai_ukk){
					$insert++;
				}
			}
		}
		if($insert){
			Session::flash('success',"Data berhasil diproses");
		} else {
			Session::flash('error',"Tidak ada data diproses");
		}
		return redirect('/penilaian/ukk');
	}
}
