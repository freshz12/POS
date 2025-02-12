<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\TransactionProducts;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardsCapstersExport;
use App\Exports\DashboardsProductsExport;
use App\Exports\DashboardsCustomersExport;
use App\Exports\DashboardsSummaryPaymentExport;

class DashboardsController extends Controller
{

    public function main()
    {
        return view('pages.dashboards.main.main', ['type_menu' => 'main_dashboard']);
    }

    public function invoice()
    {
        return view('pages.invoice.invoice.invoice', ['type_menu' => 'invoice']);
    }


    public function mainIndexData(Request $request)
    {
        $customers = Transactions::select(
            'customers.full_name as customer_name',
            DB::raw('SUM(transactions.amount) as total_spent'),
            DB::raw('COUNT(transactions.id) as total_transactions')
        )
            ->join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->whereNull('transactions.deleted_at')
            ->groupBy('customers.full_name')
            ->orderBy('total_spent', 'desc')
            ->limit(5)
            ->get();

        $pieChartData1 = $customers->pluck('total_spent')->toArray();
        $pieChartLabels1 = $customers->pluck('customer_name')->toArray();
        $pieChartAdditionals1 = $customers->pluck('total_transactions')->toArray();

        $capsters = Transactions::select(
            'capsters.full_name as capster_name',
            DB::raw('SUM(transactions.amount) as total_amount'),
            DB::raw('COUNT(transactions.id) as total_transactions')
        )
            ->join('capsters', 'transactions.capster_id', '=', 'capsters.id')
            ->whereNull('transactions.deleted_at')
            ->groupBy('capsters.full_name')
            ->orderBy('total_transactions', 'desc') // Order by total transactions
            ->limit(5)
            ->get();

        $pieChartData2 = $capsters->pluck('total_transactions')->toArray();
        $pieChartLabels2 = $capsters->pluck('capster_name')->toArray();
        $pieChartAdditionals2 = $capsters->pluck('total_amount')->toArray();


        $products = TransactionProducts::select(
            'products.product_name as product_name',
            DB::raw('SUM(transaction_products.quantity) as total_sold'),
            'products.selling_price as unit_price'
        )
            ->join('products', 'transaction_products.product_id', '=', 'products.id')
            ->whereNull('transaction_products.deleted_at')
            ->groupBy('products.id', 'products.product_name', 'products.selling_price')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Prepare data for the third pie chart
        $pieChartData3 = $products->pluck('total_sold')->toArray();
        $pieChartLabels3 = $products->pluck('product_name')->toArray();
        $pieChartAdditionals3 = $products->pluck('unit_price')->toArray();

        return response()->json([
            'pieChartData1' => $pieChartData1,
            'pieChartLabels1' => $pieChartLabels1,
            'pieChartAdditionals1' => $pieChartAdditionals1,
            'pieChartData2' => $pieChartData2,
            'pieChartLabels2' => $pieChartLabels2,
            'pieChartAdditionals2' => $pieChartAdditionals2,
            'pieChartData3' => $pieChartData3,
            'pieChartLabels3' => $pieChartLabels3,
            'pieChartAdditionals3' => $pieChartAdditionals3,
        ]);
    }

    public function mainLine(Request $request)
    {
        $range = $request->input('range');


        switch ($range) {
            case 'today':
                $transactions = Transactions::select(
                    DB::raw('SUM(transactions.amount) as total_spent'),
                    DB::raw('FLOOR(DATEPART(HOUR, transactions.created_at) / 2) * 2 as transaction_interval') // Grouping by 2-hour intervals
                )
                    ->whereDate('transactions.created_at', Carbon::now('Asia/Jakarta')->toDateString())
                    ->whereNull('transactions.deleted_at')
                    ->groupBy(DB::raw('FLOOR(DATEPART(HOUR, transactions.created_at) / 2) * 2'))
                    ->orderBy('transaction_interval')
                    ->get();

                // Create labels for 2-hour intervals
                $labelArray = [];
                for ($i = 0; $i < 24; $i += 2) {
                    $labelArray[] = Carbon::createFromTime($i, 0, 0, 'Asia/Jakarta')->format('H:i');
                }

                // Initialize data array with zeros for each interval
                $dataArray = array_fill(0, 12, 0); // 12 intervals for a 24-hour day

                // Fill data array with actual transaction totals
                foreach ($transactions as $transaction) {
                    $index = $transaction->transaction_interval / 2; // 0 for 00:00, 1 for 02:00, etc.
                    $dataArray[$index] = $transaction->total_spent; // Set total spent for the corresponding interval
                }

                break;

            case 'week':
                $transactions = Transactions::select(
                    DB::raw('SUM(transactions.amount) as total_spent'),
                    DB::raw('CAST(transactions.created_at AS DATE) as transaction_date')
                )
                    ->whereBetween('transactions.created_at', [
                        Carbon::now('Asia/Jakarta')->startOfWeek(),
                        Carbon::now('Asia/Jakarta')->endOfWeek()
                    ])
                    ->whereNull('transactions.deleted_at')
                    ->groupBy(DB::raw('CAST(transactions.created_at AS DATE)'))
                    ->orderBy('transaction_date')
                    ->get();

                // Create an array for all days of the week
                $allDays = [];
                for ($i = 0; $i < 7; $i++) {
                    $allDays[] = Carbon::now('Asia/Jakarta')->startOfWeek()->addDays($i)->format('Y-m-d');
                }

                $dataArray = array_fill(0, 7, 0); // Initialize data array with zeros
                $labelArray = [];

                // Create labels for the days of the week
                foreach ($allDays as $day) {
                    $labelArray[] = Carbon::parse($day)->format('l'); // Get the day name
                }

                // Fill data array with actual transaction totals
                foreach ($transactions as $transaction) {
                    $index = array_search($transaction->transaction_date, $allDays);
                    if ($index !== false) {
                        $dataArray[$index] = $transaction->total_spent; // Set the total spent for the corresponding day
                    }
                }

                return response()->json([
                    'lineChartData' => $dataArray,
                    'lineChartLabels' => $labelArray
                ]);


            case 'month':
                // Get the current month and year
                $currentMonth = Carbon::now('Asia/Jakarta')->month;
                $currentYear = Carbon::now('Asia/Jakarta')->year;

                // Get the number of days in the current month
                $daysInMonth = Carbon::createFromDate($currentYear, $currentMonth)->daysInMonth;

                // Fetch transactions grouped by day
                $transactions = Transactions::select(
                    DB::raw('SUM(transactions.amount) as total_spent'),
                    DB::raw('DAY(transactions.created_at) as transaction_day')
                )
                    ->whereMonth('transactions.created_at', $currentMonth)
                    ->whereYear('transactions.created_at', $currentYear)
                    ->whereNull('transactions.deleted_at')
                    ->groupBy(DB::raw('DAY(transactions.created_at)'))
                    ->orderBy('transaction_day')
                    ->get();

                // Create an array for all days of the month
                $dataArray = array_fill(0, $daysInMonth, 0); // Initialize with zeros
                $labelArray = [];

                // Generate labels for each day of the month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $labelArray[] = $day; // Day numbers (1 to daysInMonth)
                }

                // Fill data array with transaction totals
                foreach ($transactions as $transaction) {
                    $dataArray[$transaction->transaction_day - 1] = $transaction->total_spent; // Set total spent for the corresponding day
                }

                return response()->json([
                    'lineChartData' => $dataArray,
                    'lineChartLabels' => $labelArray
                ]);


            case 'year':
                // Get the current year
                $currentYear = Carbon::now('Asia/Jakarta')->year;

                // Fetch transactions grouped by month
                $transactions = Transactions::select(
                    DB::raw('SUM(transactions.amount) as total_spent'),
                    DB::raw('MONTH(transactions.created_at) as transaction_month')
                )
                    ->whereYear('transactions.created_at', $currentYear)
                    ->whereNull('transactions.deleted_at')
                    ->groupBy(DB::raw('MONTH(transactions.created_at)'))
                    ->orderBy('transaction_month')
                    ->get();

                // Create an array for all months (12 months)
                $dataArray = array_fill(0, 12, 0); // Initialize with zeros
                $labelArray = [];

                // Generate labels for each month
                for ($month = 1; $month <= 12; $month++) {
                    $labelArray[] = Carbon::create()->month($month)->format('F'); // Month names (January to December)
                }

                // Fill data array with transaction totals
                foreach ($transactions as $transaction) {
                    $dataArray[$transaction->transaction_month - 1] = $transaction->total_spent; // Set total spent for the corresponding month
                }

                return response()->json([
                    'lineChartData' => $dataArray,
                    'lineChartLabels' => $labelArray
                ]);


            case 'custom':
                $startDate = Carbon::parse($request->input('start_date'));
                $endDate = Carbon::parse($request->input('end_date'));

                $diff = $startDate->diffInDays($endDate);

                if ($diff <= 1) {
                    $transactions = Transactions::select(
                        DB::raw('SUM(transactions.amount) as total_spent'),
                        DB::raw('CAST(transactions.created_at AS DATE) as transaction_date')
                    )
                        ->whereBetween('transactions.created_at', [
                            $startDate->setTimezone('Asia/Jakarta')->startOfDay(),
                            $endDate->setTimezone('Asia/Jakarta')->endOfDay()
                        ])
                        ->whereNull('transactions.deleted_at')
                        ->groupBy(DB::raw('CAST(transactions.created_at AS DATE)'))
                        ->orderBy('transaction_date')
                        ->get();
                } elseif ($diff < 7) {
                    $transactions = Transactions::select(
                        DB::raw('SUM(transactions.amount) as total_spent'),
                        DB::raw('CAST(transactions.created_at AS DATE) as transaction_date')
                    )
                        ->whereBetween('transactions.created_at', [
                            Carbon::now('Asia/Jakarta')->startOfWeek(),
                            Carbon::now('Asia/Jakarta')->endOfWeek()
                        ])
                        ->whereNull('transactions.deleted_at')
                        ->groupBy(DB::raw('CAST(transactions.created_at AS DATE)'))
                        ->orderBy('transaction_date')
                        ->get();
                } elseif ($diff <= 30) {
                    $transactions = Transactions::select(
                        DB::raw('SUM(transactions.amount) as total_spent'),
                        DB::raw('CAST(transactions.created_at AS DATE) as transaction_date')
                    )
                        ->whereBetween('transactions.created_at', [
                            Carbon::now('Asia/Jakarta')->startOfMonth(),
                            Carbon::now('Asia/Jakarta')->endOfMonth()
                        ])
                        ->whereNull('transactions.deleted_at')
                        ->groupBy(DB::raw('CAST(transactions.created_at AS DATE)'))
                        ->orderBy('transaction_date')
                        ->get();
                } else {
                    // Custom date range
                    $transactions = Transactions::select(
                        DB::raw('SUM(transactions.amount) as total_spent'),
                        DB::raw('CAST(transactions.created_at AS DATE) as transaction_date')
                    )
                        ->whereBetween('transactions.created_at', [$startDate, $endDate])
                        ->whereNull('transactions.deleted_at')
                        ->groupBy(DB::raw('CAST(transactions.created_at AS DATE)'))
                        ->orderBy('transaction_date')
                        ->get();
                }

                $dataArray = $transactions->pluck('total_spent')->toArray();
                $labelArray = $transactions->pluck('transaction_date')->toArray();
                break;
            default:
                return response()->json(['error' => 'Invalid range'], 400);
        }

        return response()->json([
            'lineChartData' => array_values($dataArray),
            'lineChartLabels' => array_values($labelArray)
        ]);
    }

    public function mainTotal(Request $request)
    {
        $range = $request->input('range');
        $startDate = null;
        $endDate = null;

        switch ($range) {
            case 'today':
                $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
                $endDate = Carbon::now('Asia/Jakarta')->endOfDay();
                break;
            case 'week':
                $startDate = Carbon::now('Asia/Jakarta')->startOfWeek();
                $endDate = Carbon::now('Asia/Jakarta')->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now('Asia/Jakarta')->startOfMonth();
                $endDate = Carbon::now('Asia/Jakarta')->endOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now('Asia/Jakarta')->startOfYear();
                $endDate = Carbon::now('Asia/Jakarta')->endOfYear();
                break;
            case 'custom':
                $startDate = Carbon::parse($request->input('start_date'))->setTimezone('Asia/Jakarta')->startOfDay();
                $endDate = Carbon::parse($request->input('end_date'))->setTimezone('Asia/Jakarta')->endOfDay();
                break;
            default:
                return response()->json(['error' => 'Invalid range'], 400);
        }

        // Query to get total transactions and total amount
        $totalTransactions = Transactions::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $totalAmount = Transactions::whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('amount');

        return response()->json([
            'total_transactions' => $totalTransactions,
            'total_amount' => $totalAmount,
        ]);
    }

    public function transactions()
    {
        return view('pages.dashboards.transactions.transactions', ['type_menu' => 'transactions_dashboard']);
    }

    public function transactionsIndexData(Request $request)
    {
        $transactions = Transactions::filterIndex($request)->orderBy('id', 'desc')->get();

        return response()->json(['data' => $transactions]);
    }

    public function capsters()
    {
        return view('pages.dashboards.capsters.capsters', ['type_menu' => 'capsters_dashboard']);
    }

    public function capstersIndexData(Request $request)
    {
        $capsters = Transactions::join('capsters', 'transactions.capster_id', '=', 'capsters.id')
            ->selectRaw('capsters.full_name as capster_name, transactions.capster_id, SUM(transactions.amount) as total_amount, COUNT(*) as total_transactions')
            ->groupBy('capsters.full_name', 'transactions.capster_id')
            ->when($request->capster_name, function ($query) use ($request) {
                $query->where('capsters.full_name', 'like', '%' . $request->capster_name . '%');
            })
            ->when($request->created_at_from || $request->created_at_to || $request->created_at, function ($query) use ($request) {
                $timezone = 'Asia/Jakarta';
                if (!$request->created_at_from && !$request->created_at_to) {
                    if ($request->created_at === 'daily') {
                        $query->whereBetween('transactions.created_at', [
                            Carbon::now($timezone)->startOfDay(),
                            Carbon::now($timezone)->endOfDay()
                        ]);
                    } elseif ($request->created_at === 'weekly') {
                        $query->whereBetween('transactions.created_at', [
                            Carbon::now($timezone)->subDays(7)->startOfDay(),
                            Carbon::now($timezone)->endOfDay()
                        ]);
                    } elseif ($request->created_at === 'monthly') {
                        $query->whereBetween('transactions.created_at', [
                            Carbon::now($timezone)->startOfMonth(),
                            Carbon::now($timezone)->endOfMonth()
                        ]);
                    }
                } else if ($request->created_at_from && $request->created_at_to) {
                    $query->whereBetween('transactions.created_at', [
                        Carbon::parse($request->created_at_from)->setTimezone($timezone)->startOfDay(),
                        Carbon::parse($request->created_at_to)->setTimezone($timezone)->endOfDay()
                    ]);
                }
            })
            ->when($request->total_amount, function ($query) use ($request) {
                $query->havingRaw('SUM(transactions.amount) LIKE ?', ['%' . $request->total_amount . '%']);
            })
            ->when($request->total_transactions, function ($query) use ($request) {
                $query->havingRaw('COUNT(*) LIKE ?', ['%' . $request->total_transactions . '%']);
            })
            ->orderBy('total_transactions', 'desc')
            ->get();

        return response()->json(['data' => $capsters]);
    }

    public function capstersExport(Request $request)
    {
        try {
            return Excel::download(new DashboardsCapstersExport($request), 'dashboards_capsters.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function products()
    {
        return view('pages.dashboards.products.products', ['type_menu' => 'products_dashboard']);
    }

    public function productsIndexData(Request $request)
    {
        $products = TransactionProducts::join('products', 'transaction_products.product_id', '=', 'products.id')
            ->join('transactions', 'transaction_products.transaction_id', '=', 'transactions.id')
            ->selectRaw('products.product_name as product_name, transaction_products.product_id, SUM(transaction_products.quantity) as total_sold_quantity, products.quantity as quantity_left')
            ->groupBy('products.product_name', 'transaction_products.product_id', 'products.quantity')
            ->when($request->product_name, function ($query) use ($request) {
                $query->where('products.product_name', 'like', '%' . $request->product_name . '%');
            })
            ->when($request->total_sold_quantity, function ($query) use ($request) {
                $query->havingRaw('SUM(transaction_products.quantity) LIKE ?', ['%' . $request->total_sold_quantity . '%']);
            })
            ->when($request->quantity_left, function ($query) use ($request) {
                $query->where('products.quantity', 'like', '%' . $request->quantity_left . '%');
            })
            ->orderBy('total_sold_quantity', 'desc')
            ->get();

        return response()->json(['data' => $products]);
    }

    public function productsExport(Request $request)
    {
        try {
            return Excel::download(new DashboardsProductsExport($request), 'dashboards_products.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function customers()
    {
        return view('pages.dashboards.customers.customers', ['type_menu' => 'customers_dashboard']);
    }

    public function customersIndexData(Request $request)
    {
        $customers = Transactions::join('customers', 'transactions.customer_id', '=', 'customers.id')
            ->selectRaw('customers.full_name as customer_name, 
                 customers.phone_number as phone_number,
                 customers.id as id,
                 SUM(transactions.amount) as total_spent, 
                 COUNT(transactions.id) as total_transactions')
            ->groupBy('customers.phone_number', 'customers.id', 'customers.full_name')
            ->when($request->customer_name, function ($query) use ($request) {
                $query->where('customers.full_name', 'like', '%' . $request->customer_name . '%');
            })
            ->when($request->phone_number, function ($query) use ($request) {
                $query->where('customers.phone_number', 'like', '%' . $request->phone_number . '%');
            })
            ->when($request->total_spent, function ($query) use ($request) {
                $query->havingRaw('SUM(transactions.amount) LIKE ?', ['%' . $request->total_spent . '%']);
            })
            ->when($request->total_transactions, function ($query) use ($request) {
                $query->havingRaw('COUNT(transactions.id) LIKE ?', ['%' . $request->total_transactions . '%']);
            })
            ->orderBy('total_spent', 'desc')
            ->get();

        return response()->json(['data' => $customers]);
    }

    public function customersExport(Request $request)
    {
        try {
            return Excel::download(new DashboardsCustomersExport($request), 'dashboards_customers.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }

    public function summaryPayment()
    {
        return view('pages.dashboards.summary_payment.summary_payment', ['type_menu' => 'summary_payment_dashboard']);
    }

    public function summaryPaymentIndexData(Request $request)
    {
        $period = $request->input('created_type', 'today');
        $startDate = Carbon::now('Asia/Jakarta');
        $endDate = Carbon::now('Asia/Jakarta');

        switch ($period) {
            case 'today':
                $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
                $endDate = Carbon::now('Asia/Jakarta')->endOfDay();
                break;

            case 'this_week':
                $startDate = Carbon::now('Asia/Jakarta')->startOfWeek();
                $endDate = Carbon::now('Asia/Jakarta')->endOfWeek();
                break;

            case 'this_month':
                $startDate = Carbon::now('Asia/Jakarta')->startOfMonth();
                $endDate = Carbon::now('Asia/Jakarta')->endOfMonth();
                break;

            case 'this_year':
                $startDate = Carbon::now('Asia/Jakarta')->startOfYear();
                $endDate = Carbon::now('Asia/Jakarta')->endOfYear();
                break;

            case 'custom':
                if (!$request->input('created_from_filter') || !$request->input('created_to_filter')) {
                    $startDate = Carbon::now('Asia/Jakarta')->startOfDay();
                    $endDate = Carbon::now('Asia/Jakarta')->endOfDay();
                } else {
                    $startDate = $request->input('created_from_filter')
                        ? Carbon::createFromFormat('Y-m-d', $request->input('created_from_filter'), 'Asia/Jakarta')->startOfDay()
                        : Carbon::now('Asia/Jakarta')->startOfDay();
                    $endDate = $request->input('created_to_filter')
                        ? Carbon::createFromFormat('Y-m-d', $request->input('created_to_filter'), 'Asia/Jakarta')->endOfDay()
                        : Carbon::now('Asia/Jakarta')->endOfDay();
                }

                break;

            default:
                return response()->json(['error' => 'Invalid period selected'], 400);
        }

        $periodeTransaksi = $startDate->isSameDay($endDate)
            ? $startDate->translatedFormat('j F Y')
            : $startDate->translatedFormat('j F Y') . ' to ' . $endDate->translatedFormat('j F Y'); // Date range

        $summary_payment = Transactions::selectRaw('
                ? AS transaction_period,
                COUNT(id) AS total_customer,
                SUM(amount) AS total_amount,
                SUM(CASE WHEN payment_method = \'Cash\' THEN amount ELSE 0 END) AS total_cash,
                SUM(CASE WHEN payment_method = \'EDC\' THEN amount ELSE 0 END) AS total_edc,
                SUM(CASE WHEN payment_method = \'QRIS\' THEN amount ELSE 0 END) AS total_qris
            ', [$periodeTransaksi])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->first();

        return response()->json(['data' => [$summary_payment]]);
    }

    public function summaryPaymentExport(Request $request)
    {
        try {
            return Excel::download(new DashboardsSummaryPaymentExport($request), 'dashboards_summary_payment.xlsx');
        } catch (\Exception $e) {
            return back()->withErrors([
                'error_message' => 'Something went wrong, please contact administrator',
            ]);
        }
    }
}
