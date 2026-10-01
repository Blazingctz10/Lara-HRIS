<?php

namespace App\Filament\Resources\JobApplicants\Pages;

use App\Filament\Resources\JobApplicants\JobApplicantResource;
use App\Services\ImapRecruitmentService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListJobApplicants extends ListRecords
{
    protected static string $resource = JobApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_imap')
                ->label('Tarik Email (cPanel IMAP)')
                ->icon('heroicon-o-arrow-path')
                ->color('primary')
                ->modalHeading('Tarik Email Pelamar via cPanel IMAP')
                ->modalDescription('Sistem akan terhubung ke mail server cPanel BPR Banda Raya (Port 993 SSL / 143 TLS) dan mengunduh email pelamar baru beserta lampiran CV.')
                ->modalSubmitActionLabel('Mulai Tarik Email')
                ->form([
                    TextInput::make('limit')
                        ->label('Jumlah Email Maksimal yang Diperiksa')
                        ->numeric()
                        ->default(20)
                        ->required(),
                ])
                ->action(function (array $data, ImapRecruitmentService $imapService): void {
                    $limit = (int) ($data['limit'] ?? 20);
                    $result = $imapService->fetchApplicantsFromImap($limit);

                    if ($result['status'] === 'success') {
                        Notification::make()
                            ->title('Sinkronisasi IMAP Berhasil')
                            ->body($result['message'])
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Informasi Koneksi IMAP')
                            ->body($result['message'])
                            ->warning()
                            ->persistent()
                            ->send();
                    }
                }),

            Action::make('generate_demo')
                ->label('Muat Data Simulasi')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Muat Data Contoh Pelamar?')
                ->modalDescription('Sistem akan membuat 5 data pelamar simulasi lengkap dengan berkas CV format PDF untuk uji coba fitur rekrutmen.')
                ->modalSubmitActionLabel('Ya, Buat Data Simulasi')
                ->action(function (ImapRecruitmentService $imapService): void {
                    $createdCount = $imapService->generateSampleApplicants();

                    Notification::make()
                        ->title('Data Simulasi Berhasil Dibuat')
                        ->body("Berhasil menambahkan {$createdCount} data pelamar simulasi beserta berkas CV.")
                        ->success()
                        ->send();
                }),

            CreateAction::make()
                ->label('Tambah Pelamar Manual'),
        ];
    }
}
