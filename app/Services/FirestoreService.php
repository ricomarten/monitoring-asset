<?php

namespace App\Services;

use GuzzleHttp\Client;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class FirestoreService
{
    protected $client;
    protected $projectId;
    protected $accessToken;

    public function __construct()
    {
        $this->projectId = env('FIREBASE_PROJECT_ID');
        $credentialsPath = storage_path('firebase_credentials.json');

        // ✅ Validasi file credentials
        if (!file_exists($credentialsPath)) {
            Log::error("Firebase credentials not found at: {$credentialsPath}");
            abort(500, 'Firebase credentials file missing.');
        }

        try {
            // 🔐 Autentikasi menggunakan service account
            $credentials = new ServiceAccountCredentials(
                'https://www.googleapis.com/auth/datastore',
                json_decode(file_get_contents($credentialsPath), true)
            );

            $this->accessToken = $credentials->fetchAuthToken()['access_token'];

            // ⚙️ Inisialisasi Guzzle client
            $this->client = new Client([
                'base_uri' => "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/",
                'headers' => [
                    'Authorization' => "Bearer {$this->accessToken}",
                    'Content-Type' => 'application/json',
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to initialize Firestore connection: ' . $e->getMessage());
            abort(500, 'Firestore initialization error.');
        }
    }

    /* ======================================================
     |  GENERIC GETTER
     |  Ambil data dari koleksi Firestore
     ====================================================== */
    public function getCollection(string $collection)
    {
        return $this->handleRequest("GET", $collection);
    }

    /* ======================================================
     |  Ambil dokumen tunggal
     ====================================================== */
    public function getDocument(string $collection, string $documentId)
    {
        return $this->handleRequest("GET", "{$collection}/{$documentId}");
    }

    /* ======================================================
     |  Ambil subcollection (contoh: call_logs/{id}/calls)
     ====================================================== */
    public function getSubCollection(string $collection, string $documentId, string $subCollection)
    {
        return $this->handleRequest("GET", "{$collection}/{$documentId}/{$subCollection}");
    }

    /* ======================================================
     |  Tambahkan dokumen baru ke koleksi
     ====================================================== */
    public function createDocument(string $collection, array $data)
    {
        return $this->handleRequest("POST", $collection, [
            'json' => ['fields' => $this->formatFields($data)],
        ]);
    }

    /* ======================================================
     |  Update dokumen
     ====================================================== */
    public function updateDocument(string $collection, string $documentId, array $data)
    {
        return $this->handleRequest("PATCH", "{$collection}/{$documentId}", [
            'json' => ['fields' => $this->formatFields($data)],
        ]);
    }

    /* ======================================================
     |  Hapus dokumen
     ====================================================== */
    public function deleteDocument(string $collection, string $documentId)
    {
        return $this->handleRequest("DELETE", "{$collection}/{$documentId}");
    }

    /* ======================================================
     |  Format field sesuai struktur Firestore API
     ====================================================== */
    protected function formatFields(array $data): array
    {
        $formatted = [];
        foreach ($data as $key => $value) {
            $formatted[$key] = match (true) {
                is_int($value)   => ['integerValue' => $value],
                is_bool($value)  => ['booleanValue' => $value],
                is_float($value) => ['doubleValue' => $value],
                $this->isTimestamp($value) => ['timestampValue' => $value],
                default          => ['stringValue' => (string) $value],
            };
        }
        return $formatted;
    }

    protected function isTimestamp($value): bool
    {
        // Deteksi format timestamp ISO 8601 (contoh: 2025-10-19T08:15:00Z)
        return is_string($value) && preg_match('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z/', $value);
    }

    /* ======================================================
     |  Core HTTP handler (dengan caching & error handling)
     ====================================================== */
    protected function handleRequest(string $method, string $uri, array $options = [])
    {
        $cacheKey = "firestore_" . md5($method . $uri . json_encode($options));

        // 💾 Cache selama 3 menit untuk GET
        if ($method === 'GET' && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = $this->client->request($method, $uri, $options);
            $data = json_decode($response->getBody(), true);

            if ($method === 'GET') {
                Cache::put($cacheKey, $data, now()->addMinutes(3));
            }

            return $data;
        } catch (Exception $e) {
            Log::error("Firestore API error [{$method} {$uri}]: " . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }
}
