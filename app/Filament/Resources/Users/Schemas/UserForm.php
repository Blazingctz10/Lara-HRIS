<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Akun Pengguna BPR')
                    ->description('Kelola kredensial login, role hak akses sistem, dan penempatan cabang pegawai.')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Select::make('employee_id')
                            ->label('Hubungkan dengan Data Pegawai Terdaftar (Opsional)')
                            ->placeholder('Pilih nama pegawai untuk mengisi form secara otomatis...')
                            ->options(function (?User $record) {
                                return Employee::query()
                                    ->when($record?->id, function ($q) use ($record) {
                                        $q->whereNull('user_id')->orWhere('user_id', $record->id);
                                    }, function ($q) {
                                        $q->whereNull('user_id');
                                    })
                                    ->orderBy('full_name')
                                    ->get()
                                    ->mapWithKeys(fn (Employee $e) => [$e->id => "{$e->full_name} (NIP: {$e->nip}) - {$e->email}"]);
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state && ($employee = Employee::find($state))) {
                                    $set('name', $employee->full_name);
                                    $set('email', $employee->email);
                                    if ($employee->branch_id) {
                                        $set('branch_id', $employee->branch_id);
                                    }
                                }
                            })
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->helperText('Pilih pegawai dari basis data untuk mengisi Nama, Email, dan Kantor Penempatan secara otomatis.'),

                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Pengguna / Pegawai')
                                ->placeholder('Contoh: Ratna Dewi, S.Psi / Budi Santoso')
                                ->required()
                                ->maxLength(255),

                            TextInput::make('email')
                                ->label('Alamat Email Resmi Dinas')
                                ->placeholder('nama@bprbandaraya.com')
                                ->email()
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->maxLength(255),

                            Select::make('role')
                                ->label('Role & Tingkat Otoritas Sistem')
                                ->options([
                                    'super_admin' => 'IT Administrator (Akses Penuh Seluruh Sistem & Backup)',
                                    'direksi' => 'Direksi (Persetujuan Final Cuti & Pengawasan Eksekutif)',
                                    'hr_admin' => 'Human Capital / HR Officer (SDM, Rekrutmen & Verifikasi Cuti)',
                                    'employee' => 'Karyawan / Staf (ESS Portal Presensi, Cuti & Klaim)',
                                ])
                                ->default('employee')
                                ->required(),

                            Select::make('branch_id')
                                ->label('Kantor Penempatan')
                                ->relationship('branch', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Pilih Kantor Penempatan')
                                ->helperText('Kantor penempatan resmi pegawai di lingkungan BPR Banda Raya'),

                            TextInput::make('password')
                                ->label('Kata Sandi Masuk')
                                ->password()
                                ->revealable()
                                ->minLength(6)
                                ->maxLength(255)
                                ->placeholder('Default: password')
                                ->dehydrateStateUsing(fn ($state) => ! empty($state) ? Hash::make($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->helperText(fn (string $operation): string => $operation === 'create'
                                    ? 'Kata sandi awal untuk akun baru (Minimal 6 karakter, Default: password)'
                                    : 'Biarkan kosong jika tidak ingin mengubah kata sandi yang sudah ada.'
                                ),
                        ]),
                    ]),
            ]);
    }
}
