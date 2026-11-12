<?php

namespace App\Filament\Resources\Attendances\Tables;

use App\Models\Attendance;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->with(['employee.branch', 'employee.position', 'employee.department'])
                ->orderByRaw("CASE WHEN (SELECT status FROM employees WHERE employees.id = attendances.employee_id) = 'Active' THEN 0 ELSE 1 END")
            )
            ->defaultSort('date', 'desc')
            ->groups([
                Group::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false)
                    ->getDescriptionFromRecordUsing(function ($record) {
                        $nip = $record->employee?->nip ?? '-';
                        $pos = $record->employee?->position?->name ?? 'Pegawai';
                        $branch = $record->employee?->branch?->name ?? 'BPR Banda Raya';
                        $statusBadge = $record->employee?->status === 'Resigned' ? ' • [Alumni / Resign]' : '';
                        return "NIP: {$nip} • {$pos} • {$branch}{$statusBadge}";
                    }),
                Group::make('date')
                    ->label('Tanggal Presensi')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn ($record) => $record->date ? \Carbon\Carbon::parse($record->date)->translatedFormat('l, d F Y') : '-'),
                Group::make('employee.branch.name')
                    ->label('Kantor Cabang')
                    ->collapsible(),
            ])
            ->defaultGroup('employee.full_name')
            ->collapsedGroupsByDefault()
            ->groupingSettingsHidden(true)
            ->columns([
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn ($record) => $record->date ? \Carbon\Carbon::parse($record->date)->translatedFormat('l') : null)
                    ->sortable(),

                TextColumn::make('employee.full_name')
                    ->label('Nama Pegawai')
                    ->description(fn ($record) => 'NIP: ' . ($record->employee?->nip ?? '-'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('employee.branch.name')
                    ->label('Cabang')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('clock_in')
                    ->label('Masuk')
                    ->time('H:i')
                    ->color('success')
                    ->weight('bold')
                    ->alignCenter(),

                TextColumn::make('clock_out')
                    ->label('Pulang')
                    ->time('H:i')
                    ->color('primary')
                    ->placeholder('--:--')
                    ->alignCenter(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if ($state === 'Present' && str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'Hadir (Dispensasi)';
                        }
                        return match ($state) {
                            'Present' => 'Hadir',
                            'Late' => 'Terlambat',
                            'Excused' => str_contains($record->notes ?? '', 'Cuti') ? 'Cuti Resmi' : 'Izin / Sakit',
                            'Absent' => 'Alpa',
                            default => $state,
                        };
                    })
                    ->color(fn (string $state, $record): string => match ($state) {
                        'Present' => str_contains($record->notes ?? '', 'Dispensasi Telat') ? 'info' : 'success',
                        'Late' => 'warning',
                        'Excused' => 'info',
                        'Absent' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('late_and_penalty')
                    ->label('Keterlambatan & Denda')
                    ->state(function ($record) {
                        if ($record->late_minutes > 0) {
                            $penalty = $record->penalty_amount > 0
                                ? 'Rp ' . number_format($record->penalty_amount, 0, ',', '.')
                                : 'Bebas Denda';
                            return "{$record->late_minutes} mnt • {$penalty}";
                        }
                        if ($record->status === 'Late') {
                            return 'Terlambat (Rp 0)';
                        }
                        return 'Tepat Waktu';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->penalty_amount > 0) {
                            return 'danger';
                        }
                        if ($record->late_minutes > 0) {
                            return 'warning';
                        }
                        return 'gray';
                    })
                    ->alignCenter(),

                TextColumn::make('source_device')
                    ->label('Metode & Catatan')
                    ->state(function ($record) {
                        if (str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'Izin Telat WA';
                        }
                        if (str_contains($record->notes ?? '', 'Cuti Resmi') || str_contains($record->notes ?? '', 'Cuti')) {
                            return 'Cuti Resmi ESS';
                        }
                        return $record->status === 'Excused' ? 'Logbook HR/GA' : 'Mesin Fingerprint';
                    })
                    ->description(fn ($record) => $record->notes ? \Illuminate\Support\Str::limit($record->notes, 35) : null)
                    ->badge()
                    ->color(function ($record) {
                        if (str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'warning';
                        }
                        return $record->status === 'Excused' ? 'info' : 'success';
                    })
                    ->icon(function ($record) {
                        if (str_contains($record->notes ?? '', 'Dispensasi Telat')) {
                            return 'heroicon-m-clock';
                        }
                        if (str_contains($record->notes ?? '', 'Cuti')) {
                            return 'heroicon-m-document-check';
                        }
                        return $record->status === 'Excused' ? 'heroicon-m-clipboard-document-list' : 'heroicon-m-finger-print';
                    })
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Kehadiran')
                    ->options([
                        'Present' => 'Hadir',
                        'Late' => 'Terlambat',
                        'Excused' => 'Izin / Cuti / Sakit',
                        'Absent' => 'Alpa',
                    ]),
                SelectFilter::make('branch')
                    ->relationship('employee.branch', 'name')
                    ->label('Unit Kantor Cabang'),
                Filter::make('today')
                    ->label('Hari Ini Saja')
                    ->query(fn (Builder $query): Builder => $query->whereDate('date', now())),
            ])
            ->recordActions([
                EditAction::make()
                    ->button()
                    ->outlined()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ]);
    }
}
