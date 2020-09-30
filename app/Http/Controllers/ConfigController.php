<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tahun_ajaran;
use App\Semester;
use App\Setting;
use App\Guru;
use App\Sekolah;
use App\Rombel_empat_tahun;
//new app
use CustomHelper;
use App\Exports\RekapNilaiExport;
use App\Nilai;
use App\Rombongan_belajar;
use Image;
use File;
use Carbon\Carbon;
use Artisan;
class ConfigController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		$this->path = storage_path('app/public/images');
		$this->dimensions = ['245', '300', '500'];
    }

    public function index(){
		$user = auth()->user();
		$jenis_gtk = CustomHelper::jenis_gtk('guru');
		$jenis_gtk = array_merge($jenis_gtk,[97]);
		$data['all_guru']= Guru::with(['gelar_depan', 'gelar_belakang'])->where('sekolah_id', '=', $user->sekolah_id)->whereIn('jenis_ptk_id', $jenis_gtk)->get();
		$data['all_data'] = Tahun_ajaran::with('semester')->where('periode_aktif', '=', 1)->orderBy('tahun_ajaran_id', 'asc')->get();
		$data['sekolah_id'] = $user->sekolah_id;
		$data['all_rombel'] = Rombongan_belajar::where(function($query){
			$query->where('jenis_rombel', 1);
			$query->where('sekolah_id', session('sekolah_id'));
			$query->where('semester_id', session('semester_id'));
			$query->where('tingkat', 12);
		})->get();
		$data['rombel_4_tahun'] = Rombel_empat_tahun::select('rombongan_belajar_id')->where('sekolah_id', $user->sekolah_id)->where('semester_id', session('semester_id'))->get()->keyBy('rombongan_belajar_id')->keys()->toArray();
		//(config('global.rombel_4_tahun')) ? unserialize(config('global.rombel_4_tahun')) : [];
		//$data['sekolah'] = Sekolah::find($user->sekolah_id);
		return view('config', $data);
    }
	public function simpan(Request $request){
		$messages = [
    		'required' => ':attribute tidak boleh kosong',
    		//'mimes' => 		':attribute hanya diperbolehkan berekstensi .jpg, .png dan .jpeg',
  		];
		$validated = $this->validate($request, [
            'logo_sekolah'			=> 'image|mimes:jpg,png,jpeg',
			'tanggal_rapor'			=> 'required',
            'zona'					=> 'required',
			'guru_id'				=> 'nullable',
			'sekolah_id'			=> 'required',
        ], $messages);
		/*$setting = Setting::find(1);
		$update = array(
			'tanggal_rapor' => $request['tanggal_rapor'],
           	'zona' 			=> $request['zona'],
		);*/
		Setting::where('key', '=', 'tanggal_rapor')->update(['value' => $request['tanggal_rapor']]);
		Setting::where('key', '=', 'zona')->update(['value' => $request['zona']]);
		if($request->empat_tahun){
			foreach($request->empat_tahun as $empat_tahun){
				$rombel_4_tahun[] = $empat_tahun;
				Rombel_empat_tahun::updateOrCreate(
					[
						'rombongan_belajar_id' => $empat_tahun,
						'sekolah_id' => $request->sekolah_id,
						'semester_id' => session('semester_id')
					],
					[
						'last_sync' => date('Y-m-d H:i:s')
					]
				);
			}
			Rombel_empat_tahun::whereNotIn('rombongan_belajar_id', $rombel_4_tahun)->where('sekolah_id', $request->sekolah_id)->where('semester_id', session('semester_id'))->delete();
			/*Setting::updateOrCreate(
				['key' => 'rombel_4_tahun'],
				['value' => serialize($request->empat_tahun)]
			);*/
		} else {
			Rombel_empat_tahun::where('sekolah_id', $request->sekolah_id)->where('semester_id', session('semester_id'))->delete();
			//Setting::where('key', 'rombel_4_tahun')->delete();
		}
		$sekolah = Sekolah::find($request['sekolah_id']);
		if($request['guru_id']){
			$sekolah->guru_id = $request['guru_id'];
		}
		Semester::where('periode_aktif', '=', 1)->update(['periode_aktif' => 0]);
		Semester::find($request['semester_id'])->update(['periode_aktif' => 1]);
        //return view('proses',['data' => $request]);
		/*$role = Role::create([
            'name' => $request['name'],
            'display_name' => $request['name'],
            'description' => $request['description'],
        ]);*/
		if (!File::isDirectory($this->path)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            File::makeDirectory($this->path);
        }
		//MENGAMBIL FILE IMAGE DARI FORM
        $file = $request->file('logo_sekolah');
		if($file){
			$image_path = "storage/images/".$sekolah->logo_sekolah;
			if(File::exists($image_path)) {
				File::delete($image_path);
			} else {
				Artisan::call('storage:link');
			}
			//MEMBUAT NAME FILE DARI GABUNGAN TIMESTAMP DAN UNIQID()
			$fileName = Carbon::now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
			//UPLOAD ORIGINAN FILE (BELUM DIUBAH DIMENSINYA)
			Image::make($file)->save($this->path . '/' . $fileName);
			
			//LOOPING ARRAY DIMENSI YANG DI-INGINKAN
			//YANG TELAH DIDEFINISIKAN PADA CONSTRUCTOR
			foreach ($this->dimensions as $row) {
				$image_dimensions = "storage/images/".$row.'/'.$sekolah->logo_sekolah;
				if(File::exists($image_dimensions)) {
					File::delete($image_dimensions);
				}
				//MEMBUAT CANVAS IMAGE SEBESAR DIMENSI YANG ADA DI DALAM ARRAY 
				$canvas = Image::canvas($row, $row);
				//RESIZE IMAGE SESUAI DIMENSI YANG ADA DIDALAM ARRAY 
				//DENGAN MEMPERTAHANKAN RATIO
				$resizeImage  = Image::make($file)->resize($row, $row, function($constraint) {
					$constraint->aspectRatio();
				});
				
				//CEK JIKA FOLDERNYA BELUM ADA
				if (!File::isDirectory($this->path . '/' . $row)) {
					//MAKA BUAT FOLDER DENGAN NAMA DIMENSI
					File::makeDirectory($this->path . '/' . $row);
				}
				
				//MEMASUKAN IMAGE YANG TELAH DIRESIZE KE DALAM CANVAS
				$canvas->insert($resizeImage, 'center');
				//SIMPAN IMAGE KE DALAM MASING-MASING FOLDER (DIMENSI)
				$canvas->save($this->path . '/' . $row . '/' . $fileName);
			}
			$sekolah->logo_sekolah = $fileName;
		}
		$sekolah->save();
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
