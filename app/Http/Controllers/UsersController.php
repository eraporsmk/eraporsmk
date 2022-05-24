<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Permission;
use App\Role_user;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Image;
use File;
use Alert;
use App\Guru;
use App\Siswa;
use App\Sekolah;
use Illuminate\Support\Str;
use CustomHelper;
use App\Rombongan_belajar;
use App\Ekstrakurikuler;
use Artisan;
use Illuminate\Support\Facades\Validator;
class UsersController extends Controller
{
	public $path;
    public $dimensions;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
		$this->path = storage_path('app/public/images');
		$this->dimensions = ['245', '300', '500'];
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(){
		$params['roles'] = Role::whereNotIn('id', [1,2,6])->get();
		return view('users.list')->with($params);
    }
	public function role(){
		$content_header_right = '<a href="'.url('role/create').'" class="btn btn-success pull-right">Tambah Role</a>';
        return view('laratrust/list_role')->with('content_header_right', $content_header_right);
    }
	public function profile(){
		$params = array(
			'title' => 'Perbaharui Profil Pengguna',
			'content_header_right' => '',
			'user' => auth()->user(),
		);
		return view('users.profile')->with($params);
    }
	public function update_profile(Request $request, $id){
		$messages = [
			'image.mimes'	=> 'Foto profile harus berekstensi jpg/png/jpeg',
			'image.image'	=> 'Foto profile harus berekstensi jpg/png/jpeg',
			'current_password.nullable' => 'Please enter current password',
    		'password.nullable' => 'Please enter password',
			'email.required'	=> 'Email tidak boleh kosong',
			'password.required_with_all' => 'Kata sandi baru tidak boleh kosong',
			'password_confirmation.same' => 'Konfirmasi sandi tidak sama dengan sandi baru',
			'password_dapo.nullable' => 'Kata Sandi Lama Dapodik',
			'password_dapo_confirmation.same' => 'Konfirmasi Kata Sandi Lama Dapodik tidak sama dengan Kata Sandi Lama Dapodik',
		];
		$validator = Validator::make(request()->all(), [
			'image'					=> 'image|mimes:jpg,png,jpeg',
			'name'					=> 'required',
            'email'					=> 'required|email|unique:users,email,' . $id .',user_id',
			'current_password'		=> 'nullable',
			'password'				=> 'required_with_all:current_password,email',
			'password_confirmation'	=> 'same:password',
			'password_dapo'			=> 'nullable',
			'password_dapo_confirmation'	=> 'same:password_dapo',
		],
		$messages
		);
		if ($validator->fails()) {
			return redirect()->back()->withInput()->withErrors($validator->errors());
		}
		//JIKA FOLDERNYA BELUM ADA
        //if (!File::isDirectory($this->path)) {
            //MAKA FOLDER TERSEBUT AKAN DIBUAT
            //File::makeDirectory($this->path);
		//}
		if(!Storage::exists('public/images')) {
			Storage::makeDirectory('public/images', 0775, true); //creates directory
		}
		//MENGAMBIL FILE IMAGE DARI FORM
        $file = $request->file('image');
		$current_password_post = $request->input('current_password');
		$user = User::findOrFail($id);
		if($current_password_post){
			if(Hash::check($current_password_post, $user->password)){
			//if(Hash::check($current_password_post, $current_password)){           
				$user->password = Hash::make($request->input('password'));
				$with = 'success';
				$text = 'Profile pengguna berhasil diperbaharui.';
			} else {
				$with = 'error';
				$text = "Silahkan masukkan sandi saat ini";
			}
		} else {
			$with = 'success';
			$text = 'Profile pengguna berhasil diperbaharui.';
		}
		$password_dapo = $request->input('password_dapo');
		if($password_dapo){
			$user->password_dapo = md5($password_dapo);
		}
		if($file){
			$image_path = "storage/images/".$user->photo;
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
				$image_dimensions = "storage/images/".$row.'/'.$user->photo;
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
				//if (!File::isDirectory($this->path . '/' . $row)) {
					//MAKA BUAT FOLDER DENGAN NAMA DIMENSI
					//File::makeDirectory($this->path . '/' . $row);
				//}
				if(!Storage::exists('public/images/'.$row)) {
					Storage::makeDirectory('public/images/'.$row, 0775, true); //creates directory
				}
				
				//MEMASUKAN IMAGE YANG TELAH DIRESIZE KE DALAM CANVAS
				$canvas->insert($resizeImage, 'center');
				//SIMPAN IMAGE KE DALAM MASING-MASING FOLDER (DIMENSI)
				$canvas->save($this->path . '/' . $row . '/' . $fileName);
			}
			$user->photo = $fileName;
		}
		$user->name = $request->input('name');
        $user->email = strtolower($request->input('email'));
		$user->save();
		if($user->hasRole('siswa')){
			$siswa = $user->siswa;
			if($siswa){
				$siswa->email = strtolower($request->input('email'));
				$siswa->save();
			}
		}
		return redirect()->route('user.profile')->with($with, $text);
	}
	
	public function list_user(Request $request){
		$user = auth()->user();
		$query = User::query()->where('active', 1)->where('sekolah_id', $user->sekolah_id)->whereNotIn('user_id',function($query) {
			$query->select('user_id')->from('role_user')->whereIn('role_id', [1, 2])->orderBy('role_id', 'ASC');
		})->orderBy('users.name', 'ASC');
		$datatables =  Datatables::of($query);
		return $datatables
			->filter(function ($query) {
				if (request()->has('filter_akses')) {
					$query->whereIn('user_id',function($q) {
						$q->select('user_id')->from('role_user')->where('role_id', '=', request('filter_akses'));
					});
					if(request('filter_akses') == 5){
						$query->wherehas('siswa.anggota_rombel', function($query){
							$query->whereHas('rombongan_belajar', function($query){
								$query->where('jenis_rombel', 1);
								$query->where('semester_id', session('semester_id'));
							});
						});
					}
				}
			}, true)
			->addColumn('name', function ($item) {
				return strtoupper($item->name);
			})
			->addColumn('jenis_pengguna', function ($item) {
				//dd($item->roles);
				//$find_role_user = DB::table('role_user')->join('roles', 'role_user.role_id', '=', 'roles.id')->where('user_id', $item->user_id)->get();
				$role = [];
				foreach($item->roles as $role_user){
					$role[] = $role_user->display_name;
				}
				$return  = implode(', ',$role);
				return $return;
			})
			->addColumn('last_login', function ($item) {
				$return  = ($item->last_login_at) ? CustomHelper::TanggalIndo(date('Y-m-d', strtotime($item->last_login_at))).' '.date('H:i:s', strtotime($item->last_login_at)) : '';
				return $return;
			})
			->addColumn('hashedPassword', function ($item) {
				//if(Hash::check(12345678, $item->password)){
				if(Hash::check($item->default_password, $item->password)){
    				$password = '<div class="text-center">'.$item->default_password.'</div>';
				} else {
					$password = '<div class="text-center"><span class="btn btn-xs btn-success"> Custom </span></div>';
				}
				return $password;
			})
            ->addColumn('actions', function ($item) {
				$aktifkan = ($item->active == 1)  ? '<a href="'.url('admin/config/deactivate/'.$item->user_id).'" class="toggle-modal"><i class="fa fa-power-off"></i>Non Aktifkan</a>' : '<a href="'.url('admin/config/activate/'.$item->user_id).'"><i class="fa fa-check-square-o"></i>Aktifkan</a>';
                $links = '<div class="text-center"><div class="btn-group">
							<button type="button" class="btn btn-default btn-sm">Aksi</button>
							<button type="button" class="btn btn-info btn-sm dropdown-toggle" data-toggle="dropdown">
								<span class="caret"></span>
								<span class="sr-only">Toggle Dropdown</span>
							</button>
							<ul class="dropdown-menu pull-right text-left" role="menu">
								<li><a href="'.url('users/edit/'.$item->user_id).'"><i class="fa fa-pencil"></i> Ubah</a></li>
								<li><a href="'.url('users/delete/'.$item->user_id).'" class="confirm"><i class="fa fa-trash-o"></i> Hapus</a></li>
								<li><a href="'.url('users/reset-password/'.$item->user_id).'"><i class="fa fa-refresh"></i> Atur Ulang Sandi</a></li>
							</ul>
						</div></div>';
					//$links = '<div class="text-center"><a class="btn btn-default btn-sm" href="'.url('users/edit/'.$item->user_id).'"><i class="fa fa-pencil"></i> Edit</a></div>';
                return $links;

            })
            ->rawColumns(['jenis_pengguna', 'actions', 'last_login', 'hashedPassword'])
            ->make(true);  
	}
	public function list_role(){
		$query = Role::query();
		return DataTables::of($query)
            ->addColumn('actions', function ($item) {
				$user = auth()->user();
                $links = '<a href=' . url('meeting/update/' . $item->id) . ' class="btn btn-primary btn-xs" title="Edit Jadwal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>' .
                '<a href="' . url('meeting/detail/' . $item->id) . '" class="btn btn-primary btn-xs" title="Lihat rincian jadwal"><span class="glyphicon glyphicon-list-alt"></span> Lihat</a>';
				if ($user) {
                    $links .= '<a href="' . url('meeting/print_report/' . $item->id) . '" class="btn btn-success btn-xs" title="Cetak Laporan"><span class="glyphicon glyphicon-print"></span> Berita Acara</a>';
                }
                $links .= '<a href="' . url('meeting/delete/' . $item->id) . '" class="btn btn-danger btn-xs" title="Hapus"><span class="glyphicon glyphicon-remove"></span>Hapus</a>';
                return $links;

            })
            ->rawColumns(['actions'])
            ->make(true);  
	}
	public function permission(){
		return view('laratrust/list_permission');
	}
	public function permission_role(){
		return view('laratrust/list_permission_role');
	}
	public function permission_user(){
		return view('laratrust/list_permission_user');
	}
	public function role_user(){
		return view('laratrust/list_role_user');
	}
	public function list_permission(){
		$query = Permission::query();
		return DataTables::of($query)
            ->addColumn('actions', function ($item) {
				$user = auth()->user();
                $links = '<a href=' . url('meeting/update/' . $item->id) . ' class="btn btn-primary btn-xs" title="Edit Jadwal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>' .
                '<a href="' . url('meeting/detail/' . $item->id) . '" class="btn btn-primary btn-xs" title="Lihat rincian jadwal"><span class="glyphicon glyphicon-list-alt"></span> Lihat</a>';
				if ($user) {
                    $links .= '<a href="' . url('meeting/print_report/' . $item->id) . '" class="btn btn-success btn-xs" title="Cetak Laporan"><span class="glyphicon glyphicon-print"></span> Berita Acara</a>';
                }
                $links .= '<a href="' . url('meeting/delete/' . $item->id) . '" class="btn btn-danger btn-xs" title="Hapus"><span class="glyphicon glyphicon-remove"></span>Hapus</a>';
                return $links;

            })
            ->rawColumns(['actions'])
            ->make(true);  
	}
	public function list_permission_role(){
		$query = Role::query();
		return DataTables::of($query)
            ->addColumn('actions', function ($item) {
				$user = auth()->user();
                $links = '<a href=' . url('meeting/update/' . $item->id) . ' class="btn btn-primary btn-xs" title="Edit Jadwal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>' .
                '<a href="' . url('meeting/detail/' . $item->id) . '" class="btn btn-primary btn-xs" title="Lihat rincian jadwal"><span class="glyphicon glyphicon-list-alt"></span> Lihat</a>';
				if ($user) {
                    $links .= '<a href="' . url('meeting/print_report/' . $item->id) . '" class="btn btn-success btn-xs" title="Cetak Laporan"><span class="glyphicon glyphicon-print"></span> Berita Acara</a>';
                }
                $links .= '<a href="' . url('meeting/delete/' . $item->id) . '" class="btn btn-danger btn-xs" title="Hapus"><span class="glyphicon glyphicon-remove"></span>Hapus</a>';
                return $links;

            })
            ->rawColumns(['actions'])
            ->make(true);  
	}
	public function list_permission_user(){
		$query = Role::query();
		return DataTables::of($query)
            ->addColumn('actions', function ($item) {
				$user = auth()->user();
                $links = '<a href=' . url('meeting/update/' . $item->id) . ' class="btn btn-primary btn-xs" title="Edit Jadwal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>' .
                '<a href="' . url('meeting/detail/' . $item->id) . '" class="btn btn-primary btn-xs" title="Lihat rincian jadwal"><span class="glyphicon glyphicon-list-alt"></span> Lihat</a>';
				if ($user) {
                    $links .= '<a href="' . url('meeting/print_report/' . $item->id) . '" class="btn btn-success btn-xs" title="Cetak Laporan"><span class="glyphicon glyphicon-print"></span> Berita Acara</a>';
                }
                $links .= '<a href="' . url('meeting/delete/' . $item->id) . '" class="btn btn-danger btn-xs" title="Hapus"><span class="glyphicon glyphicon-remove"></span>Hapus</a>';
                return $links;

            })
            ->rawColumns(['actions'])
            ->make(true);  
	}
	public function list_role_user(){
		$query = Role::query();
		return DataTables::of($query)
            ->addColumn('actions', function ($item) {
				$user = auth()->user();
                $links = '<a href=' . url('meeting/update/' . $item->id) . ' class="btn btn-primary btn-xs" title="Edit Jadwal"><span class="glyphicon glyphicon-pencil"></span> Edit</a>' .
                '<a href="' . url('meeting/detail/' . $item->id) . '" class="btn btn-primary btn-xs" title="Lihat rincian jadwal"><span class="glyphicon glyphicon-list-alt"></span> Lihat</a>';
				if ($user) {
                    $links .= '<a href="' . url('meeting/print_report/' . $item->id) . '" class="btn btn-success btn-xs" title="Cetak Laporan"><span class="glyphicon glyphicon-print"></span> Berita Acara</a>';
                }
                $links .= '<a href="' . url('meeting/delete/' . $item->id) . '" class="btn btn-danger btn-xs" title="Hapus"><span class="glyphicon glyphicon-remove"></span>Hapus</a>';
                return $links;

            })
            ->rawColumns(['actions'])
            ->make(true);  
	}
	public function edit($id){
		$user = User::with('roles')->find($id);
		if($user->hasRole('siswa')){
			$role_disabled = array(5);
			$roles = Role::find([5]);
		} elseif($user->hasRole('guru')){
			$role_disabled = array(4,10);
			$roles = Role::find([3,4,7,8,9,10]);
		} elseif($user->hasRole('tu')){
			$role_disabled = array(3);
			$roles = Role::find([3,4,7,8,9,11]);
		} else {
			$role_disabled = array(2);
			$roles = Role::find([2]);
			//$roles = Role::find([3,4,7,8]);
		}
		$role_user = array();
		foreach($user->roles as $role){
			$role_user[] = $role->id;
		}
		$params = [
                'title' => 'Edit Pengguna',
                'pengguna' => $user,
                'roles' => $roles,
				'role_user' => $role_user,
				'role_disabled'	=> $role_disabled,
				'content_header_right' => '<a href="'.url('users').'" class="btn btn-primary pull-right">Kembali</a>'
            ];
		return view('users.edit')->with($params);
    }
	public function delete($id){
		if(User::destroy($id)){
			$output['text'] = 'Pengguna berhasil dihapus';
			$output['icon'] = 'success';
		} else {
			$output['text'] = 'Pengguna gagal dihapus';
			$output['icon'] = 'error';
		}
		echo json_encode($output);
	}
	public function update(Request $request, $id)
    {
        try {
			//dd($id);
            $user = User::findOrFail($id);
			$this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id .',user_id',
				//'email' => 'required|email|unique:users,email,' . $id,
            ]);
			//dd($user);
			$user->name = $request->input('name');
            $user->email = $request->input('email');
			$user->save();
			$set_roles = [];
			if ($request->has('role_id')) {
				$set_roles = $request->input('role_id');
				$collection = collect($set_roles);
				$filtered = $collection->filter(function ($value, $key) {
					return $value > 2;
				});
				$set_roles = $filtered->all();
			}
			if($user->hasRole('siswa')){
				if($set_roles){
					$set_roles = array_merge($set_roles, ['5']);
				} else {
					$set_roles = array('5');
				}
			} elseif($user->hasRole('guru')){
				if($set_roles){
					$set_roles = array_merge($set_roles, ['4']);
				} else {
					$set_roles = array('4');
				}
				if($user->hasRole('wali')){
					if($set_roles){
						$set_roles = array_merge($set_roles, ['10']);
					} else {
						$set_roles = array('10');
					}
				}
				if($user->hasRole('pembina_ekskul')){
					if($set_roles){
						$set_roles = array_merge($set_roles, ['11']);
					} else {
						$set_roles = array('11');
					}
				}
			} elseif($user->hasRole('admin')){
				$set_roles = array('2');
			} elseif($user->hasRole('tu')){
				if($set_roles){
					$set_roles = array_merge($set_roles, ['3']);
				} else {
					$set_roles = array('3');
				}
			}
			$roles = $user->roles;

            foreach ($roles as $key => $value) {
                $user->detachRole($value);
            }
			$permissions = $user->permissions;
			if(count($permissions)){
				foreach ($permissions as $key => $value) {
					$user->detachPermission($value);
				}
			}
			foreach($set_roles as $role_id){
				$role = Role::find($role_id);
				$user->attachRole($role);
				$permission = Permission::where('name', '=', $role->name)->first();
				if($permission){
					$user->attachPermission($permission);
					$user->syncPermissions([$permission->id]);
				} else {
					$permission = Permission::create(['name' => $role->name, 'display_name' => $role->display_name, 'description' => $role->description]);
				}
				DB::table('permission_role')->updateOrInsert(['permission_id' => $permission->id, 'role_id' => $role->id]);
			}
			return redirect()->route('users')->with('success', "Pengguna <strong>$user->name</strong> berhasil diperbaharui.");
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }
	public function reset_password(Request $request, $id){
		$pengguna = User::find($id);
		if($pengguna){
			$random = Str::random(8);
			$password = ($pengguna->default_password) ? $pengguna->default_password : strtolower($random);
			$pengguna->password = Hash::make($password);
			$pengguna->default_password = $password;
			if($pengguna->save()){
				$output = [
					'title' => 'Berhasil',
					'text' => 'Password berhasil di atur ulang',
					'icon' => 'success'
				];
				Alert::success('Password berhasil di atur ulang', 'Berhasil');
			} else {
				$output = [
					'title' => 'Gagal',
					'text' => 'Password gagal di atur ulang',
					'icon' => 'error'
				];
				Alert::error('Password gagal di atur ulang', 'Gagal');
			}
		} else {
			$output = [
				'title' => 'Gagal',
				'text' => 'Password gagal di atur ulang',
				'icon' => 'error'
			];
			Alert::error('Pengguna tidak ditemukan', 'Gagal');
		}
		if($request->ajax()){
			return response()->json($output);
		} else {
			return redirect()->route('users');
		}
	}
	public function generate(Request $request){
		$user = auth()->user();
		$sekolah = Sekolah::find($user->sekolah_id);
		$ajaran = config('site.semester');//CustomHelper::get_ta();
		if($request->route('query') == 'ptk'){
			/*$find_guru = Guru::whereNotIn('guru_id',function($query) use ($user) {
				$query->select('guru_id')->from('users')->whereNotNull('guru_id')->where('sekolah_id', '=', $user->sekolah_id);
			})->where('sekolah_id', '=', $user->sekolah_id)->get();
			$jenis_tu = CustomHelper::jenis_gtk('tendik');
			$asesor = CustomHelper::jenis_gtk('asesor');
			if($find_guru->count()){
				foreach($find_guru as $guru){
					$random = Str::random(8);
					$guru->email = ($guru->email != $user->email) ? $guru->email : strtolower($random).'@erapor-smk.net';
					$guru->email = ($guru->email != $sekolah->email) ? $guru->email : strtolower($random).'@erapor-smk.net';
					$guru->email = strtolower($guru->email);
					$find_user_email = User::where('email', $guru->email)->first();
					if($find_user_email){
						$guru->email = strtolower($random).'@erapor-smk.net';
					}
					$new_password = strtolower(Str::random(8));
					$insert_user = array(
						'name' => $guru->nama,
						'email' => $guru->email,
						'nuptk'	=> $guru->nuptk,
						'password' => Hash::make($new_password),
						'last_sync'	=> date('Y-m-d H:i:s'),
						'sekolah_id'	=> $user->sekolah_id,
						'password_dapo'	=> md5($new_password),
						'guru_id'	=> $guru->guru_id,
						'default_password' => $new_password,
					);
					$create_user = User::updateOrCreate(
						['guru_id' => $guru->guru_id],
						$insert_user
					);
					if(in_array($guru->jenis_ptk_id, $jenis_tu)){
						$adminRole = Role::where('name', 'tu')->first();
					} elseif(in_array($guru->jenis_ptk_id, $asesor)){
						$adminRole = Role::where('name', 'user')->first();
					} else {
						$adminRole = Role::where('name', 'guru')->first();
					}
					$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
					if(!$CheckadminRole){
						$create_user->attachRole($adminRole);
					}
					$find_rombel = Rombongan_belajar::where('guru_id', $guru->guru_id)->where('semester_id', $ajaran->semester_id)->where('jenis_rombel', 1)->first();
					if($find_rombel){
						$WalasRole = Role::where('name', 'wali')->first();
						$CheckWalasRole = DB::table('role_user')->where('user_id', $create_user->user_id)->where('role_id', $WalasRole->id)->first();
						if(!$CheckWalasRole){
							$create_user->attachRole($WalasRole);
						}
					}
					$find_ekskul = Ekstrakurikuler::where('guru_id', $guru->guru_id)->where('semester_id', $ajaran->semester_id)->first();
					if($find_ekskul){
						$PembinaRole = Role::where('name', 'pembina_ekskul')->first();
						$CheckPembinaRole = DB::table('role_user')->where('user_id', $create_user->user_id)->where('role_id', $PembinaRole->id)->first();
						if(!$CheckPembinaRole){
							$create_user->attachRole($PembinaRole);
						}
					}
				}
			}*/
			$jenis_tu = CustomHelper::jenis_gtk('tendik');
			$asesor = CustomHelper::jenis_gtk('asesor');
			Guru::whereNotIn('guru_id',function($query) use ($user) {
				$query->select('guru_id')->from('users')->whereNotNull('guru_id')->where('sekolah_id', '=', $user->sekolah_id);
			})->where('sekolah_id', '=', $user->sekolah_id)->chunk(200, function ($find_guru) use ($jenis_tu, $asesor, $user, $sekolah, $ajaran) {
				foreach ($find_guru as $guru) {
					$random = Str::random(8);
					$guru->email = ($guru->email != $user->email) ? $guru->email : strtolower($random).'@erapor-smk.net';
					$guru->email = ($guru->email != $sekolah->email) ? $guru->email : strtolower($random).'@erapor-smk.net';
					$guru->email = strtolower($guru->email);
					$find_user_email = User::where('email', $guru->email)->first();
					if($find_user_email){
						$guru->email = strtolower($random).'@erapor-smk.net';
					}
					$new_password = strtolower(Str::random(8));
					$insert_user = array(
						'name' => $guru->nama,
						'email' => $guru->email,
						'nuptk'	=> $guru->nuptk,
						'password' => Hash::make($new_password),
						'last_sync'	=> date('Y-m-d H:i:s'),
						'sekolah_id'	=> $user->sekolah_id,
						'password_dapo'	=> md5($new_password),
						'guru_id'	=> $guru->guru_id,
						'default_password' => $new_password,
					);
					$create_user = User::updateOrCreate(
						['guru_id' => $guru->guru_id],
						$insert_user
					);
					if(in_array($guru->jenis_ptk_id, $jenis_tu)){
						$adminRole = Role::where('name', 'tu')->first();
					} elseif(in_array($guru->jenis_ptk_id, $asesor)){
						$adminRole = Role::where('name', 'user')->first();
					} else {
						$adminRole = Role::where('name', 'guru')->first();
					}
					$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
					if(!$CheckadminRole){
						$create_user->attachRole($adminRole);
					}
					$find_rombel = Rombongan_belajar::where('guru_id', $guru->guru_id)->where('semester_id', $ajaran->semester_id)->where('jenis_rombel', 1)->first();
					if($find_rombel){
						$WalasRole = Role::where('name', 'wali')->first();
						$CheckWalasRole = DB::table('role_user')->where('user_id', $create_user->user_id)->where('role_id', $WalasRole->id)->first();
						if(!$CheckWalasRole){
							$create_user->attachRole($WalasRole);
						}
					}
					$find_ekskul = Ekstrakurikuler::where('guru_id', $guru->guru_id)->where('semester_id', $ajaran->semester_id)->first();
					if($find_ekskul){
						$PembinaRole = Role::where('name', 'pembina_ekskul')->first();
						$CheckPembinaRole = DB::table('role_user')->where('user_id', $create_user->user_id)->where('role_id', $PembinaRole->id)->first();
						if(!$CheckPembinaRole){
							$create_user->attachRole($PembinaRole);
						}
					}
				}
			});
			User::where('sekolah_id', $sekolah->sekolah_id)->whereRoleIs('guru')->chunk(200, function ($all_pengguna) {
				foreach($all_pengguna as $pengguna){
					if(Hash::check(12345678, $pengguna->password) || !$pengguna->default_password){
						$new_password = strtolower(Str::random(8));
						$pengguna->password = Hash::make($new_password);
						$pengguna->default_password = $new_password;
						$pengguna->save();
					}
				}
			});
			$response = [
				'title' => 'Berhasil',
				'text' => 'Pengguna PTK berhasil diperbaharui',
				'icon' => 'success'
			];
		} elseif($request->route('query') == 'pd'){
			/*$find_siswa = Siswa::whereNotIn('peserta_didik_id', function($query) use ($user) {
				$query->select('peserta_didik_id')->from('users')->whereNotNull('peserta_didik_id')->where('sekolah_id', '=', $user->sekolah_id);
			})->where('sekolah_id', '=', $user->sekolah_id)->get();
			if($find_siswa->count()){
				foreach($find_siswa as $siswa){
					$random = Str::random(8);
					$find_user = User::where('email', $siswa->email)->first();
					$siswa->email = ($siswa->email != $user->email) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = ($siswa->email != $sekolah->email) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = (!$find_user) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = strtolower($siswa->email);
					$find_user_email = User::where('email', $siswa->email)->first();
					if($find_user_email){
						$siswa->email = strtolower($random).'@erapor-smk.net';
					}
					$new_password = strtolower(Str::random(8));
					$insert_user = array(
						'name' => $siswa->nama,
						'email' => $siswa->email,
						'nisn'	=> $siswa->nisn,
						'password' => Hash::make($new_password),
						'last_sync'	=> date('Y-m-d H:i:s'),
						'sekolah_id'	=> $user->sekolah_id,
						'password_dapo'	=> md5($new_password),
						'peserta_didik_id'	=> $siswa->peserta_didik_id,
						'default_password' => $new_password,
					);
					$create_user = User::updateOrCreate(
						['peserta_didik_id' => $siswa->peserta_didik_id],
						$insert_user
					);
					$adminRole = Role::where('name', 'siswa')->first();
					$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
					if(!$CheckadminRole){
						$create_user->attachRole($adminRole);
					}
				}
			}*/
			Artisan::call('generate:user', ['query' => 'pd', 'user' => $user, 'sekolah' => $sekolah]);
			/*Siswa::whereNotIn('peserta_didik_id', function($query) use ($user) {
				$query->select('peserta_didik_id')->from('users')->whereNotNull('peserta_didik_id')->where('sekolah_id', '=', $user->sekolah_id);
			})->where('sekolah_id', '=', $user->sekolah_id)->chunk(100, function ($find_siswa) use ($user, $sekolah){
				foreach($find_siswa as $siswa){
					$random = Str::random(8);
					$find_user = User::where('email', $siswa->email)->first();
					$siswa->email = ($siswa->email != $user->email) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = ($siswa->email != $sekolah->email) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = (!$find_user) ? $siswa->email : strtolower($random).'@erapor-smk.net';
					$siswa->email = strtolower($siswa->email);
					$find_user_email = User::where('email', $siswa->email)->first();
					if($find_user_email){
						$siswa->email = strtolower($random).'@erapor-smk.net';
					}
					$new_password = strtolower(Str::random(8));
					$insert_user = array(
						'name' => $siswa->nama,
						'email' => $siswa->email,
						'nisn'	=> $siswa->nisn,
						'password' => Hash::make($new_password),
						'last_sync'	=> date('Y-m-d H:i:s'),
						'sekolah_id'	=> $user->sekolah_id,
						'password_dapo'	=> md5($new_password),
						'peserta_didik_id'	=> $siswa->peserta_didik_id,
						'default_password' => $new_password,
					);
					$create_user = User::updateOrCreate(
						['peserta_didik_id' => $siswa->peserta_didik_id],
						$insert_user
					);
					$adminRole = Role::where('name', 'siswa')->first();
					$CheckadminRole = DB::table('role_user')->where('user_id', $create_user->user_id)->first();
					if(!$CheckadminRole){
						$create_user->attachRole($adminRole);
					}
				}
			});
			User::where('sekolah_id', $sekolah->sekolah_id)->whereRoleIs('siswa')->chunk(100, function ($all_pengguna) {
				foreach($all_pengguna as $pengguna){
					if(Hash::check(12345678, $pengguna->password) || !$pengguna->default_password){
						$new_password = strtolower(Str::random(8));
						$pengguna->password = Hash::make($new_password);
						$pengguna->default_password = $new_password;
						$pengguna->save();
					}
				}
			});*/
			$response = [
				'title' => 'Berhasil',
				'text' => 'Pengguna Peserta Didik berhasil diperbaharui',
				'icon' => 'success'
			];
		} else {
			$response = [
				'title' => 'Gagal',
				'text' => 'Permintaan tidak dikenal',
				'icon' => 'Error'
			];
		}
		return response()->json($response);
	}
}
