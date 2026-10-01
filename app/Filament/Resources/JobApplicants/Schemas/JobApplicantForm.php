<?php

namespace App\Filament\Resources\JobApplicants\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pelamar')
                    ->description('Data diri dan posisi yang dilamar dari email cPanel')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Alamat Email')
                                ->email()
                                ->required()
                                ->maxLength(255),

                            TextInput::make('phone')
                                ->label('Nomor Telepon / WhatsApp')
                                ->tel()
                                ->maxLength(50),

                            Select::make('position_applied')
                                ->label('Posisi Dilamar')
                                ->options([
                                    'Customer Service (CS)' => 'Customer Service (CS)',
                                    'Teller Kas & Kliring' => 'Teller Kas & Kliring',
                                    'Account Officer (AO) Komersial' => 'Account Officer (AO) Komersial',
                                    'Account Officer (AO) Dana / Funding' => 'Account Officer (AO) Dana / Funding',
                                    'IT Support & Developer' => 'IT Support & Developer',
                                    'Legal & Compliance' => 'Legal & Compliance',
                                    'Staff HR & GA' => 'Staff HR & GA',
                                    'Internal Audit' => 'Internal Audit',
                                    'Umum / Staf Operasional' => 'Umum / Staf Operasional',
                                ])
                                ->searchable()
                                ->required(),
                        ]),
                    ]),

                Section::make('Status & Penilaian HR')
                    ->description('Tahapan seleksi dan catatan evaluasi tim rekrutmen')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('status')
                                ->label('Status Seleksi')
                                ->options([
                                    'Baru' => 'Baru Masuk (Email)',
                                    'Screening' => 'Screening Berkas Administrasi',
                                    'Interview' => 'Tahap Wawancara',
                                    'Diterima' => 'Diterima Kerja',
                                    'Ditolak' => 'Tidak Lolos / Ditolak',
                                ])
                                ->default('Baru')
                                ->required(),

                            DateTimePicker::make('email_date')
                                ->label('Waktu Email Diterima')
                                ->default(now())
                                ->required(),
                        ]),

                        Textarea::make('notes')
                            ->label('Catatan HR / Hasil Seleksi')
                            ->placeholder('Tambahkan catatan wawancara, hasil psikotes, atau pertimbangan kualifikasi...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Surat Lamaran & Lampiran CV')
                    ->description('Salinan teks email dan berkas CV pelamar')
                    ->schema([
                        TextInput::make('subject')
                            ->label('Subjek Email')
                            ->maxLength(255)
                            ->columnSpanFull(),

                        FileUpload::make('cv_path')
                            ->label('Berkas CV / Resume (PDF/DOCX)')
                            ->disk('public')
                            ->directory('recruitment_cv')
                            ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240)
                            ->openable()
                            ->downloadable()
                            ->columnSpanFull(),

                        Textarea::make('message')
                            ->label('Isi Pesan / Cover Letter')
                            ->rows(6)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
