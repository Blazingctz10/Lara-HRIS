<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use App\Models\AttendanceDispensation;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\DataMigrationService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
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
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/x-msexcel'])
                        ->maxSize(10240)
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
                ->label('Unduh Rekap Presensi (Excel / XLS)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): StreamedResponse {
                    $attendances = Attendance::accessibleBy()
                        ->with(['employee.branch', 'employee.department'])
                        ->orderBy('date', 'desc')
                        ->get();

                    $filename = 'Rekap_Log_Presensi_BPR_' . date('Ymd_His') . '.xls';
                    $headers = [
                        'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                        'Pragma' => 'no-cache',
                        'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                        'Expires' => '0',
                    ];

                    $callback = function () use ($attendances) {
                        $streamHandle = fopen('php://output', 'w');
                        fputs($streamHandle, "\xEF\xBB\xBF");

                        $html = '<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]>
<xml>
 <x:ExcelWorkbook>
  <x:ExcelWorksheets>
   <x:ExcelWorksheet>
    <x:Name>Log Presensi</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
     <x:FreezePanes/>
     <x:FrozenNoSplit/>
     <x:SplitHorizontal>5</x:SplitHorizontal>
     <x:TopRowBottomPane>5</x:TopRowBottomPane>
     <x:ActivePane>0</x:ActivePane>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
  body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 11px; }
  table { border-collapse: collapse; width: 100%; }
  .title-main { font-size: 15px; font-weight: bold; color: #0E6838; }
  .subtitle { font-size: 11px; font-weight: bold; color: #333333; }
  .meta { font-size: 10px; color: #666666; }
  .th-head { background-color: #0E6838; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #084022; padding: 6px 8px; font-size: 11px; }
  .td-cell { border: 1px solid #cbd5e1; padding: 4px 6px; font-size: 10px; vertical-align: middle; }
  .td-center { text-align: center; }
  .td-right { text-align: right; }
  .td-nip { font-family: "Courier New", monospace; text-align: center; mso-number-format: "\@"; }
  .badge-present { color: #047857; font-weight: bold; background-color: #d1fae5; }
  .badge-late { color: #dc2626; font-weight: bold; background-color: #fee2e2; }
  .badge-cuti { color: #1e40af; font-weight: bold; background-color: #dbeafe; }
  .badge-izin { color: #92400e; font-weight: bold; background-color: #fef3c7; }
  .badge-alpa { color: #991b1b; font-weight: bold; background-color: #ffe4e6; }
  .tr-odd { background-color: #f8fafc; }
  .tr-even { background-color: #ffffff; }
</style>
</head>
<body>
<table>
  <tr><td colspan="12" class="title-main">PT BPR BANDA RAYA</td></tr>
  <tr><td colspan="12" class="subtitle">REKAPITULASI LOG PRESENSI & KEHADIRAN STAF</td></tr>
  <tr><td colspan="12" class="meta">Waktu Cetak: ' . now()->format('d/m/Y H:i:s') . ' WIB | Total Transaksi: ' . count($attendances) . ' Data</td></tr>
  <tr><td colspan="12">&nbsp;</td></tr>
  <thead>
    <tr>
      <th class="th-head">No</th>
      <th class="th-head">Tanggal</th>
      <th class="th-head">NIP Pegawai</th>
      <th class="th-head">Nama Pegawai</th>
      <th class="th-head">Unit Kantor</th>
      <th class="th-head">Departemen</th>
      <th class="th-head">Jam Masuk</th>
      <th class="th-head">Jam Pulang</th>
      <th class="th-head">Status Kehadiran</th>
      <th class="th-head">Terlambat (Mnt)</th>
      <th class="th-head">Denda (Rp)</th>
      <th class="th-head">Sumber Data & Rincian Logbook</th>
    </tr>
  </thead>
  <tbody>';

                        fputs($streamHandle, $html);

                        $no = 1;
                        foreach ($attendances as $row) {
                            $isLateDisp = str_contains($row->notes ?? '', 'Dispensasi Telat');
                            $sumber = 'Tarik Berkas Mesin';
                            if (str_contains($row->notes ?? '', 'Cuti Resmi') || str_contains($row->notes ?? '', 'Cuti')) {
                                $sumber = 'Permohonan Cuti (ESS)';
                            } elseif ($isLateDisp) {
                                $sumber = 'Mesin Presensi + Disp. Telat';
                            } elseif (str_contains($row->notes ?? '', 'Dispensasi') || $row->status === 'Excused') {
                                $sumber = 'Logbook HRD & GA';
                            }

                            $statusLabel = match ($row->status) {
                                'Present' => $isLateDisp ? 'Hadir (Disp. Telat)' : 'Hadir Tepat Waktu',
                                'Late' => 'Terlambat (' . ($row->late_minutes ?? 0) . ' m)',
                                'Excused' => str_contains($row->notes ?? '', 'Cuti') ? 'Cuti Resmi' : 'Izin / Sakit',
                                'Absent' => 'Alpa',
                                default => $row->status ?? '-',
                            };

                            $badgeClass = match ($row->status) {
                                'Present' => 'badge-present',
                                'Late' => 'badge-late',
                                'Excused' => str_contains($row->notes ?? '', 'Cuti') ? 'badge-cuti' : 'badge-izin',
                                'Absent' => 'badge-alpa',
                                default => '',
                            };

                            $rowCls = ($no % 2 === 0) ? 'tr-even' : 'tr-odd';
                            $inTime = $row->clock_in ? Carbon::parse($row->clock_in)->format('H:i') : '-';
                            $outTime = $row->clock_out ? Carbon::parse($row->clock_out)->format('H:i') : '-';
                            $notes = $row->notes ? ($sumber . ' | ' . $row->notes) : $sumber;

                            $tr = '<tr class="' . $rowCls . '">';
                            $tr .= '<td class="td-cell td-center">' . $no++ . '</td>';
                            $tr .= '<td class="td-cell td-center">' . ($row->date ? $row->date->format('d/m/Y') : '-') . '</td>';
                            $tr .= '<td class="td-cell td-nip">' . htmlspecialchars($row->employee?->nip ?? '-') . '</td>';
                            $tr .= '<td class="td-cell" style="font-weight: 600;">' . htmlspecialchars($row->employee?->full_name ?? '-') . '</td>';
                            $tr .= '<td class="td-cell">' . htmlspecialchars($row->employee?->branch?->name ?? 'Kantor Pusat') . '</td>';
                            $tr .= '<td class="td-cell">' . htmlspecialchars($row->employee?->department?->name ?? '-') . '</td>';
                            $tr .= '<td class="td-cell td-center">' . $inTime . '</td>';
                            $tr .= '<td class="td-cell td-center">' . $outTime . '</td>';
                            $tr .= '<td class="td-cell td-center ' . $badgeClass . '">' . htmlspecialchars($statusLabel) . '</td>';
                            $tr .= '<td class="td-cell td-center">' . ($row->late_minutes ?? 0) . '</td>';
                            $tr .= '<td class="td-cell td-right">' . number_format($row->penalty_amount ?? 0, 0, ',', '.') . '</td>';
                            $tr .= '<td class="td-cell" style="font-size: 9px; color: #475569;">' . htmlspecialchars($notes) . '</td>';
                            $tr .= '</tr>';

                            fputs($streamHandle, $tr);
                        }

                        $footerHtml = '</tbody>
</table>
</body>
</html>';
                        fputs($streamHandle, $footerHtml);
                        fclose($streamHandle);
                    };

                    return response()->streamDownload($callback, $filename, [
                        'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                    ]);
                }),

            Action::make('export_monthly_matrix')
                ->label('Matriks Rekap Bulanan (Preview, Cetak & Unduh)')
                ->icon('heroicon-o-printer')
                ->color('primary')
                ->modalHeading('Cetak & Pratinjau Matriks Presensi Bulanan')
                ->modalDescription('Pilih periode bulan, tahun, filter kantor cabang, serta opsi tindakan: buka halaman pratinjau siap cetak (print/PDF) atau langsung unduh berkas.')
                ->form([
                    Select::make('month')
                        ->label('Periode Bulan')
                        ->options([
                            1 => '01 - Januari',
                            2 => '02 - Februari',
                            3 => '03 - Maret',
                            4 => '04 - April',
                            5 => '05 - Mei',
                            6 => '06 - Juni',
                            7 => '07 - Juli',
                            8 => '08 - Agustus',
                            9 => '09 - September',
                            10 => '10 - Oktober',
                            11 => '11 - November',
                            12 => '12 - Desember',
                        ])
                        ->default(now()->month)
                        ->required(),
                    Select::make('year')
                        ->label('Periode Tahun')
                        ->options(function () {
                            $cur = now()->year;
                            return [
                                $cur - 2 => (string) ($cur - 2),
                                $cur - 1 => (string) ($cur - 1),
                                $cur => (string) $cur,
                                $cur + 1 => (string) ($cur + 1),
                            ];
                        })
                        ->default(now()->year)
                        ->required(),
                    Select::make('branch_id')
                        ->label('Filter Kantor Unit Kerja')
                        ->options(fn () => Branch::orderBy('name')->pluck('name', 'id')->toArray())
                        ->placeholder('Semua Unit Kerja BPR Banda Raya')
                        ->nullable(),
                    Select::make('action_type')
                        ->label('Aksi Laporan')
                        ->options([
                            'preview' => '👁️ Buka Pratinjau & Siap Cetak (Print Preview / Simpan PDF)',
                            'excel' => '📥 Langsung Unduh Berkas Excel Resmi (.xls)',
                            'csv' => '📄 Langsung Unduh Berkas Teks (.csv)',
                        ])
                        ->default('preview')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Lanjutkan')
                ->action(function (array $data) {
                    $year = (int) ($data['year'] ?? now()->year);
                    $month = (int) ($data['month'] ?? now()->month);
                    $branchId = ! empty($data['branch_id']) ? (int) $data['branch_id'] : null;
                    $actionType = $data['action_type'] ?? 'preview';

                    if ($actionType === 'excel') {
                        return redirect()->route('admin.attendance.export-matrix', [
                            'year' => $year,
                            'month' => $month,
                            'branch_id' => $branchId,
                            'format' => 'excel',
                        ]);
                    }

                    if ($actionType === 'csv') {
                        return redirect()->route('admin.attendance.export-matrix', [
                            'year' => $year,
                            'month' => $month,
                            'branch_id' => $branchId,
                            'format' => 'csv',
                        ]);
                    }

                    // Default: Buka Halaman Pratinjau & Cetak Resmi
                    return redirect()->route('admin.attendance.matrix-preview', [
                        'year' => $year,
                        'month' => $month,
                        'branch_id' => $branchId,
                    ]);
                }),
            CreateAction::make()
                ->label('Input Presensi Manual')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
        ];
    }
}
