<?php

namespace App\Filament\Resources\CareerMovements\Tables;

use App\Models\CareerMovement;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class CareerMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with([
                'employee.position',
                'employee.branch',
                'fromBranch',
                'toBranch',
                'toPosition',
            ]))
            ->defaultSort('effective_date', 'desc')
            ->groups([
                Group::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->getDescriptionFromRecordUsing(fn ($record) => ($record->employee?->position?->name ?? 'Pegawai') . ' • ' . ($record->employee?->branch?->name ?? 'BPR Banda Raya')),
                Group::make('type')
                    ->label('Tipe SK / Mutasi')
                    ->collapsible(),
                Group::make('toBranch.name')
                    ->label('Cabang Penempatan Baru')
                    ->collapsible(),
            ])
            ->defaultGroup('employee.full_name')
            ->collapsedGroupsByDefault()
            ->groupingSettingsHidden(false)
            ->filters([
                SelectFilter::make('employee_id')
                    ->label('Pilih Nama Pegawai')
                    ->relationship('employee', 'full_name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('type')
                    ->label('Tipe Mutasi / SK')
                    ->options([
                        'Promosi Jabatan' => 'Promosi Jabatan',
                        'Mutasi Cabang' => 'Mutasi Cabang',
                        'Rotasi Divisi' => 'Rotasi Divisi',
                        'Pengangkatan Tetap' => 'Pengangkatan Tetap',
                    ]),
            ])
            ->columns([
                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(isToggledHiddenByDefault: false),

                TextColumn::make('type')
                    ->label('Tipe SK')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Mutasi Cabang' => 'primary',
                        'Promosi Jabatan' => 'success',
                        'Pengangkatan Tetap' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('sk_number')
                    ->label('Nomor SK')
                    ->fontFamily('mono')
                    ->searchable(),

                TextColumn::make('fromBranch.name')
                    ->label('Dari Cabang')
                    ->placeholder('-'),

                TextColumn::make('toBranch.name')
                    ->label('Ke Cabang')
                    ->badge()
                    ->color('primary')
                    ->placeholder('-'),

                TextColumn::make('toPosition.name')
                    ->label('Jabatan Baru')
                    ->placeholder('-'),

                TextColumn::make('effective_date')
                    ->label('Tgl Efektif')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->outlined()
                    ->label('Lihat SK'),
                EditAction::make()
                    ->button()
                    ->outlined()
                    ->label('Ubah')
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ]);
    }
}
