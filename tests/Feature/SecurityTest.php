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
        $teller = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->assertNotNull($teller);

        $response = $this->post('/login', [
            'email' => 'teller.botania@bprbandaraya.com',
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
    }
}
