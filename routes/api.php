<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//Auth Route
Route::controller(AuthController::class)->group(function () {
    Route::post('store', 'store');
    Route::post('/login', 'login');
    Route::post('/forgotPasswordLink', 'forgotPasswordLink');
    Route::post('/forgotPassword', 'forgotPassword');
    Route::get('list', 'index');
});
//middleware Route
//User Route
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::controller(UserController::class)->prefix('user')->group(function () {
    Route::post('/logout','logout');
    Route::get('show/{id?}', 'show');
    Route::delete('delete/{id}', 'delete');
    Route::put('update', 'update');
    Route::post('/changepassword','changePassword');
    });
//Employee Route
Route::controller(EmployeeController::class)->prefix('employee')->group(function () {
    Route::post('/add', 'store')->middleware('checkemployee:employee,add_access');
    Route::get('/show/{id}', 'show')->middleware('checkemployee:employee,view_access');
    Route::put('/update', 'update')->middleware('checkemployee:employee,edit_access');
    Route::delete('/delete/{id}', 'delete')->middleware('checkemployee:employee,delete_access');

    });
//Job Route
Route::controller(JobController::class)->prefix('job')->group(function () {
    Route::post('/add', 'store')->middleware('checkemployee:job,add_access');
    Route::get('/show/{id}', 'show')->middleware('checkemployee:job,view_access');
    Route::put('/update', 'update')->middleware('checkemployee:job,edit_access');
    Route::delete('/delete/{id}','delete')->middleware('checkemployee:job,delete_access');
    });
});
//Module Route
Route::controller(ModuleController::class)->prefix('module')->group(function () {
    Route::post('store', 'store')->name('module.add');
    Route::get('show/{id?}', 'show')->name('module.view');
    Route::delete('delete/{id}', 'delete')->name('module.delete');
    Route::put('update/{id}', 'update')->name('module.update');
    Route::get('list', 'index');
});
//Permission Route
Route::controller(PermissionController::class)->prefix('permission')->group(function () {
    Route::post('store', 'store')->name('permission.add');
    Route::get('show/{id}', 'show')->name('permission.view');
    Route::delete('delete/{id}', 'delete')->name('permission.delete');
    Route::put('update/{id}', 'update')->name('permission.update');
    Route::get('list', 'index');
});
//Role route
Route::controller(RoleController::class)->prefix('role')->group(function () {
    Route::post('store', 'store')->name('role.add');
    Route::get('show/{id}', 'show')->name('role.view');
    Route::delete('delete/{id}', 'delete')->name('role.delete');
    Route::put('update', 'update')->name('role.update');
    Route::get('list', 'index');
});

















