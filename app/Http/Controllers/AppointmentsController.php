<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Appointments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AppointmentsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class AppointmentsController extends Controller
{

    public function index()
    {
        return view('pages.appointments.appointments', ['type_menu' => 'appointments']);
    }

    public function indexData(Request $request)
    {
        $appointments = Appointments::
        with(['customers', 'capster'])
        ->orderBy('id', 'desc')
        ->get(['id', 'start_date', 'end_date', 'status', 'remarks', 'customer_id', 'capster_id']);

        return response()->json(['data' => $appointments]);
    }

    public function store(Request $request)
    {
        try {
            $start_date = Carbon::createFromFormat('Y-m-d H:i', $request->date_start)->format('Y-m-d H:i:s');
            $end_date = Carbon::createFromFormat('Y-m-d H:i', $request->date_end)->format('Y-m-d H:i:s');

            DB::beginTransaction();
            // $duplicate_start_date = Appointments::whereNotNull('start_date')->where('start_date', '<>', '')->where('start_date', $start_date)->value('start_date');
            // if ($duplicate_start_date == $start_date && !empty($duplicate_start_date)) {
            //     return back()->withErrors([
            //         'error_message' => "The start appointment date \"$start_date\" has already been taken",
            //     ]);
            // }

            $filteredData = $request->except(['_token', 'date_start', 'date_end']);

            $additionalData = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            $appointmentData = array_merge($filteredData, $additionalData);

            Appointments::create($appointmentData);

            DB::commit();

            session()->flash('success_message', 'Appointment has been created successfully!');

            return redirect()->to('/appointments')->with('type_menu', 'appointments');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    // public function show($id)
    // {
    //     $appointments = Appointments::find($id);

    //     return response()->json([$appointments]);
    // }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $start_date = Carbon::createFromFormat('Y-m-d H:i', $request->start)->format('Y-m-d H:i:s');
            $end_date = Carbon::createFromFormat('Y-m-d H:i', $request->end)->format('Y-m-d H:i:s');

            $appointment = Appointments::find($request->id);

            $filteredData = $request->except(['_token', 'date_start', 'date_end']);

            $additionalData = [
                'start_date' => $start_date,
                'end_date' => $end_date,
                'updated_by' => auth()->user()->id,
            ];

            $appointmentData = array_merge($filteredData, $additionalData);

            $appointment->update($appointmentData);

            DB::commit();

            session()->flash('success_message', 'Appointment has been updated successfully!');

            return redirect()->to('/appointments')->with('type_menu', 'appointments');

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

            $appointment = Appointments::find($request->id);

            $appointment->deleted_by = auth()->user()->id;

            $appointment->save();

            $appointment->delete();

            DB::commit();

            session()->flash('success_message', 'Appointment has been deleted successfully!');

            return redirect()->to('/appointments')->with('type_menu', 'appointments');

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
            return Excel::download(new AppointmentsExport($request), 'appointments.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
