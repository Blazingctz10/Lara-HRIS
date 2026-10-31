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
                    ->label('Unit Kantor')
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

                TextColumn::make('user_id')
                    ->label('Akun Portal')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Belum Ada')
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('branch_id')
                    ->relationship('branch', 'name')
                    ->label('Unit Kantor'),
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
