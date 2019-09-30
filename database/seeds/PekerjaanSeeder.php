<?php

use Illuminate\Database\Seeder;

class PekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref.pekerjaan')->truncate();
		$json = File::get('database/data/ref_pekerjaan.json');
		$data = json_decode($json);
        foreach($data as $obj){
    		DB::table('ref.pekerjaan')->insert([
    			'pekerjaan_id' 	=> $obj->pekerjaan_id,
    			'nama' 			=> $obj->nama,
    			'created_at' 	=> $obj->created_at,
				'updated_at' 	=> $obj->updated_at,
				'deleted_at'	=> $obj->deleted_at,
				'last_sync'		=> $obj->last_sync,
    		]);
    	}
    }
}
