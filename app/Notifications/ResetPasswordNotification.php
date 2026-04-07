<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// PERHATIKAN: Saya sudah menghapus "implements ShouldQueue" di baris ini karena kita akan menggunakan Queueable trait saja untuk mengatur antrian.
class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    /**
     * Kita masukkan token ke dalam constructor agar bisa diakses di fungsi toMail.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Menggunakan channel 'mail'.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Representasi Body Email.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // URL ini akan ditangkap oleh Vue.js (Vite default port 5173)
        $resetUrl = "http://localhost:5173/reset-password?token={$this->token}&email=" . urlencode($notifiable->email);

        return (new MailMessage)
            ->subject('Permintaan Reset Password - CoreSpace')
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Kami menerima permintaan untuk mengatur ulang kata sandi akun CoreSpace Anda.')
            ->line('Silakan klik tombol di bawah ini untuk melanjutkan:')
            ->action('Reset Password Sekarang', $resetUrl)
            ->line('Penting: Tautan ini hanya berlaku selama 60 menit.')
            ->line('Jika Anda tidak merasa meminta ini, abaikan saja email ini.')
            ->salutation('Tim Keamanan CoreSpace');
    }

    /**
     * Jika Anda ingin menyimpan notifikasi ini di database juga (opsional).
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Permintaan reset password dikirim.',
            'email' => $notifiable->email
        ];
    }
}
