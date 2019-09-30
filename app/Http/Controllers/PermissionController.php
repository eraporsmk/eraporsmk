<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Permission;
use Illuminate\Support\Facades\DB;
class PermissionController extends Controller
{
    // Permission Listing Page
    public function index()
    {
        //
        $permissions = Permission::paginate(10);
        //dd($users);

        $params = [
            'title' => 'Permissions Listing',
            'permissions' => $permissions,
			'content_header_right' => '<a href="'.route('permission.create').'" class="btn btn-success pull-right">Tambah Data</a>'
        ];

        return view('admin.permission.perm_list')->with($params);
    }

    // Permission Create Page
    public function create()
    {
        //
        $params = [
            'title' => 'Create Permission',
			'content_header_right' => '<a href="'.route('permission.index').'" class="btn btn-primary pull-right">Kembali</a>'
        ];

        return view('admin.permission.perm_create')->with($params);
    }

    // Permission Store to DB
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required|unique:permissions',
            'display_name' => 'required',
            'description' => 'required',
        ]);
        $permission = Permission::create([
            'name' => $request->input('name'),
            'display_name' => $request->input('display_name'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('permission.index')->with('success', "The Permission <strong>$permission->name</strong> has successfully been created.");
    }

    // Permission Delete Confirmation Page
    public function show($id)
    {
        //
        try {
            $permission = Permission::findOrFail($id);

            $params = [
                'title' => 'Delete Permission',
                'permission' => $permission,
				'content_header_right' => '<a href="'.route('permission.index').'" class="btn btn-primary pull-right">Kembali</a>'
            ];

            return view('admin.permission.perm_delete')->with($params);
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Permission Editing Page
    public function edit($id)
    {
        //
        try {
            $permission = Permission::findOrFail($id);
			//$roles = Role::all();
            $params = [
                'title' => 'Edit Permission',
                'permission' => $permission,
				//'roles' => $roles,
				'content_header_right' => '<a href="'.route('permission.index').'" class="btn btn-primary pull-right">Kembali</a>'
            ];

            //dd($role_permissions);

            return view('admin.permission.perm_edit')->with($params);
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Permission update to DB
    public function update(Request $request, $id)
    {
        //
        try {
            $permission = Permission::findOrFail($id);

            $this->validate($request, [
                'display_name' => 'required',
                'description' => 'required',
				'role_id'	=> 'required',
            ]);

            $permission->name = $request->input('name');
            $permission->display_name = $request->input('display_name');
            $permission->description = $request->input('description');

            $permission->save();
			
            return redirect()->route('permission.index')->with('success', "The permission <strong>$permission->name</strong> has successfully been updated.");
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Permission Delete from DB
    public function destroy($id)
    {
        //
        try {
            $permission = Permission::findOrFail($id);
            DB::table("permission_role")->where('permission_id', $id)->delete();
            $permission->delete();
            
            return redirect()->route('permission.index')->with('success', "The Role <strong>$permission->name</strong> has successfully been archived.");
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }
}
