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
use Artisan;
class TestController extends Controller
{
	public function __construct()
    {
        $this->path = storage_path('backup');
    }
	public function index(){
		echo 'test';
	}
}
