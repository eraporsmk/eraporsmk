<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Setting;
use App\Sekolah;
use App\Guru;
use Illuminate\Support\Facades\Auth;
use CustomHelper;
use Request;
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
		/*//$user = Auth::user();
		$user = '';
		$with = '';
		$semester = '';
		view()->composer('*', function($view) use ($with){
			$user = auth()->user();
			$semester = CustomHelper::get_ta();
			if ($view->getName() == 'adminlte::page' || $view->getName() == 'config' || $view->getName() == 'home') {
        		//$view->with('variable', 'Set variable');
				$user = auth()->user();
				if($user){
					$with = [
						'user' => $user,
						'sekolah'	=> Sekolah::with(['guru' => function($query){
							$query->with('gelar_depan');
							$query->with('gelar_belakang');
						}])->find($user->sekolah_id),
						'semester' => $semester,
					];
				}
				$view->with($with);
    		} else {
				if($user){
					$with = [
						'user' => $user,
						'semester' => $semester,
						'sekolah'	=> Sekolah::with(['guru' => function($query){
							$query->with('gelar_depan');
							$query->with('gelar_belakang');
						}])->find($user->sekolah_id),
					];
				}
				$view->with($with);
			}
			Config::set('something', 'asd'); 
			/*
			$sekolah = NULL;
		if($user){
			$sekolah = Sekolah::with(['guru' => function($query){
				$query->with('gelar_depan');
				$query->with('gelar_belakang');
			}])->find($user->sekolah_id);//Sekolah::find($user->sekolah_id);
		}
		, // make it an array
			'site' => 
				[
					'app_name' => 'e-Rapor SMK',
					'sekolah' => $sekolah,
					'guru_id' => ($sekolah) ? $sekolah->guru_id : NULL,
					'kepsek' => ($sekolah) ? ($sekolah->guru) ? $sekolah->guru->nama : NULL : NULL,
					'sekolah_id' => ($sekolah) ? $sekolah->sekolah_id : NULL,
					'semester' => $ta,
					'user' => $user,
				]
		*/
		
			/*$app_version = Setting::where('key', '=', 'app_version')->first();
			$db_version = Setting::where('key', '=', 'db_version')->first();
			$last_sync = Setting::where('key', '=', 'last_sync')->first();
			$tanggal_rapor = Setting::where('key', '=', 'tanggal_rapor')->first();
			$zona = Setting::where('key', '=', 'zona')->first();
			$semester = CustomHelper::get_ta();
			$kepsek = NULL;
			$data_guru = NULL;
			$sekolah_id = NULL;
			if($user){
				$kepsek = Sekolah::with(['guru' => function($query){
					$query->with('gelar_depan');
					$query->with('gelar_belakang');
				}])->find($user->sekolah_id);//Sekolah::find($user->sekolah_id);
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
			config()->set(['site.sekolah' => $kepsek]);
			config()->set(['site.app_version' => $app_version->value]);
			config()->set(['self-update.version_installed' => $app_version->value]);
			config()->set(['site.db_version' => $db_version->value]);
			config()->set(['site.last_sync' => $last_sync->value]);
			config()->set(['site.tanggal_rapor' => $tanggal_rapor->value]);
			config()->set(['site.guru_id' => ($kepsek) ? $kepsek->guru_id : NULL]);
			config()->set(['site.kepsek' => ($kepsek) ? ($kepsek->guru) ? $kepsek->guru->nama : NULL : NULL]);
			config()->set(['site.sekolah_id' => $sekolah_id]);
			config()->set(['site.semester' => $semester]);*/
			//config()->set(['site.user' => $user]);
			//config(['site.semester' => $semester]);
			//config()->set(['site.app_name_baru' => 'e-Rapor SMK']);
		//});
		/*config([
			'global' => Setting::all([
				'key','value'
			])
			->keyBy('key') // key every setting by its name
			->transform(function ($setting) {
				return $setting->value; // return only the value
			})
			->toArray(),
			'site' => 
				[
					'app_name' 	=> 'e-Rapor SMK',
					'semester' 	=> $semester,
					'asd'		=> 'tambahan',
				]
		]);*/
    }
}
