<?php

namespace Tests\Feature;

use App\Filament\Pages\SystemLogs;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use Tests\TestCase;

class SystemLogsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_only_super_admin_can_access_system_logs_page(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $hrAdmin = User::where('role', 'hr_admin')->first();
        $employee = User::where('role', 'employee')->first();

        $this->actingAs($superAdmin)->get('/admin/system-logs')->assertStatus(200);
        $this->actingAs($hrAdmin)->get('/admin/system-logs')->assertStatus(403);
        $this->actingAs($employee)->get('/portal/system-logs')->assertStatus(404);
    }

    public function test_system_logs_masks_server_path_and_filters_entries_safely(): void
    {
        Log::error('[SYS500] Uji coba pencatatan log kritis untuk test SystemLogs');

        $superAdmin = User::where('role', 'super_admin')->first();
        $this->actingAs($superAdmin);

        Livewire::test(SystemLogs::class)
            ->assertSuccessful()
            ->assertSee('••••••••••••/storage/logs')
            ->assertSee('Redundansi & Retensi', false)
            ->set('levelFilter', 'error')
            ->assertSuccessful();
    }
}
