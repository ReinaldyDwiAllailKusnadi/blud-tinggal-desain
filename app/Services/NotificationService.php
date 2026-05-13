<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim push notification ke user via FCM
     *
     * @param string $token FCM Device Token
     * @param string $title Judul Notifikasi
     * @param string $body Isi Notifikasi
     * @param array $data Data tambahan (optional)
     * @return bool
     */
    public function sendNotification($token, $title, $body, $data = [])
    {
        if (!$token) {
            return false;
        }

        try {
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error('FCM Error: Gagal mendapatkan access token.');
                return false;
            }

            $projectId = config('services.fcm.project_id');
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => array_map('strval', $data), // FCM data values must be strings
                ],
            ];

            $response = Http::withToken($accessToken)
                ->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('FCM Error Response:', $response->json());
            return false;
        } catch (\Exception $e) {
            Log::error('FCM Exception: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Mendapatkan OAuth2 Access Token dari Service Account
     */
    private function getAccessToken()
    {
        $credentialsPath = config('services.fcm.service_account_path');

        if (!$credentialsPath || !file_exists($credentialsPath)) {
            Log::warning('FCM Warning: File service account tidak ditemukan di ' . $credentialsPath);
            return null;
        }

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'] ?? null;
    }
}
