<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Branch;
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
                                ->placeholder('Default: password')
                                ->dehydrateStateUsing(fn ($state) => ! empty($state) ? Hash::make($state) : null)
                                ->dehydrated(fn ($state) => filled($state))
                                ->required(fn (string $operation): bool => $operation === 'create')
                                ->helperText(fn (string $operation): string => $operation === 'create'
                                    ? 'Kata sandi awal untuk akun baru (Default: password)'
                                    : 'Biarkan kosong jika tidak ingin mengubah kata sandi yang sudah ada.'
                                ),
                        ]),
                    ]),
            ]);
    }
}
