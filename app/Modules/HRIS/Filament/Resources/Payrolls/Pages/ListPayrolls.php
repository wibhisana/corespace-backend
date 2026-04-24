<?php

namespace App\Modules\HRIS\Filament\Resources\Payrolls\Pages;

use App\Modules\HRIS\Filament\Resources\Payrolls\PayrollResource;
use App\Models\User;
use App\Modules\HRIS\Models\Payroll;
use App\Modules\HRIS\Models\Attendance;
use App\Modules\HRIS\Models\OvertimeRequest;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
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
                ->color('primary')
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
                ->action(function (array $data) {
                    $users = User::with('employeeFinance')->get();
                    $count = 0;

                    foreach ($users as $user) {
                        $finance = $user->employeeFinance;
                        // Jika tidak ada data gaji pokok, skip
                        if (!$finance || !$finance->basic_salary) continue;

                        $basic = $finance->basic_salary;

                        // 1. Hitung Potongan Keterlambatan (Misal: Rp 1000 per menit terlambat)
                        $totalLateMinutes = Attendance::where('user_id', $user->id)
                            ->whereMonth('date', $data['month'])
                            ->whereYear('date', $data['year'])
                            ->sum('lateness_minutes');

                        $lateDeduction = $totalLateMinutes * 1000;

                        $deductions = [];
                        if ($lateDeduction > 0) {
                            $deductions[] = ['name' => "Keterlambatan ($totalLateMinutes menit)", 'amount' => $lateDeduction];
                        }

                        // 2. Hitung Tunjangan Lembur (Misal: Rp 50000 per jam / dibagi 60 per menit)
                        $totalOvertimeMinutes = OvertimeRequest::where('user_id', $user->id)
                            ->whereMonth('date', $data['month'])
                            ->whereYear('date', $data['year'])
                            ->where('status', 'Approved')
                            ->sum('duration_minutes');

                        $overtimeAllowance = round($totalOvertimeMinutes * (50000 / 60));

                        $allowances = [];
                        if ($overtimeAllowance > 0) {
                            $allowances[] = ['name' => "Lembur Approved ($totalOvertimeMinutes menit)", 'amount' => $overtimeAllowance];
                        }

                        $totalDedAmt = collect($deductions)->sum('amount');
                        $totalAllAmt = collect($allowances)->sum('amount');

                        $netSalary = $basic + $totalAllAmt - $totalDedAmt;

                        Payroll::updateOrCreate(
                            ['user_id' => $user->id, 'month' => $data['month'], 'year' => $data['year']],
                            [
                                'basic_salary' => $basic,
                                'total_allowances' => $totalAllAmt,
                                'total_deductions' => $totalDedAmt,
                                'net_salary' => $netSalary,
                                'allowance_details' => $allowances,
                                'deduction_details' => $deductions,
                                'status' => 'Draft',
                            ]
                        );
                        $count++;
                    }

                    Notification::make()
                        ->title('Payroll Generated!')
                        ->body("$count data slip gaji bulan {$data['month']}/{$data['year']} telah dibuat dan dihitung otomatis.")
                        ->success()
                        ->send();
                }),

            CreateAction::make(),
        ];
    }
}
