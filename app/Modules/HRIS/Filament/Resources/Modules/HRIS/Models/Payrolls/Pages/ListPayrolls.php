<?php

namespace App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\Pages;

use App\Modules\HRIS\Filament\Resources\Modules\HRIS\Models\Payrolls\PayrollResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class ListPayrolls extends ListRecords
{
    protected static string $resource = PayrollResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generatePayroll')
                ->label('Generate Gaji Bulanan')
                ->color('success')
                ->icon('heroicon-o-cpu-chip')
                ->form([
                    Select::make('month')
                        ->label('Bulan')
                        ->options([
                            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                        ])
                        ->required()
                        ->default(now()->month),
                    TextInput::make('year')
                        ->label('Tahun')
                        ->numeric()
                        ->default(now()->year)
                        ->required(),
                ])
                // TAMBAHKAN BARIS INI UNTUK KEAMANAN
                ->visible(fn () => auth()->user()->hasAnyRole(['HR Manager', 'Super Admin']))
                ->action(function (array $data) {
                    $users = \App\Models\User::all();
                    $workDays = 22;
                    $count = 0;

                    foreach ($users as $user) {
                        $finance = $user->finance;
                        if (!$finance || !$finance->basic_salary) continue;

                        $presentDays = \App\Modules\HRIS\Models\Attendance::where('user_id', $user->id)
                            ->whereMonth('date', $data['month'])
                            ->whereYear('date', $data['year'])
                            ->count();

                        $basic = $finance->basic_salary;
                        $deduction = ($basic / $workDays) * max(0, $workDays - $presentDays);

                        \App\Modules\HRIS\Models\Payroll::updateOrCreate(
                            ['user_id' => $user->id, 'month' => $data['month'], 'year' => $data['year']],
                            [
                                'basic_salary' => $basic,
                                'total_present' => $presentDays,
                                'deduction' => $deduction,
                                'net_salary' => $basic - $deduction,
                                'is_paid' => false,
                            ]
                        );
                        $count++;
                    }

                    Notification::make()
                        ->title('Generate Selesai')
                        ->body("$count data payroll telah dibuat sebagai DRAFT. Silakan rilis gaji agar dapat dilihat staff.")
                        ->success()
                        ->persistent() // Agar notifikasi tidak hilang sendiri (seperti pop-up)
                        ->send();
                }),
        ];
    }
}
