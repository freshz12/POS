<?php

namespace App\Exports;

use App\Models\Products;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ProductsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return Products::
        filterIndex($this->request)
        ->with(['updatedBy', 'createdBy'])
        ->select('products.id',
        'products.product_name',
        'products.sku',
        'products.description',
        'products.purchase_price',
        'products.selling_price',
        'products.quantity',
        'products.type',
        'products.unit_of_measurement',
        'products.created_at',
        'products.created_by',
        'products.updated_at',
        'products.updated_by',
        )
        ->get();
    }

    public function map($product): array
    {
        return [
            $product?->id ?? 'N/A',
            $product?->product_name ?? 'N/A',
            $product?->sku ?? 'N/A',
            $product?->description ?? 'N/A',
            $product?->purchase_price ?? 'N/A',
            $product?->selling_price ?? 'N/A',
            $product?->quantity ?? 'N/A',
            $product?->type ?? 'N/A',
            $product?->unit_of_measurement ?? 'N/A',
            $product?->createdBy?->name ?? 'N/A',
            $product?->created_at ?? 'N/A',
            $product?->updatedBy?->name ?? 'N/A',
            $product?->updated_at ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Sku',
            'Description',
            'Purchase Price',
            'Selling Price',
            'Quantity',
            'Product Type',
            'Unit of Measurement',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'M' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

