<?php

namespace Tests\Feature;

use App\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Filament\Resources\CareerMovements\Pages\ListCareerMovements;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Overtime;
use App\Models\Position;
use App\Models\User;
use App\Services\HrisDashboardCacheService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentTabsAndOriginTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        URL::forceRootUrl(null);
        parent::tearDown();
    }

    private function createSuperAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Test Super Admin',
            'email' => 'testadmin@bprbandaraya.com',
            'role' => 'super_admin',
        ]);
    }

    public function test_all_list_pages_support_switching_filter_tabs(): void
    {
        $this->actingAs($this->createSuperAdmin());

        foreach ([
            ListUsers::class => ['active', 'resigned', 'all'],
            ListEmployees::class => ['active', 'resigned', 'all'],
            ListAttendances::class => ['active', 'today', 'late', 'resigned', 'all'],
            ListCareerMovements::class => ['active', 'resigned', 'all'],
        ] as $pageClass => $expectedTabs) {
            $component = Livewire::test($pageClass);
            $actualTabs = array_keys($component->instance()->getTabs());

            $this->assertSame($expectedTabs, $actualTabs, "Unexpected tabs on {$pageClass}");

            foreach ($expectedTabs as $tabKey) {
                $component->set('activeTab', $tabKey);
                $this->assertSame($tabKey, $component->get('activeTab'), "Failed switching to tab {$tabKey} on {$pageClass}");
            }
        }
    }

    public function test_livewire_and_filament_asset_urls_match_request_origin(): void
    {
        $this->actingAs($this->createSuperAdmin());

        foreach (['http://127.0.0.1:8000', 'http://localhost:8000', 'http://192.176.48.103:8000'] as $index => $origin) {
            $response = $this->get("{$origin}/admin/users");
            $response->assertOk();

            $html = $response->getContent();
            if ($index === 0) {
                $this->assertStringContainsString("data-update-uri=\"{$origin}/livewire-", $html);
            }
            $this->assertStringContainsString("x-load-src=\"{$origin}/js/filament/schemas/components/tabs.js", $html);
            $this->assertStringContainsString("src=\"{$origin}/js/filament/filament/app.js", $html);
        }
    }

    public function test_dashboard_cache_invalidates_immediately_when_overtime_changes(): void
    {
        $branch = Branch::create([
            'code' => 'HO',
            'name' => 'Kantor Pusat',
            'city' => 'Batam',
            'address' => 'Batam',
            'is_active' => true,
        ]);
        $position = Position::create([
            'name' => 'Staf IT',
            'grade' => 5,
            'base_salary_min' => 5000000,
            'base_salary_max' => 8000000,
        ]);
        $employee = Employee::create([
            'nip' => '25109999',
            'full_name' => 'Test Pegawai Lembur',
            'email' => 'lembur@bprbandaraya.com',
            'branch_id' => $branch->id,
            'position_id' => $position->id,
            'status' => 'Active',
            'join_date' => now()->toDateString(),
        ]);

        $overtime = Overtime::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'start_time' => '17:00',
            'end_time' => '19:00',
            'duration_minutes' => 120,
            'reason' => 'Deploy',
            'status' => 'Pending',
        ]);

        $cacheService = app(HrisDashboardCacheService::class);
        $this->assertSame(1, $cacheService->getMetrics()['pending_overtime']);

        $overtime->update(['status' => 'Approved_HR']);
        $this->assertSame(0, $cacheService->getMetrics()['pending_overtime']);
    }
}
