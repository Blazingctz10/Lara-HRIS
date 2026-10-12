<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use App\Models\Employee;
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
                Section::make('Formulir Permohonan Cuti Resmi - PT BPR Banda Raya')
                    ->description('Pencatatan permohonan cuti, verifikasi kuota personalia (HRD & GA), dan otorisasi bertingkat.')
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
                                ->relationship('leaveType', 'name', fn ($query) => $query->where('is_active', true))
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
                                        $emp = Employee::find($empId);
                                        $balance = EmployeeLeaveBalance::where('employee_id', $empId)
                                            ->where('leave_type_id', $leaveTypeId)
                                            ->where('year', now()->year)
                                            ->first();

                                        $info = 'Sisa Kuota: ' . ($balance ? $balance->remaining_quota : ($type->default_days ?? 0)) . ' Hari Kerja.';
                                        if ($type && $type->max_consecutive_days) {
                                            $info .= " (Maks. {$type->max_consecutive_days} hari berturut-turut)";
                                        }
                                        if ($type && $type->code === 'LONG_LEAVE') {
                                            $joinDate = $emp?->join_date ? \Carbon\Carbon::parse($emp->join_date) : null;
                                            $years = $joinDate ? (int) $joinDate->diffInYears(now()) : 0;
                                            if ($years < 5) {
                                                $info .= " ⚠️ Cuti Besar hanya berhak untuk masa kerja min. 5 tahun (Masa kerja saat ini: {$years} tahun).";
                                            } else {
                                                $info .= " ✅ Memenuhi syarat Cuti Besar (Masa kerja: {$years} tahun, durasi: 6 HK).";
                                            }
                                        }
                                        if ($emp && $emp->user?->role === 'direksi') {
                                            $info .= " [Status Pemohon: Direksi - Cuti <= 2 hari di-ACC HRD, > 2 hari ke Dewan Komisaris]";
                                        }
                                        return $info;
                                    }
                                    return null;
                                }),

                            DatePicker::make('start_date')
                                ->label('Dari Tanggal')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateDays($get, $set)),

                            DatePicker::make('end_date')
                                ->label('Sampai Dengan Tanggal')
                                ->required()
                                ->native(false)
                                ->live()
                                ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateDays($get, $set)),

                            TextInput::make('total_days')
                                ->label('Total Hari Kerja (HK)')
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
                                            if ($type && $type->max_consecutive_days && $value > $type->max_consecutive_days) {
                                                $fail("Jenis cuti {$type->name} maksimal {$type->max_consecutive_days} hari kerja berturut-turut per pengajuan untuk menjamin kelancaran operasional bank.");
                                            }
                                            if ($type && $type->code === 'LONG_LEAVE') {
                                                $empId = $get('employee_id');
                                                $emp = Employee::find($empId);
                                                $joinDate = $emp?->join_date ? \Carbon\Carbon::parse($emp->join_date) : null;
                                                $years = $joinDate ? (int) $joinDate->diffInYears(now()) : 0;
                                                if ($years < 5) {
                                                    $fail("Cuti Besar hanya dapat diajukan oleh pegawai dengan masa kerja minimal 5 tahun di PT BPR Banda Raya. Masa kerja saat ini: {$years} tahun.");
                                                }
                                                if ($value > 6) {
                                                    $fail("Durasi Cuti Besar maksimal adalah 6 hari kerja.");
                                                }
                                            }
                                        }
                                    },
                                ]),

                            Select::make('status')
                                ->label('Status Persetujuan / Otorisasi')
                                ->options([
                                    'Pending' => 'Pending (Menunggu Verifikasi HR)',
                                    'Approved_HR' => 'Approved HR (Menunggu Otorisasi Direksi)',
                                    'Approved_Direksi' => 'Approved Direksi (Disetujui Final)',
                                    'Approved_HRD' => 'Approved HRD (Disetujui Final - Khusus Cuti Direksi <= 2 Hari)',
                                    'Approved_Komisaris' => 'Approved Komisaris (Disetujui Final - Cuti Direksi > 2 Hari)',
                                    'Rejected' => 'Rejected (Ditolak)',
                                ])
                                ->default('Pending')
                                ->required(),
                        ]),

                        Textarea::make('reason')
                            ->label('Untuk Keperluan / Alasan Cuti')
                            ->rows(3)
                            ->required(),

                        Grid::make(2)->schema([
                            TextInput::make('leave_address')
                                ->label('Alamat yang Dapat Dihubungi Selama Cuti')
                                ->placeholder('Contoh: Jl. Engku Putri No. 12, Batam Center')
                                ->nullable(),

                            TextInput::make('contact_phone')
                                ->label('Nomor Telepon / WhatsApp Selama Cuti')
                                ->placeholder('Contoh: 0812-3456-7890')
                                ->tel()
                                ->nullable(),
                        ]),

                        FileUpload::make('attachment_url')
                            ->label('Lampiran Dokumen / Surat Keterangan Pendukung')
                            ->directory('leaves/attachments')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120),

                        Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan (Jika Ditolak)')
                            ->rows(2)
                            ->visible(fn (Get $get) => $get('status') === 'Rejected'),

                        Textarea::make('komisaris_notes')
                            ->label('Catatan / Keputusan Dewan Komisaris (Khusus Cuti Direksi > 2 Hari)')
                            ->rows(2)
                            ->placeholder('Masukkan nomor surat izin atau catatan rapat Dewan Komisaris')
                            ->nullable(),
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
