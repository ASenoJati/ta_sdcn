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

    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = [])
    {
        try {
            $notification = Notification::create($title, $body);
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification($notification)
                ->withData($data);
            $this->messaging->send($message);
            return ['success' => true];
        } catch (\Exception $e) {
            Log::error('FCM send error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendToMultipleDevices(array $tokens, string $title, string $body, array $data = [])
    {
        $results = [];
        foreach ($tokens as $token) {
            $result = $this->sendToDevice($token, $title, $body, $data);
            $results[] = $result;
            if (!$result['success']) {
                Log::error('Gagal kirim ke token: ' . $token . ' - ' . ($result['error'] ?? 'unknown error'));
            }
        }
        return $results;
    }
}
