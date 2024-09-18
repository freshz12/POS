<?php

namespace App\Http\Controllers;

use Hash;
use Carbon\Carbon;
use App\Models\User;
use App\Exports\UsersExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{

    public function index()
    {
        return view('pages.settings.users.users', ['type_menu' => 'users']);
    }

    public function indexData(Request $request)
    {
        $role = User::filterIndex($request)->orderBy('id', 'desc')->get();

        return response()->json(['data' => $role]);
    }

    public function rolesData()
    {
        $users = Role::get(['id', 'name']);

        return response()->json(['data' => $users]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $duplicateUsername = User::whereNotNull('username')->where('username', '<>', '')->where('username', $request->username)->value('username');
            if ($duplicateUsername == $request->username && !empty($duplicateUsername)) {
                return back()->withErrors([
                    'error_message' => "The username \"$request->username\" has already been taken",
                ]);
            }

            $data = [
                'name' => $request->name,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            $user = User::create($data);
            $role = Role::find($request->role);

            $user->assignRole($role);

            DB::commit();

            session()->flash('success_message', 'User has been created successfully!');

            return redirect()->to('/users')->with('type_menu', 'users');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function show($id)
    {
        $user = User::with(['roles'])->where('id', $id)->first();

        return response()->json([$user]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = User::find($request->id);

            $user->syncRoles([]);

            $filteredData = $request->except(['_token']);

            $additionalData = [
                'updated_by' => auth()->user()->id,
            ];

            $userData = array_merge($filteredData, $additionalData);

            $user->update($userData);

            $role = Role::find($request->role);

            $user->assignRole($role);

            DB::commit();

            session()->flash('success_message', 'User has been updated successfully!');

            return redirect()->to('/users')->with('type_menu', 'users');

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

            $user = User::find($request->id);

            $user->syncRoles([]);

            $user->deleted_by = auth()->user()->id;

            $user->save();

            $user->delete();

            DB::commit();

            session()->flash('success_message', 'User has been deleted successfully!');

            return redirect()->to('/users')->with('type_menu', 'users');

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
            return Excel::download(new UsersExport($request), 'users.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
