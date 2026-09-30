<?php

namespace App\Filament\Portal\Pages;

use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?string $title = 'Profil Pegawai & Biodata Mandiri';

    protected string $view = 'filament.portal.pages.my-profile';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();
        $employee = $user?->employee;

        $this->form->fill([
            'name' => $user?->name,
            'email' => $user?->email,

            // Readonly HR Data (Tervalidasi & Terkunci untuk Audit)
            'nip' => $employee?->nip ?? '-',
            'nik' => $employee?->nik ?? '-',
            'branch' => $employee?->branch?->name ?? 'Kantor Pusat',
            'department' => $employee?->department?->name ?? '-',
            'position' => $employee?->position?->name ?? '-',
            'employment_status' => $employee?->employment_status === 'Permanent' ? 'Pegawai Tetap' : 'Kontrak PKWT / Probation',
            'join_date' => $employee?->join_date ? $employee->join_date->format('d M Y') : '-',
            'contract_end_date' => $employee?->contract_end_date ? $employee->contract_end_date->format('d M Y') : ($employee?->probation_end_date ? $employee->probation_end_date->format('d M Y') : 'Tidak Ada (Permanen)'),

            // Data Pribadi & Kontak (Dapat Diperbarui Mandiri)
            'phone' => $employee?->phone ?? '',
            'address' => $employee?->address ?? '',
            'bank_account_number' => $employee?->bank_account_number ?? '',
            'bank_account_holder' => $employee?->bank_account_holder ?? '',
            'npwp' => $employee?->npwp ?? '',
        ]);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Kepegawaian Resmi BPR Banda Raya')
                    ->description('Data struktur kepegawaian resmi yang telah diverifikasi oleh Divisi Human Resources (Terkunci untuk akurasi data kepatuhan audit bank).')
                    ->icon('heroicon-m-shield-check')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('nip')
                                ->label('Nomor Induk Pegawai (NIP)')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('nik')
                                ->label('Nomor NIK KTP')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('branch')
                                ->label('Kantor Penempatan')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                        Grid::make(3)->schema([
                            TextInput::make('department')
                                ->label('Departemen / Divisi')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('position')
                                ->label('Jabatan Resmi')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('employment_status')
                                ->label('Status Kepegawaian')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('join_date')
                                ->label('Tanggal Mulai Bekerja')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('contract_end_date')
                                ->label('Masa Jatuh Tempo Kontrak / Evaluasi')
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                    ])
                    ->collapsible(),

                Section::make('Biodata Kontak Pribadi & Rekening Gaji (Payroll)')
                    ->description('Pastikan nomor telepon/WhatsApp, alamat tempat tinggal, dan rekening penggajian Anda selalu akurat dan valid.')
                    ->icon('heroicon-m-identification')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('phone')
                                ->label('Nomor HP / WhatsApp Aktif')
                                ->tel()
                                ->required()
                                ->maxLength(20)
                                ->placeholder('Contoh: 081234567890'),
                            TextInput::make('npwp')
                                ->label('Nomor Pokok Wajib Pajak (NPWP)')
                                ->maxLength(30)
                                ->placeholder('Contoh: 72.333.444.5-021.000'),
                        ]),
                        Grid::make(2)->schema([
                            TextInput::make('bank_account_number')
                                ->label('Nomor Rekening Payroll Bank')
                                ->numeric()
                                ->maxLength(30)
                                ->placeholder('Contoh: 1200056789012'),
                            TextInput::make('bank_account_holder')
                                ->label('Nama Pemilik Rekening')
                                ->maxLength(100)
                                ->placeholder('Sesuai buku rekening bank'),
                        ]),
                        Textarea::make('address')
                            ->label('Alamat Domisili Tempat Tinggal')
                            ->rows(3)
                            ->maxLength(255)
                            ->placeholder('Alamat domisili tempat tinggal saat ini di Batam / Kepri'),
                    ]),

                Section::make('Keamanan Akun & Sandi Masuk')
                    ->description('Kosongkan bagian ini jika Anda tidak bermaksud mengganti kata sandi login Anda.')
                    ->icon('heroicon-m-key')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('new_password')
                                ->label('Kata Sandi Baru')
                                ->password()
                                ->revealable()
                                ->rule(Password::default()),
                            TextInput::make('new_password_confirmation')
                                ->label('Konfirmasi Kata Sandi Baru')
                                ->password()
                                ->revealable()
                                ->same('new_password')
                                ->requiredWith('new_password'),
                        ]),
                    ])
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan Profil')
                ->submit('save')
                ->color('primary')
                ->icon('heroicon-m-check'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = auth()->user();
        $employee = $user?->employee;

        if ($employee) {
            $employee->update([
                'phone' => $data['phone'] ?? $employee->phone,
                'address' => $data['address'] ?? $employee->address,
                'bank_account_number' => $data['bank_account_number'] ?? $employee->bank_account_number,
                'bank_account_holder' => $data['bank_account_holder'] ?? $employee->bank_account_holder,
                'npwp' => $data['npwp'] ?? $employee->npwp,
            ]);
        }

        if (! empty($data['new_password'])) {
            $user?->update([
                'password' => Hash::make($data['new_password']),
            ]);
        }

        Notification::make()
            ->title('Profil Berhasil Diperbarui')
            ->body('Data kontak dan informasi akun Anda telah berhasil disimpan dengan akurat.')
            ->success()
            ->send();
    }
}
