<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendances extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $table = 'attendances';

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'clock_in' => 'datetime:Y-m-d H:i:s',
        'clock_out' => 'datetime:Y-m-d H:i:s',
    ];

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }
    public function getClockInAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }
    public function getClockOutAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }

    public function scopeFilterIndex($query, $request)
    {
        return $query->when($request->full_name, function ($query) use ($request) {
            $query->whereHas('users', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->full_name . '%');
            });
        })

            ->when($request->status, function ($query) use ($request) {
                $query->where('status', 'LIKE', '%' . $request->status . '%');
            })

            ->when($request->request_reason, function ($query) use ($request) {
                $query->where('request_reason', 'LIKE', '%' . $request->request_reason . '%');
            })

            ->when($request->approved_or_rejected_reason, function ($query) use ($request) {
                $query->where('approved_or_rejected_reason', 'LIKE', '%' . $request->approved_or_rejected_reason . '%');
            })

            ->when($request->request_reason, function ($query) use ($request) {
                $query->where('request_reason', 'LIKE', '%' . $request->request_reason . '%');
            })

            ->when($request->clock_in_from && $request->clock_in_to, function ($query) use ($request) {
                $date1 = Carbon::parse($request->clock_in_from)->format('Y-m-d');
                $startOfDay1 = Carbon::parse($date1)->startOfDay()->toDateTimeString();

                $date2 = Carbon::parse($request->clock_in_to)->format('Y-m-d');
                $endOfDay2 = Carbon::parse($date2)->endOfDay()->toDateTimeString();

                $query->whereBetween('clock_in', [$startOfDay1, $endOfDay2]);
            })
            ->when($request->clock_out_from && $request->clock_out_to, function ($query) use ($request) {
                $date1 = Carbon::parse($request->clock_out_from)->format('Y-m-d');
                $startOfDay1 = Carbon::parse($date1)->startOfDay()->toDateTimeString();

                $date2 = Carbon::parse($request->clock_out_to)->format('Y-m-d');
                $endOfDay2 = Carbon::parse($date2)->endOfDay()->toDateTimeString();

                $query->whereBetween('clock_out', [$startOfDay1, $endOfDay2]);
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

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
