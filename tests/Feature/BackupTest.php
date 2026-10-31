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
        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        $existing = collect(File::files($backupDir))->map(fn ($f) => $f->getPathname())->all();

        Livewire::actingAs($admin)
            ->test(BackupData::class)
            ->call('createDatabaseBackup')
            ->assertHasNoErrors();

        $after = collect(File::files($backupDir))->map(fn ($f) => $f->getPathname());
        $newFiles = $after->diff($existing);

        $this->assertNotEmpty($newFiles, 'Backup file should have been generated');

        // Only cleanup test files generated in this test
        foreach ($newFiles as $filePath) {
            File::delete($filePath);
        }
    }

    public function test_admin_can_generate_full_backup_zip(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }
        $existing = collect(File::files($backupDir))->map(fn ($f) => $f->getPathname())->all();

        Livewire::actingAs($admin)
            ->test(BackupData::class)
            ->call('createFullBackup')
            ->assertHasNoErrors();

        $after = collect(File::files($backupDir))->map(fn ($f) => $f->getPathname());
        $newFiles = $after->diff($existing);

        $this->assertNotEmpty($newFiles, 'Full backup ZIP file should have been generated');

        foreach ($newFiles as $filePath) {
            File::delete($filePath);
        }
    }

    public function test_backup_history_is_displayed_on_page(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $backupDir = storage_path('app/backups');
        if (! File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $dummyFilename = 'backup_hris_db_test_history.sqlite';
        $dummyPath = $backupDir . DIRECTORY_SEPARATOR . $dummyFilename;
        File::put($dummyPath, 'DUMMY_DATABASE_CONTENT');

        $response = $this->actingAs($admin)->get('/admin/backup-data');
        $response->assertStatus(200);
        $response->assertSee($dummyFilename);
        $response->assertSee('Cadangkan Database Saja');
        $response->assertSee('Cadangkan Arsip Lengkap');

        File::delete($dummyPath);
    }
}
