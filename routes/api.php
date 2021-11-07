<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('user/register', [AuthController::class, 'register'])->name('user.register');
Route::post('user/token', [AuthController::class, 'token'])->name('user.token');
Route::middleware(['auth:sanctum'])->group(function (){
    Route::post('loan/store', [LoanController::class, 'store'])->name('loan.store');
    Route::post('loan/approve', [LoanController::class, 'approve'])->name('loan.approve');
    Route::post('loan/pay', [LoanController::class, 'pay'])->name('loan.pay');
    
    Route::post('user/logout', [AuthController::class, 'logout'])->name('user.logout');
});