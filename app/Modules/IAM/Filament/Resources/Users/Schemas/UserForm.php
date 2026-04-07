<?php

namespace App\Modules\IAM\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255),

                DateTimePicker::make('email_verified_at'),

                // Perbaikan Password agar aman (hashed) dan hanya wajib saat buat user baru
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create')
                    ->maxLength(255),

                // Mengubah input angka biasa menjadi Dropdown Relasi ke Department
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Department'),

                // Menambahkan Dropdown Relasi ke Roles (Spatie)
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                // Input Kuota Cuti di bawah input Roles
                TextInput::make('leave_quota')
                    ->label('Kuota Cuti (Hari)')
                    ->numeric()
                    ->default(12) // Nilai bawaan jika tidak diisi
                    ->required(),
            ]);
    }
}
