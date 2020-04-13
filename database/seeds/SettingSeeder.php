<?php

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $all_data = array(
			array('key' => 'app_version', 'value' => '5.0.8'),
			array('key' => 'db_version', 'value' => '4.0.1'),
			array('key' => 'zona', 'value' => 1),
			array('key' => 'tanggal_rapor', 'value' => date('Y-m-d')),
			array('key' => 'last_sync', 'value' => date('Y-m-d')),
		);
		DB::table('settings')->truncate();
		foreach($all_data as $data){
			DB::table('settings')->insert($data);
		}
    }
}
