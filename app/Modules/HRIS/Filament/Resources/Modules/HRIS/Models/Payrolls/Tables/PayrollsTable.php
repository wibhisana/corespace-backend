<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Tables;

use App\Modules\HRIS\Models\Payroll;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollsTable
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

                TextColumn::make('month')
                    ->label('Bulan')
                    ->sortable(),

                TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                TextColumn::make('net_salary')
                    ->label('Take Home Pay')
                    ->money('IDR')
                    ->color('success')
                    ->weight('bold'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Approved' => 'info',
                        'Draft' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->recordActions([
                // Cetak PDF Slip Gaji
                Action::make('downloadPdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function (Payroll $record) {
                        $record->load(['user.department', 'user.unit']);

                        $pdf = Pdf::loadView('pdf.payslip', [
                            'payroll' => $record,
                            'user' => $record->user,
                            'date' => now()->format('d F Y'),
                        ]);

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            "Slip_Gaji_{$record->user->nik}_{$record->month}_{$record->year}.pdf"
                        );
                    }),

                // Tandai Dibayar
                Action::make('markAsPaid')
                    ->label('Tandai Dibayar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Payroll $record) => $record->update(['status' => 'Paid', 'payment_date' => now()]))
                    ->visible(fn (Payroll $record) => $record->status !== 'Paid'),

                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
