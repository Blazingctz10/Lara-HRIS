<?php

namespace App\Filament\Portal\Resources\CompliancePolicies\Tables;

use App\Models\CompliancePolicy;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

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
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('title')
                    ->label('Judul Kebijakan / SOP')
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

                TextColumn::make('summary')
                    ->label('Ringkasan Ketentuan')
                    ->limit(90)
                    ->wrap()
                    ->placeholder('Klik untuk melihat ringkasan...'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Kategori Kebijakan')
                    ->options([
                        'apu_ppt' => 'APU & PPT (Regulasi OJK)',
                        'code_of_conduct' => 'Kode Etik & Anti-Fraud',
                        'sop_operation' => 'SOP Operasional & Layanan Kas',
                        'risk_management' => 'Manajemen Risiko & Keamanan',
                        'company_regulation' => 'Peraturan Perusahaan & Disiplin',
                    ]),
            ])
            ->recordActions([
                Action::make('detail')
                    ->label('Ringkasan')
                    ->icon('heroicon-o-information-circle')
                    ->color('primary')
                    ->modalHeading(fn (CompliancePolicy $record) => "Kebijakan: {$record->document_number}")
                    ->modalDescription(fn (CompliancePolicy $record) => $record->title)
                    ->modalContent(function (CompliancePolicy $record) {
                        $html = '<div style="line-height: 1.6; font-size: 0.925rem;">';
                        $html .= '<p><strong>Kategori:</strong> ' . e($record->category_label) . '</p>';
                        $html .= '<p><strong>Tanggal Berlaku:</strong> ' . $record->effective_date->format('d M Y') . '</p>';
                        $html .= '<p><strong>Lingkup:</strong> ' . e($record->target_audience_label) . '</p>';
                        if ($record->summary) {
                            $html .= '<div style="margin-top: 1rem; padding: 1rem; background: var(--m3-surface-container, rgba(0,0,0,0.03)); border-radius: 12px;">';
                            $html .= '<strong>Ringkasan / Intisari Kebijakan:</strong><br>';
                            $html .= nl2br(e($record->summary));
                            $html .= '</div>';
                        }
                        $html .= '</div>';
                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Action::make('viewPdf')
                    ->label('Buka PDF')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn (CompliancePolicy $record) => $record->attachment_url)
                    ->openUrlInNewTab(),

                Action::make('downloadPdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (CompliancePolicy $record) => $record->attachment_url)
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading('Belum Ada Kebijakan Kepatuhan')
            ->emptyStateDescription('Divisi Kepatuhan belum merilis dokumen kebijakan aktif untuk unit kerja Anda.')
            ->emptyStateIcon('heroicon-o-shield-check');
    }
}

