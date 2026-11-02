<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('admin@bprbandaraya.com|127.0.0.1');
        RateLimiter::clear('target@bprbandaraya.com|127.0.0.1');
        RateLimiter::clear('teller.botania@bprbandaraya.com|127.0.0.1');
    }

    public function test_security_headers_are_present_in_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=*, geolocation=*, microphone=()');
        
        // Assert X-Powered-By is not present
        $this->assertFalse($response->headers->has('X-Powered-By'));
    }

    public function test_honeypot_rejects_bot_login_attempts(): void
    {
        // Attacker bot fills honeypot hidden field 'website_hp'
        $response = $this->post('/login', [
            'email' => 'admin@bprbandaraya.com',
            'password' => 'password',
            'website_hp' => 'https://spambot-site.com',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_rate_limiter_locks_out_after_max_failed_attempts(): void
    {
        $email = 'target@bprbandaraya.com';

        // Perform 5 failed login attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'wrongpassword',
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th attempt should be blocked by rate limiter lockout
        $response = $this->post('/login', [
            'email' => $email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertStringContainsString('Terlalu banyak percobaan', session('errors')->first('email'));
    }

    public function test_successful_login_clears_rate_limiter_and_authenticates(): void
    {
        $teller = User::where('email', 'siti.teller@bprbandaraya.com')->first()
            ?? User::where('role', 'employee')->first();
        $this->assertNotNull($teller);

        $response = $this->post('/login', [
            'email' => $teller->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($teller);
        $response->assertRedirect('/portal');
    }

    public function test_super_admin_redirects_to_admin_panel(): void
    {
        $admin = User::where('email', 'admin@bprbandaraya.com')->first();
        $this->assertNotNull($admin);

        $response = $this->post('/login', [
            'email' => 'admin@bprbandaraya.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin');
    }

    public function test_regular_employee_cannot_access_admin_panel(): void
    {
        $employee = User::where('role', 'employee')->first();
        $this->assertNotNull($employee);

        $this->actingAs($employee);

        // Regular employee role should not have access to admin panel
        $this->assertFalse($employee->canAccessPanel(\Filament\Facades\Filament::getPanel('admin')));
        $this->assertTrue($employee->canAccessPanel(\Filament\Facades\Filament::getPanel('portal')));

        $response = $this->get('/portal');
        $response->assertStatus(200);

        // Akses langsung ke panel admin via HTTP GET wajib ditolak 403 Forbidden
        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(403);
    }

    public function test_admin_widgets_deny_access_to_regular_employees(): void
    {
        $employee = User::where('role', 'employee')->first();
        $hrAdmin = User::where('role', 'hr_admin')->first();
        $this->assertNotNull($employee);
        $this->assertNotNull($hrAdmin);

        // 1. Karyawan biasa tidak boleh melihat satupun widget manajerial admin
        $this->actingAs($employee);
        $this->assertFalse(\App\Filament\Widgets\BankStatsOverview::canView());
        $this->assertFalse(\App\Filament\Widgets\AdminShortcutWidget::canView());
        $this->assertFalse(\App\Filament\Widgets\RecentLeaveRequestsWidget::canView());
        $this->assertFalse(\App\Filament\Widgets\EmployeesByBranchChartWidget::canView());
        $this->assertFalse(\App\Filament\Widgets\WeeklyAttendanceChartWidget::canView());
        $this->assertFalse(\App\Filament\Widgets\ContractAlertWidget::canView());
        $this->assertFalse(\App\Filament\Pages\Dashboard::canAccess());
        $this->assertFalse(\App\Filament\Resources\Announcements\AnnouncementResource::canViewAny());

        // 2. HR Admin berwenang melihat widget admin
        $this->actingAs($hrAdmin);
        $this->assertTrue(\App\Filament\Widgets\BankStatsOverview::canView());
        $this->assertTrue(\App\Filament\Widgets\AdminShortcutWidget::canView());
        $this->assertTrue(\App\Filament\Widgets\RecentLeaveRequestsWidget::canView());
        $this->assertTrue(\App\Filament\Widgets\EmployeesByBranchChartWidget::canView());
        $this->assertTrue(\App\Filament\Widgets\WeeklyAttendanceChartWidget::canView());
        $this->assertTrue(\App\Filament\Widgets\ContractAlertWidget::canView());
        $this->assertTrue(\App\Filament\Pages\Dashboard::canAccess());
        $this->assertTrue(\App\Filament\Resources\Announcements\AnnouncementResource::canViewAny());
    }

    public function test_employee_intended_admin_url_is_safely_sanitized(): void
    {
        $employee = User::where('role', 'employee')->first();
        $this->assertNotNull($employee);

        // Simulasikan session intended URL menuju ke /admin
        session(['url.intended' => 'http://localhost/admin']);

        $response = $this->post('/login', [
            'email' => $employee->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($employee);
        // Karyawan wajib diarahkan ke /portal, bukan /admin
        $response->assertRedirect('/portal');
    }

    public function test_login_audit_trail_logs_success_and_failures(): void
    {
        // 1. Success login
        $this->post('/login', [
            'email' => 'admin@bprbandaraya.com',
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('login_logs', [
            'email' => 'admin@bprbandaraya.com',
            'status' => 'Success',
            'failure_reason' => null,
        ]);

        // 2. Failed password
        $this->post('/login', [
            'email' => 'admin@bprbandaraya.com',
            'password' => 'wrong-pass',
        ]);

        $this->assertDatabaseHas('login_logs', [
            'email' => 'admin@bprbandaraya.com',
            'status' => 'Failed',
            'failure_reason' => 'Kata sandi salah',
        ]);

        // 3. Non-existent email
        $this->post('/login', [
            'email' => 'unknown.hacker@evil.com',
            'password' => 'anything',
        ]);

        $this->assertDatabaseHas('login_logs', [
            'email' => 'unknown.hacker@evil.com',
            'status' => 'Failed',
            'failure_reason' => 'Email tidak terdaftar',
        ]);
    }

    public function test_super_admin_can_access_login_logs_resource(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $this->actingAs($superAdmin);

        $response = $this->get('/admin/login-logs');
        $response->assertStatus(200);
    }

    public function test_non_super_admin_cannot_access_login_logs_resource(): void
    {
        $hrAdmin = User::where('role', 'hr_admin')->first();
        $this->actingAs($hrAdmin);

        $response = $this->get('/admin/login-logs');
        $response->assertStatus(403);
    }

    public function test_regular_employee_cannot_download_admin_csv_templates(): void
    {
        $employee = User::where('role', 'employee')->first();
        $this->actingAs($employee);

        $response = $this->get(route('admin.download-template.employee'));
        $response->assertStatus(403);

        $responseAtt = $this->get(route('admin.download-template.attendance'));
        $responseAtt->assertStatus(403);
    }

    public function test_admin_can_download_admin_csv_templates(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.download-template.employee'));
        $response->assertStatus(200);

        $responseCv = $this->get(route('admin.download-template.employee-bpr-cv'));
        $responseCv->assertStatus(200);
    }
}
