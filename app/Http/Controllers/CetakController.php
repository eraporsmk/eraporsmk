<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
/*
output(): Outputs the PDF as a string.
save($filename): Save the PDF to a file
download($filename): Make the PDF downloadable by the user.
stream($filename): Return a response with the PDF to show in the browser.
*/
use App\Anggota_rombel;
use App\Rencana_ukk;
use App\Paket_ukk;
use App\Guru;
use App\Sekolah;
use App\Nilai_ukk;
use CustomHelper;
use App\Rombongan_belajar;
use App\Rencana_penilaian;
use App\Rapor_pts;
use App\Siswa;
use App\Rencana_budaya_kerja;
use App\Opsi_budaya_kerja;
use App\Budaya_kerja;
use App\Rombel_empat_tahun;
class CetakController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
    }
    public function generate_pdf(){
		$data = [
			'foo' => 'bar'
		];
		$pdf = PDF::loadView('cetak.document', $data);
		return $pdf->stream('document.pdf');
	}
	public function sertifikat($anggota_rombel_id, $rencana_ukk_id){
		$user = auth()->user();
        $anggota_rombel = Anggota_rombel::with('siswa')->find($anggota_rombel_id);
		$callback = function($query) use ($anggota_rombel_id){
			$query->where('anggota_rombel_id', $anggota_rombel_id);
		};
		$rencana_ukk = Rencana_ukk::with('guru_internal')->with(['guru_eksternal' => function($query){
			$query->with('dudi');
		}])->with(['nilai_ukk' => $callback])->find($rencana_ukk_id);
		$count_penilaian_ukk = Nilai_ukk::where('peserta_didik_id', $anggota_rombel->peserta_didik_id)->count();
		$data['siswa'] = $anggota_rombel;
		$data['sekolah_id'] = $user->sekolah_id;
		$data['rencana_ukk'] = $rencana_ukk;
		$data['count_penilaian_ukk'] = $count_penilaian_ukk;
		$data['paket'] = Paket_ukk::with('jurusan')->with('unit_ukk')->find($rencana_ukk->paket_ukk_id);
		$data['asesor'] = Guru::with('dudi')->find($rencana_ukk->eksternal);
		$data['sekolah'] = Sekolah::with('guru')->find($user->sekolah_id);
		$pdf = PDF::loadView('cetak.sertifikat1', $data);
		$pdf->getMpdf()->AddPage('P');
		$rapor_cover= view('cetak.sertifikat2', $data);
		$pdf->getMpdf()->WriteHTML($rapor_cover);
		$general_title = strtoupper($anggota_rombel->siswa->nama);
		return $pdf->stream($general_title.'-SERTIFIKAT.pdf');  
	}
	public function rapor_uts($rombongan_belajar_id){
		$callback = function($query){
			$query->with('nilai');
		};
		$rombongan_belajar = Rombongan_belajar::with('wali')->with(['anggota_rombel' => function($query){
			$query->with(['catatan_wali', 'siswa.agama']);
			/*$query->whereHas('anggota_rombel', function($q){
				$q->where('semester_id', session('semester_id'));
			});
			$query->with('agama');
			$query->with(['anggota_rombel' => function($q){
				$q->with('catatan_wali');
			}]);
			$query->with(['sekolah' => function($q){
				$q->with('guru');
			}]);
			$query->orderBy('nama');*/
		}])->with(['pembelajaran' => function($query) use ($callback){
			$query->with('kelompok')->orderBy('kelompok_id', 'asc')->orderBy('no_urut', 'asc');
			$query->whereHas('rapor_pts', $callback)->with(['rapor_pts'=> $callback]);
		}])->with('semester')->with('jurusan')->with('kurikulum')->with(['sekolah' => function($q){
			$q->with('guru');
		}])->find($rombongan_belajar_id);
		/*
		$rombongan_belajar = Rombongan_belajar::with('wali')->with(['siswa' => function($query){
			$query->whereHas('anggota_rombel', function($q){
				$q->where('semester_id', session('semester_id'));
			});
			$query->with('agama');
			$query->with(['anggota_rombel' => function($q){
				$q->with('catatan_wali');
			}]);
			$query->with(['sekolah' => function($q){
				$q->with('guru');
			}]);
			$query->orderBy('nama');
		}])->with(['pembelajaran' => function($query) use ($callback){
			$query->with('kelompok')->orderBy('kelompok_id', 'asc')->orderBy('no_urut', 'asc');
			$query->whereHas('rapor_pts', $callback)->with(['rapor_pts'=> $callback, 'rapor_pts']);
		}])->with('semester')->with('jurusan')->with('kurikulum')->find($rombongan_belajar_id);
		*/
		if (strpos($rombongan_belajar->kurikulum->nama_kurikulum, 'REV') !== false) {
			$kur = 2017;
		} elseif (strpos($rombongan_belajar->kurikulum->nama_kurikulum, '2013') !== false) {
			$kur = 2013;
		} else {
			$kur = 2006;
		}
		$pdf = PDF::loadView('cetak.blank');
		$pdf->getMpdf()->defaultfooterfontsize=7;
		$pdf->getMpdf()->defaultfooterline=0;
		$data['rombongan_belajar'] = $rombongan_belajar;
		$tanggal_rapor = CustomHelper::get_setting('tanggal_rapor');
		$tanggal_rapor = date('Y-m-d', strtotime($tanggal_rapor));
		$data['tanggal_rapor'] = $tanggal_rapor;
		foreach($rombongan_belajar->anggota_rombel as $anggota_rombel){
			$pdf->getMpdf()->SetFooter(strtoupper($anggota_rombel->siswa->nama).' - '.$rombongan_belajar->nama.'|{PAGENO}|Dicetak dari '.config('site.app_name').' v.'.CustomHelper::get_setting('app_version'));
			$data['siswa'] = $anggota_rombel->siswa;
			$data['anggota_rombel'] = $anggota_rombel;
			$data['sekolah'] = $rombongan_belajar->sekolah;
			$data['data_semester'] = $rombongan_belajar->semester;
			$rapor_cover = view('cetak.pts.cover', $data);
			$pdf->getMpdf()->WriteHTML($rapor_cover);
			$get_pembelajaran=[];
			foreach($rombongan_belajar->pembelajaran as $pembelajaran){
				if(in_array($pembelajaran->mata_pelajaran_id, CustomHelper::mapel_agama())){
					if(CustomHelper::filter_pembelajaran_agama($anggota_rombel->siswa->agama->nama, $pembelajaran->nama_mata_pelajaran)){
						$get_pembelajaran[$pembelajaran->pembelajaran_id] = $pembelajaran;
					}
				} else {
					$get_pembelajaran[$pembelajaran->pembelajaran_id] = $pembelajaran;
				}
			}
			//dd($rombongan_belajar->pembelajaran);
			if($get_pembelajaran){
				foreach($get_pembelajaran as $pembelajaran){
					$rasio_p = ($pembelajaran->rasio_p) ? $pembelajaran->rasio_p : 50;
					foreach($pembelajaran->rapor_pts as $rapor_pts){
						$nilai[$pembelajaran->pembelajaran_id][$anggota_rombel->peserta_didik_id][] = $rapor_pts->nilai()->where('anggota_rombel_id', $anggota_rombel->anggota_rombel_id)->avg('nilai');
					}
					if(count($pembelajaran->rapor_pts) > 1){
						//$nilai_siswa = (array_sum($nilai[$pembelajaran->pembelajaran_id][$anggota_rombel->peserta_didik_id]) * $rasio_p) / 100;
						$nilai_siswa = array_sum($nilai[$pembelajaran->pembelajaran_id][$anggota_rombel->peserta_didik_id]) / count($nilai[$pembelajaran->pembelajaran_id][$anggota_rombel->peserta_didik_id]);
					} else {
						$nilai_siswa = array_sum($nilai[$pembelajaran->pembelajaran_id][$anggota_rombel->peserta_didik_id]);
					}
					$all_nilai[$pembelajaran->kelompok->nama_kelompok][$anggota_rombel->peserta_didik_id][] = array(
						'nama_mata_pelajaran'	=> $pembelajaran->nama_mata_pelajaran,
						'kkm'	=> CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm),
						'angka'	=> number_format($nilai_siswa,0),//number_format($pembelajaran->rapor_pts->nilai()->where('anggota_rombel_id', $siswa->anggota_rombel->anggota_rombel_id)->avg('nilai'),0),
						'terbilang' => CustomHelper::terbilang(number_format($nilai_siswa,0)),//CustomHelper::terbilang(number_format($pembelajaran->rapor_pts->nilai()->where('anggota_rombel_id', $siswa->anggota_rombel->anggota_rombel_id)->avg('nilai'),0)),
					);
				}
			} else {
				$all_nilai[$pembelajaran->kelompok->nama_kelompok][$anggota_rombel->peserta_didik_id][] = [];
			}
			$data['all_nilai'] = $all_nilai;
			$pdf->getMpdf()->AddPage('P','','','','',5,5,5,5,5,5,'', 'A4');
			$rapor_nilai = view('cetak.pts.rapor_nilai_'.$kur, $data);
			$pdf->getMpdf()->WriteHTML($rapor_nilai);
			$pdf->getMpdf()->AddPage('P','','1','','',10,10,10,10,5,5,'', 'A4');
		}
		$filename = 'Rapor_PTS_'.str_replace(' ','_', CustomHelper::clean($rombongan_belajar->nama)).'_TA_'.$rombongan_belajar->semester->nama;
		return $pdf->stream($filename.'.pdf');  
	}
	public function rapor_top($query, $id){
		if($query){
			$get_siswa = Anggota_rombel::with(['siswa' => function($query){
				$query->with('agama')->with(['get_kecamatan' => function($query){
					$query->with('get_kabupaten');
				}]);
				$query->with('pekerjaan_ayah');
				$query->with('pekerjaan_ibu');
				$query->with('pekerjaan_wali');
			}])->with(['rombongan_belajar' => function($query){
				$query->with(['pembelajaran' => function($query){
					$query->with('kelompok');
					$query->with('nilai_akhir_pengetahuan');
					$query->with('nilai_akhir_keterampilan');
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
			$params = array(
				'get_siswa'	=> $get_siswa,
			);
			$pdf = PDF::loadView('cetak.blank', $params, [], [
				'format' => 'A4',
				'margin_left' => 15,
				'margin_right' => 15,
				'margin_top' => 15,
				'margin_bottom' => 15,
				'margin_header' => 5,
				'margin_footer' => 5,
			]);
			$pdf->getMpdf()->defaultfooterfontsize=7;
			$pdf->getMpdf()->defaultfooterline=0;
			$general_title = strtoupper($get_siswa->siswa->nama).' - '.$get_siswa->rombongan_belajar->nama;
			$pdf->getMpdf()->SetFooter($general_title.'|{PAGENO}|Dicetak dari '.config('site.app_name').' v.'.CustomHelper::get_setting('app_version'));
			$rapor_top = view('cetak.rapor_top', $params);
			$identitas_sekolah = view('cetak.identitas_sekolah', $params);
			$identitas_peserta_didik = view('cetak.identitas_peserta_didik', $params);
			$pdf->getMpdf()->WriteHTML($rapor_top);
			$pdf->getMpdf()->WriteHTML($identitas_sekolah);
			$pdf->getMpdf()->WriteHTML('<pagebreak />');
			$pdf->getMpdf()->WriteHTML($identitas_peserta_didik);
			return $pdf->stream($general_title.'-IDENTITAS.pdf');
		} else {
			$get_siswa = Anggota_rombel::with('siswa')->with('rombongan_belajar')->where('rombongan_belajar_id', $id)->order()->get();
		}
		$tanggal_rapor = CustomHelper::get_setting('tanggal_rapor');
		$tanggal_rapor = date('Y-m-d', strtotime($tanggal_rapor));
		$params = array(
			'get_siswa'	=> $get_siswa,
			'tanggal_rapor'	=> $tanggal_rapor,
		);
	}
	public function rapor_p5bk($anggota_rombel_id){
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
		$pdf = PDF::loadView('cetak.blank', $params, [], [
			'format' => 'A4',
			'margin_left' => 15,
			'margin_right' => 15,
			'margin_top' => 15,
			'margin_bottom' => 15,
			'margin_header' => 5,
			'margin_footer' => 5,
		]);
		$pdf->getMpdf()->defaultfooterfontsize=7;
		$pdf->getMpdf()->defaultfooterline=0;
		$general_title = strtoupper($get_siswa->siswa->nama).' - '.$get_siswa->rombongan_belajar->nama;
		$pdf->getMpdf()->SetFooter($general_title.'|{PAGENO}|Dicetak dari '.config('site.app_name').' v.'.CustomHelper::get_setting('app_version'));
		$rapor_p5bk = view('cetak.rapor_p5bk', $params);
		$pdf->getMpdf()->WriteHTML($rapor_p5bk);
		return $pdf->stream($general_title.'-IDENTITAS.pdf');
	}
	public function rapor_nilai($query, $id){
		if($query){
			$user = auth()->user();
			$cari_tingkat_akhir = Rombongan_belajar::where('sekolah_id', $user->sekolah_id)->where('semester_id', session('semester_id'))->where('tingkat', 13)->first();
			$get_siswa = Anggota_rombel::with([
				'siswa' => function($query){
					$query->with('agama')->with(['get_kecamatan' => function($query){
						$query->with('get_kabupaten');
					}]);
					$query->with('pekerjaan_ayah');
					$query->with('pekerjaan_ibu');
					$query->with('pekerjaan_wali');
				},
				'rombongan_belajar' => function($query) use ($id){
					$query->where('jenis_rombel', 1);
					$query->with(['pembelajaran' => function($query) use ($id){
						$callback = function($query) use ($id){
							$query->where('anggota_rombel_id', $id);
						};
						$query->with([
							'kelompok',
							'nilai_akhir_pengetahuan' => $callback,
							'nilai_akhir_keterampilan' => $callback,
							'nilai_akhir_pk' => $callback,
							'deskripsi_mata_pelajaran' => $callback,
						]);
						$query->whereNotNull('kelompok_id');
						$query->orderBy('kelompok_id', 'asc');
						$query->orderBy('no_urut', 'asc');
					}]);
					$query->with('semester');
					$query->with('jurusan');
					$query->with('kurikulum');
					$query->with('wali');
				},
				'sekolah' => function($q){
					$q->with('guru');
				},
				'catatan_ppk' => function($query){
					$query->with(['nilai_karakter' => function($query){
						$query->with('sikap');
					}]);
				},
				'kenaikan', 
				'all_nilai_ekskul' => function($query){
					$query->whereHas('ekstrakurikuler', function($query){
						$query->where('semester_id', session('semester_id'));
					});
					$query->with('ekstrakurikuler');
					$query->with('rombongan_belajar');
				},
				'kehadiran',
				'all_prakerin',
			])->with('catatan_wali')->find($id);
			$tanggal_rapor = CustomHelper::get_setting('tanggal_rapor');
			$tanggal_rapor = date('Y-m-d', strtotime($tanggal_rapor));
			$rombel_4_tahun = Rombel_empat_tahun::select('rombongan_belajar_id')->where('sekolah_id', $user->sekolah_id)->where('semester_id', session('semester_id'))->get()->keyBy('rombongan_belajar_id')->keys()->toArray();
			$params = array(
				'get_siswa'	=> $get_siswa,
				'tanggal_rapor'	=> $tanggal_rapor,
				'cari_tingkat_akhir'	=> $cari_tingkat_akhir,
				'rombel_4_tahun' => $rombel_4_tahun,
			);
			//return view('cetak.rapor_nilai', $params);
			//return view('cetak.rapor_catatan', $params);
			$pdf = PDF::loadView('cetak.blank', $params, [], [
				'format' => 'A4',
				'margin_left' => 15,
				'margin_right' => 15,
				'margin_top' => 15,
				'margin_bottom' => 15,
				'margin_header' => 5,
				'margin_footer' => 5,
			]);
			$pdf->getMpdf()->defaultfooterfontsize=7;
			$pdf->getMpdf()->defaultfooterline=0;
			$general_title = strtoupper($get_siswa->siswa->nama).' - '.$get_siswa->rombongan_belajar->nama;
			$pdf->getMpdf()->SetFooter($general_title.'|{PAGENO}|Dicetak dari '.config('site.app_name').' v.'.CustomHelper::get_setting('app_version'));
			$rapor_nilai = view('cetak.rapor_nilai', $params);
			//dd($params);
			$pdf->getMpdf()->WriteHTML($rapor_nilai);
			if (strpos($get_siswa->rombongan_belajar->kurikulum->nama_kurikulum, 'Pusat') == false){
				$pdf->getMpdf()->WriteHTML('<pagebreak />');
				$rapor_catatan = view('cetak.rapor_catatan', $params);
				$pdf->getMpdf()->WriteHTML($rapor_catatan);
				$rapor_karakter = view('cetak.rapor_karakter', $params);
				$pdf->getMpdf()->WriteHTML('<pagebreak />');
				$pdf->getMpdf()->WriteHTML($rapor_karakter);
			}
			return $pdf->stream($general_title.'-NILAI.pdf');
		} else {
			//$id = rombongan_belajar_id
		}
	}
	public function rapor_pendukung($query, $id){
		if($query){
			$get_siswa = Anggota_rombel::with('siswa')->with('sekolah')->with('prestasi')->find($id);
			$params = array(
				'get_siswa'	=> $get_siswa,
			);
			$pdf = PDF::loadView('cetak.blank', $params, [], [
				'format' => 'A4',
				'margin_left' => 15,
				'margin_right' => 15,
				'margin_top' => 15,
				'margin_bottom' => 15,
				'margin_header' => 5,
				'margin_footer' => 5,
			]);
			$pdf->getMpdf()->defaultfooterfontsize=7;
			$pdf->getMpdf()->defaultfooterline=0;
			$general_title = strtoupper($get_siswa->siswa->nama).' - '.$get_siswa->rombongan_belajar->nama;
			$pdf->getMpdf()->SetFooter($general_title.'| |Dicetak dari eRaporSMK v.'.CustomHelper::get_setting('app_version'));
			$rapor_pendukung = view('cetak.rapor_pendukung', $params);
			$pdf->getMpdf()->WriteHTML($rapor_pendukung);
			return $pdf->stream($general_title.'-LAMPIRAN.pdf');
		} else {
			//$id = rombongan_belajar_id
		}
		$pdf = PDF::loadView('cetak.perbaikan');
		return $pdf->stream('document.pdf');
	}
	public function rapor_user(Request $request, $user_id){
		$user = auth()->user();
		$siswa = Siswa::with(['anggota_rombel' => function($query){
			$query->where('semester_id', session('semester_id'));
			$query->with(['rombongan_belajar' => function($query){
				$query->with(['pembelajaran' => function($query){
					$query->whereHas('nilai_akhir_pengetahuan', function($query){
						$query->where('last_sync', '<', config('global.last_sync'));
					});
					$query->whereHas('nilai_akhir_keterampilan', function($query){
						$query->where('last_sync', '<', config('global.last_sync'));
					});
					$query->with('kelompok');
					$query->with('nilai_akhir_pengetahuan');
					$query->with('nilai_akhir_keterampilan');
					$query->whereNotNull('kelompok_id');
					$query->orderBy('kelompok_id', 'asc');
					$query->orderBy('no_urut', 'asc');
				}]);
				$query->with('semester');
				$query->with('jurusan');
				$query->with('kurikulum');
			}, 'sekolah' => function($q){
				$q->with('guru');
			}]);
		}, 'get_kecamatan' => function($query){
			$query->with('get_kabupaten');
		}, 'agama', 'pekerjaan_ayah', 'pekerjaan_ibu', 'pekerjaan_wali'])->find($user->peserta_didik_id);
		if($request->cetak){
			return $this->cetak_all($siswa->anggota_rombel->anggota_rombel_id);
		} else {
			if($siswa->anggota_rombel->rombongan_belajar->pembelajaran->count()){
				$output = [
					'success' => TRUE,
					'title' => 'Validasi Selesai',
					'text' => 'Silahkan klik Cetak',
					'icon' => 'success'
				];
			} else {
				$output = [
					'success' => FALSE,
					'title' => 'Validasi Gagal',
					'text' => 'Data e-Rapor belum disinkronisasi ke server pusat. Silahkan hubungi administrator',
					'icon' => 'error'
				];
			}
			return response()->json($output);
		}
	}
	public function cetak_all($anggota_rombel_id){
		//$this->rapor_top(1, $anggota_rombel_id);
		$this->rapor_nilai(1, $anggota_rombel_id);
		$this->rapor_pendukung(1, $anggota_rombel_id);
	}
}
