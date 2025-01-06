<?php

namespace App\Http\Controllers;

use App\Models\Promos;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\TransactionsExport;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use charlieuki\ReceiptPrinter\ReceiptPrinter as ReceiptPrinter;

class TransactionsController extends Controller
{

    public function index(Request $request)
    {
        // return $request;
        $capster_id = $request->capster_id;
        $customer_id = $request->customer_id;
        $capster_name = $request->capster_name;
        $customer_name = $request->customer_name;
        $type_menu = 'transactions';

        return view('pages.transactions.transactions', compact('capster_id', 'customer_id', 'type_menu', 'capster_name', 'customer_name'));
    }

    public function store(Request $request)
    {
        // return $request;
        try {
            $request->validate([
                'total_amount' => 'required',
                'cart_items' => 'required',
                'payment_method' => 'required',
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

        // try {
            DB::beginTransaction();

            $runningNumberRecord = DB::table('running_numbers')->first();

            $runningNumber = $this->generateInvoiceNumber();

            $total_amount = str_replace('.', '', $request->input('total_amount'));
            $cash_paid = str_replace('.', '', $request->input('amount'));
            $amount_before_discount = str_replace('.', '', $request->input('amount_before_discount'));

            $tr = Transactions::create([
                'payment_method' => $request->payment_method,
                'promo_id' => $request->promo_id,
                'amount_before_discount' => $amount_before_discount,
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
                    'promo_id' => $item['coupon'] == '' ? null : $item['coupon'],
                    'product_id' => $item['id'],
                    'quantity' => $item['qty'],
                    'created_by' => auth()->user()->id,
                    'updated_by' => auth()->user()->id,
                    'price' => str_replace('.', '', $item['price']),
                    'is_new_data' => 1,
                ]);
                Products::where('id', $item['id'])
                    ->decrement('quantity', $item['qty']);
            }


            if ($runningNumberRecord) {
                DB::table('running_numbers')->where('id', $runningNumberRecord->id)->update([
                    'running_number' => $runningNumber,
                ]);
            }

            $change = intval($cash_paid) - intval($total_amount);
            $discount = Promos::where('id', $request->promo_id)->first(['value', 'type']);

            DB::commit();

            if ($change == 0 && $request->payment_method !== 'Cash') {
                $successMessage = 'Transaction create successfully';
            } else {
                $successMessage = 'Your change is ' . $this->formatNumberWithCommas($change);
            }

            session()->flash('change_message', $successMessage);

            // $this->invoice($cartItems, $total_amount, $runningNumber, $tr, $amount_before_discount, $discount, $request->payment_method);
            // sleep(4);
            // $this->invoice($cartItems, $total_amount, $runningNumber, $tr, $amount_before_discount, $discount, $request->payment_method);

            return redirect()->to('/transactions')->with('type_menu', 'transactions');
        // } catch (\Exception $e) {
        //     DB::rollBack();

        //     return back()->withErrors([
        //         'error_message' => 'Something went wrong, please contact administrator',
        //     ]);
        // }
    }

    public function invoice($cartItems, $total_amount, $runningNumber, $tr, $amount_before_discount, $discount, $payment_method)
    {
        $mid = '123123456';
        $store_name = 'DENSETSU';
        $store_address = 'Ruko Jl Grand Wisata Bekasi No. 16 Blok AA-12 Lambangsari Kec. Tambun Selatan Kab. Bekasi Jawa Barat 17510';
        $store_phone = '1234567890';
        $store_email = 'yourmart@email.com';
        $store_website = 'densetsu.co.id';
        // $tax_percentage = 10;
        $transaction_id = $runningNumber;
        $currency = 'Rp.';
        $image_path = public_path('files/logo.png');



        $printer = new ReceiptPrinter;
        $printer->init(
            config('receiptprinter.connector_type'),
            config('receiptprinter.connector_descriptor')
        );

        $operator = auth()->user()->name;
        $created_date = $tr->created_at;

        $printer->setStore(null, $store_name, $store_address, $store_phone, $store_email, $store_website, $operator);

        $printer->setCurrency($currency);
        $printer->setCreatedat($created_date);

        $items = [];
        foreach ($cartItems as $item) {
            $product = Products::find($item['id']);

            if ($product) {
                if ($product->is_included_in_receipt) {
                    $items[] = [
                        'name' => $product->product_name,
                        'qty' => $item['qty'],
                        'price' => $product->selling_price,
                    ];
                }
            }
        }

        foreach ($items as $item) {
            $printer->addItem(
                $item['name'],
                $item['qty'],
                $item['price']
            );
        }

        // $printer->setTax($tax_percentage);

        $printer->calculateTotal($total_amount, $amount_before_discount);
        $printer->calculateDiscount($discount);

        $printer->setTransactionID($transaction_id);
        $printer->setPaymentMethod($payment_method);

        $printer->setLogo($image_path);

        // Set QR code
        // $printer->setQRcode([
        //     'tid' => $transaction_id,
        // ]);

        $printer->printReceipt();

        // return response()->json(['status' => 'Printing']);
    }

    public function generateInvoiceNumber()
    {
        $lastInvoice = DB::table('running_numbers')->orderBy('id', 'desc')->first();
        $nextNumber = 0;
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->running_number, 2); // Remove the 'TR' prefix
            $nextNumber = $lastNumber + 1;
        } else {
            DB::table('running_numbers')->insert([
                'running_number' => 'TR0000001',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $nextNumber = 1;
        }

        return 'TR' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    }

    protected function formatNumberWithCommas($number)
    {
        return number_format($number, 0, ',', '.');
    }
}
