<?php

namespace App\Modules\IAM\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ==========================================
                // KATEGORI 1: Data HR (Diisi Admin/HR)
                // ==========================================
                Section::make('Data Karyawan (HR)')
                    ->description('Informasi penempatan di struktur Holding.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Kantor')
                            ->email()
                            ->required()
                            ->maxLength(255),

                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => $state)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255)
                            ->helperText('Kosongkan jika tidak ingin mengubah password.'),

                        TextInput::make('nik')
                            ->label('NIK (Nomor Induk Karyawan)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50),

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
                            ->helperText('Manager yang akan meng-approve cuti & lembur karyawan ini.'),

                        // 💡 PENAMBAHAN GRUP KEHADIRAN DI SINI
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
                                'Tetap' => 'Tetap',
                                'Kontrak' => 'Kontrak',
                                'Probation' => 'Probation',
                            ])
                            ->required(),

                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->label('Role Akses'),
                    ]),

                // ==========================================
                // KATEGORI 2: Data Pribadi & Berkas (ESS)
                // ==========================================
                Section::make('Data Pribadi & Berkas')
                    ->description('Data identitas dan dokumen pendukung (Private Storage).')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('phone_number')
                            ->label('No. HP (WhatsApp)')
                            ->tel()
                            ->maxLength(20),

                        TextInput::make('personal_email')
                            ->label('Email Pribadi')
                            ->email()
                            ->maxLength(255),

                        Select::make('gender')
                            ->label('Jenis Kelamin')
                            ->options(['L' => 'Laki-laki', 'P' => 'Perempuan']),

                        TextInput::make('birth_place')
                            ->label('Tempat Lahir'),

                        DatePicker::make('birth_date')
                            ->label('Tanggal Lahir'),

                        Select::make('marital_status')
                            ->label('Status Pernikahan')
                            ->options([
                                'Belum Menikah' => 'Belum Menikah',
                                'Menikah' => 'Menikah',
                                'Cerai' => 'Cerai',
                            ]),

                        Textarea::make('current_address')
                            ->label('Alamat Domisili')
                            ->columnSpanFull(),

                        // --- IDENTITAS & UPLOAD BERKAS ---
                        TextInput::make('id_card_number')
                            ->label('No. KTP / KITAS'),

                        FileUpload::make('id_card_path')
                            ->label('Upload Foto KTP/KITAS')
                            ->disk('employee_private') // Sesuai config sebelumnya
                            ->directory('identitas')
                            ->visibility('private')
                            ->maxSize(5120) // 5MB
                            ->image(),

                        TextInput::make('tax_id')
                            ->label('NPWP'),

                        FileUpload::make('tax_id_path')
                            ->label('Upload Foto NPWP')
                            ->disk('employee_private')
                            ->directory('pajak')
                            ->visibility('private')
                            ->maxSize(5120)
                            ->image(),

                        TextInput::make('emergency_contact_name')
                            ->label('Kontak Darurat (Nama)'),

                        TextInput::make('emergency_contact_phone')
                            ->label('Kontak Darurat (Telp)')
                            ->tel(),
                    ]),

                Section::make('Info Sistem')
                    ->collapsed()
                    ->schema([
                        DateTimePicker::make('email_verified_at'),
                        TextInput::make('leave_quota')
                            ->label('Sisa Cuti')
                            ->numeric()
                            ->default(12),
                    ]),
            ]);
    }
}
