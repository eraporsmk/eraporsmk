<?php

use Illuminate\Database\Seeder;

class KelompokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref.kelompok')->truncate();
		$json = File::get('database/data/ref_kelompok.json');
		$data = json_decode($json);
        foreach($data as $obj){
    		DB::table('ref.kelompok')->insert([
    			'kelompok_id' 	=> $obj->id,
    			'nama_kelompok' => $obj->nama_kelompok,
				'kurikulum'		=> $obj->kurikulum,
    			'created_at' 	=> $obj->created_at,
				'updated_at' 	=> $obj->updated_at,
				'last_sync'		=> date('Y-m-d H:i:s'),
    		]);
    	}
    }
}
