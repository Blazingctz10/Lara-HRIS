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
                    ->tooltip(fn (JobApplicant $record): ?string => $record->cv_filename)
                    ->action('view_details'),

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
            ->recordAction('view_details')
            ->actions([
                Action::make('view_details')
                    ->label('Lihat CV')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->button()
                    ->outlined()
                    ->modalWidth('5xl')
                    ->modalHeading(fn (JobApplicant $record) => "Berkas CV Pelamar: {$record->name}")
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->modalContent(fn (JobApplicant $record) => view('filament.modals.job-applicant-detail', ['record' => $record])),

                Action::make('hire_as_employee')
                    ->label('Terima Jadi Pegawai')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn (JobApplicant $record): bool => $record->status !== 'Diterima' && in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                    ->modalHeading('Penerimaan & Pengangkatan Pegawai Baru')
                    ->modalDescription('Pelamar ini akan otomatis didaftarkan ke direktori Pegawai Aktif, dibuatkan NIP resmi, akun login portal (@bprbandaraya.com), serta jatah cuti tahunan awal (12 hari).')
                    ->modalSubmitActionLabel('Konfirmasi & Angkat Pegawai')
                    ->form([
                        \Filament\Forms\Components\Select::make('branch_id')
                            ->label('Kantor Cabang Penempatan')
                            ->options(\App\Models\Branch::pluck('name', 'id'))
                            ->default(fn () => \App\Models\Branch::first()?->id)
                            ->required(),

                        \Filament\Forms\Components\Select::make('position_id')
                            ->label('Jabatan Resmi')
                            ->options(\App\Models\Position::pluck('name', 'id'))
                            ->searchable()
                            ->required(),

                        \Filament\Forms\Components\DatePicker::make('join_date')
                            ->label('Tanggal Mulai Bekerja (Join Date)')
                            ->default(now())
                            ->required(),

                        \Filament\Forms\Components\Select::make('employment_status')
                            ->label('Status Ikatan Kerja')
                            ->options([
                                'Probation' => 'Masa Percobaan (Probation 3 Bulan)',
                                'Contract' => 'Perjanjian Kerja Waktu Tertentu (PKWT / Kontrak)',
                                'Permanent' => 'Pegawai Tetap',
                            ])
                            ->default('Probation')
                            ->required(),
                    ])
                    ->action(function (JobApplicant $record, array $data): void {
                        $employee = $record->promoteToEmployee($data);

                        Notification::make()
                            ->title('Pelamar Resmi Diangkat Menjadi Pegawai!')
                            ->body("Selamat! {$record->name} otomatis terdaftar sebagai Pegawai Aktif dengan NIP {$employee->nip}. Akun portal ({$employee->email}) telah aktif.")
                            ->success()
                            ->persistent()
                            ->send();
                    }),

                Action::make('update_status')
                    ->label('Ubah Status')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->button()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
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
                                'Diterima' => 'Diterima Kerja (Otomatis Masuk Direktori Pegawai)',
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
                        if ($data['status'] === 'Diterima') {
                            $employee = $record->promoteToEmployee(['notes' => $data['notes']]);
                            Notification::make()
                                ->title('Status Diperbarui: Pelamar Diterima')
                                ->body("{$record->name} otomatis masuk direktori Pegawai dengan NIP: {$employee->nip}")
                                ->success()
                                ->send();
                        } else {
                            $record->update([
                                'status' => $data['status'],
                                'notes' => $data['notes'] ?? $record->notes,
                            ]);

                            Notification::make()
                                ->title('Status Berhasil Diperbarui')
                                ->body("Status lamaran {$record->name} berhasil diubah menjadi {$data['status']}")
                                ->success()
                                ->send();
                        }
                    }),

                EditAction::make()
                    ->button()
                    ->outlined()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),

                DeleteAction::make()
                    ->button()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
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
                ])->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])),
            ]);
    }
}
