<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FirestoreController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('dashboard.call-usage'));

/* =============================
|  Dashboard Routes
============================= */
Route::prefix('dashboard')->group(function () {
    Route::get('/call-usage', [DashboardController::class, 'callUsage'])->name('dashboard.call-usage');
    Route::get('/app-usage', [DashboardController::class, 'appUsageWithTopApps'])->name('dashboard.app-usage');
});

/* =============================
|  Firestore API Routes (AJAX)
============================= */
Route::prefix('api')->group(function () {
    Route::get('/call-logs', [FirestoreController::class, 'getCallLogs']);
    Route::get('/call-logs/{documentId}/calls', [FirestoreController::class, 'getCalls']);
    Route::get('/call-logs-with-calls', [FirestoreController::class, 'getAllCallLogsWithCalls']);

    Route::get('/app-usage', [FirestoreController::class, 'getAppUsage']);
    Route::get('/app-usage/{documentId}/usage', [FirestoreController::class, 'getUsage']);
    Route::get('/app-usage-with-top-apps', [FirestoreController::class, 'getAllAppUsageWithTopApps']);
});
