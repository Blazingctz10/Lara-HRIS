<?php

namespace App\Filament\Resources\JobApplicants\Tables;

use App\Models\JobApplicant;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobApplicantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pelamar')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (JobApplicant $record): string => $record->email),

                TextColumn::make('position_applied')
                    ->label('Posisi Dilamar')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status Seleksi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Baru' => 'info',
                        'Screening' => 'warning',
                        'Interview' => 'primary',
                        'Diterima' => 'success',
                        'Ditolak' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('phone')
                    ->label('WhatsApp / Telepon')
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('cv_filename')
                    ->label('Lampiran CV')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'gray')
                    ->formatStateUsing(fn ($state) => $state ? '📄 Ada CV' : 'Tidak Ada')
                    ->tooltip(fn (JobApplicant $record): ?string => $record->cv_filename),

                TextColumn::make('email_date')
                    ->label('Waktu Masuk')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('email_date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Filter Status')
                    ->options([
                        'Baru' => 'Baru Masuk',
                        'Screening' => 'Screening',
                        'Interview' => 'Wawancara',
                        'Diterima' => 'Diterima',
                        'Ditolak' => 'Tidak Lolos',
                    ]),

                SelectFilter::make('position_applied')
                    ->label('Filter Posisi')
                    ->options([
                        'Customer Service (CS)' => 'Customer Service (CS)',
                        'Teller Kas & Kliring' => 'Teller Kas & Kliring',
                        'Account Officer (AO) Komersial' => 'Account Officer (AO) Komersial',
                        'Account Officer (AO) Dana / Funding' => 'Account Officer (AO) Dana / Funding',
                        'IT Support & Developer' => 'IT Support & Developer',
                        'Legal & Compliance' => 'Legal & Compliance',
                        'Staff HR & GA' => 'Staff HR & GA',
                    ]),
            ])
            ->actions([
                Action::make('download_cv')
                    ->label('Unduh CV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn (JobApplicant $record): bool => ! empty($record->cv_path))
                    ->url(fn (JobApplicant $record): string => asset('storage/' . $record->cv_path))
                    ->openUrlInNewTab(),

                Action::make('update_status')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->modalHeading('Perbarui Status Seleksi Pelamar')
                    ->modalDescription('Pastikan keputusan seleksi telah sesuai dengan standar kualifikasi BPR Banda Raya.')
                    ->modalSubmitActionLabel('Konfirmasi & Simpan')
                    ->form([
                        Select::make('status')
                            ->label('Status Baru')
                            ->options([
                                'Baru' => 'Baru Masuk',
                                'Screening' => 'Screening Administrasi',
                                'Interview' => 'Tahap Wawancara',
                                'Diterima' => 'Diterima Kerja',
                                'Ditolak' => 'Tidak Lolos / Ditolak',
                            ])
                            ->required(),

                        Textarea::make('notes')
                            ->label('Catatan Keputusan HR')
                            ->placeholder('Alasan atau catatan tindak lanjut...')
                            ->rows(3),
                    ])
                    ->fillForm(fn (JobApplicant $record): array => [
                        'status' => $record->status,
                        'notes' => $record->notes,
                    ])
                    ->action(function (JobApplicant $record, array $data): void {
                        $record->update([
                            'status' => $data['status'],
                            'notes' => $data['notes'] ?? $record->notes,
                        ]);

                        Notification::make()
                            ->title('Status Berhasil Diperbarui')
                            ->body("Status lamaran {$record->name} berhasil diubah menjadi {$data['status']}")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),

                DeleteAction::make()
                    ->modalHeading('Hapus Data Pelamar?')
                    ->modalDescription('Apakah Anda yakin ingin menghapus data pelamar ini beserta berkas CV-nya? Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Hapus Data')
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->modalHeading('Hapus Pelamar Terpilih?')
                        ->modalDescription('Semua data pelamar yang dipilih akan dihapus permanen dari sistem.')
                        ->requiresConfirmation(),
                ]),
            ]);
    }
}
