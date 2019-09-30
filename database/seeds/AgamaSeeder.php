<?php

use Illuminate\Database\Seeder;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref.agama')->truncate();
		$json = File::get('database/data/ref_agama.json');
		$data = json_decode($json);
        foreach($data as $obj){
			DB::table('ref.agama')->insert([
				'id' 			=> $obj->id,
				'nama' 			=> $obj->nama,
				'created_at'	=> $obj->created_at,
				'updated_at' 	=> $obj->updated_at,
				'deleted_at'	=> $obj->deleted_at,
				'last_sync'		=> date('Y-m-d H:i:s'),
			]);
    	}
    }
}
