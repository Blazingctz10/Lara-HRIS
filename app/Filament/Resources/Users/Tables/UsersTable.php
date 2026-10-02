<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->copyable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Role Sistem')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'direksi' => 'success',
                        'hr_admin' => 'primary',
                        'branch_manager' => 'info',
                        'employee' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'IT Administrator',
                        'direksi' => 'Direksi',
                        'hr_admin' => 'HR Officer',
                        'branch_manager' => 'Pemimpin Cabang',
                        'employee' => 'Karyawan (ESS)',
                        default => $state,
                    })
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label('Kantor Cabang')
                    ->badge()
                    ->color('info')
                    ->placeholder('Kantor Pusat / Semua')
                    ->searchable(),

                TextColumn::make('employee.nip')
                    ->label('Profil Pegawai')
                    ->badge()
                    ->color('primary')
                    ->placeholder('Belum Terhubung')
                    ->formatStateUsing(fn ($record) => $record->employee ? "NIP: {$record->employee->nip}" : 'Belum Terhubung'),

                TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role Sistem')
                    ->options([
                        'super_admin' => 'IT Administrator',
                        'direksi' => 'Direksi',
                        'hr_admin' => 'HR Officer',
                        'branch_manager' => 'Pemimpin Cabang',
                        'employee' => 'Karyawan (ESS)',
                    ]),

                SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Kantor Cabang'),
            ])
            ->recordActions([
                Action::make('reset_password')
                    ->label('Ubah Sandi')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->modalHeading(fn (User $record) => "Ubah Kata Sandi - {$record->name}")
                    ->modalDescription(fn (User $record) => "Atur kata sandi baru untuk akun {$record->email}. Default sandi sistem adalah 'password'.")
                    ->modalSubmitActionLabel('Simpan Kata Sandi')
                    ->modalCancelActionLabel('Batal')
                    ->form([
                        TextInput::make('new_password')
                            ->label('Kata Sandi Baru')
                            ->password()
                            ->revealable()
                            ->default('password')
                            ->required()
                            ->helperText('Default: password. Masukkan kata sandi baru untuk akun pengguna ini.'),
                    ])
                    ->action(function (User $record, array $data) {
                        $record->update([
                            'password' => Hash::make($data['new_password']),
                        ]);

                        Notification::make()
                            ->title('Kata sandi berhasil diperbarui!')
                            ->body("Akun {$record->email} kini dapat login dengan kata sandi baru.")
                            ->success()
                            ->send();
                    }),

                EditAction::make(),
                DeleteAction::make()
                    ->hidden(fn (User $record) => $record->id === auth()->id()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
