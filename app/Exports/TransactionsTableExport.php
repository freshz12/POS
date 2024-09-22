<?php

namespace App\Exports;

use App\Models\TransactionProducts;
use Illuminate\Http\Request;
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
        return TransactionProducts::
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
    }

    public function map($transaction): array
    {
        return [
            $transaction->transaction->id,
            $transaction->transaction->transaction_id,
            $transaction->transaction->customers->full_name,
            $transaction->product->product_name,
            $transaction->quantity,
            $transaction->transaction->amount,
            $transaction->transaction->capster->full_name,
            $transaction->createdBy->name ?? 'N/A',
            $transaction->created_at,
            $transaction->updatedBy->name ?? 'N/A',
            $transaction->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Transaction ID',
            'Customer Name',
            'Product Name',
            'Quantity',
            'Amount',
            'Capster Name',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

