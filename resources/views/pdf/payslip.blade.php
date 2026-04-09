<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; margin: 0; }
        .title { font-size: 16px; margin: 5px 0 0 0; text-transform: uppercase; letter-spacing: 1px; }
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; }
        .content-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .content-table th, .content-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .content-table th { background-color: #f4f4f4; }
        .amount { text-align: right !important; }
        .net-pay { font-size: 16px; font-weight: bold; background-color: #e8f5e9; }
        .footer { margin-top: 40px; text-align: right; }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="company-name">{{ $user->unit->name ?? 'KPN CORP' }}</h1>
        <h2 class="title">Slip Gaji Karyawan</h2>
    </div>

    <table class="info-table">
        <tr>
            <td width="15%"><strong>Nama</strong></td>
            <td width="35%">: {{ $user->name }}</td>
            <td width="15%"><strong>Periode</strong></td>
            <td width="35%">: Bulan {{ $payroll->month }} / Tahun {{ $payroll->year }}</td>
        </tr>
        <tr>
            <td><strong>NIK</strong></td>
            <td>: {{ $user->nik }}</td>
            <td><strong>Departemen</strong></td>
            <td>: {{ $user->department->name ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Jabatan</strong></td>
            <td>: {{ $user->job_title ?? '-' }}</td>
            <td><strong>Tgl Bayar</strong></td>
            <td>: {{ $payroll->payment_date ? $payroll->payment_date->format('d M Y') : '-' }}</td>
        </tr>
    </table>

    <table class="content-table">
        <thead>
            <tr>
                <th width="50%">Penerimaan (Earnings)</th>
                <th width="50%">Potongan (Deductions)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="vertical-align: top;">
                    <table width="100%">
                        <tr>
                            <td>Gaji Pokok</td>
                            <td class="amount">Rp {{ number_format($payroll->basic_salary, 0, ',', '.') }}</td>
                        </tr>
                        @if($payroll->allowance_details)
                            @foreach($payroll->allowance_details as $allowance)
                            <tr>
                                <td>{{ $allowance['name'] }}</td>
                                <td class="amount">Rp {{ number_format($allowance['amount'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @endif
                    </table>
                </td>
                <td style="vertical-align: top;">
                    <table width="100%">
                        @if($payroll->deduction_details)
                            @foreach($payroll->deduction_details as $deduction)
                            <tr>
                                <td>{{ $deduction['name'] }}</td>
                                <td class="amount">Rp {{ number_format($deduction['amount'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        @else
                            <tr><td colspan="2" style="color:#888;">Tidak ada potongan</td></tr>
                        @endif
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="100%">
                        <tr>
                            <td><strong>Total Penerimaan</strong></td>
                            <td class="amount"><strong>Rp {{ number_format($payroll->basic_salary + $payroll->total_allowances, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table width="100%">
                        <tr>
                            <td><strong>Total Potongan</strong></td>
                            <td class="amount"><strong>Rp {{ number_format($payroll->total_deductions, 0, ',', '.') }}</strong></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="net-pay">
                <td colspan="2">
                    <table width="100%">
                        <tr>
                            <td>TAKE HOME PAY (Gaji Bersih)</td>
                            <td class="amount">Rp {{ number_format($payroll->net_salary, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Jakarta, {{ $date }}</p>
        <br><br><br>
        <p><strong>( HR Department )</strong></p>
    </div>

</body>
</html>
