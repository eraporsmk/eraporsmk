<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Permission;
use Illuminate\Support\Facades\DB;
class PenggunaController extends Controller
{
    //public function __construct()
    //{
        //$this->middleware('role:users');
    //}
	public function __construct()
    {
        $this->middleware('auth');
    }

    // Index Page for Users
    public function index()
    {
        $users = User::paginate(10);
        
        $params = [
            'title' => 'Users Listing',
            'users' => $users,
			'content_header_right' => '<a href="'.route('users.create').'" class="btn btn-success pull-right">Tambah Data</a>',
        ];

        return view('admin.users.users_list')->with($params);
    }

    // Create User Page
    public function create()
    {
        $roles = Role::all();

        $params = [
            'title' => 'Create User',
            'roles' => $roles,
			'content_header_right' => '<a href="'.route('permission.create').'" class="btn btn-success pull-right">Tambah Data</a>',
        ];

        return view('admin.users.users_create')->with($params);
    }

    // Store New User
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users',
            'password' => 'required',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
        ]);

        $role = Role::find($request->input('role_id'));

        $user->attachRole($role);

        return redirect()->route('users.index')->with('success', "The user <strong>$user->name</strong> has successfully been created.");
    }

    // Delete Confirmation Page
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            $params = [
                'title' => 'Confirm Delete Record',
                'user' => $user,
				'content_header_right' => '<a href="'.route('users.index').'" class="btn btn-primary pull-right">Kembali</a>',
            ];

            return view('admin.users.users_delete')->with($params);
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Editing User Information Page
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);

            //$roles = Role::all();
            $roles = Role::with('permissions')->get();
            $permissions = Permission::all();

            $params = [
                'title' => 'Edit User',
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions,
				'content_header_right' => '<a href="'.route('users.index').'" class="btn btn-primary pull-right">Kembali</a>',
            ];

            return view('admin.users.users_edit')->with($params);
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Update User Information to DB
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
			$this->validate($request, [
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id .',user_id',
				//'email' => 'required|email|unique:users,email,' . $id,
            ]);

            $user->name = $request->input('name');
            $user->email = $request->input('email');

            $user->save();

            // Update role of the user
            $roles = $user->roles;

            foreach ($roles as $key => $value) {
                $user->detachRole($value);
            }

            $role = Role::find($request->input('role_id'));

            $user->attachRole($role);
			
			$permissions = $user->permissions;
			if(count($permissions)){
				foreach ($permissions as $key => $value) {
					$user->detachPermission($value);
				}
			}
            // Update permission of the user
            $permission = Permission::find($request->input('permission_id'));
            $user->attachPermission($permission);
			$user->syncPermissions([$permission->id]);
			//$user->syncRoles([$permission->id, $role->id]);
			DB::table('permission_role')->updateOrInsert(['permission_id' => $permission->id, 'role_id' => $role->id]);
			/*$find_permission_role = DB::table('permission_role')->where('permission_id', '=', $permission->id)->where('role_id', '=', $role->id)->first();
			if(!$find_permission_role){
			}*/
            return redirect()->route('users.index')->with('success', "The user <strong>$user->name</strong> has successfully been updated.");
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Remove User from DB with detaching Role
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Detach from Role
            $roles = $user->roles;

            foreach ($roles as $key => $value) {
                $user->detachRole($value);
            }

            $user->delete();

            return redirect()->route('users.index')->with('success', "The user <strong>$user->name</strong> has successfully been archived.");
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }
}
