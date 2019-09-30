<?php

use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref.jurusan')->truncate();
		$json = File::get('database/data/jurusan.json');
		$data = json_decode($json);
        foreach($data as $obj){
			DB::table('ref.jurusan')->updateOrInsert([
				'jurusan_id' 			=> trim($obj->jurusan_id),
				'nama_jurusan' 			=> $obj->nama_jurusan,
				'nama_jurusan_en' 		=> $obj->nama_jurusan_en,
				'untuk_sma'				=> $obj->untuk_sma,
				'untuk_smk'				=> $obj->untuk_smk,
				'untuk_pt'				=> $obj->untuk_pt,
				'untuk_slb'				=> $obj->untuk_slb,
				'untuk_smklb'			=> $obj->untuk_smklb,
				'jenjang_pendidikan_id'	=> $obj->jenjang_pendidikan_id,
				'jurusan_induk'			=> $obj->jurusan_induk,
				'level_bidang_id'		=> $obj->level_bidang_id,
				'created_at' 			=> $obj->created_at,
				'updated_at' 			=> $obj->updated_at,
				'deleted_at'			=> $obj->deleted_at,
				'last_sync'				=> $obj->last_sync,
			]);
    	}
    }
}
