<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\MainController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('driver/login',[LoginController::class, 'driverLogin'])->name('driverLogin');
Route::group( ['prefix' => 'driver','middleware' => ['auth:driver-api','scopes:driver'] ],function(){
   // authenticated staff routes here 
    Route::get('logout',[LoginController::class, 'driverLogout']);
    Route::get('checktoken',[LoginController::class, 'checktoken']);
    Route::post('getRides', [MainController::class, 'getRides'])->name('getRides');

    //create ride Ayig
    Route::post('StartMovingAyigRide', [MainController::class, 'StartMovingAyigRide'])->name('StartMovingAyigRide');
    Route::post('ArrivedToCustomerAyig', [MainController::class, 'ArrivedToCustomerAyig'])->name('ArrivedToCustomerAyig');
    Route::post('StartAyigRide', [MainController::class, 'StartAyigRide'])->name('StartAyigRide');
    Route::post('InfoDriverAyigRide', [MainController::class, 'InfoDriverAyigRide'])->name('InfoDriverAyigRide');
    Route::post('EndUserAyigRide', [MainController::class, 'EndUserAyigRide'])->name('EndUserAyigRide');
    Route::post('CreateRideAyig', [MainController::class, 'CreateRideAyig'])->name('CreateRideAyig');
    Route::post('AcceptRideAyig', [MainController::class, 'AcceptRideAyig'])->name('AcceptRideAyig');
    
    Route::post('DriverIsOnline', [MainController::class, 'DriverIsOnline'])->name('DriverIsOnline');

    Route::post('getUserInfo', [MainController::class, 'getUserInfo'])->name('getUserInfo');
    
    //update user account
    Route::post('updateAccount', [AccountController::class, 'updateAccount'])->name('updateAccount');
    //update user password
    Route::post('updatePassword', [AccountController::class, 'updatePassword'])->name('updatePassword');
    
});  