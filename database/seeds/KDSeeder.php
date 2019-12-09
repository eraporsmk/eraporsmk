<?php

use Illuminate\Database\Seeder;
//use App\Kompetensi_dasar;
//use App\Kd;
//use Illuminate\Support\Facades\Storage;
class KDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('ref.kompetensi_dasar')->truncate();
		$this->command->getOutput()->writeln("Memulai proses import ref. Kompetensi Dasar");
		/*$data = Kd::all();
		foreach($data as $obj){
			Kompetensi_dasar::create([
				'id_kompetensi' 			=> $obj->id_kompetensi,
				'kompetensi_id' 			=> $obj->kompetensi_id,
				'mata_pelajaran_id'			=> $obj->mata_pelajaran_id,
				'kelas_10' 					=> $obj->kelas_10,
				'kelas_11'					=> $obj->kelas_11,
				'kelas_12'					=> $obj->kelas_12,
				'kelas_13'					=> $obj->kelas_13,
				'id_kompetensi_nas'			=> $obj->id_kompetensi_nas,
				'kompetensi_dasar'			=> $obj->kompetensi_dasar,
				'kompetensi_dasar_alias'	=> $obj->kompetensi_dasar_alias,
				'user_id'					=> $obj->user_id,
				'aktif'						=> $obj->aktif,
				'kurikulum'					=> $obj->kurikulum,
				'created_at'				=> $obj->created_at,
				'updated_at'				=> $obj->updated_at,
				'deleted_at'				=> $obj->deleted_at,
				'last_sync'					=> $obj->last_sync,
			]);
    	}*/
		$sql = File::get('database/data/ref_kd00.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd01.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd02.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd03.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd04.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd05.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd06.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd07.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd08.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd09.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd10.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref_kd11.sql');
		DB::unprepared($sql);
		$this->command->getOutput()->writeln("Proses import ref. Kompetensi Dasar selesai");
    }
}
