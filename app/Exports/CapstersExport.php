<?php

namespace App\Exports;

use App\Models\Capsters;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class CapstersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return Capsters::
        filterIndex($this->request)
        ->with(['updatedBy', 'createdBy'])
        ->select('id',
        'full_name',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        )
        ->get();
    }

    public function map($capster): array
    {
        return [
            $capster?->id ?? 'N/A',
            $capster?->full_name ?? 'N/A',
            $capster?->createdBy?->name ?? 'N/A',
            $capster?->created_at ?? 'N/A',
            $capster?->updatedBy?->name ?? 'N/A',
            $capster?->updated_at ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

