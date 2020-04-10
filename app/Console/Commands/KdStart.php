<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Kompetensi_dasar;
use App\Kd_nilai;
class KdStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kd:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ekskusi Sinkronisasi Referensi Kompetensi Dasar';

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
		$this->info("Memulai proses cleansing referensi kompetensi dasar");
		$all_kd = Kompetensi_dasar::get();
		foreach($all_kd as $kd){
			$delete = Kompetensi_dasar::where(function($query) use ($kd){
				$query->where('id_kompetensi', $kd->id_kompetensi);
				$query->where('kompetensi_id', $kd->kompetensi_id);
				$query->where('mata_pelajaran_id', $kd->mata_pelajaran_id);
				$query->where('kelas_10', $kd->kelas_10);
				$query->where('kelas_11', $kd->kelas_11);
				$query->where('kelas_12', $kd->kelas_12);
				$query->where('kelas_13', $kd->kelas_13);
				$query->where('kurikulum', $kd->kurikulum);
				$query->where('kompetensi_dasar_id', '!=', $kd->kompetensi_dasar_id);
			})->delete();
			if($delete){
				$kd_nilai = Kd_nilai::whereHas('kompetensi_dasar', function($query) use ($kd){
					$query->where('id_kompetensi', $kd->id_kompetensi);
					$query->where('kompetensi_id', $kd->kompetensi_id);
					$query->where('mata_pelajaran_id', $kd->mata_pelajaran_id);
					$query->where('kelas_10', $kd->kelas_10);
					$query->where('kelas_11', $kd->kelas_11);
					$query->where('kelas_12', $kd->kelas_12);
					$query->where('kelas_13', $kd->kelas_13);
					$query->where('kurikulum', $kd->kurikulum);
					$query->where('kompetensi_dasar_id', '!=', $kd->kompetensi_dasar_id);
				})->update(['kompetensi_dasar_id' => $kd->kompetensi_dasar_id]);
			}
		}
		$this->info("Proses cleansing referensi kompetensi dasar selesai");
    }
}
