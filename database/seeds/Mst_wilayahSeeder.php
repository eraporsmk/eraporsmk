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
		$sql = File::get('database/data/ref.mst_wilayah00.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah01.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah02.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah03.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah04.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah05.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah06.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah07.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah08.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah09.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah10.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah11.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah12.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah13.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah14.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah15.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah16.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah17.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah18.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mst_wilayah19.sql');
		DB::unprepared($sql);
    }
}
