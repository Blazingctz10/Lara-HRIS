<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveType;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
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
        return $schema
            ->components([
                Section::make('Formulir Pengajuan Cuti')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live(),

                            Select::make('leave_type_id')
                                ->relationship('leaveType', 'name')
                                ->label('Jenis Cuti')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->helperText(function (Get $get) {
                                    $leaveTypeId = $get('leave_type_id');
                                    $empId = $get('employee_id');
                                    if ($leaveTypeId && $empId) {
                                        $type = LeaveType::find($leaveTypeId);
                                        $balance = EmployeeLeaveBalance::where('employee_id', $empId)
                                            ->where('leave_type_id', $leaveTypeId)
                                            ->where('year', now()->year)
                                            ->first();

                                        $info = 'Sisa Kuota: ' . ($balance ? $balance->remaining_quota : ($type->default_days ?? 0)) . ' Hari.';
                                        if ($type && $type->min_consecutive_days) {
                                            $info .= " (Wajib min. {$type->min_consecutive_days} hari berturut-turut untuk {$type->name})";
                                        }
                                        return $info;
                                    }
                                    return null;
                                }),

                            DatePicker::make('start_date')
                                ->label('Tanggal Mulai')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::calculateDays($get, $set);
                                }),

                            DatePicker::make('end_date')
                                ->label('Tanggal Selesai')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(function (Get $get, Set $set) {
                                    self::calculateDays($get, $set);
                                }),

                            TextInput::make('total_days')
                                ->label('Total Hari Kerja')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->helperText('Dihitung otomatis: Senin s/d Sabtu hari kerja (hanya Minggu yang tidak dihitung)')
                                ->rules([
                                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get) {
                                        $leaveTypeId = $get('leave_type_id');
                                        if ($leaveTypeId) {
                                            $type = LeaveType::find($leaveTypeId);
                                            if ($type && $type->min_consecutive_days && $value < $type->min_consecutive_days) {
                                                $fail("Jenis cuti {$type->name} mewajibkan minimal {$type->min_consecutive_days} hari kerja berturut-turut sesuai regulasi OJK.");
                                            }
                                        }
                                    },
                                ]),

                            Select::make('status')
                                ->label('Status Approval')
                                ->options([
                                    'Pending' => 'Pending (Menunggu Verifikasi HR)',
                                    'Approved_HR' => 'Approved HR (Menunggu Otorisasi Direksi)',
                                    'Approved_Direksi' => 'Approved Direksi (Disetujui Final)',
                                    'Rejected' => 'Rejected (Ditolak)',
                                ])
                                ->default('Pending')
                                ->required(),
                        ]),

                        Textarea::make('reason')
                            ->label('Alasan Pengajuan Cuti')
                            ->rows(3)
                            ->required(),

                        FileUpload::make('attachment_url')
                            ->label('Lampiran Dokumen / Surat Keterangan')
                            ->directory('leaves/attachments')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120),

                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan (Jika Ditolak)')
                            ->rows(2)
                            ->visible(fn (Get $get) => $get('status') === 'Rejected'),
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
