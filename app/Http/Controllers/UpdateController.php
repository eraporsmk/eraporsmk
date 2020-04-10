<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use File;
use Artisan;
use App\Setting;
use App\Kompetensi_dasar;
use App\Kelompok;
use Illuminate\Support\Facades\Storage;
use App\Rombongan_belajar;
use App\Anggota_rombel;
use App\Ekstrakurikuler;
use App\Siswa;
class UpdateController extends Controller
{
	public function __construct()
    {
        $this->middleware('auth');
		$this->path = base_path('updates');
    }
    public function index(){
		if (!File::isDirectory($this->path)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory($this->path);
        }
		$files =   File::allFiles($this->path);
		File::delete($files);
		return view('update');
    }
	public function update_versi(){
		//$user = auth()->user();
		/*
		$a = Rombongan_belajar::where(function($query){
			$query->where('jenis_rombel', 51);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
		})->onlyTrashed()->get();
		$rombongan_belajar_id = [];
		foreach($a as $b){
			$rombongan_belajar_id[] = $b->rombongan_belajar_id;
			$c = Ekstrakurikuler::where(function($query) use ($b){
				$query->where('nama_ekskul', $b->nama);
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
			})->first();
			if($c){
				$anggota_not_deleted = Anggota_rombel::where('rombongan_belajar_id', $c->rombongan_belajar_id)->get();
				foreach($anggota_not_deleted as $not_deleted){
					$not_deleted->delete();
				}
				$c->rombongan_belajar_id = $b->rombongan_belajar_id;
				$c->save();
			}
			$anggota_deleted = Anggota_rombel::where('rombongan_belajar_id', $b->rombongan_belajar_id)->onlyTrashed()->get();
			foreach($anggota_deleted as $deleted){
				$siswa = Siswa::onlyTrashed()->find($deleted->peserta_didik_id);
				if($siswa){
					$siswa->restore();
				}
				$deleted->restore();
			}
			$b->restore();
		}
		if($rombongan_belajar_id){
			Rombongan_belajar::whereHas('anggota_rombel', function($query){
				$query->onlyTrashed();
			})->where(function($query) use ($rombongan_belajar_id){
				$query->whereNotIn('rombongan_belajar_id', $rombongan_belajar_id);
				$query->where('jenis_rombel', 51);
				$query->where('sekolah_id', session('sekolah_id'));
				$query->where('semester_id', session('semester_id'));
			})->delete();
		}
		*/
		Setting::where('key', 'app_version')->update(['value' => '5.0.8']);
		Setting::where('key', 'db_version')->update(['value' => '4.0.1']);
		//Artisan::call('migrate');
		Artisan::call('config:clear');
		Artisan::call('cache:clear');
		Artisan::call('view:clear');
		File::put(base_path().'/version.txt', '5.0.8');
		echo 'sukses';
	}
}
