<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirestoreController extends Controller
{
    protected $client;
    protected $projectId;
    protected $accessToken;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $credentialsPath = storage_path('firebase_credentials.json');

        $credentials = new ServiceAccountCredentials(
            'https://www.googleapis.com/auth/datastore',
            json_decode(file_get_contents($credentialsPath), true)
        );

        $this->accessToken = $credentials->fetchAuthToken()['access_token'];

        $this->client = new Client([
            'base_uri' => "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/",
            'headers' => [
                'Authorization' => "Bearer {$this->accessToken}",
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function getCallLogs()
    {
        try {
            $response = $this->client->get('call_logs');
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getCalls($documentId)
    {
        try {
            $response = $this->client->get("call_logs/{$documentId}/calls");
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getAppUsage()
    {
        try {
            $response = $this->client->get('app_usage');
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUsage($documentId)
    {
        try {
            $response = $this->client->get("app_usage/{$documentId}/usage");
            $data = json_decode($response->getBody(), true);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
