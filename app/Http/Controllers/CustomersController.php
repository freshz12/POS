<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Models\Customers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class CustomersController extends Controller
{

    public function index()
    {
        return view('pages.customers.customers', ['type_menu' => 'customers']);
    }

    public function indexData(Request $request)
    {
        $customers = Customers::filterIndex($request)->orderBy('id', 'desc')->get();

        return response()->json(['data' => $customers]);
    }

    public function customersData(Request $request)
    {
        $searchTerm = '%' . $request->search . '%';

        $customers = Customers::where('full_name', 'LIKE', $searchTerm)
            ->orWhere('email', 'LIKE', $searchTerm)
            ->orWhere('phone_number', 'LIKE', $searchTerm)
            ->orderBy('id', 'desc')
            ->get(['id', 'full_name', 'gender', 'email', 'phone_number']);

        return response()->json(['data' => $customers]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $duplicateEmail = Customers::whereNotNull('email')->where('email', '<>', '')->where('email', $request->email)->value('email');
            if ($duplicateEmail == $request->email && !empty($duplicateEmail)) {
                return back()->withErrors([
                    'error_message' => "The email \"$request->email\" has already been taken",
                ]);
            }

            $duplicatePhone = Customers::whereNotNull('phone_number')->where('phone_number', '<>', '')->where('phone_number', $request->phone_number)->value('phone_number');
            if ($duplicatePhone == $request->phone_number && !empty($duplicatePhone)) {
                return back()->withErrors([
                    'error_message' => "The phone number \"$request->phone_number\" has already been taken",
                ]);
            }

            $filteredData = $request->except(['_token']);

            $additionalData = [
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            $customerData = array_merge($filteredData, $additionalData);

            Customers::create($customerData);

            DB::commit();

            session()->flash('success_message', 'Customer has been created successfully!');

            return redirect()->to('/customers')->with('type_menu', 'customers');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function storeAjax(Request $request)
    {
        try {
            DB::beginTransaction();
            $duplicateEmail = Customers::whereNotNull('email')->where('email', '<>', '')->where('email', $request->email)->value('email');
            if ($duplicateEmail == $request->email && !empty($duplicateEmail)) {
                return response()->json([
                    'success' => false,
                    'message' => "The email \"$request->email\" has already been taken"
                ]);
            }

            $duplicatePhone = Customers::whereNotNull('phone_number')->where('phone_number', '<>', '')->where('phone_number', $request->phone_number)->value('phone_number');
            if ($duplicatePhone == $request->phone_number && !empty($duplicatePhone)) {
                return response()->json([
                    'success' => false,
                    'message' => "The phone number \"$request->phone_number\" has already been taken"
                ]);
            }

            $filteredData = $request->except(['_token']);

            $additionalData = [
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
            ];

            $customerData = array_merge($filteredData, $additionalData);

            $customer = Customers::create($customerData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Customer has been created successfully!',
                'data' => $customer
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => $e
            ]);
        }
    }

    public function show($id)
    {
        $customers = Customers::find($id);

        return response()->json([$customers]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $customer = Customers::find($request->id);

            $filteredData = $request->except(['_token']);

            $additionalData = [
                'updated_by' => auth()->user()->id,
            ];

            $customerData = array_merge($filteredData, $additionalData);

            $customer->update($customerData);

            DB::commit();

            session()->flash('success_message', 'Customer has been updated successfully!');

            return redirect()->to('/customers')->with('type_menu', 'customers');
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

            $customer = Customers::find($request->id);

            $customer->deleted_by = auth()->user()->id;

            $customer->save();

            $customer->delete();

            DB::commit();

            session()->flash('success_message', 'Customer has been deleted successfully!');

            return redirect()->to('/customers')->with('type_menu', 'customers');
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
            return Excel::download(new CustomersExport($request), 'customers.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
