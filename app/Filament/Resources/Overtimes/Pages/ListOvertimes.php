<?php

namespace App\Filament\Resources\Overtimes\Pages;

use App\Filament\Resources\Overtimes\OvertimeResource;
use App\Models\Overtime;
use App\Support\BprExcelExport;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Grid;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_overtime_excel')
                ->label('Unduh Rekap Lembur (Excel)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->modalHeading('Unduh Laporan Rekapitulasi Lembur (.xls)')
                ->modalDescription('Ekspor daftar SPK/pengajuan lembur pegawai beserta durasi jam dan estimasi upah lembur (1/173 x Gaji Pokok x 1.5).')
                ->modalSubmitActionLabel('Unduh Excel (.xls)')
                ->form([
                    Grid::make(2)->schema([
                        Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => '01 - Januari', 2 => '02 - Februari', 3 => '03 - Maret',
                                4 => '04 - April', 5 => '05 - Mei', 6 => '06 - Juni',
                                7 => '07 - Juli', 8 => '08 - Agustus', 9 => '09 - September',
                                10 => '10 - Oktober', 11 => '11 - November', 12 => '12 - Desember',
                            ])
                            ->default((int) date('m'))
                            ->required(),
                        TextInput::make('year')
                            ->label('Tahun')
                            ->numeric()
                            ->default((int) date('Y'))
                            ->required(),
                    ]),
                ])
                ->action(function (array $data): StreamedResponse {
                    $month = (int) $data['month'];
                    $year = (int) $data['year'];

                    $records = Overtime::accessibleBy()
                        ->with(['employee.branch', 'employee.position'])
                        ->whereMonth('date', $month)
                        ->whereYear('date', $year)
                        ->orderBy('date', 'desc')
                        ->get();

                    $periodLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');
                    $filename = sprintf('Rekap_Lembur_BPR_%04d_%02d.xlsx', $year, $month);

                    $rows = [];
                    $totalHours = 0;
                    $totalPay = 0.0;

                    foreach ($records as $idx => $ot) {
                        $emp = $ot->employee;
                        $hours = (int) $ot->duration_hours;
                        $pay = (float) $ot->estimated_compensation;
                        $totalHours += $hours;
                        $totalPay += $pay;

                        $statusClass = match ($ot->status) {
                            'Approved_HR', 'Approved', 'Approved_Pincab', 'Paid' => 'badge-ok',
                            'Rejected' => 'badge-danger',
                            default => 'badge-warn',
                        };

                        $rows[] = [
                            ['value' => $idx + 1, 'class' => 'td-center'],
                            ['value' => $ot->date ? Carbon::parse($ot->date)->format('d/m/Y') : '-', 'class' => 'td-center'],
                            ['value' => $emp?->nip ?? '-', 'class' => 'td-nip'],
                            ['value' => $emp?->full_name ?? '-', 'class' => 'td-bold'],
                            ['value' => $emp?->branch?->name ?? 'Kantor Pusat'],
                            ['value' => ($ot->start_time ?? '--:--') . ' - ' . ($ot->end_time ?? '--:--'), 'class' => 'td-center'],
                            ['value' => $hours, 'class' => 'td-num'],
                            ['value' => number_format($pay, 0, '.', ''), 'class' => 'td-rp td-bold'],
                            ['value' => $ot->status, 'class' => $statusClass],
                            ['value' => $ot->reason ?? '-'],
                        ];
                    }

                    $summaryRow = [
                        ['value' => '', 'class' => 'td-center'],
                        ['value' => 'TOTAL', 'class' => 'td-center'],
                        ['value' => count($records) . ' Pengajuan', 'class' => 'td-center'],
                        ['value' => ''],
                        ['value' => ''],
                        ['value' => ''],
                        ['value' => $totalHours, 'class' => 'td-num'],
                        ['value' => number_format($totalPay, 0, '.', ''), 'class' => 'td-rp'],
                        ['value' => ''],
                        ['value' => ''],
                    ];

                    return BprExcelExport::stream(
                        $filename,
                        'Rekap Lembur',
                        'REKAPITULASI LEMBUR (OVERTIME) PEGAWAI',
                        $periodLabel,
                        [
                            ['label' => 'No'], ['label' => 'Tanggal'], ['label' => 'NIP'], ['label' => 'Nama Pegawai'],
                            ['label' => 'Unit Kantor'], ['label' => 'Jam Lembur'], ['label' => 'Durasi (Jam)'],
                            ['label' => 'Estimasi Upah (Rp)'], ['label' => 'Status'], ['label' => 'Keterangan Tugas'],
                        ],
                        $rows,
                        $summaryRow,
                    );
                }),

            CreateAction::make()
                ->label('Buat SPK Lembur')
                ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin', 'branch_manager'])),
        ];
    }
}
