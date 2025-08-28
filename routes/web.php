<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\system\CarController;
use App\Http\Controllers\system\ClearanceOfficeController;
use App\Http\Controllers\system\ContainerFlowController;
use App\Http\Controllers\system\CustomsDeclarationController;
use App\Http\Controllers\system\DailyTransactionController;
use App\Http\Controllers\system\mainController;
use App\Http\Controllers\system\RoleController;
use App\Http\Controllers\system\TipController;
use App\Http\Controllers\system\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('custom-login', [AuthController::class, 'customLogin'])->name('login.custom');
Route::post('custom-registration', [AuthController::class, 'customRegistration'])->name('register.custom');
Route::get('signout', [AuthController::class, 'signOut'])->name('signout');



Route::group(['prefix' => 'system', 'middleware' => ['auth']], function () {

    Route::get('home', [mainController::class, 'index'])->name('home');

    Route::resource('users', UserController::class);

    Route::resource('roles', RoleController::class);
    Route::resource('clearance-offices', ClearanceOfficeController::class)->parameters(['clearance-offices' => 'clearance_office']);

    Route::post('customs-declarations', [CustomsDeclarationController::class, 'store'])->name('customs-declarations.store');

    Route::resource('daily-transactions', DailyTransactionController::class)->names('transactions');
    Route::get('get-transactionable-records', [DailyTransactionController::class, 'getTransactionableRecords'])->name('transactions.get_records');

    Route::resource('cars', CarController::class);

    Route::get('/containers/flow', [ContainerFlowController::class, 'index'])->name('containers.flow.index');
    // تغيير حالة الحاوية + إنشاء Tip اختياريًا
    Route::post('/containers/{container}/flow/change', [ContainerFlowController::class, 'change'])->name('containers.flow.change');
    // AJAX: سيارات السائق
    Route::get('/containers/flow/drivers/{driver}/cars', [ContainerFlowController::class, 'carsByDriver'])->name('containers.flow.driverCars');
});
