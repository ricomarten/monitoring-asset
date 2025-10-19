<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $firestoreController;

    public function __construct(FirestoreController $firestoreController)
    {
        $this->firestoreController = $firestoreController;
    }

    /* ======================================================
     |  🔹 Call Usage View
     ====================================================== */
    public function callUsage(Request $request)
    {
        // Ambil data dari FirestoreController (API internal)
        $response = $this->firestoreController->getAllCallLogsWithCalls($request);
        $data = $response->getData(true);

        $callLogs = collect($data['callLogs'] ?? []);
        $totalPeerNumber = $callLogs->pluck('peer_number')->unique()->count();
        $totalCallDuration = $callLogs->sum('call_duration');

        return view('dashboard.call_usage', compact('callLogs', 'totalPeerNumber', 'totalCallDuration'));
    }

    /* ======================================================
     |  🔹 App Usage View
     ====================================================== */
    public function appUsageWithTopApps(Request $request)
    {
        $response = $this->firestoreController->getAllAppUsageWithTopApps($request);
        $data = $response->getData(true);

        $appUsageLogs = collect($data['appUsageLogs'] ?? []);
        $totalTimeMs  = $appUsageLogs->sum('total_usage_time_ms');
        $totalDevices = $appUsageLogs->pluck('device_id')->unique()->count();
        $totalVisitors = $appUsageLogs->pluck('android_version')->unique()->count();

        return view('dashboard.app_usage', compact('appUsageLogs', 'totalTimeMs', 'totalDevices', 'totalVisitors'));
    }
}
