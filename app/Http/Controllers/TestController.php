<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Validator;
use App\Guru;

class TestController extends Controller
{
	public function __construct()
    {
        $this->path = storage_path('backup');
    }
	public function formulir(Request $request){
		if ($request->isMethod('post')) {
			$messages = [
				'nama.required' => 'Nama Lengkap tidak boleh kosong',
				'email.email' => 'Email tidak valid',
				'email.required' => 'Email tidak boleh kosong',
				'nik.required' => 'NIK tidak boleh kosong',
				'nik.digits' => 'NIK harus terdiri dari 16 digit',
				'tempat_lahir.required' => 'Tempat Lahir tidak boleh kosong',
				'tanggal_lahir.required' => 'Tanggal Lahir tidak boleh kosong',
				'jenis_kelamin.required' => 'Jenis Kelamin tidak boleh kosong',
				'agama_id.required' => 'Agama tidak boleh kosong',
				'alamat.required' => 'Alamat Rumah tidak boleh kosong',
				'rt.required' => 'RT tidak boleh kosong',
				'rw.required' => 'RW tidak boleh kosong',
				'desa_kelurahan.required' => 'Desa/Kelurahan tidak boleh kosong',
				'kode_pos.required' => 'Kode Pos tidak boleh kosong',
				'no_hp.required' => 'Nomor HP tidak boleh kosong',
			];
			$validator = Validator::make(request()->all(), [
				'nama' => 'required',
				'email' => 'required|email',
				'nik' => 'required|digits:16',
				'tempat_lahir' => 'required',
				'tanggal_lahir' => 'required',
				'jenis_kelamin' => 'required',
				'agama_id' => 'required',
				'alamat' => 'required',
				'rt' => 'required',
				'rw' => 'required',
				'desa_kelurahan' => 'required',
				'kode_pos' => 'required',
				'no_hp' => 'required',
			],
			$messages
			)->validate();
			$data = $request->except(['_token', 'email', 'sekolah_id', 'nuptk']);
			$data['nuptk'] = $request->nuptk ?: mt_rand();
			$sukses = Guru::updateOrCreate(
				[
					'sekolah_id' => $request->sekolah_id,
					'email' => $request->email,
				],
				$data
			);
			if($sukses){
				$flash['success'] = 'Biodata berhasil disimpan. Terima Kasih';
			} else {
				$flash['error'] = 'Biodata gagal disimpan. Silahkan coba lagi';
			}
			return redirect()->route('formulir')->with($flash);
		} else {
			return view('formulir');
		}
	}
	public function index(){
		dd(config());
		//echo 'test';
		//$a = 'pg_dump -U postgres -h 127.0.0.1 -p 5432 erapor_git > erapor_smk.sql';
		//$last_line = system('composer update', $retval);
		dump(getcwd());
		chdir('..');
		// current directory
		dump(getcwd());
		//$last_line = system('composer update', $retval);
		//dump($last_line);
		//dd($retval);
	}
}
