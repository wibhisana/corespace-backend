<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\LeaveTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Forms\Get;
use Filament\Schemas\Schema;

class LeaveTypeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Aturan Jenis Cuti')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Cuti')
                            ->placeholder('Cth: Cuti Tahunan, Cuti Sakit')
                            ->required(),

                        TextInput::make('default_quota')
                            ->label('Kuota Default (Hari)')
                            ->numeric()
                            ->default(0)
                            ->disabled(fn (Get $get) => $get('is_unlimited'))
                            ->helperText('Jumlah hari yang diberikan otomatis setiap tahun.'),

                        Toggle::make('is_unlimited')
                            ->label('Kuota Tak Terbatas')
                            ->live()
                            ->helperText('Aktifkan untuk jenis cuti seperti Cuti Berduka atau Melahirkan.'),

                        Toggle::make('requires_attachment')
                            ->label('Wajib Lampiran')
                            ->helperText('Karyawan wajib mengunggah dokumen pendukung (Cth: Surat Dokter).'),

                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true),
                    ]),
            ]);
    }
}
