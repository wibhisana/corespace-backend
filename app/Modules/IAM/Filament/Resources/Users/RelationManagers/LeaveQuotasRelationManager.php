<?php

namespace App\Modules\IAM\Filament\Resources\Users\RelationManagers;

use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns;
use Filament\Tables\Table;

class LeaveQuotasRelationManager extends RelationManager
{
    // Nama relasi yang ada di model User.php
    protected static string $relationship = 'leaveQuotas';

    protected static ?string $recordTitleAttribute = 'year';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\TextInput::make('year')
                    ->label('Tahun')
                    ->required()
                    ->numeric(),
                Components\TextInput::make('quota')
                    ->label('Total Kuota (Hari)')
                    ->required()
                    ->numeric(),
                Components\TextInput::make('used')
                    ->label('Sudah Dipakai')
                    ->default(0)
                    ->numeric(),
                Components\DatePicker::make('expires_at')
                    ->label('Berlaku Sampai Tanggal')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('year')
            ->columns([
                Columns\TextColumn::make('year')->label('Tahun'),
                Columns\TextColumn::make('quota')->label('Total Kuota'),
                Columns\TextColumn::make('used')->label('Terpakai'),
                Columns\TextColumn::make('expires_at')->label('Kedaluwarsa')->date(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Actions\CreateAction::make(),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
