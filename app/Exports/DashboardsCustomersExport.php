<?php

namespace App\Exports;

use App\Models\Transactions;
use Illuminate\Http\Request;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DashboardsCustomersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return Transactions::join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->selectRaw('customers.full_name as customer_name, 
             SUM(transactions.amount) as total_spent, 
             COUNT(transactions.id) as total_transactions')
            ->groupBy('customers.full_name')
            ->when($this->request->customer_name, function ($query) {
                $query->where('customers.full_name', 'like', '%' . $this->request->customer_name . '%');
            })
            ->when($this->request->total_spent, function ($query) {
                $query->havingRaw('SUM(transactions.amount) LIKE ?', ['%' . $this->request->total_spent . '%']);
            })
            ->when($this->request->total_transactions, function ($query) {
                $query->havingRaw('COUNT(transactions.id) LIKE ?', ['%' . $this->request->total_transactions . '%']);
            })
            ->orderBy('customers.full_name')
            ->get();
    }

    public function map($customer): array
    {
        return [
            $customer?->customer_name ?? 'N/A',
            $customer?->total_spent ?? 'N/A',
            $customer?->total_transactions ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Customers Name',
            'Total Spent',
            'Total Transactions',
        ];
    }
}
