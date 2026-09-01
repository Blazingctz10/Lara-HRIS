<?php

namespace App\Filament\Resources\Announcements\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

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
                        'urgent' => '🔴 Mendesak',
                        'important' => '🟡 Penting',
                        'normal' => '🟢 Normal',
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
