<?php

namespace App\Modules\IAM\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UsersImport
{
    public array $createdUsers = [];
    public array $failedRows = [];

    /**
     * Import Data Menggunakan Scanner Dinamis
     * Anti-bocor/Anti-error meskipun kandidat menambah atau menghapus baris (Row).
     */
    public function import(string $filePath): void
    {
        // OBAT ANTI-TIMEOUT: Beri waktu lebih lama dan RAM lebih besar
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');

        $reader = IOFactory::createReaderForFile($filePath);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Ambil array mentah dari Excel
        $rawSheetData = $sheet->toArray(null, true, true, false);

        // FILTER ANTI-HANTU: Buang baris yang kosong melompong agar tidak membebani memori
        $sheetData = array_filter($rawSheetData, function($row) {
            $filteredRow = array_filter($row, fn($cell) => trim((string)$cell) !== '');
            return count($filteredRow) > 0;
        });

        // === KATEGORI 2: Data Pribadi ===
        $name = $this->findValue($sheetData, 'nama lengkap');
        $ttl = $this->findValue($sheetData, 'tempat & tanggal lahir');
        $gender = $this->findValue($sheetData, 'jenis kelamin');
        $maritalStatus = $this->findValue($sheetData, 'status perkawinan');

        // 💡 LOGIKA IDENTITAS (KTP / KITAS / NIK KARYAWAN)
        $ktp = $this->findValue($sheetData, 'no. ktp') ?? $this->findValue($sheetData, 'ktp');
        $kitas = $this->findValue($sheetData, 'no. kitas') ?? $this->findValue($sheetData, 'kitas');

        // 1. id_card_number diisi KITAS (WNA). Jika tidak ada, pakai KTP (WNI).
        $idCardNumber = $kitas ?? $ktp;

        // 2. Cari tahu apakah ada isian spesifik untuk "Nomor Induk Karyawan" di form
        $nomorIndukKaryawan = $this->findValue($sheetData, 'nomor induk karyawan');

        // 3. Sesuai instruksi Anda: NIK (Karyawan) defaultnya disamakan dengan nilai KTP/KITAS
        $nik = $nomorIndukKaryawan ?? $idCardNumber;

        // 4. Jika pelamar benar-benar lupa mengisi KTP & KITAS, berikan NIK Darurat agar Database tidak Crash
        if (empty($nik)) {
            $nik = 'EMP-' . strtoupper(Str::random(6));
        }

        $taxId = $this->findValue($sheetData, 'no. npwp');
        $address = $this->findValue($sheetData, 'alamat sekarang');
        $phone = $this->findValue($sheetData, 'telp & hp') ?? $this->findValue($sheetData, 'no. hp');

        $email = $this->findValue($sheetData, 'email');
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = null;
        }

        // === Kontak Darurat ===
        $emergencyName = $this->findValue($sheetData, 'nama kontak darurat') ?? $this->findValue($sheetData, 'dalam keadaan darurat');
        $emergencyPhone = $this->findValue($sheetData, 'no. telp darurat') ?? $this->findValue($sheetData, 'telepon darurat');


        // ==========================================
        // VALIDASI MINIMAL
        // ==========================================
        if (empty($name)) {
            $this->failedRows[] = ['email' => '(kosong)', 'reason' => 'Nama Lengkap tidak ditemukan di formulir'];
            return;
        }

        if (empty($email)) {
            $this->failedRows[] = ['email' => '(kosong)', 'reason' => 'Email tidak ditemukan di formulir'];
            return;
        }

        // BLOKIRAN "Email sudah terdaftar" DIHAPUS DARI SINI AGAR BISA UPSERT

        // ==========================================
        // NORMALISASI DATA (Logika Original Anda)
        // ==========================================

        // Parse tempat & tanggal lahir: "Bogor, 8 Mei 1996"
        $birthPlace = null;
        $birthDate = null;
        if ($ttl && str_contains($ttl, ',')) {
            [$birthPlace, $rawDate] = array_map('trim', explode(',', $ttl, 2));
            $birthDate = $this->parseIndonesianDate($rawDate);
        }

        // Normalisasi gender: "Laki-laki" -> "L", "Perempuan" -> "P"
        $genderCode = null;
        if ($gender) {
            $genderCode = str_starts_with(strtolower($gender), 'l') ? 'L' : 'P';
        }

        // Normalisasi status perkawinan
        $maritalMap = [
            'k0' => 'Belum Menikah', 'tk' => 'Belum Menikah',
            'k1' => 'Menikah', 'k2' => 'Menikah', 'k3' => 'Menikah',
            'belum menikah' => 'Belum Menikah', 'menikah' => 'Menikah',
            'cerai' => 'Cerai', 'cerai hidup' => 'Cerai', 'cerai mati' => 'Cerai',
        ];
        $normalizedMarital = $maritalMap[strtolower(trim($maritalStatus))] ?? ($maritalStatus ?: null);


        // ==========================================
        // UPSERT (UPDATE JIKA SUDAH ADA, INSERT JIKA BARU)
        // ==========================================
        try {
            $user = User::where('email', $email)->first();

            if ($user) {
                // SKENARIO A: UPDATE DATA (Karyawan sudah ada)
                $user->update([
                    'name' => $name,
                    'job_title' => $jobTitle ?: $user->job_title,
                    'phone_number' => $phone ?: $user->phone_number,
                    'current_address' => $address ?: $user->current_address,
                    'gender' => $genderCode ?: $user->gender,
                    'birth_place' => $birthPlace ?: $user->birth_place,
                    'birth_date' => $birthDate ?: $user->birth_date,
                    'marital_status' => $normalizedMarital ?: $user->marital_status,
                    'emergency_contact_name' => $emergencyName ?: $user->emergency_contact_name,
                    'emergency_contact_phone' => $emergencyPhone ?: $user->emergency_contact_phone,
                    'id_card_number' => $idCardNumber ?: $user->id_card_number,
                    'tax_id' => $taxId ?: $user->tax_id,
                    'nik' => $nik ?: $user->nik,
                ]);

                $this->createdUsers[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => '*** (Diperbarui, Password Tetap)', // Feedback untuk notifikasi HRD
                ];
            } else {
                // SKENARIO B: INSERT DATA BARU (Pelamar baru)
                $plainPassword = Str::random(8);

                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $plainPassword,
                    'nik' => $nik,
                    'job_title' => $jobTitle ?: null,
                    'phone_number' => $phone ?: null,
                    'personal_email' => $email,
                    'current_address' => $address ?: null,
                    'gender' => $genderCode,
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'marital_status' => $normalizedMarital,
                    'emergency_contact_name' => $emergencyName ?: null,
                    'emergency_contact_phone' => $emergencyPhone ?: null,
                    'id_card_number' => $idCardNumber ?: null,
                    'tax_id' => $taxId ?: null,
                    'employment_status' => 'Probation', // Status default
                ]);

                $user->assignRole($role);

                $this->createdUsers[] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $plainPassword,
                ];
            }
        } catch (\Exception $e) {
            $this->failedRows[] = [
                'email' => $email,
                'reason' => $e->getMessage(),
            ];
        }
    }

    /**
     * Mesin Pencari Dinamis: Mencari kata kunci, lalu melirik ke sel di sebelahnya.
     */
    private function findValue(array $sheetData, string $keyword): ?string
    {
        $keyword = strtolower($keyword);

        foreach ($sheetData as $row) {
            $rowValues = array_values($row);

            foreach ($rowValues as $colIndex => $cellValue) {
                $cellStr = strtolower(trim((string) $cellValue));

                if (str_contains($cellStr, $keyword)) {
                    // Cek jika datanya tergabung dengan titik dua (misal: "Nama Lengkap : Budi")
                    if (str_contains((string) $cellValue, ':')) {
                        $parts = explode(':', (string) $cellValue, 2);
                        if (isset($parts[1]) && trim($parts[1]) !== '') {
                            return trim($parts[1]);
                        }
                    }

                    // Cek sel-sel di sebelah kanannya
                    for ($i = $colIndex + 1; $i < count($rowValues); $i++) {
                        $val = trim((string) $rowValues[$i]);
                        // Abaikan sel kosong atau yang hanya berisi titik dua
                        if ($val !== '' && $val !== ':') {
                            return $val;
                        }
                    }
                }
            }
        }
        return null;
    }

    /**
     * Parse tanggal Indonesia: "8 Mei 1996" -> "1996-05-08"
     */
    private function parseIndonesianDate(string $raw): ?string
    {
        $months = [
            'januari' => 1, 'februari' => 2, 'maret' => 3, 'april' => 4,
            'mei' => 5, 'juni' => 6, 'juli' => 7, 'agustus' => 8,
            'september' => 9, 'oktober' => 10, 'november' => 11, 'desember' => 12,
        ];

        // Hilangkan koma atau karakter aneh jika ada
        $raw = str_replace(',', '', trim($raw));
        $parts = preg_split('/\s+/', $raw);

        if (count($parts) < 3) {
            return null;
        }

        $day = (int) $parts[0];
        $month = $months[strtolower($parts[1])] ?? null;
        $year = (int) $parts[2];

        if (!$month || !$day || !$year) {
            return null;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
