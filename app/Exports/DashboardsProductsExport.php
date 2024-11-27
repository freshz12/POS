<?php

namespace App\Exports;

use Illuminate\Http\Request;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DashboardsProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        return TransactionProducts::join('products', 'transaction_products.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_products.transaction_id', '=', 'transactions.id')
            ->selectRaw('products.product_name as product_name, transaction_products.product_id, SUM(transaction_products.quantity) as total_sold_quantity, products.quantity as quantity_left')
            ->groupBy('products.product_name', 'transaction_products.product_id', 'products.quantity')
            ->when($this->request->product_name, function ($query){
                $query->where('products.product_name', 'like', '%' . $this->request->product_name . '%');
            })
            ->when($this->request->total_sold_quantity, function ($query){
                $query->havingRaw('SUM(transaction_products.quantity) LIKE ?', ['%' . $this->request->total_sold_quantity . '%']);
            })
            ->when($this->request->quantity_left, function ($query){
                $query->where('products.quantity', 'like', '%' . $this->request->quantity_left . '%');
            })
            ->orderBy('transaction_products.product_id')
            ->get();
    }

    public function map($capster): array
    {
        return [
            $capster?->product_id ?? 'N/A',
            $capster?->product_name ?? 'N/A',
            $capster?->quantity_left ?? 'N/A',
            $capster?->total_sold_quantity ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'Products ID',
            'Products Name',
            'Quantity Left',
            'Total Sold Quantity',
        ];
    }
}
