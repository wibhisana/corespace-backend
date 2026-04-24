<?php

namespace App\Modules\HRIS\Filament\Resources\Units\Tables;

use App\Modules\HRIS\Models\Unit;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UnitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Unit')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tipe Unit')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Holding' => 'danger',
                        'Subsidiary' => 'success',
                        'Branch' => 'info',
                        default => 'gray',
                    })
                    ->searchable(),

                TextColumn::make('departments_count')
                    ->label('Departemen')
                    ->counts('departments')
                    ->badge()
                    ->color('info'),

                TextColumn::make('departments.users_count')
                    ->label('Total Karyawan')
                    ->getStateUsing(fn (Unit $record): int =>
                        $record->departments->sum(fn ($dept) => $dept->users()->count())
                    )
                    ->badge()
                    ->color('success'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Tipe Unit')
                    ->options([
                        'Holding' => 'Holding',
                        'Subsidiary' => 'Anak Perusahaan',
                        'Branch' => 'Kantor Cabang',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),

                // Delete with protection
                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Unit')
                    ->action(function (Unit $record) {
                        if ($record->departments()->count() > 0) {
                            Notification::make()
                                ->title('Tidak bisa dihapus')
                                ->body("Unit \"{$record->name}\" masih memiliki {$record->departments()->count()} departemen. Pindahkan atau hapus semua departemen terlebih dahulu.")
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->delete();

                        Notification::make()
                            ->title('Unit dihapus')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
