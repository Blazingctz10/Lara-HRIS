<?php

namespace App\Filament\Widgets;

use App\Models\CompliancePolicy;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentCompliancePoliciesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return in_array(auth()->user()?->role, ['compliance', 'direksi', 'super_admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CompliancePolicy::query()
                    ->where('status', 'active')
                    ->latest('effective_date')
            )
            ->heading('Daftar Kebijakan Kepatuhan, Regulasi OJK & SOP Bank Terkini')
            ->description('Katalog pedoman operasional dan regulasi kepatuhan yang aktif berlaku di PT BPR Banda Raya.')
            ->columns([
                TextColumn::make('document_number')
                    ->label('Nomor SK / Regulasi')
                    ->searchable()
                    ->weight('bold')
                    ->color('primary'),

                TextColumn::make('title')
                    ->label('Judul Kebijakan / SOP')
                    ->searchable()
                    ->wrap(),

                TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->category_label)
                    ->color(fn ($record) => $record->category_color),

                TextColumn::make('effective_date')
                    ->label('Berlaku Sejak')
                    ->date('d M Y'),

                TextColumn::make('target_audience')
                    ->label('Audiens')
                    ->badge()
                    ->formatStateUsing(fn ($record) => $record->target_audience_label)
                    ->color('info'),
            ])
            ->recordActions([
                Action::make('viewPdf')
                    ->label('Buka Dokumen')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('primary')
                    ->url(fn (CompliancePolicy $record) => $record->attachment_url)
                    ->openUrlInNewTab(),

                Action::make('downloadPdf')
                    ->label('Unduh PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn (CompliancePolicy $record) => $record->attachment_url)
                    ->openUrlInNewTab(),
            ]);
    }
}
