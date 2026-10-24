<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Profil Kepegawaian BPR')
                    ->columnSpanFull()
                    ->tabs([
                        // =========================================================================
                        // TAB 1: DATA PRIBADI & KEPEGAWAIAN
                        // =========================================================================
                        Tab::make('Data Diri & Penempatan')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('I. Identitas Pokok Pegawai')
                                    ->description('Informasi biodata diri, NIP/NIK BPR, dan kontak resmi')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('nip')
                                                ->label('NIK / NIP Pegawai BPR')
                                                ->placeholder('Contoh: 1101 0064 / BNK-2026-0001')
                                                ->required()
                                                ->unique(ignoreRecord: true),

                                            TextInput::make('full_name')
                                                ->label('Nama Lengkap & Gelar')
                                                ->placeholder('Sesuai KTP / SK')
                                                ->required(),

                                            Select::make('gender')
                                                ->label('Jenis Kelamin')
                                                ->options([
                                                    'Male' => 'Laki-Laki',
                                                    'Female' => 'Perempuan',
                                                ])
                                                ->required(),

                                            TextInput::make('birth_place')
                                                ->label('Tempat Lahir')
                                                ->placeholder('Contoh: Kerinci, Batam'),

                                            DatePicker::make('birth_date')
                                                ->label('Tanggal Lahir')
                                                ->native(false),

                                            TextInput::make('phone')
                                                ->label('Nomor WhatsApp / HP')
                                                ->placeholder('Contoh: 0852 6579 7184')
                                                ->tel()
                                                ->required(),

                                            TextInput::make('email')
                                                ->label('Email Dinas / Kantor BPR')
                                                ->placeholder('nama@bprbandaraya.com')
                                                ->email()
                                                ->required()
                                                ->unique(ignoreRecord: true),

                                            TextInput::make('personal_email')
                                                ->label('Email Pribadi')
                                                ->placeholder('Contoh: user@gmail.com')
                                                ->email(),

                                            FileUpload::make('photo_url')
                                                ->label('Pas Foto Pegawai')
                                                ->image()
                                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'image/jpg'])
                                                ->avatar()
                                                ->imageEditor()
                                                ->directory('employees/photos')
                                                ->maxSize(5120),
                                        ]),

                                        Textarea::make('address')
                                            ->label('Alamat Domisili Lengkap')
                                            ->placeholder('Nama jalan, blok, nomor rumah, kelurahan/kecamatan')
                                            ->rows(2),
                                    ]),

                                Section::make('II. Jabatan, Grade & Penempatan Kerja')
                                    ->description('Unit kerja penempatan, tingkatan grade perbankan, dan surat keputusan')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Select::make('branch_id')
                                                ->relationship('branch', 'name')
                                                ->label('Unit Kerja / Kantor')
                                                ->searchable()
                                                ->preload()
                                                ->required(),

                                            Select::make('department_id')
                                                ->relationship('department', 'name')
                                                ->label('Divisi Organisasi')
                                                ->searchable()
                                                ->preload()
                                                ->required(),

                                            Select::make('position_id')
                                                ->relationship('position', 'name')
                                                ->label('Jabatan')
                                                ->searchable()
                                                ->preload()
                                                ->required(),

                                            DatePicker::make('job_effective_date')
                                                ->label('Tanggal Efektif Jabatan')
                                                ->native(false),

                                            TextInput::make('grade')
                                                ->label('Grade Jabatan BPR')
                                                ->numeric()
                                                ->minValue(1)
                                                ->maxValue(15)
                                                ->placeholder('Contoh: 4, 5, 6')
                                                ->helperText('Tingkat grade kepangkatan perbankan'),

                                            DatePicker::make('grade_effective_date')
                                                ->label('Tanggal Efektif Grade')
                                                ->native(false),
                                        ]),
                                    ]),

                                Section::make('III. Masa Kerja & Status Ketenagakerjaan')
                                    ->description('Tanggal bergabung, evaluasi kontrak/probation, hingga masa pensiun/keluar')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Select::make('employment_status')
                                                ->label('Status Ketenagakerjaan')
                                                ->options([
                                                    'Permanent' => 'Permanent (Karyawan Tetap)',
                                                    'Contract' => 'Contract (PKWT)',
                                                    'Probation' => 'Probation (Masa Percobaan)',
                                                    'Internship' => 'Internship (Magang)',
                                                ])
                                                ->default('Contract')
                                                ->live()
                                                ->required(),

                                            DatePicker::make('join_date')
                                                ->label('Join Date (Mulai Bekerja)')
                                                ->default(now())
                                                ->required()
                                                ->native(false),

                                            DatePicker::make('permanent_date')
                                                ->label('Tanggal Pengangkatan Tetap (Permanent)')
                                                ->helperText('Diisi saat diangkat menjadi Karyawan Tetap')
                                                ->native(false),

                                            DatePicker::make('contract_end_date')
                                                ->label('Tanggal Berakhir Kontrak (PKWT)')
                                                ->visible(fn ($get) => in_array($get('employment_status'), ['Contract', 'Internship']))
                                                ->native(false),

                                            DatePicker::make('probation_end_date')
                                                ->label('Tanggal Akhir Masa Percobaan')
                                                ->visible(fn ($get) => $get('employment_status') === 'Probation')
                                                ->native(false),

                                            Select::make('status')
                                                ->label('Status Pegawai Saat Ini')
                                                ->options([
                                                    'Active' => 'Aktif Bekerja',
                                                    'On Leave' => 'Cuti Panjang',
                                                    'Resigned' => 'Keluar (Mengundurkan Diri)',
                                                    'Terminated' => 'Keluar (Diberhentikan / Habis Kontrak / Pensiun)',
                                                ])
                                                ->default('Active')
                                                ->live()
                                                ->required(),

                                            DatePicker::make('exit_date')
                                                ->label('Tanggal Keluar')
                                                ->visible(fn ($get) => in_array($get('status'), ['Resigned', 'Terminated']))
                                                ->native(false),

                                            Select::make('exit_reason')
                                                ->label('Keterangan / Alasan Keluar')
                                                ->options([
                                                    'Mengundurkan diri' => 'Mengundurkan diri',
                                                    'Habis kontrak' => 'Habis kontrak',
                                                    'Diberhentikan' => 'Diberhentikan',
                                                    'Usia Pensiun' => 'Usia Pensiun',
                                                ])
                                                ->visible(fn ($get) => in_array($get('status'), ['Resigned', 'Terminated'])),
                                        ]),
                                    ]),
                            ]),

                        // =========================================================================
                        // TAB 2: PENDIDIKAN, LEGALITAS & REKENING
                        // =========================================================================
                        Tab::make('Pendidikan & Legalitas')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                Section::make('Riwayat Jenjang Pendidikan Terakhir')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            Select::make('education_level')
                                                ->label('Jenjang Pendidikan')
                                                ->options([
                                                    'SMP' => 'SMP',
                                                    'SMA' => 'SMA',
                                                    'SMK' => 'SMK',
                                                    'D-1' => 'D-1',
                                                    'D-3' => 'D-3',
                                                    'D-4' => 'D-4',
                                                    'S-1' => 'S-1',
                                                    'S-2' => 'S-2',
                                                ])
                                                ->placeholder('Pilih Jenjang'),

                                            TextInput::make('education_major')
                                                ->label('Jurusan')
                                                ->placeholder('Contoh: Teknik Elektronika, Akuntansi'),

                                            TextInput::make('education_institution')
                                                ->label('Universitas / Sekolah')
                                                ->placeholder('Contoh: Univ. Negeri Yogyakarta'),

                                            TextInput::make('ipk')
                                                ->label('IPK')
                                                ->numeric()
                                                ->step(0.01)
                                                ->placeholder('Contoh: 2.59'),
                                        ]),
                                    ]),

                                Section::make('Dokumen Legalitas & Jaminan Sosial')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('nik')
                                                ->label('Nomor KTP (16 Digit Dukcapil)')
                                                ->placeholder('16 digit NIK KTP')
                                                ->maxLength(20)
                                                ->unique(ignoreRecord: true),

                                            TextInput::make('npwp')
                                                ->label('Nomor Pokok Wajib Pajak (NPWP)')
                                                ->placeholder('Contoh: 15.690.690.1-215.000'),

                                            TextInput::make('bpjs_tk_number')
                                                ->label('No. BPJS Ketenagakerjaan (BPJSTK)')
                                                ->placeholder('Contoh: 08017700462'),

                                            TextInput::make('bpjs_kes_number')
                                                ->label('No. BPJS Kesehatan')
                                                ->placeholder('Contoh: 0001617375611'),
                                        ]),
                                    ]),

                                Section::make('Informasi Rekening Bank')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('bank_bpr_account')
                                                ->label('Nomor Rekening Bank BPR Banda Raya (Rek. BR)')
                                                ->placeholder('Contoh: 01.10.05307')
                                                ->helperText('Rekening internal operasional BPR'),

                                            TextInput::make('bank_external_name')
                                                ->label('Nama Bank Lain (Umum)')
                                                ->placeholder('Contoh: Bank Mandiri / BCA / BNI'),

                                            TextInput::make('bank_external_account')
                                                ->label('Nomor Rekening Bank Lain')
                                                ->placeholder('Contoh: 109-00-1514578-2'),

                                            TextInput::make('bank_account_holder')
                                                ->label('Nama Pemilik Rekening')
                                                ->placeholder('Nama lengkap sesuai buku tabungan'),
                                        ]),
                                    ]),
                            ]),

                        // =========================================================================
                        // TAB 3: DATA KELUARGA & PTKP
                        // =========================================================================
                        Tab::make('Keluarga & PTKP')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Status Pajak (PTKP) & Kontak Darurat')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            Select::make('ptkp_status')
                                                ->label('Status PTKP Pajak')
                                                ->options([
                                                    'TK/0' => 'TK/0 (Tidak Kawin - Tanpa Tanggungan)',
                                                    'TK/1' => 'TK/1 (Tidak Kawin - 1 Tanggungan)',
                                                    'TK/2' => 'TK/2 (Tidak Kawin - 2 Tanggungan)',
                                                    'TK/3' => 'TK/3 (Tidak Kawin - 3 Tanggungan)',
                                                    'K/0'  => 'K/0 (Kawin - Tanpa Tanggungan)',
                                                    'K/1'  => 'K/1 (Kawin - 1 Tanggungan)',
                                                    'K/2'  => 'K/2 (Kawin - 2 Tanggungan)',
                                                    'K/3'  => 'K/3 (Kawin - 3 Tanggungan)',
                                                ])
                                                ->placeholder('Pilih Status PTKP'),

                                            TextInput::make('emergency_contact_name')
                                                ->label('Nama Kontak Darurat (Emergency Call)')
                                                ->placeholder('Nama kontak keluarga'),

                                            TextInput::make('emergency_contact_phone')
                                                ->label('No. HP Kontak Darurat')
                                                ->placeholder('Contoh: 0812xxxxxxx'),

                                            TextInput::make('emergency_contact_relation')
                                                ->label('Hubungan')
                                                ->placeholder('Contoh: Istri, Suami, Orang Tua'),
                                        ]),
                                    ]),

                                Section::make('Daftar Anggota Keluarga (Pasangan & Anak)')
                                    ->schema([
                                        Repeater::make('families')
                                            ->relationship()
                                            ->label('Anggota Keluarga')
                                            ->schema([
                                                Grid::make(6)->schema([
                                                    TextInput::make('name')
                                                        ->label('Nama Anggota')
                                                        ->required()
                                                        ->columnSpan(2),

                                                    Select::make('relationship')
                                                        ->label('Hubungan')
                                                        ->options([
                                                            'ybs' => 'Yang Bersangkutan (ybs)',
                                                            'Istri' => 'Istri',
                                                            'Suami' => 'Suami',
                                                            'Anak' => 'Anak',
                                                            'Orang Tua' => 'Orang Tua',
                                                        ])
                                                        ->required(),

                                                    TextInput::make('nik')
                                                        ->label('NIK KTP / KK')
                                                        ->placeholder('16 digit NIK')
                                                        ->columnSpan(1),

                                                    TextInput::make('birth_place')
                                                        ->label('Tempat Lahir')
                                                        ->placeholder('Kota/Kabupaten'),

                                                    DatePicker::make('birth_date')
                                                        ->label('Tanggal Lahir')
                                                        ->native(false),
                                                ]),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('+ Tambah Anggota Keluarga')
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),

                        // =========================================================================
                        // TAB 4: RIWAYAT KARIR
                        // =========================================================================
                        Tab::make('Riwayat Karir')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Riwayat Mutasi Jabatan, Cabang & Kenaikan Grade')
                                    ->description('Rekam jejak Surat Keputusan (SK) Direksi pengangkatan dan promosi jabatan')
                                    ->schema([
                                        Repeater::make('careerMovements')
                                            ->relationship()
                                            ->label('Catatan Riwayat Karir')
                                            ->schema([
                                                Grid::make(4)->schema([
                                                    Select::make('to_branch_id')
                                                        ->relationship('toBranch', 'name')
                                                        ->label('Unit Kerja / Cabang')
                                                        ->searchable()
                                                        ->preload(),

                                                    Select::make('to_position_id')
                                                        ->relationship('toPosition', 'name')
                                                        ->label('Posisi / Jabatan')
                                                        ->searchable()
                                                        ->preload(),

                                                    DatePicker::make('effective_date')
                                                        ->label('Tgl Efektif Jabatan')
                                                        ->native(false),

                                                    TextInput::make('sk_number')
                                                        ->label('No. Surat Pengangkatan (SK)')
                                                        ->placeholder('Contoh: 003/KEP/DIR/BPR-BR/IV/2015'),

                                                    TextInput::make('grade')
                                                        ->label('Grade')
                                                        ->numeric()
                                                        ->placeholder('Contoh: 5, 6'),

                                                    DatePicker::make('grade_effective_date')
                                                        ->label('Tgl Efektif Grade')
                                                        ->native(false),

                                                    Select::make('movement_notes')
                                                        ->label('Keterangan SK')
                                                        ->options([
                                                            'Karyawan Kontrak' => 'Karyawan Kontrak',
                                                            'Karyawan Tetap' => 'Karyawan Tetap',
                                                            'Kenaikan Grade' => 'Kenaikan Grade',
                                                            'Mutasi Jabatan dan Kenaikan Grade' => 'Mutasi Jabatan dan Kenaikan Grade',
                                                            'Rotasi Antar Cabang' => 'Rotasi Antar Cabang',
                                                        ])
                                                        ->columnSpan(2),
                                                ]),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('+ Tambah Catatan Karir / SK')
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),

                        // =========================================================================
                        // TAB 5: RIWAYAT PELATIHAN
                        // =========================================================================
                        Tab::make('Riwayat Pelatihan')
                            ->icon('heroicon-o-clipboard-document-check')
                            ->schema([
                                Section::make('Daftar Pelatihan & Pengembangan Kompetensi (Training History)')
                                    ->description('Workshop, sertifikasi profesi perbankan, dan pembinaan internal')
                                    ->schema([
                                        Repeater::make('trainings')
                                            ->relationship()
                                            ->label('Daftar Pelatihan yang Diikuti')
                                            ->schema([
                                                Grid::make(5)->schema([
                                                    TextInput::make('training_name')
                                                        ->label('Nama Pelatihan')
                                                        ->placeholder('Contoh: Penilaian Agunan Kredit BPR, Smart Service')
                                                        ->required()
                                                        ->columnSpan(2),

                                                    TextInput::make('schedule')
                                                        ->label('Jadwal / Waktu')
                                                        ->placeholder('Contoh: 17-Jul-13 / 17-24 Okt 2016'),

                                                    TextInput::make('organizer')
                                                        ->label('Penyelenggara')
                                                        ->placeholder('Contoh: BPR Banda Raya, MAPPI, Perbarindo'),

                                                    TextInput::make('trainer')
                                                        ->label('Pemateri')
                                                        ->placeholder('Contoh: Bp. Kardi P, SH / Direksi'),

                                                    TextInput::make('location')
                                                        ->label('Lokasi')
                                                        ->placeholder('Contoh: Batam, Jakarta, Bali, Zoom')
                                                        ->columnSpan(2),
                                                ]),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('+ Tambah Riwayat Pelatihan')
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),

                        // =========================================================================
                        // TAB 6: PERFORMANCE APPRAISAL (PA)
                        // =========================================================================
                        Tab::make('Performance Appraisal')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Riwayat Penilaian Kinerja Karyawan (Performance Appraisal - PA)')
                                    ->description('Skor angka mutu dan predikat penilaian tahunan BPR Banda Raya')
                                    ->schema([
                                        Repeater::make('performanceAppraisals')
                                            ->relationship()
                                            ->label('Nilai PA Tahunan')
                                            ->schema([
                                                Grid::make(4)->schema([
                                                    TextInput::make('period_year')
                                                        ->label('Periode Penilaian (Tahun Buku)')
                                                        ->numeric()
                                                        ->placeholder('Contoh: 2024, 2025, 2026')
                                                        ->required(),

                                                    Select::make('letter_grade')
                                                        ->label('Nilai Predikat Huruf')
                                                        ->options([
                                                            'A' => 'A (Istimewa / Sangat Baik)',
                                                            'B' => 'B (Baik / Memenuhi Target)',
                                                            'C' => 'C (Cukup)',
                                                            'D' => 'D (Kurang)',
                                                            'E' => 'E (Sangat Kurang)',
                                                        ])
                                                        ->default('B')
                                                        ->required(),

                                                    TextInput::make('score')
                                                        ->label('Angka Mutu (Skor PA)')
                                                        ->numeric()
                                                        ->step(0.01)
                                                        ->placeholder('Contoh: 3.80, 3.77, 3.97')
                                                        ->required(),

                                                    TextInput::make('notes')
                                                        ->label('Catatan Penilaian Evaluator')
                                                        ->placeholder('Komentar atas pencapaian kinerja'),
                                                ]),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('+ Tambah Penilaian Kinerja (PA)')
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
