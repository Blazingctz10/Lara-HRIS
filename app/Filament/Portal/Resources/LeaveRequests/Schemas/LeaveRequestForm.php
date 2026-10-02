<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Schemas;

use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class LeaveRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        $employee = auth()->user()?->employee;

        return $schema
            ->components([
                Section::make('Permohonan Cuti / Izin Kerja')
                    ->description('Isi data pengajuan cuti resmi BPR (Alur: Persetujuan Cabang -> Verifikasi HR -> Otorisasi Direksi)')
                    ->schema([
                        Hidden::make('employee_id')
                            ->default($employee?->id),

                        Hidden::make('status')
                            ->default('Pending'),

                        Grid::make(2)->schema([
                            Select::make('leave_type_id')
                                ->relationship('leaveType', 'name', fn ($query) => $query->where('is_active', true))
                                ->label('Pilih Jenis Cuti')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->helperText(function (Get $get) use ($employee) {
                                    $leaveTypeId = $get('leave_type_id');
                                    if ($leaveTypeId && $employee) {
                                        $type = LeaveType::find($leaveTypeId);
                                        $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
                                            ->where('leave_type_id', $leaveTypeId)
                                            ->where('year', now()->year)
                                            ->first();

                                        $remaining = $balance ? $balance->remaining_quota : ($type->default_days ?? 0);
                                        $msg = "Sisa Kuota Cuti Anda: {$remaining} Hari.";
                                        if ($type && $type->min_consecutive_days) {
                                            $msg .= " (Wajib diambil minimal {$type->min_consecutive_days} hari berturut-turut sesuai regulasi OJK)";
                                        }
                                        return $msg;
                                    }
                                    return null;
                                }),

                            TextInput::make('total_days')
                                ->label('Total Hari Kerja')
                                ->numeric()
                                ->readOnly()
                                ->required()
                                ->helperText('Dihitung otomatis: Senin s/d Sabtu hari kerja (hanya Minggu yang tidak dihitung)')
                                ->rules([
                                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $employee) {
                                        $leaveTypeId = $get('leave_type_id');
                                        if ($leaveTypeId) {
                                            $type = LeaveType::find($leaveTypeId);
                                            // 1. Validasi Min Consecutive Days (e.g. 5 days for Block Leave OJK)
                                            if ($type && $type->min_consecutive_days && $value < $type->min_consecutive_days) {
                                                $fail("Pengajuan cuti {$type->name} mewajibkan minimal {$type->min_consecutive_days} hari kerja berturut-turut sesuai aturan OJK.");
                                            }

                                            // 2. Validasi Sisa Kuota Cuti
                                            if ($employee) {
                                                $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
                                                    ->where('leave_type_id', $leaveTypeId)
                                                    ->where('year', now()->year)
                                                    ->first();

                                                $remaining = $balance ? $balance->remaining_quota : ($type->default_days ?? 0);
                                                if ($value > $remaining) {
                                                    $fail("Sisa kuota cuti Anda ({$remaining} hari) tidak mencukupi untuk mengajukan {$value} hari kerja.");
                                                }
                                            }
                                        }
                                    },
                                ]),

                            DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateDays($get, $set)),

                            DatePicker::make('end_date')
                                ->label('Tanggal Selesai')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateDays($get, $set)),
                        ]),

                        Textarea::make('reason')
                            ->label('Alasan / Keterangan Pengajuan')
                            ->rows(3)
                            ->placeholder('Jelaskan alasan atau rencana serah terima tugas pekerjaan selama cuti')
                            ->required(),

                        FileUpload::make('attachment_url')
                            ->label('Lampiran Dokumen Pendukung (Surat Dokter / Undangan Nikah / dll)')
                            ->directory('leaves/attachments')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120),
                    ]),
            ]);
    }

    private static function calculateDays(Get $get, Set $set): void
    {
        $start = $get('start_date');
        $end = $get('end_date');

        if ($start && $end) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);

            if ($endDate->gte($startDate)) {
                $days = $startDate->diffInDaysFiltered(function (Carbon $date) {
                    return ! $date->isSunday(); // Senin s/d Sabtu adalah hari kerja BPR
                }, $endDate) + 1;

                $set('total_days', $days);
            }
        }
    }
}
