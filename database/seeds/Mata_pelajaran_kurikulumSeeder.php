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
		$sql = File::get('database/data/ref.mata_pelajaran_kurikulum.sql');
		DB::unprepared($sql);
    }
}
