<?php

namespace App\Filament\Resources\CompliancePolicies\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompliancePolicyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identitas Kebijakan & Regulasi Bank')
                    ->description('Pastikan nomor SK / Surat Edaran sesuai arsip fisik resmi Divisi Kepatuhan.')
                    ->icon('heroicon-o-shield-check')
                    ->iconColor('primary')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('document_number')
                                ->label('Nomor Dokumen / SK Direksi')
                                ->placeholder('Contoh: SK.DIR/014/BPR-BR/2026 atau SE-KEP/002/2026')
                                ->required()
                                ->maxLength(100)
                                ->unique(ignoreRecord: true),

                            TextInput::make('title')
                                ->label('Judul Kebijakan / SOP Kepatuhan')
                                ->placeholder('Contoh: Pedoman Penerapan Program APU & PPT')
                                ->required()
                                ->maxLength(255),
                        ]),

                        Grid::make(3)->schema([
                            Select::make('category')
                                ->label('Kategori Regulasi')
                                ->options([
                                    'apu_ppt' => 'APU & PPT (Regulasi OJK)',
                                    'code_of_conduct' => 'Kode Etik & Anti-Fraud',
                                    'sop_operation' => 'SOP Operasional & Layanan Kas',
                                    'risk_management' => 'Manajemen Risiko & Keamanan',
                                    'company_regulation' => 'Peraturan Perusahaan & Disiplin',
                                    'other' => 'Kebijakan Lainnya',
                                ])
                                ->required()
                                ->native(false),

                            DatePicker::make('effective_date')
                                ->label('Tanggal Efektif Berlaku')
                                ->required()
                                ->native(false)
                                ->default(now()),

                            Select::make('target_audience')
                                ->label('Target Audiens Pegawai')
                                ->options([
                                    'all' => 'Seluruh Karyawan (Nasional)',
                                    'branch_ops' => 'Operasional & Layanan Cabang',
                                    'executive_only' => 'Khusus Pejabat Eksekutif & Pincab',
                                ])
                                ->required()
                                ->default('all')
                                ->native(false),
                        ]),

                        Select::make('status')
                            ->label('Status Kebijakan')
                            ->options([
                                'active' => 'Aktif (Berlaku)',
                                'archived' => 'Arsip / Tidak Berlaku Lagi',
                            ])
                            ->required()
                            ->default('active')
                            ->native(false),

                        Textarea::make('summary')
                            ->label('Ringkasan / Poin Kunci Kebijakan')
                            ->placeholder('Tuliskan rangkuman pokok penting atau intisari regulasi untuk dipahami staf...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),

                Section::make('Berkas Lampiran Resmi (PDF)')
                    ->description('Unggah dokumen kebijakan resmi bertanda tangan Dewan Direksi.')
                    ->icon('heroicon-o-document-arrow-up')
                    ->iconColor('success')
                    ->schema([
                        FileUpload::make('attachment_path')
                            ->label('Berkas PDF Kebijakan')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('compliance-policies')
                            ->disk('public')
                            ->maxSize(15360) // 15MB
                            ->required()
                            ->helperText('Hanya menerima format dokumen PDF resmi (Maksimal 15 MB).')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}

