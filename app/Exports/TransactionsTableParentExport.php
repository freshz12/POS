<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Transactions;
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
        $table = Transactions::with([
            'promo',
            'capster',
            'customers',
        ])
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
                'created_at',
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
        $promoValue = $transaction->amount_before_discount - $transaction->amount;

        $netAmount = $transaction?->amount_before_discount - $promoValue;

        return [
            $transaction?->month ?? 'N/A',
            $transaction?->created_at ?? 'N/A',
            $transaction?->transaction_id ?? 'N/A',
            $transaction?->capster?->full_name ?? 'N/A',
            $transaction?->customers?->full_name ?? 'N/A',
            $transaction?->amount_before_discount ?? 'N/A',
            $promoValue ?? 0,
            $netAmount ?? 'N/A',
            $transaction?->payment_method ?? 'N/A',
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
