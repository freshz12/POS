<?php

namespace App\Http\Controllers;

use App\Exports\TransactionsExport;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class TransactionsController extends Controller
{

    public function index()
    {
        return view('pages.transactions.transactions', ['type_menu' => 'transactions']);
    }

    public function indexData(Request $request)
    {
        $transactions = Transactions::filterIndex($request)->orderBy('id', 'desc')->get();

        return response()->json(['data' => $transactions]);
    }

    public function transactionsData(Request $request)
    {
        $searchTerm = '%' . $request->search . '%';

        $transactions = Transactions::where('full_name', 'LIKE', $searchTerm)
            ->orWhere('email', 'LIKE', $searchTerm)
            ->orWhere('phone_number', 'LIKE', $searchTerm)
            ->orderBy('id', 'desc')
            ->get(['id', 'full_name', 'gender', 'email', 'phone_number']);

        return response()->json(['data' => $transactions]);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $duplicateEmail = Transactions::whereNotNull('email')->where('email', '<>', '')->where('email', $request->email)->value('email');
            if ($duplicateEmail == $request->email && !empty($duplicateEmail)) {
                return back()->withErrors([
                    'error_message' => "The email \"$request->email\" has already been taken",
                ]);
            }

            $duplicatePhone = Transactions::whereNotNull('phone_number')->where('phone_number', '<>', '')->where('phone_number', $request->phone_number)->value('phone_number');
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

            $transactionData = array_merge($filteredData, $additionalData);

            Transactions::create($transactionData);

            DB::commit();

            session()->flash('success_message', 'Transaction has been created successfully!');

            return redirect()->to('/transactions')->with('type_menu', 'transactions');
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
            $duplicateEmail = Transactions::whereNotNull('email')->where('email', '<>', '')->where('email', $request->email)->value('email');
            if ($duplicateEmail == $request->email && !empty($duplicateEmail)) {
                return response()->json([
                    'success' => false,
                    'message' => "The email \"$request->email\" has already been taken"
                ]);
            }

            $duplicatePhone = Transactions::whereNotNull('phone_number')->where('phone_number', '<>', '')->where('phone_number', $request->phone_number)->value('phone_number');
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

            $transactionData = array_merge($filteredData, $additionalData);

            $transaction = Transactions::create($transactionData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaction has been created successfully!',
                'data' => $transaction
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
        $transactions = Transactions::find($id);

        return response()->json([$transactions]);
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
            $transaction = Transactions::find($request->id);

            $filteredData = $request->except(['_token']);

            $additionalData = [
                'updated_by' => auth()->user()->id,
            ];

            $transactionData = array_merge($filteredData, $additionalData);

            $transaction->update($transactionData);

            DB::commit();

            session()->flash('success_message', 'Transaction has been updated successfully!');

            return redirect()->to('/transactions')->with('type_menu', 'transactions');
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

            $transaction = Transactions::find($request->id);

            $transaction->deleted_by = auth()->user()->id;

            $transaction->save();

            $transaction->delete();

            DB::commit();

            session()->flash('success_message', 'Transaction has been deleted successfully!');

            return redirect()->to('/transactions')->with('type_menu', 'transactions');
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
            return Excel::download(new TransactionsExport($request), 'transactions.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
