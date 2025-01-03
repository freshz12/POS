<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promos extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $table = 'promos';

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

            ->when($request->unique_code, function ($query) use ($request) {
                $query->where('unique_code', 'LIKE', '%' . $request->unique_code . '%');
            })

            ->when($request->is_without_package, function ($query) use ($request) {
                $query->whereNot('type', 'Package');
            })

            ->when($request->type, function ($query) use ($request) {
                $query->where('type', 'LIKE', '%' . $request->type . '%');
            })

            ->when($request->value, function ($query) use ($request) {
                $query->where('value', 'LIKE', '%' . $request->value . '%');
            })

            ->when($request->start_date, function ($query) use ($request) {
                $date = Carbon::parse($request->start_date)->format('Y-m-d');
                $startOfDay = Carbon::parse($date)->startOfDay()->toDateTimeString();
                $endOfDay = Carbon::parse($date)->endOfDay()->toDateTimeString();

                $query->whereBetween('start_date', [$startOfDay, $endOfDay]);
            })

            ->when($request->end_date, function ($query) use ($request) {
                $date = Carbon::parse($request->end_date)->format('Y-m-d');
                $startOfDay = Carbon::parse($date)->startOfDay()->toDateTimeString();
                $endOfDay = Carbon::parse($date)->endOfDay()->toDateTimeString();

                $query->whereBetween('end_date', [$startOfDay, $endOfDay]);
            })

            ->when($request->is_active, function ($query) {
                $today = Carbon::today()->toDateString();
    
                $query->where(function ($query) use ($today) {
                          $query->whereDate('start_date', '<=', $today)
                                ->whereDate('end_date', '>=', $today);
                      });
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
