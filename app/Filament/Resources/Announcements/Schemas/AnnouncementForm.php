<?php

namespace App\Filament\Resources\Announcements\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AnnouncementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Rincian Memo & Pengumuman Internal BPR Banda Raya')
                    ->description('Diterbitkan untuk staf kantor cabang dan head office')
                    ->schema([
                        Hidden::make('author_id')
                            ->default(auth()->id()),

                        TextInput::make('title')
                            ->label('Judul Pengumuman / Memo')
                            ->placeholder('Contoh: Surat Edaran Direksi Terkait Ketentuan Operasional...')
                            ->required()
                            ->columnSpanFull(),

                        Grid::make(3)->schema([
                            Select::make('category')
                                ->label('Kategori')
                                ->options([
                                    'direksi_memo' => 'Memo / Surat Edaran Direksi',
                                    'ojk_compliance' => 'Kepatuhan Regulasi OJK',
                                    'internal_news' => 'Berita Internal Bank',
                                    'holiday' => 'Libur & Kalender Kerja',
                                ])
                                ->default('direksi_memo')
                                ->required(),

                            Select::make('priority')
                                ->label('Tingkat Prioritas')
                                ->options([
                                    'normal' => 'Normal',
                                    'important' => 'Penting',
                                    'urgent' => 'Sangat Mendesak (Urgent)',
                                ])
                                ->default('normal')
                                ->required(),

                            Select::make('branch_id')
                                ->relationship('branch', 'name')
                                ->label('Target Cabang')
                                ->placeholder('Nasional (Semua Cabang)')
                                ->searchable()
                                ->preload(),
                        ]),

                        Grid::make(3)->schema([
                            DatePicker::make('published_at')
                                ->label('Tanggal Tayang')
                                ->default(now())
                                ->required(),

                            DatePicker::make('expires_at')
                                ->label('Tanggal Berakhir (Opsional)')
                                ->helperText('Kosongkan jika berlaku permanen'),

                            Toggle::make('is_active')
                                ->label('Status Aktif / Publikasikan')
                                ->default(true)
                                ->inline(false),
                        ]),

                        RichEditor::make('content')
                            ->label('Isi Pengumuman / Memo Lengkap')
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('attachment_url')
                            ->label('Lampiran Dokumen PDF / Surat Edaran')
                            ->directory('announcements/attachments')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
