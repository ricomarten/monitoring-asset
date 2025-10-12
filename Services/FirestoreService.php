<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FirestoreService
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

    public function getDocument($collection, $documentId)
    {
        $response = $this->client->get("{$collection}/{$documentId}");
        return json_decode($response->getBody(), true);
    }

    public function createDocument($collection, $data)
    {
        $response = $this->client->post($collection, [
            'json' => ['fields' => $this->formatFields($data)],
        ]);
        return json_decode($response->getBody(), true);
    }

    protected function formatFields(array $data)
    {
        $formatted = [];
        foreach ($data as $key => $value) {
            $formatted[$key] = ['stringValue' => (string) $value];
        }
        return $formatted;
    }
}
