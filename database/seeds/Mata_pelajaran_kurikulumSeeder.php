<?php

use Illuminate\Database\Seeder;

class Mata_pelajaran_kurikulumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref.mata_pelajaran_kurikulum')->truncate();
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum00.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum01.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum02.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum03.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum04.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum05.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum06.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum07.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum08.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum09.sql');
		DB::unprepared($sql);
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum10.sql');
		DB::unprepared($sql);
    }
}
