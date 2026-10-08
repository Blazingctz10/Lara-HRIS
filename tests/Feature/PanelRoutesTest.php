<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelRoutesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_all_admin_resources_render_successfully(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        $routes = [
            '/admin',
            '/admin/branches',
            '/admin/departments',
            '/admin/positions',
            '/admin/employees',
            '/admin/attendances',
            '/admin/leave-types',
            '/admin/leave-requests',
            '/admin/announcements',
            '/admin/fingerprint-devices',
            '/admin/overtimes',
            '/admin/reimbursements',
            '/admin/career-movements',
            '/admin/warning-letters',
            '/admin/backup-data',
            '/admin/job-applicants',
            '/admin/import-data',
            '/admin/users',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertEquals(200, $response->getStatusCode(), "Route {$route} failed with status {$response->getStatusCode()}");
        }
    }

    public function test_non_super_admin_cannot_access_user_management(): void
    {
        $hrAdmin = User::where('role', 'hr_admin')->first();
        if ($hrAdmin) {
            $this->actingAs($hrAdmin);
            $response = $this->get('/admin/users');
            $this->assertEquals(403, $response->getStatusCode(), "HR Admin should not access /admin/users");
        }

        $direksi = User::where('role', 'direksi')->first();
        if ($direksi) {
            $this->actingAs($direksi);
            $response = $this->get('/admin/users');
            $this->assertEquals(403, $response->getStatusCode(), "Direksi should not access /admin/users");
        }

        $pincab = User::where('role', 'branch_manager')->first();
        if ($pincab) {
            $this->actingAs($pincab);
            $response = $this->get('/admin/users');
            $this->assertEquals(403, $response->getStatusCode(), "Pincab should not access /admin/users");
        }
    }

    public function test_direksi_has_read_only_access_and_cannot_create_or_edit_employees_and_master_data(): void
    {
        $direksi = User::where('role', 'direksi')->first();
        if (!$direksi) {
            $direksi = User::factory()->create(['role' => 'direksi']);
        }
        $this->actingAs($direksi);

        // Direksi CAN VIEW directory/lists
        $this->get('/admin/employees')->assertStatus(200);
        $this->get('/admin/positions')->assertStatus(200);
        $this->get('/admin/departments')->assertStatus(200);
        $this->get('/admin/branches')->assertStatus(200);

        // Direksi CANNOT CREATE employees or master data (Must be 403 Forbidden)
        $this->get('/admin/employees/create')->assertStatus(403);
        $this->get('/admin/positions/create')->assertStatus(403);
        $this->get('/admin/departments/create')->assertStatus(403);
        $this->get('/admin/branches/create')->assertStatus(403);
        $this->get('/admin/leave-types/create')->assertStatus(403);
        $this->get('/admin/attendances/create')->assertStatus(403);
        $this->get('/admin/warning-letters/create')->assertStatus(403);
        $this->get('/admin/career-movements/create')->assertStatus(403);
        $this->get('/admin/job-applicants/create')->assertStatus(403);
    }

    public function test_all_portal_resources_render_successfully(): void
    {
        $employee = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->actingAs($employee);

        $routes = [
            '/portal',
            '/portal/attendances',
            '/portal/leave-requests',
            '/portal/reimbursements',
            '/portal/overtimes',
            '/portal/my-profile',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertEquals(200, $response->getStatusCode(), "Portal route {$route} failed with status {$response->getStatusCode()}");
        }
    }
}
