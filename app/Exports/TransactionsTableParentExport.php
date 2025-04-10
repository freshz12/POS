<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TransactionsTableParentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $table = Transactions::with(['promo', 'capster', 'customers'])
            ->filterIndex($this->request)
            ->select(
                'id',
                'transaction_id',
                'customer_id',
                'amount',
                'capster_id',
                'promo_id',
                'amount_before_discount',
                'payment_method',
                'created_at'
            )
            ->orderBy('transactions.transaction_id', 'desc')
            ->get();

        foreach ($table as $transaction) {
            $transaction->gross_amount = 0;
            $transaction->total_discount = 0;
            $transaction->month = Carbon::parse($transaction->created_at)->setTimezone('Asia/Jakarta')->format('F');

            $transaction_products = TransactionProducts::with(['productDiscount', 'product'])
                ->where('transaction_id', $transaction->id)
                ->get(['id', 'price', 'quantity', 'is_new_data', 'transaction_id', 'promo_id', 'product_id']);

            foreach ($transaction_products as $product) {
                $selling_price = $product->is_new_data == 0 ? ($product->product?->selling_price ?? 0) : ($product->price ?? 0);
                $transaction->gross_amount += $selling_price * $product->quantity;

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

            $transaction->clean_amount = $transaction->gross_amount - $transaction->total_discount;
        }

        return $table;
    }

    public function map($transaction): array
    {
        return [
            $transaction->month ?? 'N/A',
            $transaction->created_at ?? 'N/A',
            $transaction->transaction_id ?? 'N/A',
            $transaction->capster?->full_name ?? 'N/A',
            $transaction->customers?->full_name ?? 'N/A',
            $transaction->gross_amount ?? 'N/A',
            $transaction->total_discount ?? 0,
            $transaction->clean_amount ?? 'N/A',
            $transaction->payment_method ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Bulan',
            'Tanggal dan Jam Transaksi',
            'Nomor Pesanan',
            'Nama Capster',
            'Nama Pelanggan',
            'Penjualan Kotor',
            'Diskon / Promosi',
            'Penjualan Bersih',
            'Metode Pembayaran',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}
