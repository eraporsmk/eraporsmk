<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tahun_ajaran;
use App\Semester;
use App\Setting;
use App\Guru;
use App\Sekolah;
use CustomHelper;
use App\Exports\RekapNilaiExport;
use App\Nilai;
use App\Rombongan_belajar;
class ConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(){
		$user = auth()->user();
		$jenis_gtk = CustomHelper::jenis_gtk('guru');
		$data['all_guru']= Guru::where('sekolah_id', '=', $user->sekolah_id)->whereIn('jenis_ptk_id', $jenis_gtk)->get();
		$data['all_data'] = Tahun_ajaran::with('semester')->where('periode_aktif', '=', 1)->orderBy('tahun_ajaran_id', 'asc')->get();
		$data['sekolah_id'] = $user->sekolah_id;
		return view('config', $data);
    }
	public function simpan(Request $request){
		$this->validate($request,[
           'tanggal_rapor' 	=> 'required',
           'zona' 			=> 'required',
		   'semester_id' 	=> 'required',
		   //'guru_id'		=> 'required',
		   'sekolah_id'		=> 'required',
        ]);
		/*$setting = Setting::find(1);
		$update = array(
			'tanggal_rapor' => $request['tanggal_rapor'],
           	'zona' 			=> $request['zona'],
		);*/
		Setting::where('key', '=', 'tanggal_rapor')->update(['value' => $request['tanggal_rapor']]);
		Setting::where('key', '=', 'zona')->update(['value' => $request['zona']]);
		if($request['guru_id']){
			Sekolah::find($request['sekolah_id'])->update(['guru_id' => $request['guru_id']]);
		}
		Semester::where('periode_aktif', '=', 1)->update(['periode_aktif' => 0]);
		Semester::find($request['semester_id'])->update(['periode_aktif' => 1]);
        //return view('proses',['data' => $request]);
		/*$role = Role::create([
            'name' => $request['name'],
            'display_name' => $request['name'],
            'description' => $request['description'],
        ]);*/
		return redirect()->route('konfigurasi')->with('success', "Konfigurasi berhasil disimpan");
	}
	public function year(){
		$year = 2019;
		return $year;
	}
	public function month(){
        //$sheets = [];
        //for ($month = 1; $month <= 12; $month++) {
            //$sheets[] = $this->rekap_nilai($this->year(), $month);
        //}
        //return $sheets;
		$month = 7;
		return $month;
    }
	public function rekap_nilai(){
		$user = auth()->user();
		$data = array(
			'all_rombel' => Rombongan_belajar::withCount('nilai')->where('sekolah_id', $user->sekolah_id)->where('tingkat', 12)->get(),
		);
        return view('rekap_nilai', $data);
	}
	public function download(){
		return (new RekapNilaiExport(2019))->download('invoices.xlsx');
	}
}
