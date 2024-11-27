<?php

namespace App\Exports;

use App\Models\Capsters;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class DashboardsCapstersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return Transactions::join('capsters', 'transactions.capster_id', '=', 'capsters.id')
            ->selectRaw('capsters.full_name as capster_name, transactions.capster_id, SUM(transactions.amount) as total_amount, COUNT(*) as total_transactions')
            ->groupBy('capsters.full_name', 'transactions.capster_id')
            ->when($this->request->capster_name, function ($query) {
                $query->where('capsters.full_name', 'like', '%' . $this->request->capster_name . '%');
            })
            ->when($this->request->total_amount, function ($query) {
                $query->havingRaw('SUM(transactions.amount) LIKE ?', ['%' . $this->request->total_amount . '%']);
            })
            ->when($this->request->total_transactions, function ($query) {
                $query->havingRaw('COUNT(*) LIKE ?', ['%' . $this->request->total_transactions . '%']);
            })
            ->orderBy('transactions.capster_id')
            ->get();
    }

    public function map($capster): array
    {
        return [
            $capster?->capster_id ?? 'N/A',
            $capster?->capster_name ?? 'N/A',
            $capster?->total_amount ?? 'N/A',
            $capster?->total_transactions ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Caspter ID',
            'Caspter Name',
            'Total Amount',
            'Total Transactions',
        ];
    }
}

