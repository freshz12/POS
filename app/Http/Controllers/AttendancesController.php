<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendances;
use Illuminate\Http\Request;
use App\Exports\AttendancesExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class AttendancesController extends Controller
{

    public function index()
    {
        $attendance = Attendances::where('user_id', auth()->user()->id)
            ->whereDate('clock_in', Carbon::today())
            ->whereNull('clock_out')
            ->whereNull('request_reason')
            ->whereNull('approved_or_rejected_reason')
            ->whereNull('status')
            ->value('clock_in');

        return view('pages.attendances.attendances', compact('attendance'))->with(['type_menu' => 'attendances']);
    }

    public function approval()
    {
        return view('pages.attendances_approval.attendances_approval')->with(['type_menu' => 'attendances/index_approval']);
    }

    public function approval_index_data(Request $request)
    {
        $attendances = Attendances::with('users')
            ->filterIndex($request)
            ->where('status', 'Pending Approval')
            ->orderBy('id', 'desc')->get();

        return response()->json(['data' => $attendances]);
    }

    public function approve_or_reject(Request $request)
    {
        try {
            DB::beginTransaction();
            $attendance = Attendances::find($request->id);

            $filteredData = $request->except(['_token', 'approve_or_reject']);

            $additionalData = [
                'status' => $request->approve_or_reject,
                'approve_or_reject_by' => auth()->user()->id,
            ];

            $attendanceData = array_merge($filteredData, $additionalData);

            $attendance->update($attendanceData);

            DB::commit();

            session()->flash('success_message', 'User attendance has been ' . $request->approve_or_reject . ' successfully!');

            return redirect()->to('/attendances/approval')->with('type_menu', 'attendances_history');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function checkIn(Request $request)
    {
        $type = null;

        if (!Storage::disk('public')->exists('files/attendance')) {
            Storage::disk('public')->makeDirectory('files/attendance');
        }

        $photo = $request->input('photo');
        $photo = str_replace('data:image/png;base64,', '', $photo);
        $photo = str_replace(' ', '+', $photo);
        $imageName = time() . '.png';

        Storage::disk('public')->put("files/attendance/{$imageName}", base64_decode($photo));

        if ($request->has('clock_in')) {
            $now = Carbon::now();

            $attendance = Attendances::create([
                'user_id' => auth()->user()->id,
                'clock_in' => $now,
                'photo_path_clock_in' => "storage/files/attendance/{$imageName}",
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ]);

            $type = 'Successfully clocked in at ' . $now;
        } else if ($request->has('clock_out')) {
            $now = Carbon::now();

            $attendance = Attendances::where('user_id', auth()->user()->id)
                ->whereDate('clock_in', Carbon::today())
                ->whereNull('clock_out')
                ->whereNull('request_reason')
                ->whereNull('approved_or_rejected_reason')
                ->whereNull('status')
                ->first();

            $attendance->update([
                'clock_out' => $now,
                'photo_path_clock_out' => "storage/files/attendance/{$imageName}",
                'updated_by' => auth()->user()->id,
            ]);

            $type = 'Successfully clocked out at ' . $now;
        }

        session()->flash('success_message_endless', $type . '!');

        return redirect()->to('/attendances')->with('type_menu', 'attendances');
    }

    public function index_history()
    {
        return view('pages.attendances_table.attendances_table')->with(['type_menu' => 'attendances']);
    }

    public function indexData(Request $request)
    {
        $attendances = Attendances::with('users')
            ->filterIndex($request)
            ->when(!auth()->user()->hasRole(['Admin', 'admin']), function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })
            ->orderBy('id', 'desc')->get();

        return response()->json(['data' => $attendances]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $clock_in = Carbon::createFromFormat('Y-m-d H:i', $request->clock_in)->format('Y-m-d H:i:s');
            $clock_out = Carbon::createFromFormat('Y-m-d H:i', $request->clock_out)->format('Y-m-d H:i:s');

            $filteredData = $request->except(['_token', 'clock_in', 'clock_out']);

            $additionalData = [
                'clock_in' => $clock_in,
                'clock_out' => $clock_out,
                'status' => 'Pending Approval',
                'user_id' => auth()->user()->id,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            $attendanceData = array_merge($filteredData, $additionalData);

            Attendances::create($attendanceData);

            DB::commit();

            session()->flash('success_message', 'User attendance has been created successfully!');

            return redirect()->to('/attendances/index_history')->with('type_menu', 'attendances_history');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function show($id)
    {
        $attendances = Attendances::with('users')->find($id);

        return response()->json([$attendances]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $attendance = Attendances::find($request->id);

            $clock_in = Carbon::createFromFormat('Y-m-d H:i', $request->clock_in)->format('Y-m-d H:i:s');
            $clock_out = Carbon::createFromFormat('Y-m-d H:i', $request->clock_out)->format('Y-m-d H:i:s');

            $filteredData = $request->except(['_token', 'clock_in', 'clock_out']);

            $additionalData = [
                'approved_or_rejected_reason' => '',
                'clock_in' => $clock_in,
                'clock_out' => $clock_out,
                'status' => 'Pending Approval',
                'updated_by' => auth()->user()->id,
            ];

            $attendanceData = array_merge($filteredData, $additionalData);

            $attendance->update($attendanceData);

            DB::commit();

            session()->flash('success_message', 'User attendance has been updated successfully!');

            return redirect()->to('/attendances/index_history')->with('type_menu', 'attendances_history');
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

            $attendance = Attendances::find($request->id);

            if ($attendance->photo_path_clock_in) {
                $pathToDelete = str_replace('storage/', '', $attendance->photo_path_clock_in);
                Storage::disk('public')->delete($pathToDelete);
            }

            if ($attendance->photo_path_clock_out) {
                $pathToDelete2 = str_replace('storage/', '', $attendance->photo_path_clock_out);
                Storage::disk('public')->delete($pathToDelete2);
            }

            $attendance->deleted_by = auth()->user()->id;

            $attendance->save();

            $attendance->delete();

            DB::commit();

            session()->flash('success_message', 'User attendance has been deleted successfully!');

            return redirect()->to('/attendances/index_history')->with('type_menu', 'attendances_history');
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
            return Excel::download(new AttendancesExport($request), 'attendances.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
