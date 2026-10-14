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
                        $out = fopen('php://output', 'w');
                        fputs($out, "\xEF\xBB\xBF");

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

                        fputs($out, $html);

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

                            fputs($out, $tr);
                        }

                        $footerHtml = '</tbody>
</table>
</body>
</html>';
                        fputs($out, $footerHtml);
                        fclose($out);
                    };

                    return response()->stream($callback, 200, $headers);
                }),

            Action::make('export_monthly_matrix')
                ->label('Unduh Matriks Rekap Bulanan (Buku Rekap HR)')
                ->icon('heroicon-o-table-cells')
                ->color('primary')
                ->modalHeading('Cetak & Unduh Matriks Rekap Presensi Bulanan')
                ->modalDescription('Pilih periode bulan, tahun, dan format keluaran rekapitulasi presensi terpadu (mesin biometrik, cuti resmi, dan logbook dispensasi).')
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
                    Select::make('format')
                        ->label('Format Unduhan')
                        ->options([
                            'excel' => 'Microsoft Excel (.xls) - Rekap Resmi & Berwarna (Executive HR Report)',
                            'csv' => 'File CSV Mentah (.csv) - Data Teks Sederhana',
                        ])
                        ->default('excel')
                        ->required(),
                ])
                ->modalSubmitActionLabel('Unduh Laporan')
                ->action(function (array $data): StreamedResponse {
                    $year = (int) ($data['year'] ?? now()->year);
                    $month = (int) ($data['month'] ?? now()->month);
                    $format = $data['format'] ?? 'excel';
                    $daysInMonth = Carbon::create($year, $month)->daysInMonth;
                    $periodName = Carbon::create($year, $month, 1)->translatedFormat('F Y');

                    $employees = Employee::accessibleBy()
                        ->with(['branch', 'department'])
                        ->where('status', 'Active')
                        ->orderBy('branch_id')
                        ->orderBy('nip')
                        ->get();

                    if ($format === 'excel') {
                        $filename = 'Matriks_Rekap_Presensi_BPR_' . sprintf('%04d_%02d', $year, $month) . '.xls';
                        $headers = [
                            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                            'Pragma' => 'no-cache',
                            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
                            'Expires' => '0',
                        ];

                        $callback = function () use ($employees, $year, $month, $daysInMonth, $periodName) {
                            $out = fopen('php://output', 'w');
                            fputs($out, "\xEF\xBB\xBF");

                            // Day names helper
                            $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

                            $htmlHeader = '<!DOCTYPE html>
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
    <x:Name>Rekap Presensi ' . htmlspecialchars($periodName) . '</x:Name>
    <x:WorksheetOptions>
     <x:DisplayGridlines/>
     <x:FreezePanes/>
     <x:FrozenNoSplit/>
     <x:SplitHorizontal>7</x:SplitHorizontal>
     <x:TopRowBottomPane>7</x:TopRowBottomPane>
     <x:SplitVertical>4</x:SplitVertical>
     <x:LeftColumnRightPane>4</x:LeftColumnRightPane>
     <x:ActivePane>0</x:ActivePane>
    </x:WorksheetOptions>
   </x:ExcelWorksheet>
  </x:ExcelWorksheets>
 </x:ExcelWorkbook>
</xml>
<![endif]-->
<style>
  body { font-family: "Segoe UI", Calibri, Arial, sans-serif; font-size: 11px; margin: 0; padding: 10px; }
  table { border-collapse: collapse; width: 100%; }
  .title-main { font-size: 16px; font-weight: bold; color: #0E6838; text-transform: uppercase; }
  .subtitle { font-size: 12px; font-weight: bold; color: #27272a; margin-top: 4px; }
  .meta-info { font-size: 11px; color: #52525b; margin-top: 2px; }
  
  .th-base { border: 1px solid #14532d; font-family: Calibri, sans-serif; }
  .th-main { background-color: #0E6838; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 4px; font-size: 11px; }
  .th-sun { background-color: #b91c1c; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 4px; font-size: 11px; }
  .th-sub { background-color: #15803d; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 4px 2px; font-size: 10px; }
  .th-sub-sun { background-color: #dc2626; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 4px 2px; font-size: 10px; }
  .th-summary { background-color: #1e293b; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 8px 6px; font-size: 11px; }
  .th-summary-sub { background-color: #334155; color: #ffffff; font-weight: bold; text-align: center; vertical-align: middle; padding: 4px; font-size: 10px; }

  .td-cell { border: 1px solid #cbd5e1; padding: 4px 5px; font-size: 10px; text-align: center; vertical-align: middle; mso-number-format: "\@"; }
  .td-left { text-align: left; }
  .td-right { text-align: right; }
  .td-nip { font-family: "Courier New", monospace; text-align: center; }
  .td-cuti { background-color: #dbeafe; color: #1e40af; font-weight: bold; }
  .td-izin { background-color: #fef3c7; color: #92400e; font-weight: bold; }
  .td-menunggu { background-color: #f3e8ff; color: #6b21a8; font-style: italic; }
  .td-libur { background-color: #f1f5f9; color: #94a3b8; font-weight: bold; }
  .td-late { color: #dc2626; font-weight: bold; background-color: #fee2e2; }
  .td-present { color: #047857; font-weight: 500; }
  .td-alpa { background-color: #ffe4e6; color: #be123c; font-weight: bold; }

  .sum-col { font-weight: bold; font-size: 11px; }
  .sum-hadir { color: #047857; }
  .sum-cuti { color: #1d4ed8; }
  .sum-izin { color: #b45309; }
  .sum-telat { color: #b91c1c; }
  .sum-denda { color: #b91c1c; text-align: right; }
  .sum-alpa { color: #be123c; }

  .tr-even { background-color: #ffffff; }
  .tr-odd { background-color: #f8fafc; }

  .tfoot-total { background-color: #e2e8f0; font-weight: bold; font-size: 11px; border-top: 2px solid #0E6838; border-bottom: 2px solid #0E6838; }
  .sign-box { border: 1px solid #cbd5e1; padding: 12px; text-align: center; vertical-align: top; }
</style>
</head>
<body>
<table>
  <tr>
    <td colspan="' . (4 + ($daysInMonth * 2) + 7) . '" class="title-main">PT BPR BANDA RAYA</td>
  </tr>
  <tr>
    <td colspan="' . (4 + ($daysInMonth * 2) + 7) . '" class="subtitle">MATRIKS REKAPITULASI PRESENSI & KEHADIRAN BULANAN (TERPADU: MESIN, CUTI & LOGBOOK)</td>
  </tr>
  <tr>
    <td colspan="' . (4 + ($daysInMonth * 2) + 7) . '" class="meta-info">Periode: <b>' . htmlspecialchars($periodName) . '</b> | Dicetak Pada: ' . now()->format('d/m/Y H:i:s') . ' WIB | Status Pegawai: Aktif</td>
  </tr>
  <tr><td colspan="' . (4 + ($daysInMonth * 2) + 7) . '">&nbsp;</td></tr>
  <thead>
    <tr>
      <th rowspan="2" class="th-main th-base" style="width: 35px;">NO</th>
      <th rowspan="2" class="th-main th-base" style="width: 90px;">NIP / NIK</th>
      <th rowspan="2" class="th-main th-base" style="width: 170px;">NAMA PEGAWAI</th>
      <th rowspan="2" class="th-main th-base" style="width: 130px;">KANTOR UNIT</th>';

                            // Day columns header
                            for ($d = 1; $d <= $daysInMonth; $d++) {
                                $dObj = Carbon::create($year, $month, $d);
                                $isSun = $dObj->isSunday();
                                $dayStr = sprintf('%02d', $d);
                                $dayAbbr = $dayNames[$dObj->dayOfWeek];
                                $cls = $isSun ? 'th-sun' : 'th-main';
                                $htmlHeader .= '<th colspan="2" class="' . $cls . ' th-base" style="width: 68px;">Tgl ' . $dayStr . '<br><small>' . $dayAbbr . '</small></th>';
                            }

                            $htmlHeader .= '
      <th colspan="6" class="th-summary th-base">AKUMULASI KEHADIRAN</th>
      <th rowspan="2" class="th-summary th-base" style="width: 220px;">CATATAN TERPADU (LOGBOOK & CUTI)</th>
    </tr>
    <tr>';

                            // Day sub-headers (In / Out)
                            for ($d = 1; $d <= $daysInMonth; $d++) {
                                $dObj = Carbon::create($year, $month, $d);
                                $isSun = $dObj->isSunday();
                                $subCls = $isSun ? 'th-sub-sun' : 'th-sub';
                                $htmlHeader .= '<th class="' . $subCls . ' th-base">In</th><th class="' . $subCls . ' th-base">Out</th>';
                            }

                            $htmlHeader .= '
      <th class="th-summary-sub th-base" style="width: 45px;">Hadir</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Cuti</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Izin</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Telat</th>
      <th class="th-summary-sub th-base" style="width: 80px;">Denda (Rp)</th>
      <th class="th-summary-sub th-base" style="width: 45px;">Alpa</th>
    </tr>
  </thead>
  <tbody>';

                            fputs($out, $htmlHeader);

                            $grandHadir = 0;
                            $grandCuti = 0;
                            $grandIzin = 0;
                            $grandTelat = 0;
                            $grandDenda = 0;
                            $grandAlpa = 0;
                            $no = 1;

                            foreach ($employees as $emp) {
                                $attendances = Attendance::where('employee_id', $emp->id)
                                    ->whereYear('date', $year)
                                    ->whereMonth('date', $month)
                                    ->get()
                                    ->keyBy(fn ($a) => Carbon::parse($a->date)->format('j'));

                                $rowCls = ($no % 2 === 0) ? 'tr-even' : 'tr-odd';
                                $rowHtml = '<tr class="' . $rowCls . '">';
                                $rowHtml .= '<td class="td-cell">' . $no++ . '</td>';
                                $rowHtml .= '<td class="td-cell td-nip">' . htmlspecialchars($emp->nip) . '</td>';
                                $rowHtml .= '<td class="td-cell td-left" style="font-weight: 600;">' . htmlspecialchars($emp->full_name) . '</td>';
                                $rowHtml .= '<td class="td-cell td-left">' . htmlspecialchars($emp->branch?->name ?? 'Kantor Pusat') . '</td>';

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
                                        $isLate = ($att->status === 'Late' || ($att->late_minutes ?? 0) > 0);

                                        if (str_contains($att->notes ?? '', 'Cuti Resmi') || ($att->status === 'Excused' && str_contains($att->notes ?? '', 'Cuti'))) {
                                            $rowHtml .= '<td colspan="2" class="td-cell td-cuti">CUTI</td>';
                                            $totalCuti++;
                                            $notesList[] = "Tgl {$d}: Cuti";
                                        } elseif ($att->status === 'Excused' && ! $isLateDisp) {
                                            $rowHtml .= '<td colspan="2" class="td-cell td-izin">IZIN</td>';
                                            $totalIzin++;
                                            $notesList[] = "Tgl {$d}: Izin";
                                        } elseif ($att->status === 'Present' || $isLateDisp) {
                                            $rowHtml .= '<td class="td-cell td-present">' . $in . '</td>';
                                            $rowHtml .= '<td class="td-cell td-present">' . $out . '</td>';
                                            $totalHadir++;
                                            if ($isLateDisp) {
                                                $notesList[] = "Tgl {$d}: Disp. Telat";
                                            }
                                        } elseif ($isLate) {
                                            $rowHtml .= '<td class="td-cell td-late">' . $in . '</td>';
                                            $rowHtml .= '<td class="td-cell">' . $out . '</td>';
                                            $totalHadir++;
                                            $totalTerlambat++;
                                            $totalDenda += ($att->penalty_amount ?: 50000);
                                            $notesList[] = "Tgl {$d}: Telat " . ($att->late_minutes ?? 0) . "m";
                                        } else {
                                            $rowHtml .= '<td class="td-cell">' . $in . '</td>';
                                            $rowHtml .= '<td class="td-cell">' . $out . '</td>';
                                        }

                                        if (! empty($att->notes) && ! in_array("Tgl {$d}: {$att->notes}", $notesList)) {
                                            $notesList[] = "Tgl {$d}: {$att->notes}";
                                        }
                                    } else {
                                        // Cek pengajuan cuti & dispensasi tidak tersinkron
                                        $unSyncedLeave = LeaveRequest::where('employee_id', $emp->id)
                                            ->whereIn('status', ['Approved', 'approved', 'Approved_HR'])
                                            ->whereDate('start_date', '<=', $dateObj->toDateString())
                                            ->whereDate('end_date', '>=', $dateObj->toDateString())
                                            ->first();

                                        $unSyncedDisp = AttendanceDispensation::where('employee_id', $emp->id)
                                            ->whereDate('date', $dateObj->toDateString())
                                            ->first();

                                        if ($unSyncedLeave) {
                                            $rowHtml .= '<td colspan="2" class="td-cell td-cuti">CUTI</td>';
                                            $totalCuti++;
                                            $notesList[] = "Tgl {$d}: Cuti";
                                        } elseif ($unSyncedDisp) {
                                            if ($unSyncedDisp->isLateArrivalDispensation()) {
                                                $rowHtml .= '<td colspan="2" class="td-cell td-menunggu">MENUNGGU PRESENSI</td>';
                                                $notesList[] = "Tgl {$d}: Izin Telat";
                                            } else {
                                                $rowHtml .= '<td colspan="2" class="td-cell td-izin">IZIN</td>';
                                                $totalIzin++;
                                                $notesList[] = "Tgl {$d}: Dispensasi";
                                            }
                                        } elseif ($isSunday) {
                                            $rowHtml .= '<td colspan="2" class="td-cell td-libur">LIBUR</td>';
                                        } else {
                                            $isPastWorkday = $dateObj->lte(now());
                                            if ($isPastWorkday) {
                                                $rowHtml .= '<td class="td-cell td-alpa">-</td><td class="td-cell td-alpa">-</td>';
                                                $totalTidakAbsen++;
                                            } else {
                                                $rowHtml .= '<td class="td-cell">-</td><td class="td-cell">-</td>';
                                            }
                                        }
                                    }
                                }

                                $grandHadir += $totalHadir;
                                $grandCuti += $totalCuti;
                                $grandIzin += $totalIzin;
                                $grandTelat += $totalTerlambat;
                                $grandDenda += $totalDenda;
                                $grandAlpa += $totalTidakAbsen;

                                $rowHtml .= '<td class="td-cell sum-col sum-hadir">' . $totalHadir . '</td>';
                                $rowHtml .= '<td class="td-cell sum-col sum-cuti">' . $totalCuti . '</td>';
                                $rowHtml .= '<td class="td-cell sum-col sum-izin">' . $totalIzin . '</td>';
                                $rowHtml .= '<td class="td-cell sum-col sum-telat">' . $totalTerlambat . '</td>';
                                $rowHtml .= '<td class="td-cell sum-col sum-denda">' . number_format($totalDenda, 0, ',', '.') . '</td>';
                                $rowHtml .= '<td class="td-cell sum-col sum-alpa">' . $totalTidakAbsen . '</td>';
                                $rowHtml .= '<td class="td-cell td-left" style="font-size: 9px; color: #475569;">' . htmlspecialchars(implode('; ', array_slice(array_unique($notesList), 0, 3))) . '</td>';
                                $rowHtml .= '</tr>';

                                fputs($out, $rowHtml);
                            }

                            // Footer Totals
                            $htmlFoot = '</tbody>
  <tfoot>
    <tr class="tfoot-total">
      <td colspan="4" class="td-cell" style="text-align: right; font-weight: bold; padding: 8px;">TOTAL REKAPITULASI:</td>
      <td colspan="' . ($daysInMonth * 2) . '" class="td-cell" style="color: #64748b; font-style: italic;">(Akumulasi ' . count($employees) . ' Karyawan Aktif)</td>
      <td class="td-cell sum-col sum-hadir">' . $grandHadir . '</td>
      <td class="td-cell sum-col sum-cuti">' . $grandCuti . '</td>
      <td class="td-cell sum-col sum-izin">' . $grandIzin . '</td>
      <td class="td-cell sum-col sum-telat">' . $grandTelat . '</td>
      <td class="td-cell sum-col sum-denda">' . number_format($grandDenda, 0, ',', '.') . '</td>
      <td class="td-cell sum-col sum-alpa">' . $grandAlpa . '</td>
      <td class="td-cell">&nbsp;</td>
    </tr>
  </tfoot>
</table>

<br><br>
<table style="width: 100%; border: none;">
  <tr>
    <td style="width: 30%; text-align: center; vertical-align: top;">
      <b>Dibuat Oleh:</b><br><br><br><br><br>
      <u>( Staf Human Capital )</u><br>
      <span style="font-size: 10px; color: #64748b;">Pengelola Presensi & HRIS</span>
    </td>
    <td style="width: 40%; text-align: center; vertical-align: top;">
      <b>Diperiksa Oleh:</b><br><br><br><br><br>
      <u>( HC & GA Division Head )</u><br>
      <span style="font-size: 10px; color: #64748b;">Human Capital & General Affair</span>
    </td>
    <td style="width: 30%; text-align: center; vertical-align: top;">
      <b>Disetujui Oleh:</b><br><br><br><br><br>
      <u>( Direktur Operasional )</u><br>
      <span style="font-size: 10px; color: #64748b;">PT BPR Banda Raya</span>
    </td>
  </tr>
</table>
</body>
</html>';
                            fputs($out, $htmlFoot);
                            fclose($out);
                        };

                        return response()->stream($callback, 200, $headers);
                    }

                    // Fallback to RAW CSV
                    $headers = [
                        'Content-Type' => 'text/csv; charset=UTF-8',
                        'Content-Disposition' => 'attachment; filename="matriks_rekap_presensi_' . sprintf('%04d_%02d', $year, $month) . '_bpr_banda_raya.csv"',
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
                        $headerCols = ['No', 'NIK / NIP', 'Nama Pegawai', 'Kantor / Unit Kerja'];
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
                                        $notesList[] = "Tgl {$d}: Cuti";
                                    } elseif ($att->status === 'Excused' && ! $isLateDisp) {
                                        $row[] = 'IZIN';
                                        $row[] = 'IZIN';
                                        $totalIzin++;
                                        $notesList[] = "Tgl {$d}: Izin";
                                    } elseif ($att->status === 'Present' || $isLateDisp) {
                                        $row[] = $in;
                                        $row[] = $out;
                                        $totalHadir++;
                                        if ($isLateDisp) {
                                            $notesList[] = "Tgl {$d}: Disp. Telat";
                                        }
                                    } elseif ($att->status === 'Late' || ($att->late_minutes ?? 0) > 0) {
                                        $row[] = $in;
                                        $row[] = $out;
                                        $totalHadir++;
                                        $totalTerlambat++;
                                        $totalDenda += ($att->penalty_amount ?: 50000);
                                        $notesList[] = "Tgl {$d}: Telat " . ($att->late_minutes ?? 0) . "m";
                                    } else {
                                        $row[] = $in;
                                        $row[] = $out;
                                    }

                                    if (! empty($att->notes) && ! in_array("Tgl {$d}: {$att->notes}", $notesList)) {
                                        $notesList[] = "Tgl {$d}: {$att->notes}";
                                    }
                                } else {
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
                                        $notesList[] = "Tgl {$d}: Cuti";
                                    } elseif ($unSyncedDisp) {
                                        if ($unSyncedDisp->isLateArrivalDispensation()) {
                                            $row[] = 'MENUNGGU PRESENSI';
                                            $row[] = '-';
                                            $notesList[] = "Tgl {$d}: Izin Telat";
                                        } else {
                                            $row[] = 'IZIN';
                                            $row[] = 'IZIN';
                                            $totalIzin++;
                                            $notesList[] = "Tgl {$d}: Dispensasi";
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
