<?php

use Illuminate\Database\Seeder;

class Mst_wilayahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('ref.mst_wilayah')->truncate();
		DB::table('ref.negara')->truncate();
        DB::table('ref.level_wilayah')->truncate();
		Eloquent::unguard();
		$sql = File::get('database/data/ref.level_wilayah.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.negara.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah.sql');
		DB::unprepared($sql);
    }
}
