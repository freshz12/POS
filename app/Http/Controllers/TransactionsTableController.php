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

        $query = Transactions::with(['customers', 'capster', 'promo'])
            ->filterIndex($request);

        $totalRecords = $query->count();

        $query->orderBy('transaction_id', 'desc');

        $length = $request->input('length');
        $start = $request->input('start');
        $transactions = $query->skip($start)->take($length)->get();

        foreach ($transactions as $transaction) {
            if($transaction->promo){
                if ($transaction->promo_id && $transaction->promo->type === 'Package') {
                    $products_id = json_decode($transaction->promo->product_id);
                    $totalSellingPrice = 0;
    
                    foreach ($products_id as $product_id) {
                        $product = Products::find($product_id);
    
                        if ($product) {
                            $totalSellingPrice += $product->selling_price;
                        }
                    }
                    $transaction->promo->value = $totalSellingPrice;
                }
            }
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $transactions
        ]);
    }


    public function show($id)
    {
        $transaction = Transactions::with('transaction_products', 'customers', 'capster', 'promo')->find($id);

        $transaction->transaction_products->transform(function ($transactionProduct) {
            $product = Products::find($transactionProduct->product_id);
            if ($product) {
                $transactionProduct->product_details = $product;
                $transactionProduct->product_details->selling_price = 
                    $transactionProduct->is_new_data == 0 
                        ? $transactionProduct->product_details->selling_price 
                        : $transactionProduct->price;
            }
            return $transactionProduct;
        });

        $productList = [];

        if ($transaction->promo_id) {
            if($transaction->promo->type == 'Package'){
                $products_id = json_decode($transaction->promo->product_id);
    
                foreach ($products_id as $product_id) {
                    $product = Products::find($product_id);
                    if ($product) {
                        $productList[] = $product;
                    }
                }
                $transaction->promo_products = $productList;
            }
        }


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

    public function showAllTransactionFromCustomer($id)
    {
        $transactions = Transactions::with('capster', 'promo')
        ->where('customer_id', $id)
        ->select('transactions.id','transactions.capster_id', 'transactions.promo_id', 'transactions.created_at', 'transactions.amount')
        ->get();

        foreach ($transactions as $transaction) {
            $transaction->product = TransactionProducts::join('products', 'transaction_products.product_id', '=', 'products.id')
                ->where('transaction_products.transaction_id', $transaction->id)
                ->pluck('products.product_name')
                ->implode(', ');
        }

        return response()->json(['data' => $transactions]);
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
        // try {
            return Excel::download(new TransactionsTableExport($request), 'transactions.xlsx');
        // } catch (\Exception $e) {
        //     return back()->withErrors([
        //         'error_message' => 'Something went wrong, please contact administrator',
        //     ]);
        // }
    }
}
