<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Chumper\Zipper\Facades\Zipper;
use App\Kd_json;
use App\Kompetensi_dasar;
use Illuminate\Support\Facades\Storage;
use Ixudra\Curl\Facades\Curl;
class KdStart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kd:start';

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
		$this->info("Memulai proses sinkronisasi referensi kompetensi dasar");
		/*$this->info("Memulai proses sinkronisasi referensi kompetensi dasar");
		if(!Storage::disk('public')->exists('storage/kd_json/0.json')){
			$Path = public_path('storage/kd_json.zip');
        	Zipper::make($Path)->extractTo('storage');
		}
		$all_data = Storage::files('public/kd_json');
		foreach($all_data as $data){
			$kd_kd_json = Kd_json::where('nama_file', str_replace('public/kd_json/', '', $data))->first();
			if($kd_kd_json){
				Kd_json::create(['nama_file' => str_replace('public/kd_json/', '', $data)]);
			}
			$json = Storage::disk('public')->get('kd_json/'.str_replace('public/kd_json/', '', $data));
			$response = json_decode($json);
			$json_proses_kd = Storage::disk('public')->get('proses_kompetensi_dasar.json');
			$response_proses_kd = json_decode($json_proses_kd);
			$record['table'] = $response_proses_kd->table;
			$record['jumlah'] = $response_proses_kd->jumlah;
			foreach($response->data as $obj){
				$record['inserted'] = Kompetensi_dasar::count();
				Storage::disk('public')->put('proses_kompetensi_dasar.json', json_encode($record));
				$create_kd = Kompetensi_dasar::updateOrCreate(
					['aspek' => $obj->aspek, 'mata_pelajaran_id' => $obj->mata_pelajaran_id, 'kompetensi_dasar' => $obj->kompetensi_dasar, 'kurikulum_id' => $obj->kurikulum_id, 'kelas' => $obj->kelas],
					['id_kompetensi' => $obj->id_kompetensi, 'id_kompetensi_nas' => $obj->id_kompetensi_nas, 'kompetensi_dasar_alias' => $obj->kompetensi_dasar_alias, 'aktif' => $obj->aktif, 'created_at' => $obj->created_at, 'updated_at' => $obj->updated_at, 'deleted_at' => $obj->deleted_at, 'last_sync' => $obj->last_sync]
				);
			}
		}
		$this->info("Proses sinkronisasi referensi kompetensi dasar selesai");*/
    }
}
