<?php

namespace App\Http\Controllers\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Artisan;
use App\Sekolah;
use App\User;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
	public function showLoginForm()
    {
		//$sekolah = Sekolah::first();
		//if(!$sekolah){
			//return redirect('/register');
		//}
        return view('auth.login');
    }
	public function login(Request $request){
		/*$this->validate($request, [
			'email'    => 'required',
			'password' => 'required',
		]);
		$login_type = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL ) 
		? 'email' 
		: 'nuptk';
		
		$request->merge([
			$login_type => $request->input('email')
		]);
		if (Auth::attempt($request->only($login_type, 'password'))) {
			return redirect()->intended($this->redirectPath());
		}*/
		$messages = [
			'required' => ':attribute tidak boleh kosong.',
			'email' => ':attribute harus email valid.',
			'exists' => ':attribute tidak ditemukan atau pengguna tidak aktif.',
		];
		$email = Validator::make($request->all(), [
			'email' => 'required|email|exists:users,email,active,1',
		], $messages);
		
		$nuptk = Validator::make($request->all(), [
			'email' => 'required|exists:users,nuptk,active,1',
		]);
		
		$nisn = Validator::make($request->all(), [
			'email' => 'required|exists:users,nisn,active,1',
		]);
		
		$password = Validator::make($request->all(), [
			'password' => 'required|min:1|max:100',
		]);
		$login_type = '';
		if ($email->passes() && $password->passes()){
			$login_type = 'email';
			$request->merge([
				$login_type => strtolower($request->input('email'))
			]);
			if (Auth::attempt($request->only($login_type, 'password'))) {
            	return redirect()->intended($this->redirectPath());
			}
		} elseif ($nuptk->passes() && $password->passes()){
			$login_type = 'nuptk';
			$request->merge([
				$login_type => $request->input('email')
			]);
			if (Auth::attempt($request->only($login_type, 'password'))) {
				return redirect()->intended($this->redirectPath());
			}
		} elseif ($nisn->passes() && $password->passes()){
			$login_type = 'nisn';
			$request->merge([
				$login_type => $request->input('email')
			]);
			if (Auth::attempt($request->only($login_type, 'password'))) {
				return redirect()->intended($this->redirectPath());
			}
		}
		if (array_key_exists('nisn',$request->all())){
			$login_type = 'NISN';
			if ($nisn->fails()) {
				return redirect()->back()->withErrors($nisn)->withInput();
			}
		} elseif(array_key_exists('nuptk',$request->all())){
			$login_type = 'NUPTK';
			if ($nuptk->fails()) {
				return redirect()->back()->withErrors($nuptk)->withInput();
			}
		} else {
			$login_type = 'email';
			if ($email->fails()) {
				return redirect()->back()->withErrors($email)->withInput();
			}
		}
		return redirect()->back()->withInput()->withErrors([$login_type => 'Password salah untuk '.$login_type.' yang dimasukkan.',]);
	}
	public function activated(Request $request){
		$messages = [
			'required' => ':attribute tidak boleh kosong.',
			'email' => ':attribute harus email valid.',
			'exists' => ':attribute tidak ditemukan.',
		];
		$validator = Validator::make($request->all(), [
			'email' => 'required|email|exists:users,email,active,0',
			'kode_aktivasi' => 'required|exists:users,activation_code',
		], $messages);
		if ($validator->fails()) {
			return redirect()->back()->withErrors($validator)->withInput();
		} else {
			$user = User::where('email', $request->email)->first();
			$user->active = 1;
			$user->save();
			return redirect('login')->with('success', 'Akun berhasil diaktifkan.');
		}
	}
}
