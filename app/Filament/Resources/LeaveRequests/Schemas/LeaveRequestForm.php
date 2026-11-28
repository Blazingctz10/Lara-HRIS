<?php

namespace App\Filament\Resources\LeaveRequests\Schemas;

use App\Models\Employee;
use App\Models\LeaveRequest;
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
                    ->description('Pencatatan cuti bebas teks tanpa pelimitan kuota. Alur: Karyawan -> Bagian/Divisi -> TTD Leader Divisi -> TTD Direksi -> ACC.')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set): void {
                                    $emp = $state ? Employee::with(['branch', 'department', 'position'])->find($state) : null;
                                    $set('division_category', LeaveRequest::resolveDivisionCategory($emp));
                                }),

                            Select::make('division_category')
                                ->label('Bagian / Divisi')
                                ->options(LeaveRequest::divisionCategoryOptions())
                                ->default('operasional')
                                ->required(),

                            TextInput::make('leave_type_custom')
                                ->label('Jenis / Keterangan Cuti (Teks Bebas)')
                                ->placeholder('Contoh: Cuti Tahunan / Izin Keluarga / Sakit')
                                ->required()
                                ->maxLength(150),

                            Select::make('leave_type_id')
                                ->relationship('leaveType', 'name')
                                ->label('Kategori Referensi (Opsional)')
                                ->default(fn () => LeaveType::query()->value('id'))
                                ->searchable()
                                ->preload(),

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
                                ->helperText('Dihitung otomatis: Senin s/d Sabtu hari kerja (tanpa batas maksimal).'),

                            Select::make('status')
                                ->label('Status Persetujuan / Otorisasi')
                                ->options([
                                    'Pending' => 'Menunggu TTD Leader Divisi',
                                    'Approved_Leader' => 'TTD Leader Divisi -> Menunggu TTD Direksi',
                                    'Approved_Direksi' => 'TTD Direksi (ACC Final)',
                                    'Rejected' => 'Ditolak',
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
