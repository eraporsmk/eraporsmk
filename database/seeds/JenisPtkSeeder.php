<?php

use Illuminate\Database\Seeder;

class JenisPtkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('ref.jenis_ptk')->truncate();
        Eloquent::unguard();
		$sql = File::get('database/data/ref.jenis_ptk.sql');
		DB::unprepared($sql);
    }
}
