<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(storage_path('app/firebase/firebase-credentials.json'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Kirim notifikasi ke satu perangkat
     */
    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = [])
    {
        try {
            $notification = Notification::create($title, $body);

            // Membuat CloudMessage dengan target token
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data)
                ->withToken($deviceToken);

            $this->messaging->send($message);

            Log::info('Push notification sent', ['token' => $deviceToken]);
            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('FCM send error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Kirim notifikasi ke banyak perangkat
     */
    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = [])
    {
        $results = [];
        foreach ($tokens as $token) {
            $results[] = $this->sendToDevice($token, $title, $body, $data);
        }
        return $results;
    }
}
