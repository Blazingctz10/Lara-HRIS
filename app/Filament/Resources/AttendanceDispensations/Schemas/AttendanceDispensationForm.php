<?php

namespace App\Filament\Resources\AttendanceDispensations\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AttendanceDispensationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Logbook Dispensasi Kehadiran (Khusus HRD & GA)')
                    ->description('Pencatatan izin informal (via WhatsApp/DM/Telepon), tugas dinas operasional GA, dan kondisi khusus agar otomatis tersinkron ke log presensi (status: Excused) sehingga tidak dianggap mangkir/alpa.')
                    ->schema([
                        Hidden::make('recorded_by')
                            ->default(auth()->id()),

                        Grid::make(2)->schema([
                            Select::make('employee_id')
                                ->relationship('employee', 'full_name')
                                ->label('Nama Pegawai')
                                ->searchable()
                                ->preload()
                                ->required(),

                            DatePicker::make('date')
                                ->label('Tanggal Izin / Dispensasi')
                                ->default(now())
                                ->required()
                                ->native(false),

                            Select::make('dispensation_type')
                                ->label('Kategori Dispensasi')
                                ->options([
                                    'Izin via Chat/WhatsApp' => 'Izin via Chat / WhatsApp (DM Atasan/HR)',
                                    'Tugas Lapangan GA' => 'Tugas Lapangan GA (General Affairs / Logistik Cabang)',
                                    'Tugas Dinas Luar' => 'Tugas Dinas Luar / Kunjungan Nasabah',
                                    'Sakit Mendadak' => 'Sakit Mendadak (Surat Dokter Menyusul)',
                                    'Lainnya / Isi Sendiri' => 'Lainnya / Opsi Bebas (Isi Sendiri)',
                                ])
                                ->default('Izin via Chat/WhatsApp')
                                ->required()
                                ->live(),

                            TextInput::make('custom_reason')
                                ->label('Alasan / Keterangan Bebas (Isi Sendiri)')
                                ->placeholder('Contoh: Izin terlambat karena kendala di jalan, urusan darurat keluarga, dll.')
                                ->required(fn (Get $get) => in_array($get('dispensation_type'), ['Izin via Chat/WhatsApp', 'Lainnya / Isi Sendiri']))
                                ->visible(fn (Get $get) => in_array($get('dispensation_type'), ['Izin via Chat/WhatsApp', 'Lainnya / Isi Sendiri']))
                                ->helperText('Ketikkan alasan langsung sesuai pesan WhatsApp/DM atau keperluan khusus yang dilaporkan ke HRD/GA.'),

                            TextInput::make('reference_contact')
                                ->label('Kontak / Jam Pesan Diterima')
                                ->placeholder('Contoh: WA jam 07:45 WIB dari No. 0812-xxxx')
                                ->nullable(),

                            FileUpload::make('attachment')
                                ->label('Bukti Chat / Foto Surat / Dokumen (Opsional)')
                                ->directory('dispensations')
                                ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                                ->maxSize(5120),
                        ]),

                        Textarea::make('description')
                            ->label('Catatan Rinci / Berita Acara HRD & GA')
                            ->rows(3)
                            ->placeholder('Tuliskan ringkasan pesan masuk, konfirmasi atasan, atau deskripsi penugasan luar.')
                            ->nullable(),
                    ]),
            ]);
    }
}
