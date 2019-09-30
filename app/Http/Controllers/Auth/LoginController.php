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
		$email = Validator::make($request->all(), [
			'email' => 'required|email|exists:users,email',
		]);
		
		$nuptk = Validator::make($request->all(), [
			'email' => 'required|exists:users,nuptk',
		]);
		
		$nisn = Validator::make($request->all(), [
			'email' => 'required|exists:users,nisn',
		]);
		
		$password = Validator::make($request->all(), [
			'password' => 'required|min:5|max:100',
		]);
		$login_type = '';
		if ($email->passes() && $password->passes()){
			$login_type = 'email';
			$request->merge([
				$login_type => strtolower($request->input('email'))
			]);
			if (Auth::attempt($request->only($login_type, 'password'))) {
				//Artisan::call('migrate');
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
		} elseif(array_key_exists('nuptk',$request->all())){
			$login_type = 'NUPTK';
		} else {
			$login_type = 'email';
		}
		return redirect()->back()->withInput()->withErrors([$login_type => 'Password salah untuk '.$login_type.' yang dimasukkan.',]);
	}
}
