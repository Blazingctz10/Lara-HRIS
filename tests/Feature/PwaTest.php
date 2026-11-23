<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PwaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_pwa_manifest_service_worker_and_offline_page_exist_and_are_valid(): void
    {
        $manifestPath = public_path('manifest.webmanifest');
        $swPath = public_path('sw.js');
        $offlinePath = public_path('offline.html');

        $this->assertTrue(File::exists($manifestPath));
        $this->assertTrue(File::exists($swPath));
        $this->assertTrue(File::exists($offlinePath));

        $manifest = json_decode(File::get($manifestPath), true);
        $this->assertIsArray($manifest);
        $this->assertSame('/portal', $manifest['start_url']);
        $this->assertSame('standalone', $manifest['display']);
    }

    public function test_login_and_portal_pages_include_pwa_manifest_and_ios_meta_tags(): void
    {
        $this->get('/login')
            ->assertStatus(200)
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('apple-mobile-web-app-capable', false);

        $employee = User::where('role', 'employee')->firstOrFail();

        $this->actingAs($employee)
            ->get('/portal')
            ->assertStatus(200)
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('apple-mobile-web-app-title', false);
    }
}
