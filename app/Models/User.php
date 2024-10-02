<?php

namespace App\Models;

use Carbon\Carbon;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $primaryKey = 'id';

    protected $guarded = [
        'id',
    ];


    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }

    public function scopeFilterIndex($query, $request)
    {
        return $query->when($request->name, function ($query) use ($request) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        })


            ->when($request->username, function ($query) use ($request) {
                $query->where('username', 'LIKE', '%' . $request->username . '%');
            })

            ->when($request->updated_at, function ($query) use ($request) {
                $date = Carbon::parse($request->updated_at)->format('Y-m-d');
                $startOfDay = Carbon::parse($date)->startOfDay()->toDateTimeString();
                $endOfDay = Carbon::parse($date)->endOfDay()->toDateTimeString();

                $query->whereBetween('updated_at', [$startOfDay, $endOfDay]);
            });
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
