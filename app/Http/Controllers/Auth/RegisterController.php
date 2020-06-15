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
use ServerProvider;
use Illuminate\Support\Facades\DB;
use HTMLDomParser;
use Illuminate\Http\Request;
use App\Mst_wilayah;
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
            'password' => 'required|string|min:1|confirmed',
        ]);
		$semester = CustomHelper::get_ta();
		$data_sync = array(
			'username_dapo'		=> $validatedData['email'],
			'password_dapo'		=> md5($validatedData['password']),
			'npsn'				=> $validatedData['name'],
			'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
			'semester_id'		=> $semester->semester_id,
			'user'				=> $validatedData['email'],
			'pass'				=> $validatedData['password'],
		);
		$url_register = config('erapor.url_register');
		//echo $url_register;
		$curl = Curl::to($url_register)
		->withHeaders(['Referer: http://103.40.55.249/Do/SSODPD'])
		->allowRedirect()
		->returnResponseObject()
		->withData($data_sync)
		->post();
		$response_token = 'Server tidak merespon';
		if($curl->status == 200){
			$find_key = HTMLDomParser::str_get_html($curl->content)->find('input[type=hidden]');
			if($find_key){
				$name = $find_key[0];
				$access_token = 'http://103.40.55.249/Do/GetLoginInfo/?access_token='.substr($name->value,1).'&key='.config('erapor.api_key');
				$curl_token = Curl::to($access_token)->returnResponseObject()->get();
				if($curl_token->status == 200){
					$response_token = json_decode($curl_token->content);
					$set_data = $response_token->sekolah_profile;
					$get_kode_wilayah = Mst_wilayah::where('nama', $set_data->desa)->first();
					$insert_sekolah = array(
						'npsn' 					=> $set_data->npsn,
						'nss' 					=> 0,
						'nama' 					=> $set_data->nama_sekolah,
						'alamat' 				=> $set_data->alamat,
						'desa_kelurahan'		=> $set_data->desa,
						'kode_wilayah'			=> ($get_kode_wilayah) ? $get_kode_wilayah->kode_wilayah : $set_data->kd_kec,
						'kecamatan' 			=> $set_data->kec,
						'kabupaten' 			=> $set_data->kab,
						'provinsi' 				=> $set_data->prov,
						'kode_pos' 				=> $set_data->kode_pos,
						'lintang' 				=> 0,//$set_data->lintang,
						'bujur' 				=> 0,//$set_data->bujur,
						'no_telp' 				=> $set_data->no_telepon,
						'no_fax' 				=> $set_data->no_telepon,
						'email' 				=> $set_data->email,
	//					'website' 				=> $set_data->website,
						'status_sekolah'		=> 0,
						'last_sync'				=> date('Y-m-d H:i:s'),
					);
					$sekolah = Sekolah::updateOrCreate(
						['sekolah_id' => $set_data->sekolah_id],
						$insert_sekolah
					);
					$data_user = $response_token->user_profile;
					$user = User::updateOrCreate(
						['name' => $data_user->nama, 'email' => $request['email']],
						['password' => Hash::make($request['password']), 'last_sync' => date('Y-m-d H:i:s'), 'sekolah_id' => $sekolah->sekolah_id, 'password_dapo'	=> md5(1)]
					);
					$adminRole = Role::where('name', 'admin')->first();
					$user = User::where('email', $request['email'])->first();
					$CheckadminRole = DB::table('role_user')->where('user_id', $user->user_id)->first();
					if(!$CheckadminRole){
						$user->attachRole($adminRole);
					}
				}
			} else {
				return redirect()->back()->withInput($request->input())->with('error', 'GAGAL melakukan Autentikasi, email dan password tidak cocok atau pengguna tidak diijinkan');
			}
		} else {
			return redirect()->back()->withInput($request->input())->with('error', 'Server tidak merespon');
		}
		return redirect('login')->with('success', 'Registrasi berhasil.');
    }
}