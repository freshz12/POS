<?php

namespace App\Exports;

use App\Models\Attendances;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AttendancesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return Attendances::
        filterIndex($this->request)
        ->with(['updatedBy', 'createdBy', 'users'])
        ->select('attendances.id',
        'attendances.user_id',
        'attendances.clock_in',
        'attendances.clock_out',
        'attendances.status',
        'attendances.request_reason',
        'attendances.approved_or_rejected_reason',
        'attendances.created_at',
        'attendances.created_by',
        'attendances.updated_at',
        'attendances.updated_by',
        )
        ->get();
    }

    public function map($attendance): array
    {
        return [
            $attendance?->id ?? 'N/A',
            $attendance?->users?->name ?? 'N/A',
            $attendance?->clock_in ?? 'N/A',
            $attendance?->clock_out ?? 'N/A',
            $attendance?->status ?? 'N/A',
            $attendance?->request_reason ?? 'N/A',
            $attendance?->approved_or_rejected_reason ?? 'N/A',
            $attendance?->createdBy?->name ?? 'N/A',
            $attendance?->created_at ?? 'N/A',
            $attendance?->updatedBy?->name ?? 'N/A',
            $attendance?->updated_at ?? 'N/A',
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Clock In',
            'Clock Out',
            'Request Reason',
            'Status',
            'Approved or Rejected Reason',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'I' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

