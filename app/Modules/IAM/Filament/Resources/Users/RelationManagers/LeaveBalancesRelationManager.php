<?php

namespace App\Modules\IAM\Filament\Resources\Users\RelationManagers;

use App\Modules\HRIS\Models\LeaveBalance;
use Filament\Actions;
use Filament\Forms\Components;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns;
use Filament\Tables\Table;

class LeaveBalancesRelationManager extends RelationManager
{
    // Nama relasi di User model
    protected static string $relationship = 'leaveBalances';

    protected static ?string $title = 'Saldo Cuti';

    protected static ?string $recordTitleAttribute = 'year';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Components\Select::make('leave_type_id')
                    ->label('Jenis Cuti')
                    ->relationship('leaveType', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                Components\TextInput::make('year')
                    ->label('Tahun')
                    ->numeric()
                    ->default(now()->year)
                    ->required(),

                Components\Select::make('source')
                    ->label('Tipe Bucket')
                    ->options([
                        LeaveBalance::SOURCE_ANNUAL        => 'Annual',
                        LeaveBalance::SOURCE_CARRY_FORWARD => 'Carry-Forward',
                    ])
                    ->default(LeaveBalance::SOURCE_ANNUAL)
                    ->required(),

                Components\TextInput::make('total_quota')
                    ->label('Jatah (Hari)')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Components\TextInput::make('used_quota')
                    ->label('Terpakai')
                    ->numeric()
                    ->default(0)
                    ->required(),

                Components\DatePicker::make('expires_at')
                    ->label('Kedaluwarsa'),

                Components\Textarea::make('notes')
                    ->label('Catatan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('year')
            ->columns([
                Columns\TextColumn::make('leaveType.name')->label('Jenis Cuti')->badge(),
                Columns\TextColumn::make('year')->label('Tahun')->sortable(),
                Columns\TextColumn::make('source')
                    ->label('Bucket')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        LeaveBalance::SOURCE_CARRY_FORWARD => 'warning',
                        default => 'info',
                    }),
                Columns\TextColumn::make('total_quota')->label('Jatah')->alignCenter(),
                Columns\TextColumn::make('used_quota')->label('Terpakai')->alignCenter()->color('danger'),
                Columns\TextColumn::make('remaining_quota')->label('Sisa')->alignCenter()->color('success')->weight('bold'),
                Columns\TextColumn::make('expires_at')->label('Kedaluwarsa')->date('d M Y')->placeholder('—'),
            ])
            ->defaultSort('expires_at', 'asc')
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
