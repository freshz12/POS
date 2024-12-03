<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $table = 'products';

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }

    public function scopeFilterIndex($query, $request)
    {
        return $query->when($request->product_name, function ($query) use ($request) {
            $query->where('product_name', 'LIKE', '%' . $request->product_name . '%');
        })

        ->when($request->selling_price, function ($query) use ($request) {
            $query->where('selling_price', 'LIKE', '%' . $request->selling_price . '%');
        })

        ->when($request->type, function ($query) use ($request) {
            $query->where('type', 'LIKE', '%' . $request->type . '%');
        })

        ->when($request->unit_of_measurement, function ($query) use ($request) {
            $query->where('unit_of_measurement', 'LIKE', '%' . $request->unit_of_measurement . '%');
        })

        ->when($request->is_included_in_receipt !== null, function ($query) use ($request) {
            $query->where('is_included_in_receipt', intval($request->is_included_in_receipt));
        })

        ->when($request->is_custom_price !== null, function ($query) use ($request) {
            $query->where('is_custom_price', intval($request->is_custom_price));
        })

        ->when($request->quantity, function ($query) use ($request) {
            $query->where('quantity', 'LIKE', '%' . $request->quantity . '%');
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
