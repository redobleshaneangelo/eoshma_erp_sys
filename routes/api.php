<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrebiddingController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PayrollRunController;
use App\Http\Controllers\TimekeepingController;
use App\Http\Controllers\AttendanceController;


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Public Resource Routes
|--------------------------------------------------------------------------
*/
Route::get('/clients', [ClientController::class, 'index']); 
Route::post('/clients', [ClientController::class, 'store']); 
Route::get('/clients/{client}', [ClientController::class, 'show']);

Route::post('/users', [UserController::class, 'store']);

 


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/projects/{id}/setup/submit', [ProjectController::class, 'store']);

    Route::get('/prebiddings', [PrebiddingController::class, 'index']);
    Route::post('/prebiddings', [PrebiddingController::class, 'store']);
    Route::get('/prebiddings/{id}', [PrebiddingController::class, 'show']);
    Route::put('/prebiddings/{id}/documents', [PrebiddingController::class, 'updateDocuments']);
    Route::post('/prebiddings/{id}/send-to-operations', [PrebiddingController::class, 'sendToOperations']);
    Route::get('/biddings-won', [PrebiddingController::class, 'getBiddingsWon']);

    Route::get('/pending-approvals/operations/prebiddings', [PrebiddingController::class, 'pendingApprovalsOperationsPrebiddings']);
    Route::get('/pending-approvals/operations/prebiddings/{id}', [PrebiddingController::class, 'getPendingApprovalsOperationsPrebiddings']);
    Route::put('/pending-approvals/operations/prebiddings/{id}/approve', [PrebiddingController::class, 'approvePendingApprovalsOperationsPrebiddings']);

    Route::get('/pending-approvals/finance/prebiddings', [PrebiddingController::class, 'pendingApprovalsFinancePrebiddings']);
    Route::get('/pending-approvals/finance/prebiddings/{id}', [PrebiddingController::class, 'getPendingApprovalsFinancePrebiddings']);
    Route::put('/pending-approvals/finance/prebiddings/{id}/approve', [PrebiddingController::class, 'approvePendingApprovalsFinancePrebiddings']);

    Route::get('/payroll-runs', [PayrollRunController::class, 'index']);
    Route::post('/payroll-runs', [PayrollRunController::class, 'store']);
    Route::get('/payroll-runs/{payrollRun}', [PayrollRunController::class, 'show']);
    Route::get('/payroll-runs/{payrollRun}/attendance-summary', [PayrollRunController::class, 'attendanceSummary']);
    Route::get('/payroll-runs/{payrollRun}/allowances', [PayrollRunController::class, 'allowances']);
    Route::post('/payroll-runs/{payrollRun}/allowances', [PayrollRunController::class, 'storeAllowance']);
    Route::patch('/payroll-runs/{payrollRun}/allowances/{allowance}', [PayrollRunController::class, 'updateAllowance']);
    Route::delete('/payroll-runs/{payrollRun}/allowances/{allowance}', [PayrollRunController::class, 'deleteAllowance']);
    Route::get('/payroll-runs/{payrollRun}/deductions', [PayrollRunController::class, 'deductions']);
    Route::post('/payroll-runs/{payrollRun}/deductions', [PayrollRunController::class, 'storeDeduction']);
    Route::patch('/payroll-runs/{payrollRun}/deductions/{deduction}', [PayrollRunController::class, 'updateDeduction']);
    Route::delete('/payroll-runs/{payrollRun}/deductions/{deduction}', [PayrollRunController::class, 'deleteDeduction']);
    Route::get('/payroll-runs/{payrollRun}/statutory-compliance', [PayrollRunController::class, 'statutoryCompliance']);
    Route::get('/payroll-runs/{payrollRun}/computed-payroll', [PayrollRunController::class, 'computedPayroll']);
    Route::post('/payroll-runs/{payrollRun}/send-for-approval', [PayrollRunController::class, 'sendForApproval']);
    Route::post('/payroll-runs/{payrollRun}/approve', [PayrollRunController::class, 'approve']);
    Route::post('/payroll-runs/{payrollRun}/release-payslip', [PayrollRunController::class, 'releasePayslip']);
    Route::post('/payroll-runs/{payrollRun}/eligibilities', [PayrollRunController::class, 'updateEligibility']);
    Route::post('/payroll-runs/{payrollRun}/eligibilities/bulk', [PayrollRunController::class, 'bulkEligibility']);

    Route::get('/timekeeping/days', [TimekeepingController::class, 'days']);
    Route::get('/timekeeping/days/{date}', [TimekeepingController::class, 'dayDetail']);
    Route::patch('/timekeeping/days/{date}/employees/{employee}', [TimekeepingController::class, 'updateDayRecord']);

    Route::get('/attendance/records', [AttendanceController::class, 'records']);
    Route::patch('/attendance/records/{date}', [AttendanceController::class, 'updateRecord']);
    Route::get('/attendance/qr', [AttendanceController::class, 'qrCode']);




    
});
