<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceDispensation;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceReportController extends Controller
{
    /**
     * Tampilkan Halaman Pratinjau (Preview) & Siap Cetak (Print View) Matriks Presensi Bulanan
     */
    public function previewMatrix(Request $request)
    {
        $this->checkAccess();

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

        $data = $this->buildMatrixData($year, $month, $branchId);

        return view('attendance.matrix-preview', $data);
    }

    /**
     * Unduh Matriks Presensi Bulanan dalam format Excel (.xls) atau CSV (.csv)
     */
    public function exportMatrix(Request $request): StreamedResponse
    {
        $this->checkAccess();

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);
        $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;
        $format = $request->input('format', 'excel');

        $data = $this->buildMatrixData($year, $month, $branchId);

        if ($format === 'csv') {
            return $this->streamCsvExport($data);
        }

        return $this->streamExcelExport($data);
    }

    /**
     * Hitung & Susun Data Matriks Presensi Bulanan (Re-usable untuk Preview, Print & Export)
     */
    public function buildMatrixData(int $year, int $month, ?int $branchId): array
    {
        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $periodDate = Carbon::create($year, $month, 1);
        $periodName = $periodDate->translatedFormat('F Y');

        $user = auth()->user();
        $branches = Branch::orderBy('name')->get();

        $employeeQuery = Employee::accessibleBy()
            ->with(['branch', 'department', 'position'])
            ->where('status', 'Active');

        if ($branchId) {
            $employeeQuery->where('branch_id', $branchId);
        }

        $employees = $employeeQuery->orderBy('branch_id')->orderBy('nip')->get();

        $monthStart = $periodDate->copy()->startOfMonth()->toDateString();
        $monthEnd = $periodDate->copy()->endOfMonth()->toDateString();

        $matrixRows = [];
        $grandTotals = [
            'hadir' => 0,
            'cuti' => 0,
            'izin' => 0,
            'telat' => 0,
            'denda' => 0,
            'alpa' => 0,
        ];

        // Format nama hari singkat
        $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        $daysMeta = [];
        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dObj = Carbon::create($year, $month, $d);
            $daysMeta[$d] = [
                'day' => $d,
                'pad' => sprintf('%02d', $d),
                'name' => $dayNames[$dObj->dayOfWeek],
                'is_sunday' => $dObj->isSunday(),
                'date_string' => $dObj->toDateString(),
                'is_past' => $dObj->lte(now()),
            ];
        }

        $no = 1;
        foreach ($employees as $emp) {
            $attendances = Attendance::where('employee_id', $emp->id)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get()
                ->keyBy(fn ($a) => Carbon::parse($a->date)->format('j'));

            $leaveRequests = LeaveRequest::where('employee_id', $emp->id)
                ->whereIn('status', ['Approved', 'approved', 'Approved_HR'])
                ->where('start_date', '<=', $monthEnd)
                ->where('end_date', '>=', $monthStart)
                ->get();

            $dispensations = AttendanceDispensation::where('employee_id', $emp->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->get()
                ->keyBy(fn ($d) => Carbon::parse($d->date)->format('j'));

            $daysData = [];
            $totalHadir = 0;
            $totalCuti = 0;
            $totalIzin = 0;
            $totalTerlambat = 0;
            $totalDenda = 0;
            $totalTidakAbsen = 0;
            $dailyNotes = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $meta = $daysMeta[$d];
                $att = $attendances->get($d);

                if ($att) {
                    $timeIn = $att->clock_in ? Carbon::parse($att->clock_in)->format('H:i') : '-';
                    $timeOut = $att->clock_out ? Carbon::parse($att->clock_out)->format('H:i') : '-';
                    $isLateDisp = str_contains($att->notes ?? '', 'Dispensasi Telat');
                    $isLate = ($att->status === 'Late' || ($att->late_minutes ?? 0) > 0);

                    if (str_contains($att->notes ?? '', 'Cuti Resmi') || ($att->status === 'Excused' && str_contains($att->notes ?? '', 'Cuti'))) {
                        $type = 'cuti';
                        $totalCuti++;
                        $cleanNote = $this->cleanNoteText($att->notes ?? '');
                        $dailyNotes[$d][] = $cleanNote ?: 'Cuti';
                    } elseif ($att->status === 'Excused' && ! $isLateDisp) {
                        $type = 'izin';
                        $totalIzin++;
                        $cleanNote = ! empty($att->notes) && ! str_contains($att->notes, 'Tarik Log') ? $this->cleanNoteText($att->notes) : 'Izin';
                        $dailyNotes[$d][] = $cleanNote;
                    } elseif ($att->status === 'Present' || $isLateDisp) {
                        $type = 'present';
                        $totalHadir++;
                        if ($isLateDisp) {
                            $cleanNote = trim(str_replace(['Hadir (', ')'], '', $att->notes ?? ''));
                            $dailyNotes[$d][] = $cleanNote ?: 'Disp. Telat';
                        } elseif (! empty($att->notes) && ! str_contains($att->notes, 'Tarik Log Mesin')) {
                            $dailyNotes[$d][] = $att->notes;
                        }
                    } elseif ($isLate) {
                        $type = 'late';
                        $totalHadir++;
                        $totalTerlambat++;
                        $totalDenda += ($att->penalty_amount ?: 50000);
                        $dailyNotes[$d][] = "Telat " . ($att->late_minutes ?? 0) . "m";
                    } else {
                        $type = 'normal';
                        if (! empty($att->notes) && ! str_contains($att->notes, 'Tarik Log Mesin')) {
                            $dailyNotes[$d][] = $att->notes;
                        }
                    }

                    $daysData[$d] = [
                        'type' => $type,
                        'in' => $timeIn,
                        'out' => $timeOut,
                    ];
                } else {
                    $dateStr = $meta['date_string'];
                    $unSyncedLeave = $leaveRequests->first(fn ($lr) => $lr->start_date && $lr->start_date->toDateString() <= $dateStr && $lr->end_date && $lr->end_date->toDateString() >= $dateStr);
                    $unSyncedDisp = $dispensations->get($d);

                    if ($unSyncedLeave) {
                        $totalCuti++;
                        $leaveType = $unSyncedLeave->leaveType?->name ?? 'Cuti';
                        $reason = trim($unSyncedLeave->reason ?? '');
                        $note = $reason !== '' ? "{$leaveType} ({$reason})" : $leaveType;
                        $dailyNotes[$d][] = $note;
                        $daysData[$d] = ['type' => 'cuti', 'in' => 'CUTI', 'out' => 'CUTI'];
                    } elseif ($unSyncedDisp) {
                        if ($unSyncedDisp->isLateArrivalDispensation()) {
                            $dailyNotes[$d][] = 'Izin Telat';
                            $daysData[$d] = ['type' => 'menunggu', 'in' => 'MENUNGGU', 'out' => 'PRESENSI'];
                        } else {
                            $totalIzin++;
                            $dispReason = $unSyncedDisp->custom_reason ?: ($unSyncedDisp->description ?: '');
                            $dailyNotes[$d][] = "Dispensasi" . ($dispReason ? ": {$dispReason}" : '');
                            $daysData[$d] = ['type' => 'izin', 'in' => 'IZIN', 'out' => 'IZIN'];
                        }
                    } elseif ($meta['is_sunday']) {
                        $daysData[$d] = ['type' => 'libur', 'in' => 'LIBUR', 'out' => 'LIBUR'];
                    } else {
                        if ($meta['is_past']) {
                            $totalTidakAbsen++;
                            $daysData[$d] = ['type' => 'alpa', 'in' => '-', 'out' => '-'];
                        } else {
                            $daysData[$d] = ['type' => 'future', 'in' => '-', 'out' => '-'];
                        }
                    }
                }
            }

            $grandTotals['hadir'] += $totalHadir;
            $grandTotals['cuti'] += $totalCuti;
            $grandTotals['izin'] += $totalIzin;
            $grandTotals['telat'] += $totalTerlambat;
            $grandTotals['denda'] += $totalDenda;
            $grandTotals['alpa'] += $totalTidakAbsen;

            $notesText = $this->formatGroupedNotes($dailyNotes, $month, $year, $daysMeta);

            $matrixRows[] = [
                'no' => $no++,
                'employee' => $emp,
                'days' => $daysData,
                'total_hadir' => $totalHadir,
                'total_cuti' => $totalCuti,
                'total_izin' => $totalIzin,
                'total_telat' => $totalTerlambat,
                'total_denda' => $totalDenda,
                'total_alpa' => $totalTidakAbsen,
                'notes' => $notesText,
            ];
        }

        $selectedBranch = $branchId ? $branches->firstWhere('id', $branchId) : null;

        return compact(
            'year',
            'month',
            'branchId',
            'periodName',
            'daysInMonth',
            'daysMeta',
            'employees',
            'matrixRows',
            'grandTotals',
            'branches',
            'selectedBranch'
        );
    }

    /**
     * Stream Unduhan Excel (.xls)
     */
    protected function streamExcelExport(array $data): StreamedResponse
    {
        $filename = 'Matriks_Rekap_Presensi_BPR_' . sprintf('%04d_%02d', $data['year'], $data['month']) . '.xls';

        return response()->streamDownload(function () use ($data) {
            $streamHandle = fopen('php://output', 'w');
            fputs($streamHandle, "\xEF\xBB\xBF");
            fputs($streamHandle, view('attendance.matrix-excel-table', $data)->render());
            fclose($streamHandle);
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
    }

    /**
     * Stream Unduhan CSV (.csv)
     */
    protected function streamCsvExport(array $data): StreamedResponse
    {
        $filename = 'matriks_rekap_presensi_' . sprintf('%04d_%02d', $data['year'], $data['month']) . '_bpr_banda_raya.csv';

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['PT BPR BANDA RAYA - MATRIKS REKAPITULASI PRESENSI TERPADU (MESIN, CUTI & LOGBOOK)']);
            fputcsv($file, ['Periode: ' . $data['periodName'] . ' | Kantor: ' . ($data['selectedBranch']?->name ?? 'Semua Unit')]);
            fputcsv($file, []);

            // Header baris 1
            $headerCols = ['No', 'NIK / NIP', 'Nama Pegawai', 'Kantor / Unit Kerja'];
            for ($d = 1; $d <= $data['daysInMonth']; $d++) {
                $pad = sprintf('%02d', $d);
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

            foreach ($data['matrixRows'] as $row) {
                $csvRow = [
                    $row['no'],
                    $row['employee']->nip,
                    $row['employee']->full_name,
                    $row['employee']->branch?->name ?? 'Kantor Pusat',
                ];

                for ($d = 1; $d <= $data['daysInMonth']; $d++) {
                    $dayInfo = $row['days'][$d] ?? ['in' => '-', 'out' => '-'];
                    $csvRow[] = $dayInfo['in'];
                    $csvRow[] = $dayInfo['out'];
                }

                $csvRow[] = $row['total_hadir'];
                $csvRow[] = $row['total_cuti'];
                $csvRow[] = $row['total_izin'];
                $csvRow[] = $row['total_telat'];
                $csvRow[] = number_format($row['total_denda'], 0, ',', '.');
                $csvRow[] = $row['total_alpa'];
                $csvRow[] = $row['notes'];

                fputcsv($file, $csvRow);
            }

            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Bersihkan teks catatan cuti/izin agar rapi dan informatif
     */
    protected function cleanNoteText(string $raw): string
    {
        $note = trim($raw);

        // [Cuti Resmi: Cuti Tahunan] Karena Aku Benci Iman -> Cuti Tahunan (Karena Aku Benci Iman)
        if (preg_match('/\[Cuti Resmi:\s*([^\]]+)\]\s*(.*)/i', $note, $m)) {
            $type = trim($m[1]);
            $reason = trim($m[2]);
            return $reason !== '' ? "{$type} ({$reason})" : $type;
        }

        // Cuti Resmi Tahunan (Disetujui HRD & Pincab) -> Cuti Tahunan (Disetujui HRD & Pincab)
        if (preg_match('/^Cuti Resmi\s+(.*)/i', $note, $m)) {
            return 'Cuti ' . trim($m[1]);
        }

        // Hilangkan tag kurung siku sisa jika ada
        $note = trim(str_replace(['[Cuti Resmi: ', ']'], '', $note));

        return $note;
    }

    /**
     * Gabungkan catatan harian jika bertanggal beruntun (contoh: 19-22 Oktober 2026: Cuti Tahunan)
     * Tanggal tunggal tetap ditampilkan per tanggal (contoh: Tgl 9: Telat 24m)
     */
    protected function formatGroupedNotes(array $dailyNotes, int $month, int $year, array $daysMeta): string
    {
        if (empty($dailyNotes)) {
            return '-';
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->locale('id')->translatedFormat('F');

        // Balikkan pemetaan: noteText => [daftar tanggal int]
        $noteToDays = [];
        foreach ($dailyNotes as $day => $notes) {
            foreach (array_unique($notes) as $note) {
                $note = trim($note);
                if ($note !== '') {
                    $noteToDays[$note][] = (int) $day;
                }
            }
        }

        if (empty($noteToDays)) {
            return '-';
        }

        $ranges = [];

        foreach ($noteToDays as $noteText => $days) {
            sort($days);
            $count = count($days);
            $currStart = $days[0];
            $currEnd = $days[0];

            for ($i = 1; $i < $count; $i++) {
                $d = $days[$i];

                // Hari berurutan langsung (misal: 19, 20, 21, 22)
                if ($d === $currEnd + 1) {
                    $currEnd = $d;
                    continue;
                }

                // Jika jeda hanya hari Minggu / Libur (misal: Jumat cuti, Minggu libur, Senin cuti)
                $onlySundays = true;
                for ($mid = $currEnd + 1; $mid < $d; $mid++) {
                    if (! ($daysMeta[$mid]['is_sunday'] ?? false)) {
                        $onlySundays = false;
                        break;
                    }
                }

                if ($onlySundays) {
                    $currEnd = $d;
                    continue;
                }

                // Terputus, simpan blok sebelumnya
                $ranges[] = [
                    'start' => $currStart,
                    'end' => $currEnd,
                    'text' => $noteText,
                ];
                $currStart = $d;
                $currEnd = $d;
            }

            // Simpan blok terakhir
            $ranges[] = [
                'start' => $currStart,
                'end' => $currEnd,
                'text' => $noteText,
            ];
        }

        // Urutkan secara kronologis berdasarkan tanggal mulai
        usort($ranges, function ($a, $b) {
            if ($a['start'] === $b['start']) {
                return $a['end'] <=> $b['end'];
            }
            return $a['start'] <=> $b['start'];
        });

        $formatted = [];
        foreach ($ranges as $r) {
            if ($r['start'] === $r['end']) {
                $formatted[] = "Tgl {$r['start']}: {$r['text']}";
            } else {
                $formatted[] = "{$r['start']}-{$r['end']} {$monthName} {$year}: {$r['text']}";
            }
        }

        return ! empty($formatted) ? implode('; ', $formatted) : '-';
    }

    /**
     * Otorisasi Keamanan: Khusus Super Admin, HR, Direksi, dan Branch Manager
     */
    protected function checkAccess(): void
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['super_admin', 'hr_admin', 'direksi', 'branch_manager'])) {
            abort(403, 'Akses terbatas untuk Administrator, Personalia/HR, Direksi, atau Kepala Cabang.');
        }
    }
}
