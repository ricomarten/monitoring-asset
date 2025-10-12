<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\FirestoreController;
use App\Http\Controllers\DashboardController;

Route::get('/call-logs', [FirestoreController::class, 'getCallLogs']);
Route::get('/call-logs/{documentId}/calls', [FirestoreController::class, 'getCalls']);
Route::get('/app-usage', [FirestoreController::class, 'getAppUsage']);
Route::get('/app-usage/{documentId}/usage', [FirestoreController::class, 'getUsage']);
Route::get('/dashboard/call-usage', [DashboardController::class, 'callUsage']);

