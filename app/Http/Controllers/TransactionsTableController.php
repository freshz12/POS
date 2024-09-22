<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TransactionsTableExport;
use App\Models\Products;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class TransactionsTableController extends Controller
{

    public function index()
    {
        return view('pages.transactions_table.transactions_table', ['type_menu' => 'transactions_table']);
    }

    public function indexData(Request $request)
    {

        $query = Transactions::with(['customers', 'capster'])
            ->filterIndex($request);

        $totalRecords = $query->count();

        $query->orderBy('transaction_id', 'desc');

        $length = $request->input('length');
        $start = $request->input('start');
        $transactions = $query->skip($start)->take($length)->get();

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $transactions
        ]);
    }


    public function show($id)
    {
        $transaction = Transactions::with('transaction_products', 'customers', 'capster')->find($id);

        $transaction->transaction_products->map(function ($transactionProduct) {
            $product = Products::find($transactionProduct->product_id);
            $transactionProduct->product_details = $product;
        });

        return response()->json([$transaction]);
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

            foreach ($transaction->transaction_products as $product) {
                $product->deleted_by = auth()->user()->id;
                $product->save();
            }

            $transaction->transaction_products()->delete();

            $transaction->save();

            $transaction->delete();

            DB::commit();

            session()->flash('success_message', 'Transaction has been deleted successfully!');

            return redirect()->to('/transactions_table')->with('type_menu', 'transactions');
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
            return Excel::download(new TransactionsTableExport($request), 'transactions.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
