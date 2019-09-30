<?php

use Illuminate\Database\Seeder;
use App\Kompetensi_dasar;
use App\Kurikulum;
use Illuminate\Support\Facades\Storage;
class KDSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		DB::table('ref.kompetensi_dasar')->truncate();
		$this->command->getOutput()->writeln("Memulai proses import ref. Kompetensi Dasar");
		Eloquent::unguard();
		$sql = File::get('database/data/ref.kompetensi_dasar.sql');
		DB::unprepared($sql);
		$this->command->getOutput()->writeln("Proses import ref. Kompetensi Dasar selesai");
    }
}
