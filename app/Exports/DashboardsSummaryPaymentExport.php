<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DashboardsSummaryPaymentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $period = $this->request->input('created_type', 'today');
        $startDate = Carbon::now('Asia/Jakarta');
        $endDate = Carbon::now('Asia/Jakarta');

        switch ($period) {
            case 'today':
                $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
                $endDate = Carbon::now('Asia/Jakarta')->endOfDay();
                break;

            case 'this_week':
                $startDate = Carbon::now('Asia/Jakarta')->startOfWeek();
                $endDate = Carbon::now('Asia/Jakarta')->endOfWeek();
                break;

            case 'this_month':
                $startDate = Carbon::now('Asia/Jakarta')->startOfMonth();
                $endDate = Carbon::now('Asia/Jakarta')->endOfMonth();
                break;

            case 'this_year':
                $startDate = Carbon::now('Asia/Jakarta')->startOfYear();
                $endDate = Carbon::now('Asia/Jakarta')->endOfYear();
                break;

            case 'custom':
                if (!$this->request->input('created_from_filter') || !$this->request->input('created_to_filter')) {
                    $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
                    $endDate = Carbon::now('Asia/Jakarta')->endOfDay();
                } else {
                    $startDate = $this->request->input('created_from_filter')
                        ? Carbon::createFromFormat('Y-m-d', $this->request->input('created_from_filter'), 'Asia/Jakarta')->startOfDay()
                        : Carbon::now('Asia/Jakarta')->startOfDay();
                    $endDate = $this->request->input('created_to_filter')
                        ? Carbon::createFromFormat('Y-m-d', $this->request->input('created_to_filter'), 'Asia/Jakarta')->endOfDay()
                        : Carbon::now('Asia/Jakarta')->endOfDay();
                }

                break;
        }

        $periodeTransaksi = $startDate->isSameDay($endDate)
            ? $startDate->translatedFormat('j F Y')
            : $startDate->translatedFormat('j F Y') . ' to ' . $endDate->translatedFormat('j F Y'); // Date range

        $summary_payment = Transactions::selectRaw('
                ? AS transaction_period,
                COUNT(id) AS total_customer,
                SUM(amount) AS total_amount,
                SUM(CASE WHEN payment_method = \'Cash\' THEN amount ELSE 0 END) AS total_cash,
                SUM(CASE WHEN payment_method = \'EDC\' THEN amount ELSE 0 END) AS total_edc,
                SUM(CASE WHEN payment_method = \'QRIS\' THEN amount ELSE 0 END) AS total_qris
            ', [$periodeTransaksi])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        return $summary_payment;
    }

    public function map($capster): array
    {
        return [
            $capster?->transaction_period ?? 'N/A',
            $capster?->total_customer ?? 'N/A',
            $capster?->total_amount ?? 'N/A',
            $capster?->total_cash ?? 'N/A',
            $capster?->total_edc ?? 'N/A',
            $capster?->total_qris ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Transaction Period',
            'total Customer',
            'total Amount',
            'total Cash',
            'total EDC',
            'total QRIS',
        ];
    }
}
