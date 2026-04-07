<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Tables;

use App\Modules\HRIS\Models\Payroll;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PayrollsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('month')
                    ->label('Bulan'),
                TextColumn::make('year')
                    ->label('Tahun'),
                TextColumn::make('net_salary')
                    ->label('Gaji Bersih')
                    ->money('IDR')
                    ->color('success')
                    ->weight('bold'),
                IconColumn::make('is_paid')
                    ->label('Status Rilis')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
            ])
            ->recordActions([
                // Aksi untuk HR melakukan rilis/pembayaran
                Action::make('markAsPaid')
                    ->label('Rilis Gaji')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Tandai gaji ini sebagai sudah dibayar? Karyawan akan bisa melihat slip ini di aplikasi mereka.')
                    ->action(fn (Payroll $record) => $record->update(['is_paid' => true]))
                    ->visible(fn (Payroll $record) => !$record->is_paid),

                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
