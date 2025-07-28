<?php

use App\Http\Controllers\Backend\AppointmentController;
use App\Http\Controllers\Backend\AuthController;
use App\Http\Controllers\Backend\BirthDayController;
use App\Http\Controllers\Backend\BulkActionController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ContractController;
use App\Http\Controllers\Backend\ContractTypeController;
use App\Http\Controllers\Backend\CustomerCareController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\EmployeeController;
use App\Http\Controllers\Backend\EmployeeMonthlyWorkdayController;
use App\Http\Controllers\Backend\MediaItemController;

use App\Http\Controllers\Backend\MonthlyWorkdayController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\PayrollController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\SettingController;
use App\Models\Employee;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['admin.auth', 'subdomain'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/filter', [DashboardController::class, 'filterDashboard'])->name('dashboard.filter');

    Route::post('handle-bulk-action', [BulkActionController::class, 'handleBulkAction']);

    Route::get('logout', [AuthController::class, 'logout']);

    Route::group(['middleware' => ['admin'], 'prefix' => 'employees', 'controller' => EmployeeController::class], function () {
        Route::get('/', 'index');
        Route::get('save/{id?}', 'save');
        Route::post('save', 'store');
        Route::put('save/{id}', 'update');
        Route::get('/view/{id}', 'view')->name('view');
        Route::get('/information', 'information')->name('information');
        Route::get('permissions', 'showPermissionForm');
        Route::post('permissions', 'assignPermissions');
    });

    Route::group(['prefix' => 'customers', 'controller' => CustomerController::class], function () {
        Route::get('/', 'index');

        Route::middleware('restrict.admin.write')->group(function () {
            Route::get('save/{id?}', 'save');
            Route::post('save', 'store');
            Route::put('save/{id}', 'update');
        });
    });

    Route::group(['prefix' => 'orders', 'controller' => OrderController::class], function () {
        Route::get('/', 'index');
        Route::middleware('restrict.admin.write')->group(function () {
            Route::get('save/{id?}', 'save');
            Route::post('save', 'store');
            Route::put('save/{id}', 'update');
        });
    });


    Route::group(['middleware' => ['admin'], 'prefix' => 'categorys', 'controller' => CategoryController::class], function () {
        Route::get('/', 'index')->name('index');
        Route::post('/excel-save', 'updateOrCreate');
        Route::delete('/excel-delete', 'destroy');
    });


    Route::group(['middleware' => ['admin'], 'prefix' => 'settings', 'controller' => SettingController::class], function () {
        Route::get('/', 'index');
        Route::post('/', 'save');
    });

    Route::group(['prefix' => 'notifications', 'controller' => NotificationController::class], function () {
        Route::get('/', 'index');
        Route::post('send', 'send');
    });

    Route::group(['prefix' => 'apppointment', 'controller' => AppointmentController::class], function () {
        Route::get('/', 'index');
        Route::get('save/{id?}', 'save');
        Route::put('save/{id}', 'update');
        Route::post('save', 'store');
        Route::get('/view/{id}', 'view');
        Route::post('/update-status', 'updateStatus');
        Route::post('/delete', 'delete');
        Route::get('/{id}/edit-data','editData');
    });

    Route::group(['prefix' => 'customer_care', 'controller' => CustomerCareController::class], function () {
        Route::get('/', 'index');
        Route::get('save/{id?}', 'save');
        Route::put('save/{id}', 'update');
        Route::post('save', 'store');
        Route::get('/view/{id}', 'view');
        Route::post('/update-status', 'updateStatus');
        Route::post('/delete', 'delete');
        Route::get('/{id}/edit-data','editData');
    });
});


Route::middleware(['admin.guest','subdomain'])->group(function () {
    Route::get('login', [AuthController::class, 'login'])->name('login');
    Route::post('login', [AuthController::class, 'authenticate']);
});

Route::get('', [AuthController::class, 'registerFrom']);
Route::post('dang-ky', [AuthController::class, 'register']);
Route::get('/dang-ky-thanh-cong', [AuthController::class, 'registerSuccess'])->name('register.success');
