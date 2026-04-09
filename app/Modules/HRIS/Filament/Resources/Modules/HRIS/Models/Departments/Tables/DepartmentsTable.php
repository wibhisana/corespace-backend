<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Departments\Tables;

use App\Modules\HRIS\Models\Department;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DepartmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('name')
                    ->label('Nama Departemen')
                    ->searchable()
                    ->sortable()
                    ->description(fn (Department $record): ?string =>
                        $record->parent ? "Sub dari: {$record->parent->name}" : null
                    ),

                TextColumn::make('unit.name')
                    ->label('Unit Bisnis')
                    ->placeholder('—')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Induk')
                    ->placeholder('— Root —')
                    ->sortable(),

                TextColumn::make('children_count')
                    ->label('Sub-Dept')
                    ->counts('children')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('users_count')
                    ->label('Anggota')
                    ->counts('users')
                    ->sortable()
                    ->badge()
                    ->color('success'),

                TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->relationship('parent', 'name')
                    ->label('Induk Departemen')
                    ->searchable()
                    ->preload()
                    ->placeholder('Semua'),
            ])
            ->recordActions([
                // Move Up
                Action::make('moveUp')
                    ->icon('heroicon-o-arrow-up')
                    ->color('gray')
                    ->label('')
                    ->tooltip('Geser Naik')
                    ->action(function (Department $record) {
                        $sibling = Department::where('parent_id', $record->parent_id)
                            ->where('sort_order', '<', $record->sort_order)
                            ->orderByDesc('sort_order')
                            ->first();

                        if ($sibling) {
                            $tmpSort = $record->sort_order;
                            $record->update(['sort_order' => $sibling->sort_order]);
                            $sibling->update(['sort_order' => $tmpSort]);
                        }
                    }),

                // Move Down
                Action::make('moveDown')
                    ->icon('heroicon-o-arrow-down')
                    ->color('gray')
                    ->label('')
                    ->tooltip('Geser Turun')
                    ->action(function (Department $record) {
                        $sibling = Department::where('parent_id', $record->parent_id)
                            ->where('sort_order', '>', $record->sort_order)
                            ->orderBy('sort_order')
                            ->first();

                        if ($sibling) {
                            $tmpSort = $record->sort_order;
                            $record->update(['sort_order' => $sibling->sort_order]);
                            $sibling->update(['sort_order' => $tmpSort]);
                        }
                    }),

                EditAction::make(),

                // Delete with protection
                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Departemen')
                    ->action(function (Department $record) {
                        // Proteksi: tidak bisa hapus jika masih punya anggota
                        if ($record->users()->count() > 0) {
                            Notification::make()
                                ->title('Tidak bisa dihapus')
                                ->body("Departemen \"{$record->name}\" masih memiliki {$record->users()->count()} karyawan. Pindahkan semua karyawan terlebih dahulu.")
                                ->danger()
                                ->send();
                            return;
                        }

                        // Proteksi: tidak bisa hapus jika masih punya sub-department
                        if ($record->children()->count() > 0) {
                            Notification::make()
                                ->title('Tidak bisa dihapus')
                                ->body("Departemen \"{$record->name}\" masih memiliki {$record->children()->count()} sub-departemen. Hapus atau pindahkan sub-departemen terlebih dahulu.")
                                ->danger()
                                ->send();
                            return;
                        }

                        $record->delete();

                        Notification::make()
                            ->title('Departemen dihapus')
                            ->success()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
