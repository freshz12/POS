<?php

namespace App\Http\Controllers;

use App\Exports\RolesExport;
use Illuminate\Http\Request;
use App\Models\RoleHasPermissions;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Permission;

class RolesController extends Controller
{

    public function index()
    {
        return view('pages.settings.roles.roles', ['type_menu' => 'roles']);
    }

    public function indexData(Request $request)
    {
        $roles = Role::filterIndex($request)->orderBy('id', 'desc')->get();

        return response()->json(['data' => $roles]);
    }

    public function permissionsTypeData()
    {
        $permissionTypes = Permission::select('type')->groupBy('type')->get();

        $permissionsByType = [];

        $permissions = Permission::select('id', 'name', 'type')->get();

        foreach ($permissions as $permission) {
            if (!isset($permissionsByType[$permission->type])) {
                $permissionsByType[$permission->type] = [];
            }
            $permissionsByType[$permission->type][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'type' => $permission->type,
            ];
        }

        $formattedPermissionTypes = $permissionTypes->map(function ($type) {
            return [
                'type' => $type->type,
            ];
        })->toArray();

        return response()->json([
            'data' => [
                'permissionTypes' => $formattedPermissionTypes,
                'permissions' => $permissionsByType,
            ]
        ]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $duplicateName = Role::whereNotNull('name')->where('name', '<>', '')->where('name', $request->name)->value('name');
            if ($duplicateName == $request->name && !empty($duplicateName)) {
                return back()->withErrors([
                    'error_message' => "The name \"$request->name\" has already been taken",
                ]);
            }

            $role = Role::create([
                'name' => $request->name,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id
            ]);

            if ($request->has('permissions')) {
                foreach ($request->permissions as $permission) {
                    RoleHasPermissions::create([
                        'role_id' => $role->id,
                        'permission_id' => $permission,
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id
                    ]);
                }
            }

            DB::commit();

            session()->flash('success_message', 'Role has been created successfully!');

            return redirect()->to('/roles')->with('type_menu', 'roles');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function show($id)
    {
        $roles = [];
        $roles['id'] = $id;
        $roles['name'] = Role::findById($id)->name;
        $roles['permissions'] = RoleHasPermissions::where('role_id', $id)->pluck('permission_id');

        return response()->json([$roles]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $duplicateRoleName = Role::whereNotNull('name')->where('name', '<>', '')->where('name', $request->name)->first();
            if($duplicateRoleName){
                if ($duplicateRoleName->name == $request->name && !empty($duplicateRoleName->name) && $duplicateRoleName->id !== intval($request->id)) {
                    return back()->withErrors([
                        'error_message' => "The name \"$request->name\" has already been taken",
                    ]);
                }
            }


            $role = Role::findById($request->id);
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions([]);

            if ($request->has('permissions')) {
                foreach ($request->permissions as $permission) {
                    RoleHasPermissions::create([
                        'role_id' => $role->id,
                        'permission_id' => $permission,
                        'created_by' => auth()->user()->id,
                        'updated_by' => auth()->user()->id
                    ]);
                }
            }

            DB::commit();

            session()->flash('success_message', 'Role has been updated successfully!');

            return redirect()->to('/roles')->with('type_menu', 'roles');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();

            $role = Role::findById($request->id);

            $role->syncPermissions([]);

            $role->deleted_by = auth()->user()->id;

            $role->save();

            $role->delete();

            DB::commit();

            session()->flash('success_message', 'Role has been deleted successfully!');

            return redirect()->to('/roles')->with('type_menu', 'roles');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function export(Request $request)
    {
        try {
            return Excel::download(new RolesExport($request), 'roles.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
