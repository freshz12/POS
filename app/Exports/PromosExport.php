<?php

namespace App\Exports;

use App\Models\Promos;
use App\Models\Products;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PromosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return Promos::
        filterIndex($this->request)
        ->with(['updatedBy', 'createdBy'])
        ->select('promos.id',
        'promos.name',
        'promos.unique_code',
        'promos.type',
        'promos.product_id',
        'promos.package_quantity',
        'promos.value',
        'promos.start_date',
        'promos.end_date',
        'promos.created_at',
        'promos.created_by',
        'promos.updated_at',
        'promos.updated_by',
        )
        ->get()
        ->transform(function ($promo) {
            $promo->product_name = 'N/A';
            if($promo->product_id){
                $products_id = json_decode($promo->product_id, true);

                $products_name = implode(', ', Products::whereIn('id', $products_id)->pluck('product_name')->toArray());
    
                $promo->product_name = $products_name;
            }
            return $promo;
        });
    }

    public function map($promos): array
    {
        return [
            $promos?->id ?? 'N/A',
            $promos?->name ?? 'N/A',
            $promos?->unique_code ?? 'N/A',
            $promos?->type ?? 'N/A',
            $promos?->product_name ?? 'N/A',
            $promos?->package_quantity ?? 'N/A',
            $promos?->value ?? 'N/A',
            $promos?->start_date ?? 'N/A',
            $promos?->end_date ?? 'N/A',
            $promos?->createdBy?->name ?? 'N/A',
            $promos?->created_at ?? 'N/A',
            $promos?->updatedBy?->name ?? 'N/A',
            $promos?->updated_at ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Promo Name',
            'Unique Code',
            'Type',
            'Products Name',
            'Package Quantity',
            'Value',
            'Start Date',
            'End Date',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

