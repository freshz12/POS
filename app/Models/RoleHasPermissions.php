<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleHasPermissions extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'role_has_permissions';

    protected $guarded = ['updated_at'];

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function permissions()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }

    public function scopeFilterIndex($query, $request)
    {
        return $query->when($request->name, function ($query) use ($request) {
            $query->whereHas('role', function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->name . '%');
            });
        })

            ->when($request->updated_at, function ($query) use ($request) {
                $query->whereHas('role', function ($query) use ($request) {
                    $date = Carbon::parse($request->updated_at)->format('Y-m-d');
                    $startOfDay = Carbon::parse($date)->startOfDay()->toDateTimeString();
                    $endOfDay = Carbon::parse($date)->endOfDay()->toDateTimeString();
                    $query->whereBetween('updated_at', [$startOfDay, $endOfDay]);
                });
            });
    }

}
