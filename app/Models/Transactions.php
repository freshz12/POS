<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transactions extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $primaryKey = 'id';

    protected $table = 'transactions';

    protected $casts = [
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'created_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function scopeFilterIndex($query, $request)
    {
        return $query->when($request->customer_name, function ($query) use ($request) {
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

        ->when($request->created_at, function ($query) use ($request) {
            $date = Carbon::parse($request->created_at)->format('Y-m-d');
            $startOfDay = Carbon::parse($date)->startOfDay()->toDateTimeString();
            $endOfDay = Carbon::parse($date)->endOfDay()->toDateTimeString();

            $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
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

    public function transaction_products()
    {
        return $this->hasMany(TransactionProducts::class, 'transaction_id');
    }

    public function capster()
    {
        return $this->belongsTo(Capsters::class, 'capster_id');
    }
}
