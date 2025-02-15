<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use App\Exports\TransactionsTableExport;
use App\Exports\TransactionsTableParentExport;

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
            $transaction->amount_before_discount = 0;
            $transaction->total_discount = 0;

            $transaction_products = TransactionProducts::with(['productDiscount', 'product'])
                ->where('transaction_id', $transaction->id)
                ->get(['id', 'price', 'quantity', 'is_new_data', 'transaction_id', 'promo_id', 'product_id']);

            foreach ($transaction_products as $product) {
                $selling_price = $product->is_new_data == 0 ? ($product->product?->selling_price ?? 0) : ($product->price ?? 0);
                $transaction->amount_before_discount += $selling_price * $product->quantity;

                if ($product->productDiscount) {
                    if ($product->productDiscount->type == 'Percentage') {
                        $transaction->total_discount += floor(
                            ((floatval($selling_price) * intval($product->quantity)) * floatval($product->productDiscount->value ?? 0)) / 100
                        );
                    } elseif ($product->productDiscount->type == 'Nominal') {
                        $transaction->total_discount += $product->productDiscount->value;
                    }
                }
            }

            $transaction->amount = $transaction->amount_before_discount - $transaction->total_discount;
        }

        // foreach ($transactions as $transaction) {
            // if ($transaction->promo) {
            //     if ($transaction->promo_id && $transaction->promo->type === 'Package') {
            //         $products_id = json_decode($transaction->promo->product_id);
            //         $totalSellingPrice = 0;

            //         foreach ($products_id as $product_id) {
            //             $product = Products::find($product_id);

            //             if ($product) {
            //                 $totalSellingPrice += $product->selling_price;
            //             }
            //         }
            //         $transaction->promo->value = $totalSellingPrice;
            //     }
            // }
        // }

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

            if($transactionProduct->promo_id){
                if ($transactionProduct->productDiscount->type == 'Percentage') {
                    $discount_amount = floor(((floatval($transactionProduct->price) * intval($transactionProduct->quantity)) * floatval($transactionProduct->productDiscount->value)) / 100);
                } else {
                    $discount_amount = floatval($transactionProduct->productDiscount->value);
                }
                $transactionProduct->discount_amount = $discount_amount;
            }else{
                $transactionProduct->discount_amount = 0;
            }
            return $transactionProduct;
        });

        $productList = [];

        if ($transaction->promo_id) {
            if ($transaction->promo->type == 'Package') {
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
            ->select('transactions.id', 'transactions.capster_id', 'transactions.promo_id', 'transactions.created_at', 'transactions.amount')
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
        return Excel::download(new TransactionsTableExport($request), 'transaction_products.xlsx');
        // } catch (\Exception $e) {
        //     return back()->withErrors([
        //         'error_message' => 'Something went wrong, please contact administrator',
        //     ]);
        // }
    }

    public function export_parent(Request $request)
    {
        // try {
        return Excel::download(new TransactionsTableParentExport($request), 'transactions.xlsx');
        // } catch (\Exception $e) {
        //     return back()->withErrors([
        //         'error_message' => 'Something went wrong, please contact administrator',
        //     ]);
        // }
    }
}
