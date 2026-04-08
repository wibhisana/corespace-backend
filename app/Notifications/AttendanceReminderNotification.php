<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\WhatsAppChannel;
use Carbon\Carbon;

class AttendanceReminderNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        // Bisa ditambahkan parameter jika diperlukan
    }

    public function via(object $notifiable): array
    {
        // Kirim ke WhatsApp dan simpan di database untuk dibaca Frontend
        return [WhatsAppChannel::class, 'database'];
    }

    // Format untuk WhatsApp
    public function toWhatsApp(object $notifiable): string
    {
        $hariIni = Carbon::now()->translatedFormat('l, d F Y');

        return "⚠️ *REMINDER ABSENSI CORESPACE*\n\n"
             . "Halo {$notifiable->name},\n\n"
             . "Kami melihat Anda belum melakukan *Clock-In* pada hari ini, *{$hariIni}*.\n\n"
             . "Mohon segera melakukan absensi melalui aplikasi atau hubungi HRD jika terdapat kendala teknis.\n\n"
             . "Terima kasih.";
    }

    // Format untuk Frontend (API)
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'attendance_alert',
            'title' => 'Belum Absen Hari Ini',
            'message' => "Anda belum melakukan absensi pada " . Carbon::now()->translatedFormat('d F Y'),
            'action_url' => '/attendances/clock-in', // Link untuk frontend Vue
            'created_at' => Carbon::now()->toDateTimeString(),
        ];
    }
}
