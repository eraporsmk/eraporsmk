<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CustomHelper;
use ServerProvider;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Sekolah;
use App\User;
use App\Role;
use App\Role_user;
use App\Mst_wilayah;
use App\Pembelajaran;
use HTMLDomParser;
use UserHelp;
use File;
use App\Kompetensi_dasar;
use App\Kelompok;
use App\Agama;
use Illuminate\Support\Str;
use App\Anggota_rombel;
use App\Rombongan_belajar;
use App\Ekstrakurikuler;
use App\Siswa;
use App\Nilai_akhir;
Use App\Nilai_rapor;
use App\Nilai;
use App\Rencana_penilaian;
class TestController extends Controller
{
	public function __construct()
    {
        $this->path = storage_path('backup');
    }
	public function index(){
		echo 'test<br />';
		//base_path()
		dd(base_path());
		/*
		$a = Rencana_penilaian::get();
		foreach($a as $b){
			$c = $b->pembelajaran()->withTrashed()->first();
			if($c){
				//$c->restore();
				$d = $c->rombongan_belajar()->withTrashed()->first();
				if($d){
					//dd($d);
					$e = $d->anggota_rombel()->withTrashed()->get();
					if($e->count()){
						foreach($e as $f){
							//$f->restore();
						}
					} else {
						$g = Rombongan_belajar::where('rombel_id_dapodik', $d->rombel_id_dapodik)->where('rombongan_belajar_id', '<>', $d->rombongan_belajar_id)->first();
						dd($g->pembelajaran);
					}
					//$d->restore();
				}
			}
			
		}
		/*$a = Rombongan_belajar::get();
		foreach($a as $b){
			$b->forceDelete();
		}
		$a = Rombongan_belajar::with(['pembelajaran', 'anggota_rombel'])->withTrashed()->get();
		foreach($a as $b){
			$b->restore();
			foreach($b->pembelajaran()->has('rencana_pengetahuan')->withTrashed()->get() as $pembelajaran){
				$pembelajaran->restore();
			}
			foreach($b->anggota_rombel()->withTrashed()->get() as $anggota_rombel){
				$anggota_rombel->restore();
			}
			$siswa = $anggota_rombel->siswa()->withTrashed()->first();
			if($siswa){
				$siswa->restore();
			}
		}*/
		$a = Rombongan_belajar::doesntHave('pembelajaran')->doesntHave('anggota_rombel')->get();
		foreach($a as $b){
			$b->forceDelete();
		}
		/*
		$a = Nilai_akhir::whereHas('pembelajaran', function($query){
			$query->where('pembelajaran_id', 'cdef0016-bc74-4665-98ed-6b57fbd8d16f');
		})->forceDelete();
		$b = Nilai_rapor::whereHas('pembelajaran', function($query){
			$query->where('pembelajaran_id', 'cdef0016-bc74-4665-98ed-6b57fbd8d16f');
		})->forceDelete();
		$b = Nilai::where('nilai', 0)->forceDelete();
		$password = 'C0r0m4nsukses';
		$user = User::whereRoleIs('admin')->first();
		$sekolah = Sekolah::find($user->sekolah_id);
		//dd($user);
		$user->email = strtolower($user->email);
		$user->password = bcrypt($password);
		$user->save(); 
		echo $user->email." Password untuk admin telah direset menjadi : $password";
		*/
		
	}
}
