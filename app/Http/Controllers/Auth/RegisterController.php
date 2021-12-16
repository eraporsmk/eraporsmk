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
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
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
		$npsn = $validatedData['name'];
		$content = @file_get_contents('http://103.40.55.242/erapor_server/sync/get_sekolah/'.$npsn);
		$content = json_decode($content, true);
		$data = NULL;
		if($content){
			foreach($content['data'] as $a){
				$b = (object) $a;
				if($b->username == $validatedData['email']){
					$data = $b;
				}
			}
		}
		$host_server_direktorat = 'http://103.40.55.242/erapor_server/api/';
		$client = new Client([
			'base_uri' => $host_server_direktorat,
			'verify' => false,
		]);
		if($data){
			$data_sync = [
				'username_dapo'		=> $data->username,
				'password_dapo'		=> $data->password_lama,
				'npsn'				=> $data->npsn,
				'tahun_ajaran_id'	=> $semester->tahun_ajaran_id,
				'semester_id'		=> $semester->semester_id,
				'sekolah_id'		=> $data->sekolah_id,
			];
			$response = $client->request('POST', 'register', [
				'form_params'   => $data_sync,
				'auth' => ['admin', '1234'],
				'headers' => [
					'x-api-key' => $data->sekolah_id,
				]
			]);
			$code = $response->getStatusCode();
			if($code == 200){
				$body = json_decode($response->getBody());
				$set_data = $body->data;
				$get_kode_wilayah = $set_data->wilayah;//Mst_wilayah::with(['parrentRecursive'])->find($set_data->kode_wilayah);
				$kode_wilayah = $set_data->kode_wilayah;
				$kecamatan = '-';
				$kabupaten = '-';
				$provinsi = '-';
				if($get_kode_wilayah){
					$kode_wilayah = $get_kode_wilayah->kode_wilayah;
					if($get_kode_wilayah->parrent_recursive){
						$kecamatan = $get_kode_wilayah->parrent_recursive->nama;
						if($get_kode_wilayah->parrent_recursive->parrent_recursive){
							$kabupaten = $get_kode_wilayah->parrent_recursive->parrent_recursive->nama;
							if($get_kode_wilayah->parrent_recursive->parrent_recursive->parrent_recursive){
								$provinsi = $get_kode_wilayah->parrent_recursive->parrent_recursive->parrent_recursive->nama;
								Mst_wilayah::updateOrCreate(
									[
										'kode_wilayah' => $get_kode_wilayah->parrent_recursive->parrent_recursive->parrent_recursive->kode_wilayah,
									],
									[
										'nama' => $get_kode_wilayah->parrent_recursive->parrent_recursive->parrent_recursive->nama,
										'id_level_wilayah' => $get_kode_wilayah->parrent_recursive->parrent_recursive->parrent_recursive->id_level_wilayah,
										'mst_kode_wilayah' => $get_kode_wilayah->parrent_recursive->parrent_recursive->parrent_recursive->mst_kode_wilayah,
										'negara_id' => $get_kode_wilayah->parrent_recursive->parrent_recursive->parrent_recursive->negara_id,
										'last_sync' => now(),
									]
								);
							}
							Mst_wilayah::updateOrCreate(
								[
									'kode_wilayah' => $get_kode_wilayah->parrent_recursive->parrent_recursive->kode_wilayah,
								],
								[
									'nama' => $get_kode_wilayah->parrent_recursive->parrent_recursive->nama,
									'id_level_wilayah' => $get_kode_wilayah->parrent_recursive->parrent_recursive->id_level_wilayah,
									'mst_kode_wilayah' => $get_kode_wilayah->parrent_recursive->parrent_recursive->mst_kode_wilayah,
									'negara_id' => $get_kode_wilayah->parrent_recursive->parrent_recursive->negara_id,
									'last_sync' => now(),
								]
							);
						}
						Mst_wilayah::updateOrCreate(
							[
								'kode_wilayah' => $get_kode_wilayah->parrent_recursive->kode_wilayah,
							],
							[
								'nama' => $get_kode_wilayah->parrent_recursive->nama,
								'id_level_wilayah' => $get_kode_wilayah->parrent_recursive->id_level_wilayah,
								'mst_kode_wilayah' => $get_kode_wilayah->parrent_recursive->mst_kode_wilayah,
								'negara_id' => $get_kode_wilayah->parrent_recursive->negara_id,
								'last_sync' => now(),
							]
						);
					}
					Mst_wilayah::updateOrCreate(
						[
							'kode_wilayah' => $get_kode_wilayah->kode_wilayah,
						],
						[
							'nama' => $get_kode_wilayah->nama,
							'id_level_wilayah' => $get_kode_wilayah->id_level_wilayah,
							'mst_kode_wilayah' => $get_kode_wilayah->mst_kode_wilayah,
							'negara_id' => $get_kode_wilayah->negara_id,
							'last_sync' => now(),
						]
					);
				}
				$insert_sekolah = array(
					'npsn' 					=> $set_data->npsn,
					'nss' 					=> $set_data->nss,
					'nama' 					=> $set_data->nama,
					'alamat' 				=> $set_data->alamat_jalan,
					'desa_kelurahan'		=> $set_data->desa_kelurahan,
					'kode_wilayah'			=> $kode_wilayah,
					'kecamatan' 			=> $kecamatan,
					'kabupaten' 			=> $kabupaten,
					'provinsi' 				=> $provinsi,
					'kode_pos' 				=> $set_data->kode_pos,
					'lintang' 				=> 0,//$set_data->lintang,
					'bujur' 				=> 0,//$set_data->bujur,
					'no_telp' 				=> $set_data->nomor_telepon,
					'no_fax' 				=> $set_data->nomor_fax,
					'email' 				=> $set_data->email,
					'website' 				=> $set_data->website,
					'status_sekolah'		=> 0,
					'last_sync'				=> date('Y-m-d H:i:s'),
				);
				$sekolah = Sekolah::updateOrCreate(
					['sekolah_id' => $set_data->sekolah_id],
					$insert_sekolah
				);
				$user = User::updateOrCreate(
					['name' => 'Administrator', 'email' => $request['email']],
					['password' => Hash::make($request['password']), 'last_sync' => date('Y-m-d H:i:s'), 'sekolah_id' => $sekolah->sekolah_id, 'password_dapo'	=> md5(1)]
				);
				$adminRole = Role::where('name', 'admin')->first();
				$user = User::where('email', $request['email'])->first();
				$CheckadminRole = DB::table('role_user')->where('user_id', $user->user_id)->first();
				if(!$CheckadminRole){
					$user->attachRole($adminRole);
				}
			} else {
				return redirect()->back()->withInput($request->input())->with('error', 'Server tidak merespon');
			}
		} else {
			return redirect()->back()->withInput($request->input())->with('error', 'GAGAL melakukan Autentikasi, email dan password tidak cocok atau pengguna tidak diijinkan');
		}
		return redirect('login')->with('success', 'Registrasi berhasil.');
	}

}