<?php

use Illuminate\Database\Seeder;

class StatusKepegawaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ref.status_kepegawaian')->truncate();
        Eloquent::unguard();
		$sql = File::get('database/data/ref.status_kepegawaian.sql');
		DB::unprepared($sql);
    }
}
