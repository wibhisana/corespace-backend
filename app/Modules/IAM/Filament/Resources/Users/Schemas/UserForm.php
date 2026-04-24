<?php

declare(strict_types=1);

namespace App\Modules\IAM\Filament\Resources\Users\Schemas;

// --- TAMBAHAN USE STATEMENTS UNTUK AI KTP ---
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Set;
use Filament\Forms\Get;
// --------------------------------------------

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ==========================================
                // MASTER-DETAIL SPLIT LAYOUT (1 : 2)
                // Responsive: stacked on mobile, split from `md` breakpoint.
                // ==========================================
                Grid::make()
                    ->columns([
                        'default' => 1,
                        'md'      => 3,
                    ])
                    ->schema([
                        // ------------------------------------------
                        // LEFT (weight 1) — User Profile
                        // ------------------------------------------
                        Section::make('Profil Karyawan')
                            ->description('Identitas, kredensial & penempatan di struktur Holding.')
                            ->icon('heroicon-o-identification')
                            ->columnSpan([
                                'default' => 'full',
                                'md'      => 1,
                            ])
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Lengkap')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('email')
                                    ->label('Email Kantor')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(255),

                                TextInput::make('password')
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn ($state) => $state)
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create')
                                    ->maxLength(255)
                                    ->helperText('Kosongkan jika tidak ingin mengubah password.'),

                                FileUpload::make('face_photo_path')
                                    ->label('Reference Face Photo (For AI Attendance)')
                                    ->helperText('Foto wajah baseline untuk verifikasi AI saat Clock In/Out. Wajah harus jelas, menghadap kamera, pencahayaan cukup.')
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('users/faces')
                                    ->visibility('public')
                                    ->maxSize(5120)
                                    ->imageCropAspectRatio('1:1')
                                    ->imageResizeTargetWidth('512')
                                    ->imageResizeTargetHeight('512'),

                                TextInput::make('nik')
                                    ->label('NIK (Nomor Induk Karyawan)')
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50),

                                TextInput::make('phone_number')
                                    ->label('No. HP (WhatsApp)')
                                    ->tel()
                                    ->maxLength(20),

                                Select::make('unit_id')
                                    ->label('Unit Bisnis / Anak Perusahaan')
                                    ->relationship('unit', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live(),

                                Select::make('department_id')
                                    ->label('Departemen')
                                    ->relationship('department', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('manager_id')
                                    ->label('Atasan Langsung')
                                    ->relationship(
                                        name: 'manager',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->whereKeyNot(request()->route('record')),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->job_title})")
                                    ->searchable(['name', 'email', 'nik'])
                                    ->preload()
                                    ->nullable()
                                    ->helperText('Anti-circular: user tidak dapat menjadi manager dirinya sendiri.'),
                            ]),

                        // ------------------------------------------
                        // RIGHT (weight 2) — Access & Roles
                        // ------------------------------------------
                        Section::make('Akses & Peran')
                            ->description('Pilih peran untuk memberikan hak akses spesifik pada modul-modul sistem. Permissions otomatis diturunkan dari role yang dipilih (via Spatie Permission).')
                            ->icon('heroicon-o-shield-check')
                            ->columnSpan([
                                'default' => 'full',
                                'md'      => 2,
                            ])
                            ->schema([
                                CheckboxList::make('roles')
                                    ->label('Peran (Roles)')
                                    ->relationship('roles', 'name')
                                    ->columns(2)
                                    ->gridDirection('row')
                                    ->bulkToggleable()
                                    ->searchable()
                                    ->noSearchResultsMessage('Tidak ada role yang cocok.')
                                    ->searchPrompt('Cari role...')
                                    ->helperText('Centang satu atau lebih role sesuai tanggung jawab karyawan.'),
                            ]),
                    ]),

                // ==========================================
                // DATA PRIBADI & BERKAS
                // ==========================================
                Section::make('Data Pribadi Tambahan')
                    ->description('Data kontak dan NPWP.')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('personal_email')->label('Email Pribadi')->email()->maxLength(255),
                        Select::make('gender')->label('Jenis Kelamin')->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),
                        TextInput::make('birth_place')->label('Tempat Lahir'),
                        DatePicker::make('birth_date')->label('Tanggal Lahir'),
                        Select::make('marital_status')->label('Status Pernikahan')->options(['Belum Menikah' => 'Belum Menikah','Menikah' => 'Menikah','Cerai' => 'Cerai']),
                        Textarea::make('current_address')->label('Alamat Domisili')->columnSpanFull(),
                        TextInput::make('tax_id')->label('NPWP'),
                        FileUpload::make('tax_id_path')->label('Upload Foto NPWP')->disk('employee_private')->directory('pajak')->visibility('private')->maxSize(5120)->image(),
                        TextInput::make('emergency_contact_name')->label('Kontak Darurat (Nama)'),
                        TextInput::make('emergency_contact_phone')->label('Kontak Darurat (Telp)')->tel(),
                    ]),

                // ==========================================
                // VALIDASI IDENTITAS AI (KTP) -- NEW!
                // ==========================================
                Section::make('Validasi Identitas AI (KTP)')
                    ->description('Unggah KTP pelamar dan biarkan AI mengekstrak datanya secara otomatis.')
                    ->icon('heroicon-o-sparkles')
                    ->collapsed()
                    ->schema([
                        FileUpload::make('ktp_image_path')
                            ->label('Foto KTP')
                            ->image()
                            ->disk('public') // Gunakan public agar mudah diakses API Python secara lokal
                            ->directory('ktp_scans')
                            ->columnSpanFull()
                            ->hintAction(
                                Action::make('scan_ktp')
                                    ->label('Scan KTP dengan AI')
                                    ->icon('heroicon-m-cpu-chip')
                                    ->color('primary')
                                    ->action(function (Get $get, Set $set, $state) {
                                        if (!$state) {
                                            Notification::make()->warning()->title('Oops!')->body('Silakan unggah foto KTP terlebih dahulu dan tunggu sampai selesai.')->send();
                                            return;
                                        }

                                        $path = is_array($state) ? current($state) : $state;
                                        $absolutePath = Storage::disk('public')->path($path);

                                        if (!file_exists($absolutePath)) {
                                            Notification::make()->danger()->title('File tidak ditemukan di server. Pastikan upload selesai.')->send();
                                            return;
                                        }

                                        try {
                                            Notification::make()->info()->title('AI sedang membaca KTP...')->send();

                                            $response = Http::timeout(60)->attach(
                                                'ktp_image', file_get_contents($absolutePath), basename($absolutePath)
                                            )->post(env('KTP_AI_URL', 'http://localhost:5000/api/v1/extract-ktp/smart'));

                                            if ($response->successful()) {
                                                $result = $response->json();

                                                if ($result['status'] === 'success') {
                                                    $data = $result['data'];

                                                    // Auto-Fill Fields
                                                    $set('nik_ktp', $data['nik']);
                                                    $set('ktp_nama', $data['nama']);
                                                    $set('ktp_tempat_tgl_lahir', $data['tempat_tgl_lahir']);
                                                    $set('ktp_jenis_kelamin', $data['jenis_kelamin']);
                                                    $set('ktp_alamat', $data['alamat']);
                                                    $set('ktp_rt_rw', $data['rt_rw']);
                                                    $set('ktp_kel_desa', $data['kel_desa']);
                                                    $set('ktp_kecamatan', $data['kecamatan']);
                                                    $set('ktp_agama', $data['agama']);
                                                    $set('ktp_status_perkawinan', $data['status_perkawinan']);
                                                    $set('ktp_pekerjaan', $data['pekerjaan']);
                                                    $set('ktp_kewarganegaraan', $data['kewarganegaraan']);
                                                    $set('ktp_berlaku_hingga', $data['berlaku_hingga']);

                                                    $set('is_ktp_verified_by_ai', true);
                                                    $set('ai_completeness_score', $data['confidence_score']['data_completeness'] ?? 0);

                                                    Notification::make()
                                                        ->success()
                                                        ->title('Ekstraksi AI Berhasil!')
                                                        ->body('Skor kelengkapan data: ' . ($data['confidence_score']['data_completeness'] ?? 0) . '%')
                                                        ->send();
                                                } else {
                                                    Notification::make()->danger()->title('AI Gagal: ' . $result['error'])->send();
                                                }
                                            } else {
                                                Notification::make()->danger()->title('API Python menolak koneksi')->send();
                                            }
                                        } catch (\Exception $e) {
                                            Notification::make()->danger()->title('Server Error!')->body('Pastikan Server Python berjalan di port 5000.')->send();
                                        }
                                    })
                            ),

                        \Filament\Forms\Components\Grid::make(2)->schema([
                            TextInput::make('nik_ktp')->label('NIK KTP')->numeric()->maxLength(16),
                            TextInput::make('ktp_nama')->label('Nama Lengkap (KTP)'),
                            TextInput::make('ktp_tempat_tgl_lahir')->label('Tempat, Tgl Lahir'),
                            TextInput::make('ktp_jenis_kelamin')->label('Jenis Kelamin'),
                            TextInput::make('ktp_agama')->label('Agama'),
                            TextInput::make('ktp_status_perkawinan')->label('Status Perkawinan'),
                            TextInput::make('ktp_pekerjaan')->label('Pekerjaan'),
                            TextInput::make('ktp_kewarganegaraan')->label('Kewarganegaraan'),
                            TextInput::make('ktp_berlaku_hingga')->label('Berlaku Hingga'),
                            Textarea::make('ktp_alamat')->label('Alamat (KTP)')->columnSpanFull(),
                            \Filament\Forms\Components\Grid::make(3)->schema([
                                TextInput::make('ktp_rt_rw')->label('RT/RW'),
                                TextInput::make('ktp_kel_desa')->label('Kelurahan/Desa'),
                                TextInput::make('ktp_kecamatan')->label('Kecamatan'),
                            ])->columnSpanFull(),
                        ]),
                    ]),

                // ==========================================
                // DATA KEPEGAWAIAN & SISTEM
                // ==========================================
                Section::make('Data Kepegawaian')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        Select::make('attendance_group_id')
                            ->label('Grup Kehadiran (Sif)')
                            ->relationship('attendanceGroup', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Tentukan jam kerja karyawan ini masuk ke grup mana.'),

                        TextInput::make('job_title')
                            ->label('Jabatan')
                            ->maxLength(255),

                        DatePicker::make('join_date')
                            ->label('Tanggal Bergabung')
                            ->required(),

                        Select::make('employment_status')
                            ->label('Status Kepegawaian')
                            ->options([
                                'Tetap'     => 'Tetap',
                                'Kontrak'   => 'Kontrak',
                                'Probation' => 'Probation',
                            ])
                            ->required(),

                        DateTimePicker::make('email_verified_at'),

                        TextInput::make('leave_quota')
                            ->label('Sisa Cuti')
                            ->numeric()
                            ->default(12),
                    ]),
            ]);
    }
}
