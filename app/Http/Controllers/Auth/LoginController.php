<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Tahun_ajaran;
use Illuminate\Support\Facades\Validator;
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
     * Login username to be used by the controller.
     *
     * @var string
     */
    protected $username;
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->username = $this->findUsername();
    }
	public function showLoginForm()
    {
		//$sekolah = Sekolah::first();
		//if(!$sekolah){
			//return redirect('/register');
		//}
		//if (!Schema::hasColumn('users', 'periode_aktif')) {
			//Artisan::call('migrate');
		//}
		$data['all_data'] = Tahun_ajaran::with(['semester' => function($query){
			$query->orderBy('semester_id');
		}])->where('periode_aktif', '=', 1)->orderBy('tahun_ajaran_id', 'desc')->get();
        return view('auth.login', $data);
    }
    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('email');
		$messages = [
			'email.required' => 'Email tidak boleh kosong',
		];
		$validator = Validator::make(request()->all(), [
			'email' => 'required|exists:users,nuptk',
		 ],
		$messages
		);
		if ($validator->fails()) {
			$fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'nisn';
			
		} else {
		//echo $login;
        	$fieldType = 'nuptk';
		}
		//dd($fieldType);
        request()->merge([$fieldType => $login]);
        return $fieldType;
    }
    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }
}