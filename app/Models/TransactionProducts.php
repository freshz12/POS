<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Products;
use App\Models\Transactions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionProducts extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $table = 'transaction_products';

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->setTimezone('Asia/Jakarta')->format('Y-m-d H:i:s');
    }

    public function scopeFilterIndex($query, $request)
    {
        return $query->whereHas('transaction', function ($query) use ($request) {
            $query->when($request->customer_name, function ($query) use ($request) {
                $query->whereHas('customers', function ($query) use ($request) {
                    $query->where('full_name', 'like', '%' . $request->customer_name . '%');
                });
            })
            ->when($request->capster_name, function ($query) use ($request) {
                $query->whereHas('capster', function ($query) use ($request) {
                    $query->where('full_name', 'like', '%' . $request->capster_name . '%');
                });
            })
            ->when($request->transaction_id, function ($query) use ($request) {
                $query->where('transaction_id', 'LIKE', '%' . $request->transaction_id . '%');
            })
            ->when($request->total_amount, function ($query) use ($request) {
                $query->where('amount', 'LIKE', '%' . $request->total_amount . '%');
            })
            ->when($request->created_at_from && $request->created_at_to, function ($query) use ($request) {
                $date1 = Carbon::parse($request->created_at_from)->format('Y-m-d');
                $startOfDay1 = Carbon::parse($date1)->startOfDay()->toDateTimeString();
    
                $date2 = Carbon::parse($request->created_at_to)->format('Y-m-d');
                $endOfDay2 = Carbon::parse($date2)->endOfDay()->toDateTimeString();
    
                $query->whereBetween('created_at', [$startOfDay1, $endOfDay2]);
            })
            ->when($request->created_at, function ($query) use ($request) {
                $date = Carbon::parse($request->created_at)->format('Y-m-d');
                $startOfDay = Carbon::parse($date)->startOfDay()->toDateTimeString();
                $endOfDay = Carbon::parse($date)->endOfDay()->toDateTimeString();

                $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
            });
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

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
