<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        // 1. Pastikan user punya nomor HP
        if (empty($notifiable->phone_number)) {
            return;
        }

        // 2. SANITASI NOMOR HP (Auto-Format ke 62)
        $phone = $notifiable->phone_number;

        // Hapus semua karakter yang bukan angka (seperti spasi, strip -, atau tanda +)
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Jika nomor diawali dengan angka 0, potong 0-nya dan ganti dengan 62
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $message = $notification->toWhatsApp($notifiable);

        $apiKey = config('services.fonnte.token');

        if (empty($apiKey)) {
            Log::info("WhatsApp Notification (no API key, logged only)", [
                'to' => $phone, // Gunakan variabel $phone yang sudah bersih
                'message' => $message,
            ]);
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post('https://api.fonnte.com/send', [
                'target' => $phone, // Gunakan variabel $phone yang sudah bersih
                'message' => $message,
            ]);

            Log::info("WhatsApp sent to {$phone}", [
                'status' => $response->status(),
                'response' => $response->body(), // Opsional: Untuk melihat pesan error dari Fonnte jika gagal
            ]);
        } catch (\Exception $e) {
            Log::error("WhatsApp send failed: {$e->getMessage()}");
        }
    }
}
