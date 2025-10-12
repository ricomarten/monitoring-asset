<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;

class DashboardController extends Controller
{
    public function callUsage()
    {
        $projectId = env('FIREBASE_PROJECT_ID');
        $credentialsPath = storage_path('firebase_credentials.json');

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/datastore',
            json_decode(file_get_contents($credentialsPath), true)
        );

        $accessToken = $credentials->fetchAuthToken()['access_token'];

        $client = new Client([
            'base_uri' => "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/",
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);

        // Step 1: Ambil semua deviceId dari call_logs
        $response = $client->get("call_logs");
        $callLogDocs = json_decode($response->getBody(), true)['documents'];

        $callLogs = [];

        // Step 2: Loop setiap deviceId dan ambil sub-koleksi calls
        foreach ($callLogDocs as $doc) {
            $deviceId = $doc['fields']['deviceId']['stringValue'];

            try {
                $callsResponse = $client->get("call_logs/{$deviceId}/calls");
                $calls = json_decode($callsResponse->getBody(), true)['documents'] ?? [];

                foreach ($calls as $call) {
                    $fields = $call['fields'];
                    $callLogs[] = [
                        'device_number' => $fields['deviceId']['stringValue'],
                        'peer_number' => $fields['number']['stringValue'],
                        'call_type' => $fields['type']['stringValue'],
                        'call_duration' => $fields['duration']['integerValue'] . 's',
                        'call_start' => $fields['startCall']['stringValue'],
                        'call_end' => $fields['endCall']['stringValue'],
                    ];
                }
            } catch (\Exception $e) {
                // Lewati jika sub-koleksi tidak ditemukan
                continue;
            }
        }

        return view('dashboard.call_usage', compact('callLogs'));
    }
}
