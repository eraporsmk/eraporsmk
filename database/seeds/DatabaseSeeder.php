<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingSeeder::class);
		$this->call(AgamaSeeder::class);
		$this->call(GelarSeeder::class);
		$this->call(JurusanSeeder::class);
		$this->call(KelompokSeeder::class);
		$this->call(KurikulumSeeder::class);
		$this->call(MataPelajaranSeeder::class);
		$this->call(Mata_pelajaran_kurikulumSeeder::class);
		$this->call(Mst_wilayahSeeder::class);
		$this->call(PekerjaanSeeder::class);
		$this->call(SemesterSeeder::class);
		$this->call(SikapSeeder::class);
		$this->call(RoleSeeder::class);
		$this->call(JenisPtkSeeder::class);
		$this->call(StatusKepegawaianSeeder::class);
		$this->call(TeknikSeeder::class);
		$this->call(KDSeeder::class);
    }
}
