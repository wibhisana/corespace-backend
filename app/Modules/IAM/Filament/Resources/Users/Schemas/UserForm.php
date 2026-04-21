<?php

declare(strict_types=1);

namespace App\Modules\IAM\Filament\Resources\Users\Schemas;

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
                // DATA PRIBADI & BERKAS (collapsible, full-width di bawah split)
                // ==========================================
                Section::make('Data Pribadi & Berkas')
                    ->description('Data identitas dan dokumen pendukung (Private Storage).')
                    ->columns(2)
                    ->collapsed()
                    ->schema([
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
                                'Menikah'       => 'Menikah',
                                'Cerai'         => 'Cerai',
                            ]),

                        Textarea::make('current_address')
                            ->label('Alamat Domisili')
                            ->columnSpanFull(),

                        TextInput::make('id_card_number')
                            ->label('No. KTP / KITAS'),

                        FileUpload::make('id_card_path')
                            ->label('Upload Foto KTP/KITAS')
                            ->disk('employee_private')
                            ->directory('identitas')
                            ->visibility('private')
                            ->maxSize(5120)
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
