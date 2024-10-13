<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Appointments extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $table = 'appointments';

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }

    public function scopeFilterIndex($query, $request)
    {
        return $query->when($request->full_name, function ($query) use ($request) {
            $query->where('full_name', 'LIKE', '%' . $request->full_name . '%');
        })

            ->when($request->gender, function ($query) use ($request) {
                if ($request->gender == 'B') {
                    $query->whereIn('gender', ['F', 'M']);
                } else {
                    $query->where('gender', 'LIKE', '%' . $request->gender . '%');
                }
            })

            ->when($request->email, function ($query) use ($request) {
                $query->where('email', 'LIKE', '%' . $request->email . '%');
            })

            ->when($request->phone_number, function ($query) use ($request) {
                $query->where('phone_number', 'LIKE', '%' . $request->phone_number . '%');
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

    public function customers()
    {
        return $this->belongsTo(Customers::class, 'customer_id');
    }

    public function capster()
    {
        return $this->belongsTo(Capsters::class, 'capster_id');
    }
}
