<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Guru;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use CustomHelper;
use Illuminate\Support\Facades\Schema;
use Artisan;
use Illuminate\Support\Facades\DB;
use App\Gelar;
use App\Gelar_ptk;
use App\User;
use Illuminate\Support\Facades\Storage;
use Response;
use File;
use App\Imports\AsesorImport;
use App\Imports\InstrukturImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Jenis_ptk;
use Session;
use App\Dudi;
use App\Asesor;
use Illuminate\Support\Facades\Validator;
use Alert;
use App\Agama;
class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
		$check = Jenis_ptk::find(97);
		if(!$check){
			$insert_jenis_ptk = array(
				'jenis_ptk_id'		=> 97,
				'jenis_ptk'			=> 'Instruktur',
				'guru_kelas'		=> 0,
				'guru_matpel'		=> 1,
				'guru_bk'			=> 0,
				'guru_inklusi'		=> 0,
				'pengawas_satdik'	=> 0,
				'pengawas_plb'		=> 0,
				'pengawas_matpel'	=> 0,
				'pengawas_bidang'	=> 0,
				'tas'				=> 0,
				'formal'			=> 0,
				'last_sync'			=> date('Y-m-d H:i:s'),
			);
			Jenis_ptk::create($insert_jenis_ptk);
		}
		$check = Jenis_ptk::find(98);
		if(!$check){
			$insert_jenis_ptk = array(
				'jenis_ptk_id'		=> 98,
				'jenis_ptk'			=> 'Asesor',
				'guru_kelas'		=> 0,
				'guru_matpel'		=> 0,
				'guru_bk'			=> 0,
				'guru_inklusi'		=> 1,
				'pengawas_satdik'	=> 0,
				'pengawas_plb'		=> 0,
				'pengawas_matpel'	=> 0,
				'pengawas_bidang'	=> 0,
				'tas'				=> 0,
				'formal'			=> 0,
				'last_sync'			=> date('Y-m-d H:i:s'),
			);
			Jenis_ptk::create($insert_jenis_ptk);
		}
    }

    public function index(){
		$data['query'] = 'guru';
		$data['title'] = 'Guru';
		return view('guru.list_guru', $data);
    }
	public function list_guru($query){
		$jenis_gtk = CustomHelper::jenis_gtk($query);
		$user = auth()->user();
		$query = Guru::with('gelar_depan')->with('gelar_belakang')->where('sekolah_id', session('sekolah_id'))->whereIn('jenis_ptk_id', $jenis_gtk);
		return DataTables::of($query)
			->addColumn('set_nama', function ($item) {
				if($item->gelar_depan->count()){
					$gelar_depan = $item->gelar_depan->implode('kode', '. ').'. ';
				} else {
					$gelar_depan = '';
				}
				if($item->gelar_belakang->count()){
					$gelar_belakang = ', '.$item->gelar_belakang->implode('kode', '. ').'.';
				} else {
					$gelar_belakang = '';
				}
				$return  = $gelar_depan.strtoupper($item->nama).$gelar_belakang."<br />".$item->nuptk;
				return $return;
			})
			->addColumn('set_tempat_lahir', function ($item) {
				$return  = $item->tempat_lahir.', '.CustomHelper::TanggalIndo(date('Y-m-d', strtotime($item->tanggal_lahir)));
				return $return;
			})
			->addColumn('actions', function ($item) {
				$user = auth()->user();
				$links = '<div class="text-center"><a class="toggle-modal btn btn-primary btn-sm" href="'.url('guru/view/'.$item->guru_id).'"><i class="fa fa-eye"></i> Detil</a></div>';
                return $links;

            })
            ->rawColumns(['actions', 'set_nama', 'set_tempat_lahir'])
            ->make(true);  
	}
	public function view($guru_id){
		$user = auth()->user();
		$tendik = CustomHelper::jenis_gtk('tendik');
		$guru = CustomHelper::jenis_gtk('guru');
		$instruktur = CustomHelper::jenis_gtk('instruktur');
		$asesor = CustomHelper::jenis_gtk('asesor');
		$data_guru = Guru::with(['dudi' => function($query) use ($user){
			$query->where('dudi.sekolah_id', session('sekolah_id'));
		}])->with('jenis_ptk')->with('agama')->with('status_kepegawaian')->with('gelar_depan')->with('gelar_belakang')->find($guru_id);
		$data['guru'] = $data_guru;
		if(in_array($data_guru->jenis_ptk_id, $tendik)){
			$title = 'Tenaga Kependidikan';
		} elseif(in_array($data_guru->jenis_ptk_id, $guru)){
			$title = 'Guru';
		} elseif(in_array($data_guru->jenis_ptk_id, $instruktur)){
			$title = 'Instruktur';
		} elseif(in_array($data_guru->jenis_ptk_id, $asesor)){
			$title = 'Asesor';
		} else {
			$title = '-';
		}
		$data['ref_gelar_depan'] = Gelar::where('posisi_gelar','=',1)->get();
		$data['ref_gelar_belakang'] = Gelar::where('posisi_gelar','=',2)->get();
		$data['title'] = 'Detil '.$title;
		$data['modal_s'] = 'modal_s';
		$data['data_dudi'] = Dudi::where('sekolah_id', session('sekolah_id'))->get();
		$data['ref_agama'] = Agama::get();
		return view('guru.view', $data);
	}
	public function update_data(Request $request){
		$messages = [
			'email.required' => 'Email tidak boleh kosong',
		];
		$validator = Validator::make(request()->all(), [
			'email' => 'required',
		 ],
		$messages
		);
		if ($validator->fails()) {
			$output['title'] = 'Gagal';
			$output['text'] = 'Email tidak boleh kosong';
			$output['icon'] = 'error';
			$output['sukses'] = 0;
			return response()->json($output);
		}
		$guru_id = $request['guru_id'];
		$guru = Guru::find($guru_id);
		Gelar_ptk::where('guru_id', $guru_id)->delete();
		$gelar_depan = $request['gelar_depan'];
		$jenis_ptk_id = $request['jenis_ptk_id'];
		if($gelar_depan){
			foreach($gelar_depan as $depan){
				$insert_depan = array(
					'sekolah_id'		=> $guru->sekolah_id,
					'gelar_akademik_id'	=> $depan,
					'guru_id'			=> $guru_id,
					'ptk_id'			=> ($guru->guru_id_dapodik) ? $guru->guru_id_dapodik : $guru_id,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				Gelar_ptk::create($insert_depan);
			}
		}
		$gelar_belakang = $request['gelar_belakang'];
		if($gelar_belakang){
			foreach($gelar_belakang as $belakang){
				$insert_belakang = array(
					'sekolah_id'		=> $guru->sekolah_id,
					'gelar_akademik_id'	=> $belakang,
					'guru_id'			=> $guru_id,
					'ptk_id'			=> ($guru->guru_id_dapodik) ? $guru->guru_id_dapodik : $guru_id,
					'last_sync'			=> date('Y-m-d H:i:s'),
				);
				Gelar_ptk::create($insert_belakang);
			}
		}
		//$update_data = array(
			//'email' 			=> $request['email'],
		//);
		$update_data = $request->except(['_token', 'tanggal_lahir', 'dudi_id', 'gelar_depan', 'gelar_belakang']);
		if($request->tanggal_lahir){
			$update_data['tanggal_lahir'] = date('Y-m-d', strtotime($request->tanggal_lahir));
		}
		$update = Guru::where('guru_id', $request['guru_id'])->update($update_data);
		if($update){
			if($jenis_ptk_id == 98){
				$messages = [
					'dudi_id.required' => 'DUDI tidak boleh kosong',
				];
				$validator = Validator::make(request()->all(), [
					'dudi_id' => 'required',
				],
				$messages
				);
				if ($validator->fails()) {
					$output['title'] = 'Gagal';
					$output['text'] = 'DUDI tidak boleh kosong';
					$output['icon'] = 'error';
					$output['sukses'] = 0;
					return response()->json($output);
				}
				Asesor::updateOrCreate(
					['guru_id' => $request['guru_id']],
					[
						'sekolah_id' 			=> $guru->sekolah_id, 
						'dudi_id' 				=> $request['dudi_id'],
						'last_sync'				=> date('Y-m-d H:i:s'),
					]
				);
			}
			User::where('guru_id', $request['guru_id'])->update(['email' => $request['email']]);
			$output['title'] = 'Sukses';
			$output['text'] = 'Berhasil memperbaharui data guru';
			$output['icon'] = 'success';
			$output['sukses'] = 1;
		} else {
			$output['title'] = 'Gagal';
			$output['text'] = 'Gagal memperbaharui data guru';
			$output['icon'] = 'error';
			$output['sukses'] = 0;
		}
		return response()->json($output);
	}
	public function edit_gelar($guru_id){
		$data['guru'] = Guru::find($guru_id);
		$data['title'] = 'Edit Gelar Guru';
		return view('guru.edit', $data);
	}
	public function tendik(){
		$data['query'] = 'tendik';
		$data['title'] = 'Tenaga Kependidikan';
		return view('guru.list_guru', $data);
	}
	public function instruktur(){
		$data['query'] = 'instruktur';
		$data['title'] = 'Instruktur';
		return view('guru.list_guru', $data);
	}
	public function asesor(){
		$data['query'] = 'asesor';
		$data['title'] = 'Asesor';
		return view('guru.list_guru', $data);
	}
	public function tambah_data($query){
		$data['query'] = $query;
		return view('guru.tambah', $data);
	}
	public function template($query){
		return Response::download(storage_path('excel/format_excel_'.$query.'.xlsx'));
	}
	public function simpan_data($query, Request $request){
		$this->validate($request, [
			'file' => 'required|mimes:csv,xls,xlsx'
		]);
		$file = $request->file('file');
		//$nama_file = rand().$file->getClientOriginalName();
		//$file->move('import',$nama_file);
		if($query == 'asesor'){
			Excel::import(new AsesorImport, $file);
		} else {
			Excel::import(new InstrukturImport, $file);
		}
		//$filename = public_path().'/import/'.$nama_file;
		//File::delete($filename);
		$output['title'] = 'Sukses';
		$output['text'] = 'Berhasil memproses data '.$query;
		$output['icon'] = 'success';
		echo json_encode($output);
	}
	public function hapus($id, $guru_id){
		if($id == 97){
			$query = 'instruktur';
		} else {
			$query = 'asesor';
		}
		User::where('guru_id', $guru_id)->delete();
		if(Guru::destroy($guru_id)){
			Session::flash('success',"Data $query berhasil dihapus");
		} else {
			Session::flash('error',"Data $query gagal dihapus");
		}
		return redirect('/'.$query);
	}
}
