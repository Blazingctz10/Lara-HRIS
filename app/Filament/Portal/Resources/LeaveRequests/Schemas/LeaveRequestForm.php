<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Schemas;

use App\Models\Employee;
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
        $isDireksi = auth()->user()?->role === 'direksi';

        return $schema
            ->components([
                Section::make('Formulir Permohonan Cuti - PT BPR Banda Raya')
                    ->description('Format baku formulir permohonan cuti resmi. Lengkapi keperluan, tanggal, alamat, dan nomor kontak selama cuti.')
                    ->schema([
                        Hidden::make('employee_id')
                            ->default($employee?->id),

                        Hidden::make('status')
                            ->default('Pending'),

                        Grid::make(2)->schema([
                            Select::make('leave_type_id')
                                ->relationship(
                                    'leaveType',
                                    'name',
                                    function ($query) use ($employee) {
                                        $query->where('is_active', true);
                                        // Filter jenis kelamin jika ada restriksi
                                        if ($employee && $employee->gender) {
                                            if ($employee->gender === 'Laki-laki') {
                                                $query->where(fn ($q) => $q->whereNull('gender_restriction')->orWhere('gender_restriction', '!=', 'female'));
                                            } elseif ($employee->gender === 'Perempuan') {
                                                $query->where(fn ($q) => $q->whereNull('gender_restriction')->orWhere('gender_restriction', '!=', 'male'));
                                            }
                                        }
                                    }
                                )
                                ->label('Pilih Jenis Cuti')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->helperText(function (Get $get) use ($employee, $isDireksi) {
                                    $leaveTypeId = $get('leave_type_id');
                                    if ($leaveTypeId && $employee) {
                                        $type = LeaveType::find($leaveTypeId);
                                        $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
                                            ->where('leave_type_id', $leaveTypeId)
                                            ->where('year', now()->year)
                                            ->first();

                                        $remaining = $balance ? $balance->remaining_quota : ($type->default_days ?? 0);
                                        $msg = "Sisa Hak Cuti Anda: {$remaining} Hari Kerja.";

                                        if ($type && $type->max_consecutive_days) {
                                            $msg .= " (Maks. {$type->max_consecutive_days} hari berturut-turut per pengajuan)";
                                        }
                                        if ($type && $type->min_consecutive_days) {
                                            $msg .= " (Wajib min. {$type->min_consecutive_days} hari berturut-turut)";
                                        }
                                        if ($type && $type->code === 'LONG_LEAVE') {
                                            $joinDate = $employee?->join_date ? \Carbon\Carbon::parse($employee->join_date) : null;
                                            $years = $joinDate ? (int) $joinDate->diffInYears(now()) : 0;
                                            if ($years < 5) {
                                                $msg .= " ⚠️ Cuti Besar hanya untuk masa kerja min. 5 tahun (Masa kerja Anda: {$years} tahun).";
                                            } else {
                                                $msg .= " ✅ Anda berhak Cuti Besar (Masa kerja: {$years} tahun, durasi: 6 HK).";
                                            }
                                        }
                                        if ($isDireksi) {
                                            $msg .= " [Aturan Direksi: Durasi <= 2 hari di-ACC HRD, durasi > 2 hari ke Dewan Komisaris]";
                                        }
                                        return $msg;
                                    }
                                    return null;
                                }),

                            TextInput::make('total_days')
                                ->label('Total Hari Kerja (HK)')
                                ->numeric()
                                ->readOnly()
                                ->required()
                                ->helperText('Dihitung otomatis: Senin s/d Sabtu hari kerja (hanya Minggu yang tidak dihitung)')
                                ->rules([
                                    fn (Get $get): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $employee) {
                                        $leaveTypeId = $get('leave_type_id');
                                        if ($leaveTypeId) {
                                            $type = LeaveType::find($leaveTypeId);
                                            if (! $type) {
                                                return;
                                            }

                                            // 1. Validasi Min Consecutive Days (e.g. 5 days for Block Leave OJK)
                                            if ($type->min_consecutive_days && $value < $type->min_consecutive_days) {
                                                $fail("Pengajuan cuti {$type->name} mewajibkan minimal {$type->min_consecutive_days} hari kerja berturut-turut sesuai aturan OJK.");
                                            }

                                            // 2. Validasi Max Consecutive Days (e.g. 5 days for Cuti Tahunan, 2 days for Paternity)
                                            if ($type->max_consecutive_days && $value > $type->max_consecutive_days) {
                                                $fail("Pengajuan cuti {$type->name} maksimal {$type->max_consecutive_days} hari kerja berturut-turut per pengajuan untuk menjamin kelancaran operasional bank.");
                                            }

                                            // 3. Validasi Khusus Cuti Besar (Min 5 Tahun Masa Kerja & Maks 6 Hari)
                                            if ($type->code === 'LONG_LEAVE') {
                                                $joinDate = $employee?->join_date ? \Carbon\Carbon::parse($employee->join_date) : null;
                                                $years = $joinDate ? (int) $joinDate->diffInYears(now()) : 0;
                                                if ($years < 5) {
                                                    $fail("Cuti Besar hanya dapat diajukan oleh karyawan yang telah bekerja minimal 5 tahun di PT BPR Banda Raya. Masa kerja Anda saat ini: {$years} tahun.");
                                                }
                                                if ($value > 6) {
                                                    $fail("Durasi Cuti Besar maksimal adalah 6 hari kerja.");
                                                }
                                            }

                                            // 4. Validasi Sisa Kuota Cuti
                                            if ($employee && $type->code === 'ANNUAL') {
                                                $balance = EmployeeLeaveBalance::where('employee_id', $employee->id)
                                                    ->where('leave_type_id', $leaveTypeId)
                                                    ->where('year', now()->year)
                                                    ->first();

                                                $remaining = $balance ? $balance->remaining_quota : ($type->default_days ?? 0);
                                                if ($value > $remaining) {
                                                    $fail("Sisa kuota cuti tahunan Anda ({$remaining} hari) tidak mencukupi untuk mengajukan {$value} hari kerja.");
                                                }
                                            }
                                        }
                                    },
                                ]),

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
                        ]),

                        Textarea::make('reason')
                            ->label('Untuk Keperluan / Alasan Cuti')
                            ->rows(3)
                            ->placeholder('Tuliskan alasan permohonan cuti dan rencana serah terima pekerjaan')
                            ->required(),

                        Grid::make(2)->schema([
                            TextInput::make('leave_address')
                                ->label('Alamat yang Dapat Dihubungi Selama Cuti')
                                ->placeholder('Contoh: Jl. Engku Putri No. 12, Batam Center')
                                ->required(),

                            TextInput::make('contact_phone')
                                ->label('Nomor Telepon / WhatsApp Selama Cuti')
                                ->placeholder('Contoh: 0812-3456-7890')
                                ->tel()
                                ->required(),
                        ]),

                        FileUpload::make('attachment_url')
                            ->label('Lampiran Dokumen Pendukung (Surat Dokter / Undangan / dll)')
                            ->disk('public')
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
