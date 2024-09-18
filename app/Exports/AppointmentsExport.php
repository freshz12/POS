<?php

namespace App\Exports;

use App\Models\Appointments;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AppointmentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return Appointments::
        with(['updatedBy', 'createdBy', 'customers'])
        ->select(
        'id',
        'customer_id',
        'start_date',
        'end_date',
        'status',
        'remarks',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        )
        ->get();
    }

    public function map($appointment): array
    {
        return [
            $appointment->id,
            $appointment->customers->full_name,
            $appointment->start_date,
            $appointment->end_date,
            $appointment->status,
            $appointment->remarks,
            $appointment->createdBy->name ?? 'N/A',
            $appointment->created_at,
            $appointment->updatedBy->name ?? 'N/A',
            $appointment->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Customer Name',
            'Start Date',
            'End Date',
            'Status',
            'Remarks',
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
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

