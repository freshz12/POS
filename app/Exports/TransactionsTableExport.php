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

class TransactionsTableExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        $table = TransactionProducts::
        with('transaction')
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
        if ($transaction?->transaction?->promo?->value) {
            if ($transaction?->transaction?->promo?->type === 'Percentage') {
                $amountBeforeDiscount = $transaction?->transaction?->amount_before_discount ?? 0;
                $promoValue = ($amountBeforeDiscount * $transaction?->transaction?->promo?->value ?? 0) / 100;
            }else{
                $promoValue = $transaction?->transaction?->promo?->value ?? 0;
            }
        } else {
            $promoValue = 'N/A';
        }

        return [
            $transaction?->month ?? 'N/A',
            $transaction?->transaction?->created_at ?? 'N/A',
            $transaction?->transaction?->transaction_id ?? 'N/A',
            $transaction?->transaction?->capster?->full_name ?? 'N/A',
            $transaction?->transaction?->customers?->full_name ?? 'N/A',
            $transaction?->product?->product_name ?? 'N/A',
            $transaction?->quantity ?? 'N/A',
            $transaction?->product?->selling_price ?? 'N/A',
            $transaction?->transaction?->amount_before_discount ?? 'N/A',
            // $transaction?->transaction?->promo?->value ?? 'N/A',
            $promoValue,
            $transaction?->transaction?->promo?->name ?? 'N/A',
            $transaction?->transaction?->amount ?? 'N/A',
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
            'Harga per service / produk',
            'Penjualan Kotor',
            'Diskon',
            'Promosi',
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

