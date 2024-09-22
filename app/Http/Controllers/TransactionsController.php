<?php

namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TransactionsExport;
use App\Models\Products;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class TransactionsController extends Controller
{

    public function index()
    {
        return view('pages.transactions.transactions', ['type_menu' => 'transactions']);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'total_amount' => 'required|numeric',
                'cart_items' => 'required|string',
                'amount' => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation Failed',
                'messages' => $e->validator->errors(),
            ], 422);
        }

        $cartItems = json_decode($request->input('cart_items'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid cart items data'], 400);
        }

        try {
            DB::beginTransaction();

            $runningNumberRecord = DB::table('running_numbers')->first();

            $runningNumber = $this->generateInvoiceNumber();

            $total_amount = str_replace('.', '', $request->input('total_amount'));

            $tr = Transactions::create([
                'transaction_id' => $runningNumber,
                'customer_id' => $request->customer_id,
                'amount' => $total_amount,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id,
                'capster_id' => $request->capster_id,
            ]);


            foreach ($cartItems as $item) {
                TransactionProducts::create([
                    'transaction_id' => $tr->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                ]);
                Products::where('id', $item['id'])
                    ->decrement('quantity', $item['qty']);
            }


            if ($runningNumberRecord) {
                DB::table('running_numbers')->where('id', $runningNumberRecord->id)->update([
                    'running_number' => $runningNumber,
                ]);
            }

            DB::commit();

            session()->flash('success_message', 'Transaction has been placed successfully!');

            return redirect()->to('/transactions')->with('type_menu', 'transactions');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function generateInvoiceNumber()
    {
        $lastInvoice = DB::table('running_numbers')->orderBy('id', 'desc')->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->running_number, 2); // Remove the 'TR' prefix
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return 'TR' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    }
}
