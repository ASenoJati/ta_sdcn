<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $serverKey;
    protected $senderId;

    public function __construct()
    {
        $this->serverKey = config('services.fcm.server_key');
        $this->senderId = config('services.fcm.sender_id');
    }

    /**
     * Kirim notifikasi ke satu perangkat
     */
    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = [])
    {
        $payload = [
            'to' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        return $this->send($payload);
    }

    /**
     * Kirim notifikasi ke banyak perangkat
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = [])
    {
        if (count($tokens) === 1) {
            return $this->sendToDevice($tokens[0], $title, $body, $data);
        }

        $payload = [
            'registration_ids' => $tokens,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        return $this->send($payload);
    }

    /**
     * Kirim request ke FCM API
     */
    protected function send(array $payload)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('FCM send success', $result);
                return ['success' => true, 'response' => $result];
            } else {
                Log::error('FCM send error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return ['success' => false, 'error' => $response->body()];
            }
        } catch (\Exception $e) {
            Log::error('FCM exception: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
