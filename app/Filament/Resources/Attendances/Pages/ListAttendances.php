<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceDispensation;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\DataMigrationService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListAttendances extends ListRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import_attendance_csv')
                ->label('Tarik / Unggah Berkas Presensi Mesin (XLS / CSV)')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('warning')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                ->modalHeading('Tarik & Unggah Berkas Log Presensi Mesin (XLS / CSV)')
                ->modalDescription('Unggah berkas log presensi mesin cabang (.xls format Solution/ZK atau berkas .csv). Sistem akan secara otomatis MENGGABUNGKAN data jam masuk/keluar dengan data Cuti Resmi yang disetujui dan Logbook Dispensasi HRD & GA (termasuk membebaskan denda pegawai yang memiliki izin telat masuk sah).')
                ->form([
                    FileUpload::make('csv_file')
                        ->label('Pilih Berkas Mesin Presensi (.xls atau .csv)')
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/octet-stream', 'application/x-msexcel'])
                        ->disk('local')
                        ->directory('temp_imports')
                        ->required()
                        ->helperText('Mendukung berkas Excel ekspor mesin Solution/ZK (.xls template: Name, Date, Timetable, On duty, Off duty, Clock In, Clock Out, OT Time) maupun CSV HRIS baku.'),
                ])
                ->modalSubmitActionLabel('Mulai Gabungkan Data')
                ->action(function (array $data) {
                    $path = Storage::disk('local')->path($data['csv_file']);
                    $service = app(DataMigrationService::class);
                    $result = $service->importAttendancesFromCsv($path);

                    if ($result['success']) {
                        Notification::make()
                            ->title('Sinkronisasi Presensi Sukses')
                            ->body($result['message'] . ' Data cuti dan dispensasi logbook berhasil digabungkan.')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal Mengimpor Presensi')
                            ->body($result['message'])
                            ->danger()
                            ->send();
                    }

                    if (File::exists($path)) {
                        File::delete($path);
                    }
                }),

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
                            'Catatan / Rincian Gabungan',
                        ]);

                        foreach ($attendances as $row) {
                            $isLateDisp = str_contains($row->notes ?? '', 'Dispensasi Telat');
                            $sumber = 'Tarik Berkas Mesin Cabang';
                            if (str_contains($row->notes ?? '', 'Cuti Resmi') || str_contains($row->notes ?? '', 'Cuti')) {
                                $sumber = 'Permohonan Cuti (ESS/HRD)';
                            } elseif ($isLateDisp) {
                                $sumber = 'Mesin Presensi + Dispensasi Telat (HRD)';
                            } elseif (str_contains($row->notes ?? '', 'Dispensasi') || $row->status === 'Excused') {
                                $sumber = 'Logbook HRD & GA (WA/DM)';
                            }

                            $statusLabel = match ($row->status) {
                                'Present' => $isLateDisp ? 'Hadir (Dispensasi Telat)' : 'Hadir Tepat Waktu',
                                'Late' => 'Terlambat (' . ($row->late_minutes ?? 0) . ' Mnt)',
                                'Excused' => str_contains($row->notes ?? '', 'Cuti') ? 'Cuti Resmi' : 'Izin / Sakit (Dispensasi)',
                                'Absent' => 'Alpa / Tanpa Keterangan',
                                default => $row->status ?? '-',
                            };

                            fputcsv($file, [
                                $row->id,
                                $row->date ? $row->date->format('Y-m-d') : '',
                                $row->employee?->nip ?? '-',
                                $row->employee?->full_name ?? '-',
                                $row->employee?->branch?->name ?? '-',
                                $row->employee?->department?->name ?? '-',
                                $row->clock_in ? Carbon::parse($row->clock_in)->format('H:i') : '-',
                                $row->clock_out ? Carbon::parse($row->clock_out)->format('H:i') : '-',
                                $statusLabel,
                                $row->late_minutes ?? 0,
                                number_format($row->penalty_amount ?? 0, 0, ',', '.'),
                                $sumber,
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

                    $employees = Employee::accessibleBy()
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
                        fputcsv($file, ['PT BPR BANDA RAYA - MATRIKS REKAPITULASI PRESENSI TERPADU (MESIN, CUTI & LOGBOOK)']);
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
                        $headerCols[] = 'Total Cuti';
                        $headerCols[] = 'Total Izin / Dispensasi';
                        $headerCols[] = 'Total Terlambat';
                        $headerCols[] = 'Total Denda (Rp)';
                        $headerCols[] = 'Tidak Absen / Alpa';
                        $headerCols[] = 'Keterangan / Rincian Logbook & Cuti';
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
                            $totalCuti = 0;
                            $totalIzin = 0;
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
                                    $isLateDisp = str_contains($att->notes ?? '', 'Dispensasi Telat');

                                    if (str_contains($att->notes ?? '', 'Cuti Resmi') || ($att->status === 'Excused' && str_contains($att->notes ?? '', 'Cuti'))) {
                                        $row[] = 'CUTI';
                                        $row[] = 'CUTI';
                                        $totalCuti++;
                                        $notesList[] = "Tgl {$d}: Cuti Resmi";
                                    } elseif ($att->status === 'Excused' && ! $isLateDisp) {
                                        $row[] = 'IZIN';
                                        $row[] = 'IZIN';
                                        $totalIzin++;
                                        $notesList[] = "Tgl {$d}: Dispensasi HRD/GA";
                                    } elseif ($att->status === 'Present' || $isLateDisp) {
                                        $row[] = $in;
                                        $row[] = $out;
                                        $totalHadir++;
                                        if ($isLateDisp) {
                                            $notesList[] = "Tgl {$d}: Hadir (Dispensasi Telat Masuk)";
                                        }
                                    } elseif ($att->status === 'Late' || ($att->late_minutes ?? 0) > 0) {
                                        $row[] = $in;
                                        $row[] = $out;
                                        $totalHadir++;
                                        $totalTerlambat++;
                                        $totalDenda += $att->penalty_amount ?: 50000;
                                    } else {
                                        $row[] = $in;
                                        $row[] = $out;
                                    }

                                    if (! empty($att->notes) && ! in_array("Tgl {$d}: {$att->notes}", $notesList)) {
                                        $notesList[] = "Tgl {$d}: {$att->notes}";
                                    }
                                } else {
                                    // Cek langsung jika ada Cuti atau Dispensasi yang belum terindeks
                                    $unSyncedLeave = LeaveRequest::where('employee_id', $emp->id)
                                        ->whereIn('status', ['Approved', 'approved', 'Approved_HR'])
                                        ->whereDate('start_date', '<=', $dateObj->toDateString())
                                        ->whereDate('end_date', '>=', $dateObj->toDateString())
                                        ->first();

                                    $unSyncedDisp = AttendanceDispensation::where('employee_id', $emp->id)
                                        ->whereDate('date', $dateObj->toDateString())
                                        ->first();

                                    if ($unSyncedLeave) {
                                        $row[] = 'CUTI';
                                        $row[] = 'CUTI';
                                        $totalCuti++;
                                        $notesList[] = "Tgl {$d}: Cuti Resmi";
                                    } elseif ($unSyncedDisp) {
                                        if ($unSyncedDisp->isLateArrivalDispensation()) {
                                            $row[] = 'MENUNGGU PRESENSI';
                                            $row[] = '-';
                                            $notesList[] = "Tgl {$d}: Izin Telat Masuk (Menunggu Mesin)";
                                        } else {
                                            $row[] = 'IZIN';
                                            $row[] = 'IZIN';
                                            $totalIzin++;
                                            $notesList[] = "Tgl {$d}: Dispensasi HRD/GA";
                                        }
                                    } elseif ($isSunday) {
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
                            $row[] = $totalCuti;
                            $row[] = $totalIzin;
                            $row[] = $totalTerlambat;
                            $row[] = number_format($totalDenda, 0, ',', '.');
                            $row[] = $totalTidakAbsen;
                            $row[] = implode('; ', array_slice(array_unique($notesList), 0, 3));

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
