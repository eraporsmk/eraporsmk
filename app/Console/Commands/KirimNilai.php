<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Pembelajaran;
use CustomHelper;
use Illuminate\Support\Str;
use App\Matev_rapor;
use App\Nilai_rapor_dapodik;
use App\Nilai_akhir;
use Illuminate\Support\Facades\DB;
class KirimNilai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kirim:nilai {tingkat} {sekolah_id} {semester_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->arguments();
		self::proses_kirim($arguments['tingkat'], $arguments['sekolah_id'], $arguments['semester_id']);
    }
	private function proses_kirim($tingkat, $sekolah_id, $semester_id){
		$result['status'] = 1;
		$result['rombel'] = '0';
		$result['jumlah_data'] = '0';
		$result['inserted'] = 'inserted';
		$result['tingkat'] = $tingkat;
		$result['sekolah_id'] = $sekolah_id;
		$result['message'] = '';
		Storage::disk('public')->put('proses_kirim.json', json_encode($result));
		$callback = function($query) use ($tingkat, $sekolah_id, $semester_id){
			$query->where('sekolah_id', $sekolah_id);
			$query->where('tingkat', $tingkat);
			$query->where('jenis_rombel', 1);
			$query->where('kunci_nilai', 1);
			$query->where('semester_id', $semester_id);
			$query->with(['anggota_rombel' => function($query){
				$query->with(['nilai_akhir_pengetahuan', 'nilai_akhir_keterampilan']);
			}]);
		};
		$all_pembelajaran = Pembelajaran::whereHas('rombongan_belajar', $callback)->with(['rombongan_belajar' => $callback])->whereNotNull('kelompok_id')->whereNotNull('no_urut')->get();
		$jumlah_data = 0;
		$i=1;
		foreach($all_pembelajaran as $pembelajaran){
			$jumlah_data += Nilai_akhir::where('pembelajaran_id', $pembelajaran->pembelajaran_id)->count();
			$result['jumlah_data'] = $jumlah_data;
			$result['status'] = 0;
			$result['rombel'] = $pembelajaran->rombongan_belajar->nama;
			$rasio_p = ($pembelajaran->rasio_p) ? $pembelajaran->rasio_p : 50;
			$rasio_k = ($pembelajaran->rasio_k) ? $pembelajaran->rasio_k : 50;
			$produktif = array(4,5,9,10,13);
			if(in_array($pembelajaran->kelompok_id,$produktif)){
				$produktif = 1;
			} else {
				$produktif = 0;
			}
			$kkm = CustomHelper::get_kkm($pembelajaran->kelompok_id, $pembelajaran->kkm);
			$insert_matev_rapor = [
				'nm_mata_evaluasi'		=> Str::limit($pembelajaran->nama_mata_pelajaran, 50),
				'a_dari_template'		=> 1,
				'no_urut'				=> $pembelajaran->no_urut,
				'kkm_kognitif'			=> $kkm,
				'kkm_psikomotorik'		=> $kkm,
				'rombongan_belajar_id'	=> $pembelajaran->rombongan_belajar->rombel_id_dapodik,
				'mata_pelajaran_id'		=> $pembelajaran->mata_pelajaran_id,
				'soft_delete'			=> 0,
				'last_sync'				=> date('Y-m-d H:i:s'),
			];
			$matev_rapor = Matev_rapor::updateOrCreate(
				['pembelajaran_id' => $pembelajaran->pembelajaran_id_dapodik],
				$insert_matev_rapor
			);
			foreach($pembelajaran->rombongan_belajar->anggota_rombel as $anggota_rombel){
				$find_anggota = DB::connection('dapodik')->table('anggota_rombel')->where('anggota_rombel_id', $anggota_rombel->anggota_rombel_id_dapodik)->first();
				if($find_anggota){
					$result['inserted'] = $i++;
					Storage::disk('public')->put('proses_kirim.json', json_encode($result));
					$nilai_pengetahuan_value 	= ($anggota_rombel->nilai_akhir_pengetahuan) ? $anggota_rombel->nilai_akhir_pengetahuan->nilai : 0;
					$nilai_keterampilan_value 	= ($anggota_rombel->nilai_akhir_keterampilan) ? $anggota_rombel->nilai_akhir_keterampilan->nilai : 0;
					$nilai_akhir_pengetahuan	= $nilai_pengetahuan_value * $rasio_p;
					$nilai_akhir_keterampilan	= $nilai_keterampilan_value * $rasio_k;
					$nilai_akhir				= ($nilai_akhir_pengetahuan + $nilai_akhir_keterampilan) / 100;
					$nilai_akhir				= ($nilai_akhir) ? number_format($nilai_akhir,0) : 0;
					$predikat = CustomHelper::konversi_huruf($kkm, $nilai_akhir, $produktif);
					$insert_nilai = [
						'nilai_kognitif_angka' 	=> $nilai_pengetahuan_value,
						'nilai_kognitif_huruf' 	=> CustomHelper::konversi_huruf($kkm, $nilai_pengetahuan_value, $produktif),
						'nilai_psim_angka'		=> $nilai_keterampilan_value,
						'nilai_psim_huruf'		=> CustomHelper::konversi_huruf($kkm, $nilai_keterampilan_value, $produktif),
						'rapor_ke' 				=> $pembelajaran->no_urut,
						'soft_delete' 			=> 0,
						'updater_id' 			=> $matev_rapor->updater_id,
						'last_sync'				=> date('Y-m-d H:i:s'),
					];
					$nilai_rapor = Nilai_rapor_dapodik::updateOrCreate(
						['id_evaluasi' => $matev_rapor->id_evaluasi, 'anggota_rombel_id' => $anggota_rombel->anggota_rombel_id_dapodik],
						$insert_nilai
					);
				}
			}
		}
		/*$result['status'] = 0;
		$result['rombel'] = 'rombel';
		$result['jumlah_data'] = 'jumlah_data';
		$result['inserted'] = 'inserted';
		$result['tingkat'] = $tingkat;
		$result['sekolah_id'] = $sekolah_id;
		*/
		$result['status'] = 0;
		$result['jumlah_data'] = $jumlah_data;
		$result['message'] = 'Kirim nilai ke Dapodik selesai!';
		Storage::disk('public')->put('proses_kirim.json', json_encode($result));
		echo json_encode($result);
	}
}
