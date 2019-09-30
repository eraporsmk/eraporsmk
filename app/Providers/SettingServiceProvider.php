<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Setting;
use App\Sekolah;
use App\Guru;
use Illuminate\Support\Facades\Auth;
class SettingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
		$user = '';
		view()->composer('*', function($view) {
			$user = auth()->user();
			$app_version = Setting::where('key', '=', 'app_version')->first();
			$db_version = Setting::where('key', '=', 'db_version')->first();
			$last_sync = Setting::where('key', '=', 'last_sync')->first();
			$tanggal_rapor = Setting::where('key', '=', 'tanggal_rapor')->first();
			$zona = Setting::where('key', '=', 'zona')->first();
			$kepsek = NULL;
			$data_guru = NULL;
			$sekolah_id = NULL;
			if($user){
				$kepsek = Sekolah::find($user->sekolah_id);
				$data_guru = Guru::where('guru_id', $kepsek->guru_id)->first();
				$sekolah_id = $user->sekolah_id;
			}
			if($zona->value == 1){ // WIB
				date_default_timezone_set('Asia/Jakarta');
			}
			if($zona->value == 2){ //WITA
				date_default_timezone_set('Asia/Makassar');
			}
			if($zona->value == 3){ //WIT
				date_default_timezone_set('Asia/Jayapura');
			}
			config()->set(['site.app_name' => 'e-Rapor SMK']);
			config()->set(['site.app_version' => $app_version->value]);
			config()->set(['self-update.version_installed' => $app_version->value]);
			config()->set(['site.db_version' => $db_version->value]);
			config()->set(['site.last_sync' => $last_sync->value]);
			config()->set(['site.tanggal_rapor' => $tanggal_rapor->value]);
			config()->set(['site.guru_id' => ($kepsek) ? $kepsek->guru_id : NULL]);
			config()->set(['site.kepsek' => ($data_guru) ? $data_guru->nama : NULL]);
			config()->set(['site.sekolah_id' => $sekolah_id]);
		});       
    }
}
