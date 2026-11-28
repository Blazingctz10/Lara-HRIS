<?php

namespace App\Filament\Portal\Resources\LeaveRequests\Schemas;

use App\Models\Employee;
use App\Models\LeaveRequest;
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
                    ->description('Pengajuan cuti bebas teks tanpa batasan kuota. Alur: Karyawan -> Bagian/Divisi -> TTD Leader Divisi -> TTD Direksi -> ACC.')
                    ->schema([
                        Hidden::make('employee_id')
                            ->default($employee?->id),

                        Hidden::make('leave_type_id')
                            ->default(fn () => LeaveType::query()->value('id')),

                        Hidden::make('status')
                            ->default('Pending'),

                        Grid::make(2)->schema([
                            TextInput::make('leave_type_custom')
                                ->label('Jenis / Keterangan Cuti (Teks Bebas)')
                                ->placeholder('Contoh: Cuti Tahunan / Cuti Keluarga / Izin Sakit / Keperluan Pribadi')
                                ->required()
                                ->maxLength(150)
                                ->helperText('Tuliskan jenis atau kategori cuti Anda secara bebas tanpa batasan kuota.'),

                            Select::make('division_category')
                                ->label('Bagian / Divisi Anda')
                                ->options(LeaveRequest::divisionCategoryOptions())
                                ->default(fn () => LeaveRequest::resolveDivisionCategory($employee))
                                ->required()
                                ->helperText('Menentukan Leader Divisi yang akan menandatangani pengajuan cuti Anda sebelum diteruskan ke Direksi.'),

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
                                ->default(1)
                                ->minValue(1)
                                ->required()
                                ->helperText('Dihitung otomatis (Senin–Sabtu). Tidak ada batas maksimal hari atau pemblokiran kuota.')
                                ->columnSpanFull(),
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
