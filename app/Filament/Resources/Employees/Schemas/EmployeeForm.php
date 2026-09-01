<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Pegawai')
                    ->description('Data profil dan identitas kependudukan')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('nip')
                                ->label('NIP (Nomor Induk Pegawai)')
                                ->placeholder('Contoh: BNK-2026-0001')
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('nik')
                                ->label('NIK (KTP)')
                                ->placeholder('16 digit NIK')
                                ->maxLength(20)
                                ->unique(ignoreRecord: true),

                            TextInput::make('full_name')
                                ->label('Nama Lengkap & Gelar')
                                ->required(),

                            TextInput::make('email')
                                ->label('Email Kantor / Pribadi')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true),

                            TextInput::make('phone')
                                ->label('Nomor WhatsApp / HP')
                                ->tel()
                                ->required(),

                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->options([
                                    'Male' => 'Laki-Laki',
                                    'Female' => 'Perempuan',
                                ])
                                ->required(),

                            DatePicker::make('birth_date')
                                ->label('Tanggal Lahir')
                                ->native(false),

                            FileUpload::make('photo_url')
                                ->label('Foto Profil Pegawai')
                                ->image()
                                ->avatar()
                                ->imageEditor()
                                ->directory('employees/photos')
                                ->maxSize(5120)
                                ->columnSpan(2),
                        ]),

                        Textarea::make('address')
                            ->label('Alamat Domisili')
                            ->rows(2),
                    ]),

                Section::make('Penempatan & Status Kepegawaian')
                    ->description('Unit kerja cabang, divisi, jabatan, dan masa kerja')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('branch_id')
                                ->relationship('branch', 'name')
                                ->label('Kantor Cabang / Penempatan')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('department_id')
                                ->relationship('department', 'name')
                                ->label('Divisi / Unit Kerja')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('position_id')
                                ->relationship('position', 'name')
                                ->label('Jabatan')
                                ->searchable()
                                ->preload()
                                ->required(),

                            Select::make('employment_status')
                                ->label('Status Ketenagakerjaan')
                                ->options([
                                    'Permanent' => 'Permanent (Karyawan Tetap)',
                                    'Contract' => 'Contract (PKWT)',
                                    'Probation' => 'Probation (Masa Percobaan)',
                                    'Internship' => 'Internship (Magang)',
                                ])
                                ->default('Contract')
                                ->required(),

                            DatePicker::make('join_date')
                                ->label('Tanggal Mulai Bekerja')
                                ->default(now())
                                ->required()
                                ->native(false),

                            DatePicker::make('end_date')
                                ->label('Tanggal Berakhir Kontrak')
                                ->helperText('Kosongkan jika Karyawan Tetap')
                                ->native(false),

                            Select::make('status')
                                ->label('Status Aktif Pegawai')
                                ->options([
                                    'Active' => 'Active (Aktif Bekerja)',
                                    'On Leave' => 'On Leave (Sedang Cuti Panjang)',
                                    'Resigned' => 'Resigned (Mengundurkan Diri)',
                                    'Terminated' => 'Terminated (Pemberhentian)',
                                ])
                                ->default('Active')
                                ->required(),
                        ]),
                    ]),

                Section::make('Data Payroll & Perpajakan')
                    ->description('Informasi rekening penggajian & nomor NPWP')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('bank_account_number')
                                ->label('Nomor Rekening Payroll')
                                ->placeholder('Contoh: 1200012345678'),

                            TextInput::make('bank_account_holder')
                                ->label('Nama Pemilik Rekening')
                                ->placeholder('Nama sesuai buku tabungan'),

                            TextInput::make('npwp')
                                ->label('Nomor Pokok Wajib Pajak (NPWP)')
                                ->placeholder('Contoh: 70.123.456.7-015.000'),
                        ]),
                    ]),
            ]);
    }
}
