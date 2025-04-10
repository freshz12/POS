<?php

namespace App\Exports;

use App\Models\Customers;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return Customers::
        filterIndex($this->request)
        ->with(['updatedBy', 'createdBy'])
        ->select('customers.id',
        'customers.full_name',
        'customers.email',
        'customers.gender',
        'customers.phone_number',
        'customers.created_at',
        'customers.created_by',
        'customers.updated_at',
        'customers.updated_by',
        )
        ->get();
    }

    public function map($customer): array
    {
        return [
            $customer?->id ?? 'N/A',
            $customer?->full_name ?? 'N/A',
            $customer?->email ?? 'N/A',
            $customer?->gender ?? 'N/A',
            $customer?->phone_number ?? 'N/A',
            $customer?->createdBy?->name ?? 'N/A' ?? 'N/A',
            $customer?->created_at ?? 'N/A',
            $customer?->updatedBy?->name ?? 'N/A',
            $customer?->updated_at ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email',
            'Gender',
            'Phone Number',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

