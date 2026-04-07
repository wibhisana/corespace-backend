<?php

namespace App\Modules\HRIS\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class WhatsAppBotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // 1. Tangkap Data (Format Postman untuk testing)
        $senderNumber = $request->input('phone');
        $incomingMessage = trim($request->input('message'));

        // 2. Identifikasi Karyawan berdasarkan Nomor HP
        $user = User::where('phone_number', $senderNumber)->first();

        if (!$user) {
            return $this->reply($senderNumber, "Maaf, nomor ini tidak terdaftar di sistem HRIS CoreSpace. Silakan hubungi HRD.");
        }

        // 3. Cek Status Menu (Cache 5 menit)
        $sessionKey = 'wa_state_' . $senderNumber;
        $currentState = Cache::get($sessionKey, 'main_menu');

        // 4. Logika Navigasi Menu
        if ($incomingMessage == '0') {
            Cache::put($sessionKey, 'main_menu', 300);
            return $this->sendMainMenu($senderNumber, $user);
        }

        if ($currentState == 'main_menu') {
            switch ($incomingMessage) {
                case '1':
                    // Pindah ke submenu Absensi
                    Cache::put($sessionKey, 'menu_absensi', 300);
                    return $this->sendMenuAbsensi($senderNumber);
                case '2':
                    // Hubungi HRD
                    return $this->reply($senderNumber, "Silakan hubungi HRD di nomor WhatsApp: 0812-3456-7890 (Siska HR) pada jam kerja.");
                default:
                    // Input tidak valid, kembalikan ke menu utama
                    return $this->sendMainMenu($senderNumber, $user);
            }
        }

        if ($currentState == 'menu_absensi') {
            switch ($incomingMessage) {
                case '1':
                    return $this->reply($senderNumber, "✅ Anda telah Clock-In pada pukul 08:05 WIB.");
                case '2':
                    return $this->reply($senderNumber, "Sisa cuti tahunan Anda: 12 Hari.");
                default:
                    return $this->reply($senderNumber, "Pilihan tidak valid. Ketik 0 untuk kembali ke Menu Utama.");
            }
        }
    }

    // --- KUMPULAN BALASAN TEKS ---

    private function sendMainMenu($phone, $user)
    {
        $text = "Halo {$user->name} 👋\nSelamat datang di Layanan Mandiri CoreSpace.\n\n";
        $text .= "Silakan pilih menu di bawah ini:\n";
        $text .= "Ketik *1* 🕒 Layanan Absensi & Cuti\n";
        $text .= "Ketik *2* 📞 Hubungi HRD\n\n";
        $text .= "Ketik angka pilihan Anda.";

        return $this->reply($phone, $text);
    }

    private function sendMenuAbsensi($phone)
    {
        $text = "🕒 *Layanan Absensi & Cuti*\n\n";
        $text .= "Ketik *1* Cek Jam Masuk Hari Ini\n";
        $text .= "Ketik *2* Cek Sisa Kuota Cuti\n";
        $text .= "Ketik *0* Kembali ke Menu Utama";

        return $this->reply($phone, $text);
    }

    private function reply($phone, $message)
    {
        // Mengembalikan format JSON untuk memudahkan testing di Postman
        return response()->json([
            'status' => 'success',
            'target' => $phone,
            'reply' => $message
        ]);
    }
}
