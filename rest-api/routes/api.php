<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientApiController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RepaymentController;

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

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('get-token', [AuthController::class, 'getToken']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

// Client-specific authenticated routes
Route::prefix('client')->middleware('jwt.auth')->group(function () {
    Route::get('loans', [ClientApiController::class, 'loans']);
    Route::get('loan-repayment-history', [ClientApiController::class, 'loanRepaymentHistory']);
});

// Branch routes
Route::apiResource('branches', BranchController::class);

// Client routes
Route::apiResource('clients', ClientController::class);
Route::get('branches/{branch}/clients', [ClientController::class, 'getByBranch']);

// Loan routes
Route::apiResource('loans', LoanController::class);
Route::get('clients/{client}/loans', [LoanController::class, 'getByClient']);
Route::get('branches/{branch}/loans', [LoanController::class, 'getByBranch']);
Route::patch('loans/{loan}/status', [LoanController::class, 'updateStatus']);

// Repayment routes
Route::apiResource('repayments', RepaymentController::class);
Route::get('loans/{loan}/repayments', [RepaymentController::class, 'getByLoan']);

// Analytics routes
Route::get('analytics/loan-summary', [LoanController::class, 'loanSummary']);
Route::get('analytics/branch-performance', [BranchController::class, 'performance']);
Route::get('analytics/client-statistics', [ClientController::class, 'statistics']);