<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CapstersController;
use App\Http\Controllers\CustomersController;
use App\Http\Controllers\DashboardsController;
use App\Http\Controllers\AttendancesController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\PromosController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\TransactionsTableController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [LoginController::class, 'LoginForm'])->name('login');
    Route::post('/auth', [LoginController::class, 'login']);
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'index'])->middleware('can:products_view');
        Route::get('/index_data', [ProductController::class, 'indexData']);
        Route::post('/get-products', [ProductController::class, 'getProducts'])->middleware('can:products_view');
        Route::post('/store', [ProductController::class, 'store'])->middleware('can:products_create');
        Route::get('/show/{id}', [ProductController::class, 'show'])->middleware('can:products_edit');
        Route::post('/update', [ProductController::class, 'update'])->middleware('can:products_edit');
        Route::post('/delete', [ProductController::class, 'destroy'])->middleware('can:products_delete');
        Route::get('/export', [ProductController::class, 'export'])->middleware('can:products_view');
    });

    Route::prefix('transactions_table')->group(function () {
        Route::get('/', [TransactionsTableController::class, 'index'])->middleware('can:transactions_view');
        Route::post('/index_data', [TransactionsTableController::class, 'indexData'])->middleware('can:transactions_view');
        Route::get('/show/{id}', [TransactionsTableController::class, 'show'])->middleware('can:transactions_edit');
        Route::get('/all_transaction_from_customer/{id}', [TransactionsTableController::class, 'showAllTransactionFromCustomer']);
        Route::post('/update', [TransactionsTableController::class, 'update'])->middleware('can:transactions_edit');
        Route::post('/delete', [TransactionsTableController::class, 'destroy'])->middleware('can:transactions_delete');
        Route::get('/export', [TransactionsTableController::class, 'export'])->middleware('can:transactions_view');
        Route::get('/export_parent', [TransactionsTableController::class, 'export_parent'])->middleware('can:transactions_view');
    });

    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionsController::class, 'index'])->middleware('can:POS');
        Route::post('/store', [TransactionsController::class, 'store'])->middleware('can:POS');
    });

    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomersController::class, 'index'])->middleware('can:customers_view');
        Route::get('/index_data', [CustomersController::class, 'indexData']);
        Route::post('/customers_data', [CustomersController::class, 'customersData']);
        Route::post('/store', [CustomersController::class, 'store'])->middleware('can:customers_create');
        Route::post('/store_ajax', [CustomersController::class, 'storeAjax'])->middleware('can:customers_create');
        Route::get('/show/{id}', [CustomersController::class, 'show'])->middleware('can:customers_edit');
        Route::post('/update', [CustomersController::class, 'update'])->middleware('can:customers_edit');
        Route::post('/delete', [CustomersController::class, 'destroy'])->middleware('can:customers_delete');
        Route::get('/export', [CustomersController::class, 'export'])->middleware('can:customers_view');
    });

    Route::prefix('promos')->group(function () {
        Route::get('/', [PromosController::class, 'index'])->middleware('can:promos_view');
        Route::get('/index_data', [PromosController::class, 'indexData']);
        Route::post('/store', [PromosController::class, 'store'])->middleware('can:promos_create');
        Route::get('/show/{id}', [PromosController::class, 'show'])->middleware('can:promos_edit');
        Route::post('/update', [PromosController::class, 'update'])->middleware('can:promos_edit');
        Route::post('/delete', [PromosController::class, 'destroy'])->middleware('can:promos_delete');
        Route::get('/export', [PromosController::class, 'export'])->middleware('can:promos_view');
    });

    Route::prefix('attendances')->group(function () {
        Route::get('/', [AttendancesController::class, 'index'])->middleware('can:check_in');
        Route::post('/check_in', [AttendancesController::class, 'checkIn'])->middleware('can:check_in');
        
        Route::get('/approval', [AttendancesController::class, 'approval'])->middleware('can:attendances_approval_view');
        Route::get('/approval_index_data', [AttendancesController::class, 'approval_index_data'])->middleware('can:attendances_approval_view');
        Route::post('/approve_or_reject', [AttendancesController::class, 'approve_or_reject'])->middleware('can:attendances_approve_or_reject');

        Route::get('/index_history', [AttendancesController::class, 'index_history'])->middleware('can:attendances_view');
        Route::get('/index_data', [AttendancesController::class, 'indexData'])->middleware('can:attendances_view');
        Route::post('/store', [AttendancesController::class, 'store'])->middleware('can:attendances_create');
        Route::get('/show/{id}', [AttendancesController::class, 'show'])->middleware('can:attendances_edit');
        Route::post('/update', [AttendancesController::class, 'update'])->middleware('can:attendances_edit');
        Route::post('/delete', [AttendancesController::class, 'destroy'])->middleware('can:attendances_delete');
        Route::get('/export', [AttendancesController::class, 'export'])->middleware('can:attendances_view');
    });
    
    Route::prefix('appointments')->group(function () {
        Route::get('/', [AppointmentsController::class, 'index'])->middleware('can:appointments_view');
        Route::post('/index_data', [AppointmentsController::class, 'indexData'])->middleware('can:appointments_view');
        Route::post('/store', [AppointmentsController::class, 'store'])->middleware('can:appointments_create');
        Route::post('/update', [AppointmentsController::class, 'update'])->middleware('can:appointments_edit');
        Route::get('/export', [AppointmentsController::class, 'export'])->middleware('can:appointments_view');
        Route::post('/delete', [AppointmentsController::class, 'destroy'])->middleware('can:appointments_delete');
    });

    Route::prefix('roles')->group(function () {
        Route::get('/', [RolesController::class, 'index'])->middleware('can:roles_view');
        Route::get('/index_data', [RolesController::class, 'indexData'])->middleware('can:roles_view');
        Route::get('/permissions_data', [RolesController::class, 'permissionsTypeData']);
        Route::post('/store', [RolesController::class, 'store'])->middleware('can:roles_create');
        Route::get('/show/{id}', [RolesController::class, 'show'])->middleware('can:roles_edit');
        Route::post('/update', [RolesController::class, 'update'])->middleware('can:roles_edit');
        Route::post('/delete', [RolesController::class, 'destroy'])->middleware('can:roles_delete');
        Route::get('/export', [RolesController::class, 'export'])->middleware('can:roles_view');
    });

    Route::prefix('users')->group(function () {
        Route::get('/', [UsersController::class, 'index'])->middleware('can:users_view');
        Route::get('/index_data', [UsersController::class, 'indexData'])->middleware('can:users_view');
        Route::get('/roles_data', [UsersController::class, 'rolesData']);
        Route::post('/store', [UsersController::class, 'store'])->middleware('can:users_create');
        Route::get('/show/{id}', [UsersController::class, 'show'])->middleware('can:users_edit');
        Route::post('/update', [UsersController::class, 'update'])->middleware('can:users_edit');
        Route::post('/delete', [UsersController::class, 'destroy'])->middleware('can:users_delete');
        Route::get('/export', [UsersController::class, 'export'])->middleware('can:users_view');
    });

    Route::prefix('capsters')->group(function () {
        Route::get('/', [CapstersController::class, 'index'])->middleware('can:capsters_view');
        Route::get('/index_data', [CapstersController::class, 'indexData']);
        Route::post('/store', [CapstersController::class, 'store'])->middleware('can:capsters_create');
        Route::get('/show/{id}', [CapstersController::class, 'show'])->middleware('can:capsters_edit');
        Route::post('/update', [CapstersController::class, 'update'])->middleware('can:capsters_edit');
        Route::post('/delete', [CapstersController::class, 'destroy'])->middleware('can:capsters_delete');
        Route::get('/export', [CapstersController::class, 'export'])->middleware('can:capsters_view');
    });

    Route::prefix('dashboards')->group(function () {
        Route::get('/main', [DashboardsController::class, 'main'])->middleware('can:main_dashboards_views');
        Route::get('/mainLine', [DashboardsController::class, 'mainLine'])->middleware('can:main_dashboards_views');
        Route::get('/mainTotal', [DashboardsController::class, 'mainTotal'])->middleware('can:main_dashboards_views');
        Route::get('/transactions', [DashboardsController::class, 'transactions'])->middleware('can:transactions_dashboards_views');
        Route::get('/capsters', [DashboardsController::class, 'capsters'])->middleware('can:capsters_dashboards_views');
        Route::get('/products', [DashboardsController::class, 'products'])->middleware('can:products_dashboards_views');
        Route::get('/customers', [DashboardsController::class, 'customers'])->middleware('can:customers_dashboards_views');
        Route::get('/summary_payment', [DashboardsController::class, 'summaryPayment'])->middleware('can:summary_payment_dashboards_views');

        Route::get('/main/index_data', [DashboardsController::class, 'mainIndexData'])->middleware('can:main_dashboards_views');
        Route::get('/transactions/index_data', [DashboardsController::class, 'transactionsIndexData'])->middleware('can:transactions_dashboards_views');
        Route::get('/capsters/index_data', [DashboardsController::class, 'capstersIndexData'])->middleware('can:capsters_dashboards_views');
        Route::get('/products/index_data', [DashboardsController::class, 'productsIndexData'])->middleware('can:products_dashboards_views');
        Route::get('/customers/index_data', [DashboardsController::class, 'customersIndexData'])->middleware('can:customers_dashboards_views');
        Route::get('/summary_payment/index_data', [DashboardsController::class, 'summaryPaymentIndexData'])->middleware('can:summary_payment_dashboards_views');

        Route::get('/capsters/export', [DashboardsController::class, 'capstersExport'])->middleware('can:capsters_dashboards_views');
        Route::get('/products/export', [DashboardsController::class, 'productsExport'])->middleware('can:products_dashboards_views');
        Route::get('/customers/export', [DashboardsController::class, 'customersExport'])->middleware('can:customers_dashboards_views');
        Route::get('/summary_payment/export', [DashboardsController::class, 'summaryPaymentExport'])->middleware('can:summary_payment_dashboards_views');

    });
    Route::get('/invoice', [DashboardsController::class, 'invoice']);
});


Route::redirect('/', destination: '/dashboards/main')->name('default_page');

// Dashboard
Route::get('/dashboard-general-dashboard', function () {
    return view('pages.dashboard-general-dashboard', ['type_menu' => 'dashboard']);
});
Route::get('/dashboard-ecommerce-dashboard', function () {
    return view('pages.dashboard-ecommerce-dashboard', ['type_menu' => 'dashboard']);
});


// Layout
Route::get('/layout-default-layout', function () {
    return view('pages.layout-default-layout', ['type_menu' => 'layout']);
});

// Blank Page
Route::get('/blank-page', function () {
    return view('pages.blank-page', ['type_menu' => '']);
});

// Bootstrap
Route::get('/bootstrap-alert', function () {
    return view('pages.bootstrap-alert', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-badge', function () {
    return view('pages.bootstrap-badge', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-breadcrumb', function () {
    return view('pages.bootstrap-breadcrumb', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-buttons', function () {
    return view('pages.bootstrap-buttons', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-card', function () {
    return view('pages.bootstrap-card', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-carousel', function () {
    return view('pages.bootstrap-carousel', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-collapse', function () {
    return view('pages.bootstrap-collapse', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-dropdown', function () {
    return view('pages.bootstrap-dropdown', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-form', function () {
    return view('pages.bootstrap-form', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-list-group', function () {
    return view('pages.bootstrap-list-group', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-media-object', function () {
    return view('pages.bootstrap-media-object', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-modal', function () {
    return view('pages.bootstrap-modal', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-nav', function () {
    return view('pages.bootstrap-nav', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-navbar', function () {
    return view('pages.bootstrap-navbar', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-pagination', function () {
    return view('pages.bootstrap-pagination', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-popover', function () {
    return view('pages.bootstrap-popover', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-progress', function () {
    return view('pages.bootstrap-progress', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-table', function () {
    return view('pages.bootstrap-table', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-tooltip', function () {
    return view('pages.bootstrap-tooltip', ['type_menu' => 'bootstrap']);
});
Route::get('/bootstrap-typography', function () {
    return view('pages.bootstrap-typography', ['type_menu' => 'bootstrap']);
});


// components
Route::get('/components-article', function () {
    return view('pages.components-article', ['type_menu' => 'components']);
});
Route::get('/components-avatar', function () {
    return view('pages.components-avatar', ['type_menu' => 'components']);
});
Route::get('/components-chat-box', function () {
    return view('pages.components-chat-box', ['type_menu' => 'components']);
});
Route::get('/components-empty-state', function () {
    return view('pages.components-empty-state', ['type_menu' => 'components']);
});
Route::get('/components-gallery', function () {
    return view('pages.components-gallery', ['type_menu' => 'components']);
});
Route::get('/components-hero', function () {
    return view('pages.components-hero', ['type_menu' => 'components']);
});
Route::get('/components-multiple-upload', function () {
    return view('pages.components-multiple-upload', ['type_menu' => 'components']);
});
Route::get('/components-pricing', function () {
    return view('pages.components-pricing', ['type_menu' => 'components']);
});
Route::get('/components-statistic', function () {
    return view('pages.components-statistic', ['type_menu' => 'components']);
});
Route::get('/components-tab', function () {
    return view('pages.components-tab', ['type_menu' => 'components']);
});
Route::get('/components-table', function () {
    return view('pages.components-table', ['type_menu' => 'components']);
});
Route::get('/components-user', function () {
    return view('pages.components-user', ['type_menu' => 'components']);
});
Route::get('/components-wizard', function () {
    return view('pages.components-wizard', ['type_menu' => 'components']);
});

// forms
Route::get('/forms-advanced-form', function () {
    return view('pages.forms-advanced-form', ['type_menu' => 'forms']);
});
Route::get('/forms-editor', function () {
    return view('pages.forms-editor', ['type_menu' => 'forms']);
});
Route::get('/forms-validation', function () {
    return view('pages.forms-validation', ['type_menu' => 'forms']);
});

// google maps
// belum tersedia

// modules
Route::get('/modules-calendar', function () {
    return view('pages.modules-calendar', ['type_menu' => 'modules']);
});
Route::get('/modules-chartjs', function () {
    return view('pages.modules-chartjs', ['type_menu' => 'modules']);
});
Route::get('/modules-datatables', function () {
    return view('pages.modules-datatables', ['type_menu' => 'modules']);
});
Route::get('/modules-flag', function () {
    return view('pages.modules-flag', ['type_menu' => 'modules']);
});
Route::get('/modules-font-awesome', function () {
    return view('pages.modules-font-awesome', ['type_menu' => 'modules']);
});
Route::get('/modules-ion-icons', function () {
    return view('pages.modules-ion-icons', ['type_menu' => 'modules']);
});
Route::get('/modules-owl-carousel', function () {
    return view('pages.modules-owl-carousel', ['type_menu' => 'modules']);
});
Route::get('/modules-sparkline', function () {
    return view('pages.modules-sparkline', ['type_menu' => 'modules']);
});
Route::get('/modules-sweet-alert', function () {
    return view('pages.modules-sweet-alert', ['type_menu' => 'modules']);
});
Route::get('/modules-toastr', function () {
    return view('pages.modules-toastr', ['type_menu' => 'modules']);
});
Route::get('/modules-vector-map', function () {
    return view('pages.modules-vector-map', ['type_menu' => 'modules']);
});
Route::get('/modules-weather-icon', function () {
    return view('pages.modules-weather-icon', ['type_menu' => 'modules']);
});

// auth
Route::get('/auth-forgot-password', function () {
    return view('pages.auth-forgot-password', ['type_menu' => 'auth']);
});
Route::get('/auth-login', function () {
    return view('pages.auth-login', ['type_menu' => 'auth']);
});
Route::get('/auth-login2', function () {
    return view('pages.auth-login2', ['type_menu' => 'auth']);
});
Route::get('/auth-register', function () {
    return view('pages.auth-register', ['type_menu' => 'auth']);
});
Route::get('/auth-reset-password', function () {
    return view('pages.auth-reset-password', ['type_menu' => 'auth']);
});

// error
Route::get('/error-403', function () {
    return view('pages.error-403', ['type_menu' => 'error']);
})->name('error-403');
Route::get('/error-404', function () {
    return view('pages.error-404', ['type_menu' => 'error']);
});
Route::get('/error-500', function () {
    return view('pages.error-500', ['type_menu' => 'error']);
});
Route::get('/error-503', function () {
    return view('pages.error-503', ['type_menu' => 'error']);
});

// features
Route::get('/features-activities', function () {
    return view('pages.features-activities', ['type_menu' => 'features']);
});
Route::get('/features-post-create', function () {
    return view('pages.features-post-create', ['type_menu' => 'features']);
});
Route::get('/features-post', function () {
    return view('pages.features-post', ['type_menu' => 'features']);
});
Route::get('/features-profile', function () {
    return view('pages.features-profile', ['type_menu' => 'features']);
});
Route::get('/features-settings', function () {
    return view('pages.features-settings', ['type_menu' => 'features']);
});
Route::get('/features-setting-detail', function () {
    return view('pages.features-setting-detail', ['type_menu' => 'features']);
});
Route::get('/features-tickets', function () {
    return view('pages.features-tickets', ['type_menu' => 'features']);
});

// utilities
Route::get('/utilities-contact', function () {
    return view('pages.utilities-contact', ['type_menu' => 'utilities']);
});
Route::get('/utilities-invoice', function () {
    return view('pages.utilities-invoice', ['type_menu' => 'utilities']);
});
Route::get('/utilities-subscribe', function () {
    return view('pages.utilities-subscribe', ['type_menu' => 'utilities']);
});

// credits
Route::get('/credits', function () {
    return view('pages.credits', ['type_menu' => '']);
});

Route::get('/layout-top-navigation', function () {
    return DB::table('aasd')->first();
});











