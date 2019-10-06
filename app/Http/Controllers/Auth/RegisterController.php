<?php

namespace App\Http\Controllers\Auth;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Ixudra\Curl\Facades\Curl;
use App\Sekolah;
use App\Role;
use App\Role_user;
use CustomHelper;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
class RegisterController extends Controller
{
    use RegistersUsers;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';
    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
    /**
     * Register new account.
     *
     * @param Request $request
     * @return User
     */
    protected function register(Request $request)
    {
        /** @var User $user */
        $validatedData = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
		$semester = CustomHelper::get_ta();
		$data_sync = array(
			'username_dapo'		=> $validatedData['email'],
			'password_dapo'		=> md5($validatedData['password']),
			'npsn'				=> $validatedData['name'],
			'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
			'semester_id'		=> $semester->semester_id,
		);
		$curl = Curl::to(CustomHelper::url_register())
		->returnResponseObject()
		->withData($data_sync)
		->post();
		$response = json_decode($curl->content);
		try {
			$set_data = $response->data;
			if($curl->status == 200){
				$set_data = $response->data;
				if(isset($set_data->pengguna) && $set_data->pengguna != null){
					$kecamatan = '-';
					$kabupaten = '-';
					$provinsi = '-';
					if($set_data->wilayah->parrent_recursive){
						$kecamatan = $set_data->wilayah->parrent_recursive->nama;
						if($set_data->wilayah->parrent_recursive->parrent_recursive){
							$kabupaten = $set_data->wilayah->parrent_recursive->parrent_recursive->nama;
							if($set_data->wilayah->parrent_recursive->parrent_recursive->parrent_recursive){
								$provinsi = $set_data->wilayah->parrent_recursive->parrent_recursive->parrent_recursive->nama;
							}
						}
					}
					$data_sekolah = array(
						'npsn' 					=> $set_data->npsn,
						'nss' 					=> ($set_data->nss) ? $set_data->nss : 0,
						'nama' 					=> $set_data->nama,
						'alamat' 				=> $set_data->alamat_jalan,
						'desa_kelurahan'		=> $set_data->desa_kelurahan,
						'kode_wilayah'			=> $set_data->kode_wilayah,
						'kecamatan' 			=> $kecamatan,
						'kabupaten' 			=> $kabupaten,
						'provinsi' 				=> $provinsi,
						'kode_pos' 				=> $set_data->kode_pos,
						'lintang' 				=> $set_data->lintang,
						'bujur' 				=> $set_data->bujur,
						'no_telp' 				=> $set_data->nomor_telepon,
						'no_fax' 				=> $set_data->nomor_fax,
						'email' 				=> $set_data->email,
						'website' 				=> $set_data->website,
						'status_sekolah'		=> 0,
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					$sekolah = Sekolah::updateOrCreate(
						['sekolah_id' => $set_data->sekolah_id],
						$data_sekolah
					);
					$user = User::updateOrCreate(
						['name' => 'Administrator','email' => $request['email']],
						['password' => Hash::make($request['password']), 'last_sync' => date('Y-m-d H:i:s'), 'sekolah_id' => $set_data->sekolah_id, 'password_dapo'	=> md5($request['password'])]
					);
					$adminRole = Role::where('name', 'admin')->first();
					$user = User::where('email', $request['email'])->first();
					$CheckadminRole = DB::table('role_user')->where('user_id', $user->user_id)->first();
					if(!$CheckadminRole){
						$user->attachRole($adminRole);
					}
				} else {
					return redirect()->back()->withInput($request->input())->with('error', $response->message);
				}
			} else {
				return redirect()->back()->withInput($request->input())->with('error', $response->error);
			}
        } catch (\Exception $exception) {
            logger()->error($exception);
			if($response){
				$message = $response->message;
				if($message == 'Sekolah ditemukan'){
					$message = 'Username / password salah';
				}
			} else {
				$message = 'Server tidak merespon';
			}
			return redirect()->back()->withInput($request->input())->with('error', $message);
        }
        return redirect('login')->with('success', 'Registrasi berhasil.');
    }
}