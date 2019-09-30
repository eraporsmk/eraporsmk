<?php

use Illuminate\Database\Seeder;

class KurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref.kurikulum')->truncate();
		$json = File::get('database/data/kurikulum.json');
		$data = json_decode($json);
        foreach($data as $obj){
    		DB::table('ref.kurikulum')->insert([
    			'kurikulum_id' 			=> $obj->kurikulum_id,
    			'nama_kurikulum' 		=> $obj->nama_kurikulum,
				'mulai_berlaku'			=> $obj->mulai_berlaku,
				'sistem_sks'			=> $obj->sistem_sks,
				'total_sks'				=> $obj->total_sks,
				'jenjang_pendidikan_id'	=> $obj->jenjang_pendidikan_id,
				'jurusan_id'			=> $obj->jurusan_id,
    			'created_at' 			=> $obj->created_at,
				'updated_at' 			=> $obj->updated_at,
				'deleted_at'			=> $obj->deleted_at,
				'last_sync'				=> $obj->last_sync,
    		]);
    	}
    }
}
