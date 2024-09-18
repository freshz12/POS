<?php

namespace App\Exports;

use App\Models\RoleHasPermissions;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class RolesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return RoleHasPermissions::
        filterIndex($this->request)
        ->with(['updatedBy', 'createdBy', 'role', 'permissions'])
        ->select(
        'role_has_permissions.role_id',
        'role_has_permissions.permission_id',
        'role_has_permissions.created_at',
        'role_has_permissions.created_by',
        'role_has_permissions.updated_at',
        'role_has_permissions.updated_by',
        )
        ->orderBy('role_has_permissions.role_id')
        ->get();
    }

    public function map($product): array
    {
        return [
            $product->role->name,
            $product->permissions->name,
            $product->createdBy->name ?? 'N/A',
            $product->created_at,
            $product->updatedBy->name ?? 'N/A',
            $product->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Role Name',
            'Permission Name',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'D' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

