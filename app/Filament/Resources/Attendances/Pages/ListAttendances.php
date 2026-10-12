<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_csv')
                ->label('Unduh Rekap Presensi (CSV / Excel)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): StreamedResponse {
                    $attendances = Attendance::accessibleBy()
                        ->with(['employee.branch', 'employee.department'])
                        ->orderBy('date', 'desc')
                        ->get();

                    $headers = [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="rekap_presensi_bpr_banda_raya_' . date('Ymd_His') . '.csv"',
                        'Pragma' => 'no-cache',
                        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                        'Expires' => '0',
                    ];

                    $callback = function () use ($attendances) {
                        $file = fopen('php://output', 'w');
                        // Add UTF-8 BOM for Excel compatibility
                        fputs($file, "\xEF\xBB\xBF");

                        // Header row
                        fputcsv($file, [
                            'ID',
                            'Tanggal',
                            'NIP Pegawai',
                            'Nama Pegawai',
                            'Kantor Cabang',
                            'Departemen',
                            'Jam Masuk',
                            'Jam Pulang',
                            'Status Kehadiran',
                            'Terlambat (Menit)',
                            'Denda Keterlambatan (Rp)',
                            'Sumber Pencatatan',
                            'Catatan / Dispensasi',
                        ]);

                        foreach ($attendances as $row) {
                            fputcsv($file, [
                                $row->id,
                                $row->date ? $row->date->format('Y-m-d') : '',
                                $row->employee?->nip ?? '-',
                                $row->employee?->full_name ?? '-',
                                $row->employee?->branch?->name ?? '-',
                                $row->employee?->department?->name ?? '-',
                                $row->clock_in ?? '-',
                                $row->clock_out ?? '-',
                                $row->status ?? '-',
                                $row->late_minutes ?? 0,
                                number_format($row->penalty_amount ?? 0, 0, ',', '.'),
                                $row->status === 'Excused' ? 'Logbook HR/GA' : 'Mesin Fingerprint Cabang',
                                $row->notes ?? '-',
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),

            Action::make('export_monthly_matrix')
                ->label('Unduh Matriks Rekap Bulanan (Buku Rekap HR)')
                ->icon('heroicon-o-table-cells')
                ->color('primary')
                ->action(function (): StreamedResponse {
                    $year = now()->year;
                    $month = now()->month;
                    $daysInMonth = Carbon::create($year, $month)->daysInMonth;

                    $employees = \App\Models\Employee::accessibleBy()
                        ->with(['branch', 'department'])
                        ->where('status', 'Active')
                        ->orderBy('branch_id')
                        ->orderBy('nip')
                        ->get();

                    $headers = [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="matriks_rekap_presensi_' . date('Y_m') . '_bpr_banda_raya.csv"',
                        'Pragma' => 'no-cache',
                        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                        'Expires' => '0',
                    ];

                    $callback = function () use ($employees, $year, $month, $daysInMonth) {
                        $file = fopen('php://output', 'w');
                        fputs($file, "\xEF\xBB\xBF");

                        // Row 1: Judul Laporan
                        fputcsv($file, ['PT BPR BANDA RAYA - MATRIKS REKAPITULASI PRESENSI BULANAN']);
                        fputcsv($file, ['Periode: ' . Carbon::create($year, $month)->translatedFormat('F Y')]);
                        fputcsv($file, []);

                        // Header Columns
                        $headerCols = ['No', 'NIK / NIP', 'Nama Pegawai', 'Kantor / Cabang'];
                        for ($d = 1; $d <= $daysInMonth; $d++) {
                            $pad = str_pad($d, 2, '0', STR_PAD_LEFT);
                            $headerCols[] = "{$pad} In";
                            $headerCols[] = "{$pad} Out";
                        }
                        $headerCols[] = 'Total Hadir';
                        $headerCols[] = 'Total Terlambat';
                        $headerCols[] = 'Total Denda (Rp)';
                        $headerCols[] = 'Tidak Absen / Alpa';
                        $headerCols[] = 'Keterangan Absen / Dispensasi';
                        fputcsv($file, $headerCols);

                        $no = 1;
                        foreach ($employees as $emp) {
                            $attendances = Attendance::where('employee_id', $emp->id)
                                ->whereYear('date', $year)
                                ->whereMonth('date', $month)
                                ->get()
                                ->keyBy(fn ($a) => Carbon::parse($a->date)->format('j'));

                            $row = [
                                $no++,
                                $emp->nip,
                                $emp->full_name,
                                $emp->branch?->name ?? 'Kantor Pusat',
                            ];

                            $totalHadir = 0;
                            $totalTerlambat = 0;
                            $totalDenda = 0;
                            $totalTidakAbsen = 0;
                            $notesList = [];

                            for ($d = 1; $d <= $daysInMonth; $d++) {
                                $dateObj = Carbon::create($year, $month, $d);
                                $isSunday = $dateObj->isSunday();
                                $att = $attendances->get($d);

                                if ($att) {
                                    $in = $att->clock_in ? Carbon::parse($att->clock_in)->format('H:i') : '-';
                                    $out = $att->clock_out ? Carbon::parse($att->clock_out)->format('H:i') : '-';
                                    $row[] = $in;
                                    $row[] = $out;

                                    if (in_array($att->status, ['Present', 'Late'])) {
                                        $totalHadir++;
                                    }
                                    if ($att->status === 'Late' || ($att->late_minutes ?? 0) > 0) {
                                        $totalTerlambat++;
                                        $totalDenda += $att->penalty_amount ?: 50000;
                                    }
                                    if (! empty($att->notes)) {
                                        $notesList[] = "Tgl {$d}: {$att->notes}";
                                    }
                                } else {
                                    if ($isSunday) {
                                        $row[] = 'LIBUR';
                                        $row[] = 'LIBUR';
                                    } else {
                                        $row[] = '-';
                                        $row[] = '-';
                                        if ($dateObj->lte(now())) {
                                            $totalTidakAbsen++;
                                        }
                                    }
                                }
                            }

                            $row[] = $totalHadir;
                            $row[] = $totalTerlambat;
                            $row[] = number_format($totalDenda, 0, ',', '.');
                            $row[] = $totalTidakAbsen;
                            $row[] = implode('; ', array_slice($notesList, 0, 3));

                            fputcsv($file, $row);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
            CreateAction::make()
                ->label('Input Presensi Manual')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }
}
