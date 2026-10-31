<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_all_official_demo_accounts_can_login_successfully(): void
    {
        $accounts = [
            'direksi@bprbandaraya.com' => '/admin',
            'admin@bprbandaraya.com' => '/admin',
            'hrd@bprbandaraya.com' => '/admin',
            'ratna.dewi@bprbandaraya.com' => '/admin',
            'pincab.botania@bprbandaraya.com' => '/admin',
            'teller.botania@bprbandaraya.com' => '/portal',
            'cs.botania@bprbandaraya.com' => '/portal',
            'ao.botania@bprbandaraya.com' => '/portal',
            'pincab.aviari@bprbandaraya.com' => '/admin',
            'teller.aviari@bprbandaraya.com' => '/portal',
            'pincab.bengkong@bprbandaraya.com' => '/admin',
            'teller.bengkong@bprbandaraya.com' => '/portal',
        ];

        foreach ($accounts as $email => $expectedRedirect) {
            $response = $this->post('/login', [
                'email' => $email,
                'password' => 'password',
            ]);

            $response->assertRedirect($expectedRedirect);
            $this->assertAuthenticated();

            // Logout before next account test
            $this->post('/logout');
            $this->assertGuest();
        }
    }
}
