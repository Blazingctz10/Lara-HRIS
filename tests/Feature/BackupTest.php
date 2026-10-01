<?php

namespace Tests\Feature;

use App\Filament\Pages\BackupData;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Tests\TestCase;

class BackupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_super_admin_can_access_backup_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        $response = $this->actingAs($admin)->get('/admin/backup-data');
        $response->assertStatus(200);
    }

    public function test_regular_employee_cannot_access_backup_page(): void
    {
        $employee = User::where('role', 'employee')->first();

        $response = $this->actingAs($employee)->get('/admin/backup-data');
        $response->assertStatus(403);
    }

    public function test_admin_can_generate_database_backup(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        Livewire::actingAs($admin)
            ->test(BackupData::class)
            ->call('createDatabaseBackup')
            ->assertHasNoErrors();

        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);

        $this->assertNotEmpty($files, 'Backup file should have been generated');

        // Cleanup generated test files
        foreach ($files as $file) {
            File::delete($file->getPathname());
        }
    }

    public function test_admin_can_generate_full_backup_zip(): void
    {
        $admin = User::where('role', 'super_admin')->first();

        Livewire::actingAs($admin)
            ->test(BackupData::class)
            ->call('createFullBackup')
            ->assertHasNoErrors();

        $backupDir = storage_path('app/backups');
        $files = File::files($backupDir);

        $this->assertNotEmpty($files, 'Full backup ZIP file should have been generated');

        $zipFound = false;
        foreach ($files as $file) {
            if (str_ends_with($file->getFilename(), '.zip')) {
                $zipFound = true;
            }
            File::delete($file->getPathname());
        }

        $this->assertTrue($zipFound, 'A .zip backup file should be generated');
    }
}
