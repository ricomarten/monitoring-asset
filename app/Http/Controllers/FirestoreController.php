<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class FirestoreController extends Controller
{
    protected $firestore;

    public function __construct(FirestoreService $firestore)
    {
        $this->firestore = $firestore;
    }

    /* =====================================================
     |  🔹 Basic Getters (Simple Collections)
     ===================================================== */
    public function getCallLogs()
    {
        $response = $this->firestore->getCollection('call_logs');
        return response()->json($response);
    }

    public function getCalls($documentId)
    {
        $response = $this->firestore->getSubCollection('call_logs', $documentId, 'calls');
        return response()->json($response);
    }

    public function getAppUsage()
    {
        $response = $this->firestore->getCollection('app_usage');
        return response()->json($response);
    }

    public function getUsage($documentId)
    {
        $response = $this->firestore->getSubCollection('app_usage', $documentId, 'usage');
        return response()->json($response);
    }

    /* =====================================================
     |  🔹 Complex API: Call Logs with Calls
     ===================================================== */
    public function getAllCallLogsWithCalls(Request $request)
    {
        try {
            $startDate   = $request->get('startDate');
            $endDate     = $request->get('endDate');
            $phoneNumber = $request->get('phoneNumber');
            $sortOrder   = $request->get('sortOrder', 'asc');

            $callLogs = $this->firestore->getCollection('call_logs')['documents'] ?? [];
            $results  = [];

            foreach ($callLogs as $doc) {
                $deviceId = $doc['fields']['deviceId']['stringValue'] ?? null;
                if (!$deviceId) continue;

                $calls = $this->firestore->getSubCollection('call_logs', $deviceId, 'calls')['documents'] ?? [];

                foreach ($calls as $call) {
                    $f = $call['fields'] ?? [];
                    $callStart = $f['startCall']['stringValue'] ?? null;
                    $callEnd   = $f['endCall']['stringValue'] ?? null;
                    $simNumber = $f['simNumber']['stringValue'] ?? '';

                    // Filter tanggal
                    if ($startDate && $callStart < $startDate) continue;
                    if ($endDate && $callEnd > $endDate) continue;

                    // Filter nomor
                    if ($phoneNumber && !str_contains($simNumber, $phoneNumber)) continue;

                    $results[] = [
                        'device_number' => $simNumber,
                        'peer_number'   => $f['number']['stringValue'] ?? '',
                        'call_type'     => $f['type']['stringValue'] ?? '',
                        'call_duration' => (int)($f['duration']['integerValue'] ?? 0),
                        'call_start'    => $callStart,
                        'call_end'      => $callEnd,
                    ];
                }
            }

            $data = collect($results)->sortBy($sortOrder === 'asc' ? 'call_start' : fn($r) => -strtotime($r['call_start']))->values();

            return response()->json(['callLogs' => $data]);
        } catch (\Exception $e) {
            Log::error("Error fetching call logs: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch call logs.'], 500);
        }
    }

    /* =====================================================
     |  🔹 Complex API: App Usage with Top Apps
     ===================================================== */
    public function getAllAppUsageWithTopApps(Request $request)
    {
        try {
            $startDate   = $request->get('startDate');
            $endDate     = $request->get('endDate');
            $sortOrder   = $request->get('sortOrder', 'asc');

            $appDocs = $this->firestore->getCollection('app_usage')['documents'] ?? [];
            $results = [];

            foreach ($appDocs as $doc) {
                $f = $doc['fields'] ?? [];
                $deviceId = $f['deviceId']['stringValue'] ?? '';
                if (!$deviceId) continue;

                $usageDocs = $this->firestore->getSubCollection('app_usage', $deviceId, 'usage')['documents'] ?? [];

                $topApps = collect($usageDocs)->map(fn($u) => [
                    'rank'             => $u['fields']['rank']['integerValue'] ?? 0,
                    'app_name'         => $u['fields']['app_name']['stringValue'] ?? '',
                    'package_name'     => $u['fields']['package_name']['stringValue'] ?? '',
                    'category'         => $u['fields']['category']['stringValue'] ?? '',
                    'total_time_ms'    => (int)($u['fields']['total_time_ms']['integerValue'] ?? 0),
                    'last_time_used'   => $u['fields']['last_time_used_formatted']['stringValue'] ?? '',
                ])->sortBy('rank')->values();

                $results[] = [
                    'device_id'           => $deviceId,
                    'device_model'        => $f['device_model']['stringValue'] ?? '',
                    'android_version'     => $f['android_version']['stringValue'] ?? '',
                    'total_usage_time_ms' => (int)($f['totalUsageTimeMs']['integerValue'] ?? 0),
                    'last_sync'           => $f['lastSync']['timestampValue'] ?? '',
                    'top_apps'            => $topApps,
                ];
            }

            $data = collect($results)->sortBy($sortOrder === 'asc' ? 'last_sync' : fn($r) => -strtotime($r['last_sync']))->values();

            return response()->json(['appUsageLogs' => $data]);
        } catch (\Exception $e) {
            Log::error("Error fetching app usage: " . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch app usage.'], 500);
        }
    }
}
