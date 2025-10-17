<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FirestoreController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'callUsage']);

Route::get('/call-logs', [FirestoreController::class, 'getCallLogs']);
Route::get('/call-logs/{documentId}/calls', [FirestoreController::class, 'getCalls']);
Route::get('call-logs-with-calls', [FirestoreController::class, 'getAllCallLogsWithCalls']);
Route::get('/app-usage', [FirestoreController::class, 'getAppUsage']);
Route::get('/app-usage/{documentId}/usage', [FirestoreController::class, 'getUsage']);
Route::get('/dashboard/call-usage', [DashboardController::class, 'callUsage'])->name('call-logs.index');
Route::get('/dashboard/app-usage', [DashboardController::class, 'appUsageWithTopApps'])->name('app-usage.index');
Route::get('/app-usage-with-logs', [FirestoreController::class, 'getAllAppUsageWithTopApps']);
