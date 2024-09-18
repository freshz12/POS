<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class UsersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize,WithColumnFormatting
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    
    public function collection()
    {
        return User::
        filterIndex($this->request)
        ->with(['updatedBy', 'createdBy', 'roles'])
        ->select(
        'users.id',
        'users.name',
        'users.username',
        'users.created_at',
        'users.created_by',
        'users.updated_at',
        'users.updated_by',
        )
        ->orderBy('users.id')
        ->get();
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->username,
            $user->roles[0]->name ?? 'N/A',
            $user->createdBy->name ?? 'N/A',
            $user->created_at,
            $user->updatedBy->name ?? 'N/A',
            $user->updated_at,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'User Name',
            'Username',
            'Role Name',
            'Created By',
            'Created At',
            'Updated By',
            'Updated At',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD,
            'H' => NumberFormat::FORMAT_DATE_YYYYMMDD
        ];
    }
}

