<?php

namespace App\Http\Controllers;

use App\Models\Capsters;
use Illuminate\Http\Request;
use App\Exports\CapstersExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class CapstersController extends Controller
{

    public function index()
    {
        return view('pages.capsters.capsters', ['type_menu' => 'capsters']);
    }

    public function indexData(Request $request)
    {
        $capsters = Capsters::
        filterIndex($request)
        ->orderBy('id', 'desc')->get();

        return response()->json(['data' => $capsters]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $filteredData = $request->except(['_token']);

            $additionalData = [
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            $capsterData = array_merge($filteredData, $additionalData);

            Capsters::create($capsterData);

            DB::commit();

            session()->flash('success_message', 'Capster has been created successfully!');

            return redirect()->to('/capsters')->with('type_menu', 'capsters');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function show($id)
    {
        $capster = Capsters::find($id);

        return response()->json([$capster]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $capster = Capsters::find($request->id);

            $filteredData = $request->except(['_token']);

            $additionalData = [
                'updated_by' => auth()->user()->id,
            ];

            $capsterData = array_merge($filteredData, $additionalData);

            $capster->update($capsterData);

            DB::commit();

            session()->flash('success_message', 'Capster has been updated successfully!');

            return redirect()->to('/capsters')->with('type_menu', 'capsters');

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

            $capster = Capsters::find($request->id);

            $capster->deleted_by = auth()->user()->id;

            $capster->save();

            $capster->delete();

            DB::commit();

            session()->flash('success_message', 'Capster has been deleted successfully!');

            return redirect()->to('/capsters')->with('type_menu', 'capsters');

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
            return Excel::download(new CapstersExport($request), 'capsters.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
