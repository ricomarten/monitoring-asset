<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

    public function getAppUsageChartData()
    {
        try {
            $firestore = new \App\Services\FirestoreService();
            $appUsageCollection = $firestore->getCollection('app_usage');

            if (!isset($appUsageCollection['documents'])) {
                return response()->json(['labels' => [], 'values' => []]);
            }

            $topApps = [];
            $timeSeries = [];

            foreach ($appUsageCollection['documents'] as $doc) {
                $fields = $doc['fields'] ?? [];
                $deviceId = $fields['device_id']['stringValue'] ?? $fields['deviceId']['stringValue'] ?? null;
                if (!$deviceId) continue;

                // Ambil subcollection usage
                $usageDocs = $firestore->getSubCollection('app_usage', $deviceId, 'usage')['documents'] ?? [];

                foreach ($usageDocs as $usageDoc) {
                    $u = $usageDoc['fields'] ?? [];
                    $appName = $u['app_name']['stringValue'] ?? 'Unknown';
                    $duration = (int)($u['total_time_ms']['integerValue'] ?? 0);
                    $lastUsed = $u['last_time_used_formatted']['stringValue'] ?? '';

                    // Tambah total per app
                    if (!isset($topApps[$appName])) {
                        $topApps[$appName] = 0;
                    }
                    $topApps[$appName] += $duration;

                    // Tambah time series per bulan
                    if ($lastUsed) {
                        $month = date('M', strtotime($lastUsed));
                        $timeSeries[$month][$appName] = ($timeSeries[$month][$appName] ?? 0) + $duration;
                    }
                }
            }

            // Ambil Top 5
            arsort($topApps);
            $topApps = array_slice($topApps, 0, 5, true);

            return response()->json([
                'labels' => array_keys($topApps),
                'values' => array_values($topApps),
                'timeSeries' => $timeSeries
            ]);
        } catch (\Exception $e) {
            Log::error("Error loading chart data: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch data.']);
        }
    }
}
