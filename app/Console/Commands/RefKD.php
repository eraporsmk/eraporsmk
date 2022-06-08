<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Kompetensi_dasar;
use App\Kurikulum;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use File;
class RefKD extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ref_kd:start';

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
		  $this->info("Memulai proses sinkronisasi referensi kompetensi dasar");
      $kds = Kompetensi_dasar::whereNull('user_id')->orderBy('kompetensi_id')->orderBy('id_kompetensi')->orderBy('kurikulum')->orderBy('mata_pelajaran_id')->get();
      $folder = storage_path('app/public/kd');
        if (!File::isDirectory($folder)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory($folder, 0777, true, true);
        }
      foreach($kds as $kd){
        if($kd->kompetensi_id == 1){
          $kompetensi = 'Pengetahuan';
        } elseif($kd->kompetensi_id == 2){
          $kompetensi = 'Keterampilan';
        } else {
          $kompetensi = 'Pusat Unggulan';
        }
        $output[] = [
          'Kode' => $kd->id_kompetensi,
          'Kompetensi' => $kompetensi,
          'Mata Pelajaran ID' => $kd->mata_pelajaran_id,
          'Nama Mata Pelajaran' => $kd->mata_pelajaran->nama,
          'Kelas 10' => ($kd->kelas_10) ? '√' : '',
          'Kelas 11' => ($kd->kelas_11) ? '√' : '',
          'Kelas 12' => ($kd->kelas_12) ? '√' : '',
          'Kelas 13' => ($kd->kelas_13) ? '√' : '',
          'Deskripsi' => $kd->kompetensi_dasar,
          'Kurikulum' => $kd->kurikulum,
        ];
      }
      $this->info("menulis file excel");
      (new FastExcel($output))->export($folder.'/ref-kd.xlsx');
      $this->info("proses sinkronisasi referensi kompetensi dasar selesai");
    }
}
