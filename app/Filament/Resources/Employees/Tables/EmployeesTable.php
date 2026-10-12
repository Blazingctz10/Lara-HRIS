<?php

namespace App\Filament\Resources\Employees\Tables;

use App\Filament\Resources\Employees\EmployeeResource;
use App\Models\Employee;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with(['branch', 'department', 'position', 'user']))
            ->recordUrl(fn (Employee $record): string => auth()->user()?->role === 'direksi'
                ? EmployeeResource::getUrl('view', ['record' => $record])
                : (in_array(auth()->user()?->role, ['super_admin', 'hr_admin'])
                    ? EmployeeResource::getUrl('edit', ['record' => $record])
                    : EmployeeResource::getUrl('view', ['record' => $record]))
            )
            ->columns([
                ImageColumn::make('photo_url')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name) . '&color=4338ca&background=e0e7ff'),

                TextColumn::make('nip')
                    ->label('NIP')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('branch.name')
                    ->label('Kantor Cabang')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('department.name')
                    ->label('Divisi')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position.name')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('grade')
                    ->label('Grade')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Permanent' => 'success',
                        'Contract' => 'warning',
                        'Probation' => 'danger',
                        'Internship' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('working_period')
                    ->label('Masa Kerja'),

                TextColumn::make('ptkp_status')
                    ->label('PTKP')
                    ->badge()
                    ->color('info')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('status')
                    ->label('Kondisi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'On Leave' => 'warning',
                        'Resigned', 'Terminated' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Kantor Cabang'),
                SelectFilter::make('department_id')
                    ->relationship('department', 'name')
                    ->label('Divisi'),
                SelectFilter::make('employment_status')
                    ->label('Status Ketenagakerjaan')
                    ->options([
                        'Permanent' => 'Permanent',
                        'Contract' => 'Contract',
                        'Probation' => 'Probation',
                        'Internship' => 'Internship',
                    ]),
                SelectFilter::make('status')
                    ->label('Status Aktif')
                    ->options([
                        'Active' => 'Active',
                        'On Leave' => 'On Leave',
                        'Resigned' => 'Resigned',
                        'Terminated' => 'Terminated',
                    ]),
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->outlined(),

                Action::make('reset_password')
                    ->label('Ubah Sandi')
                    ->icon('heroicon-o-key')
                    ->color('warning')
                    ->button()
                    ->visible(fn () => in_array(auth()->user()?->role, ['super_admin', 'hr_admin']))
                    ->modalHeading(fn (Employee $record) => "Ubah Kata Sandi Akun - {$record->full_name}")
                    ->modalDescription(fn (Employee $record) => "Perbarui kata sandi akun masuk portal untuk pegawai {$record->full_name} ({$record->email}). Kata sandi default sistem adalah 'password'.")
                    ->modalSubmitActionLabel('Simpan Kata Sandi')
                    ->modalCancelActionLabel('Batal')
                    ->form([
                        TextInput::make('new_password')
                            ->label('Kata Sandi Baru')
                            ->password()
                            ->revealable()
                            ->default('password')
                            ->required()
                            ->helperText('Default: password. Masukkan kata sandi baru untuk akun pegawai ini.'),
                    ])
                    ->action(function (Employee $record, array $data) {
                        $newPassword = $data['new_password'];

                        if ($record->user) {
                            $record->user->update([
                                'password' => Hash::make($newPassword),
                            ]);
                        } else {
                            $user = User::firstOrCreate(
                                ['email' => $record->email],
                                [
                                    'name' => $record->full_name,
                                    'password' => Hash::make($newPassword),
                                    'role' => 'employee',
                                    'branch_id' => $record->branch_id,
                                ]
                            );
                            $record->update(['user_id' => $user->id]);
                        }

                        Notification::make()
                            ->title('Kata sandi pegawai berhasil diperbarui!')
                            ->body("Akun {$record->email} kini dapat masuk dengan kata sandi yang disetel.")
                            ->success()
                            ->send();
                    }),

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
