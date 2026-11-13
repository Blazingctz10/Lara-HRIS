<?php

namespace App\Filament\Resources\Announcements\Tables;

use App\Models\Announcement;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AnnouncementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('published_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('Judul Memo / Pengumuman')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'direksi_memo' => 'Memo Direksi',
                        'ojk_compliance' => 'Kepatuhan OJK',
                        'internal_news' => 'Berita Bank',
                        'holiday' => 'Libur Operasional',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'direksi_memo' => 'primary',
                        'ojk_compliance' => 'danger',
                        'internal_news' => 'info',
                        'holiday' => 'warning',
                        default => 'gray',
                    }),

                TextColumn::make('priority')
                    ->label('Prioritas')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'urgent' => 'Mendesak',
                        'important' => 'Penting',
                        'normal' => 'Normal',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'urgent' => 'danger',
                        'important' => 'warning',
                        default => 'success',
                    }),

                TextColumn::make('branch.name')
                    ->label('Target Cabang')
                    ->placeholder('Nasional (Semua)')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('published_at')
                    ->label('Tgl Publikasi')
                    ->date('d M Y')
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'direksi_memo' => 'Memo Direksi',
                        'ojk_compliance' => 'Kepatuhan OJK',
                        'internal_news' => 'Berita Internal',
                        'holiday' => 'Libur Operasional',
                    ]),

                SelectFilter::make('priority')
                    ->label('Prioritas')
                    ->options([
                        'urgent' => 'Mendesak',
                        'important' => 'Penting',
                        'normal' => 'Normal',
                    ]),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label('Baca Memo')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(fn (Announcement $record) => $record->title)
                    ->modalDescription(fn (Announcement $record) => ($record->branch?->name ?? 'Nasional (Semua Cabang)') . ' • ' . ($record->published_at?->format('d M Y') ?? '-'))
                    ->modalContent(function (Announcement $record) {
                        $html = '<div style="line-height: 1.65; font-size: 0.925rem;">';
                        $html .= '<div style="margin-bottom: 1rem; padding: 0.9rem 1rem; border-radius: 14px; background: rgba(14, 104, 56, 0.06);">';
                        $html .= Announcement::sanitizeContent($record->content);
                        $html .= '</div>';
                        if ($record->attachment_url) {
                            $url = e(asset('storage/' . $record->attachment_url));
                            $html .= '<p><a href="' . $url . '" target="_blank" rel="noopener noreferrer" style="font-weight: 700; text-decoration: underline;">Unduh / Buka Lampiran Dokumen</a></p>';
                        }
                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
