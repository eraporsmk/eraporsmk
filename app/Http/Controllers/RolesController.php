<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\Permission;
use Illuminate\Support\Facades\DB;
class RolesController extends Controller
{
    // Roles Listing Page
    public function index()
    {
        //
        $roles = Role::paginate(10);

        $params = [
            'title' => 'Roles Listing',
            'roles' => $roles,
			'content_header_right' => '<a href="'.url('role_create').'" class="btn btn-success pull-right">Tambah Role</a>'
        ];
		return view('admin.roles.roles_list')->with($params);
    }

    // Roles Creation Page
    public function create()
    {
        //
        $permissions = Permission::all();

        $params = [
            'title' => 'Create Roles',
            'permissions' => $permissions,
			'content_header_right' => '<a href="'.url('role_index').'" class="btn btn-primary pull-right">Kembali</a>'
        ];

        return view('admin.roles.roles_create')->with($params);
    }

    // Roles Store to DB
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required|unique:roles',
            'display_name' => 'required',
            'description' => 'required',
        ]);

        $role = Role::create([
            'name' => $request->input('name'),
            'display_name' => $request->input('display_name'),
            'description' => $request->input('description'),
        ]);

        return redirect()->route('roles.index')->with('success', "The role <strong>$role->name</strong> has successfully been created.");
    }

    // Roles Delete Confirmation Page
    public function show($id)
    {
        //
        try {
            $role = Role::findOrFail($id);

            $params = [
                'title' => 'Delete Role',
                'role' => $role,
            ];

            return view('admin.roles.roles_delete')->with($params);
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Roles Editing Page
    public function edit($id)
    {
        //
        try {
            $role = Role::findOrFail($id);
            $permissions = Permission::all();
            $role_permissions = $role->permissions()->get()->pluck('id')->toArray();

            $params = [
                'title' => 'Edit Role',
                'role' => $role,
                'permissions' => $permissions,
                'role_permissions' => $role_permissions,
				'content_header_right' => '<a href="'.url('role_index').'" class="btn btn-primary pull-right">Kembali</a>'
            ];

            return view('admin.roles.roles_edit')->with($params);
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Roles Update to DB
    public function update(Request $request, $id)
    {
        //
        try {
            $role = Role::findOrFail($id);

            $this->validate($request, [
                'display_name' => 'required',
                'description' => 'required',
            ]);

            $role->name = $request->input('name');
            $role->display_name = $request->input('display_name');
            $role->description = $request->input('description');

            $role->save();

            DB::table("permission_role")->where("permission_role.role_id", $id)->delete();
            // Attach permission to role
			if($request->input('permission_id')){
				foreach ($request->input('permission_id') as $key => $value) {
					$role->attachPermission($value);
				}
			}
            return redirect()->route('roles.index')->with('success', "The role <strong>$role->name</strong> has successfully been updated.");
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
    }

    // Delete Roles from DB
    public function destroy($id)
    {
        //
        try {
            $role = Role::findOrFail($id);

            //$role->delete();

            // Force Delete
            $role->users()->sync([]); // Delete relationship data
            $role->permissions()->sync([]); // Delete relationship data

            $role->forceDelete(); // Now force delete will work regardless of whether the pivot table has cascading delete

            return redirect()->route('roles.index')->with('success', "The Role <strong>$role->name</strong> has successfully been archived.");
        } catch (ModelNotFoundException $ex) {
            if ($ex instanceof ModelNotFoundException) {
                return response()->view('errors.' . '404');
            }
        }
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
}
