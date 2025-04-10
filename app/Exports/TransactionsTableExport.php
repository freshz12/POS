<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class TransactionsTableExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithColumnFormatting, WithStrictNullComparison
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $table = TransactionProducts::with([
            'transaction.promo',
            'transaction.capster',
            'transaction.customers',
            'product',
            'productDiscount',
        ])
            ->filterIndex($this->request)
            ->select(
                'id',
                'transaction_id',
                'product_id',
                'quantity',
                'created_at',
                'created_by',
                'updated_at',
                'updated_by',
                'is_new_data',
                'promo_id',
                'price',
            )
            ->orderBy('transaction_id', 'desc')
            ->get();

        foreach ($table as $transaction) {
            $transaction->month = Carbon::parse($transaction?->transaction?->created_at)->setTimezone('Asia/Jakarta')->format('F');
        }

        return $table;
    }

    public function map($transaction): array
    {
        if ($transaction?->is_new_data == 0) {
            $selling_price = $transaction?->product?->selling_price ?? 0;
        } else {
            $selling_price = $transaction?->price ?? 0;
        }

        if ($transaction?->promo_id) {
            if ($transaction?->productDiscount?->type === 'Percentage') {
                $amountBeforeDiscount = $transaction?->productDiscount?->amount_before_discount ?? 0;
                // $promoValue = ($amountBeforeDiscount * $transaction?->productDiscount?->value ?? 0) / 100;
                $promoValue = floor(((floatval($selling_price) * intval($transaction->quantity)) * floatval($transaction?->productDiscount?->value ?? 0)) / 100);
            } else {
                $promoValue = $transaction?->productDiscount?->value ?? 0;
            }
        } else {
            $promoValue = 0;
        }

        $gross_amount = $transaction?->quantity * $selling_price;

        $netAmount = $gross_amount - $promoValue;

        return [
            $transaction?->month ?? 'N/A',
            $transaction?->transaction?->created_at ?? 'N/A',
            $transaction?->transaction?->transaction_id ?? 'N/A',
            $transaction?->transaction?->capster?->full_name ?? 'N/A',
            $transaction?->transaction?->customers?->full_name ?? 'N/A',
            $transaction?->product?->product_name ?? 'N/A',
            $transaction?->quantity ?? 'N/A',
            $selling_price,
            $gross_amount ?? 'N/A',
            $promoValue,
            $netAmount ?? 'N/A',
            $transaction?->transaction?->payment_method ?? 'N/A',
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
            'Nama Service / Produk',
            'Unit Terjual',
            'Harga per service / Produk',
            'Penjualan Kotor',
            'Diskon / Promosi',
            // 'Promosi',
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
