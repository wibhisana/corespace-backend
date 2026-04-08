<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        // Pastikan user punya nomor HP
        if (empty($notifiable->phone_number)) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        // Kirim via Fonnte API (atau provider WA lain)
        // Jika belum setup API key, cukup log saja dulu
        $apiKey = config('services.fonnte.token');

        if (empty($apiKey)) {
            Log::info("WhatsApp Notification (no API key, logged only)", [
                'to' => $notifiable->phone_number,
                'message' => $message,
            ]);
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post('https://api.fonnte.com/send', [
                'target' => $notifiable->phone_number,
                'message' => $message,
            ]);

            Log::info("WhatsApp sent to {$notifiable->phone_number}", [
                'status' => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed: {$e->getMessage()}");
        }
    }
}
