<?php

namespace App\Modules\HRIS\Filament\Resources\Attendances\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('shift.name')
                    ->label('Sif')
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),

                TextColumn::make('clock_in')
                    ->label('Masuk')
                    ->dateTime('H:i') // Hanya tampilkan jamnya saja agar rapi
                    ->placeholder('--:--'),

                TextColumn::make('clock_out')
                    ->label('Pulang')
                    ->dateTime('H:i')
                    ->placeholder('--:--'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Present' => 'success',
                        'Late' => 'warning',
                        'Absent' => 'danger',
                        'On_Leave' => 'info',
                        'Day_Off' => 'gray',
                        default => 'gray',
                    }),

                TextColumn::make('lateness_minutes')
                    ->label('Terlambat')
                    ->suffix(' mnt')
                    ->alignCenter()
                    ->color('danger'),
            ])
            ->defaultSort('date', 'desc') // Mengurutkan dari hari paling baru
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Present' => 'Hadir',
                        'Late' => 'Terlambat',
                        'Absent' => 'Absen',
                        'On_Leave' => 'Cuti',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
