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
                            'Deviasi Jarak GPS (Meter)',
                            'Latitude Masuk',
                            'Longitude Masuk',
                            'Catatan',
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
                                $row->distance_in_meters ?? '0',
                                $row->lat_in ?? '-',
                                $row->long_in ?? '-',
                                $row->notes ?? '-',
                            ]);
                        }

                        fclose($file);
                    };

                    return response()->stream($callback, 200, $headers);
                }),
            CreateAction::make()->label('Input Presensi Manual'),
        ];
    }
}
