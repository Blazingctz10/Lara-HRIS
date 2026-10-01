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
            '/admin/payrolls',
            '/admin/announcements',
            '/admin/fingerprint-devices',
            '/admin/overtimes',
            '/admin/reimbursements',
            '/admin/career-movements',
            '/admin/warning-letters',
            '/admin/backup-data',
            '/admin/job-applicants',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $this->assertEquals(200, $response->getStatusCode(), "Route {$route} failed with status {$response->getStatusCode()}");
        }
    }

    public function test_all_portal_resources_render_successfully(): void
    {
        $employee = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->actingAs($employee);

        $routes = [
            '/portal',
            '/portal/attendances',
            '/portal/leave-requests',
            '/portal/payrolls',
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
