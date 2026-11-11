<?php

namespace App\Filament\Resources\CompliancePolicies\Tables;

use App\Models\CompliancePolicy;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompliancePoliciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('document_number')
                    ->label('Nomor SK / Regulasi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('title')
                    ->label('Judul Kebijakan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->category_label)
                    ->color(fn ($record) => $record->category_color)
                    ->sortable(),

                TextColumn::make('effective_date')
                    ->label('Berlaku Sejak')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('target_audience')
                    ->label('Lingkup Audiens')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->target_audience_label)
                    ->color('info'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif Berlaku',
                        'archived' => 'Arsip',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'archived' => 'gray',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori')
                    ->options([
                        'apu_ppt' => 'APU & PPT (Regulasi OJK)',
                        'code_of_conduct' => 'Kode Etik & Anti-Fraud',
                        'sop_operation' => 'SOP Operasional & Layanan Kas',
                        'risk_management' => 'Manajemen Risiko & Keamanan',
                        'company_regulation' => 'Peraturan Perusahaan & Disiplin',
                    ]),

                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif (Berlaku)',
                        'archived' => 'Arsip',
                    ]),
            ])
            ->recordActions([
                Action::make('viewPdf')
                    ->label('Buka PDF')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (CompliancePolicy $record) => $record->attachment_url)
                    ->openUrlInNewTab(),

                Action::make('downloadPdf')
                    ->label('Unduh')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (CompliancePolicy $record) => $record->attachment_url)
                    ->openUrlInNewTab(),

                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}

