<?php

namespace Tests\Feature;

use App\Filament\Portal\Resources\Reimbursements\ReimbursementResource as PortalReimbursementResource;
use App\Models\Reimbursement;
use App\Models\User;
use Tests\TestCase;

class PortalReimbursementWidgetTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_portal_dashboard_renders_reimbursement_widgets(): void
    {
        $user = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->actingAs($user);

        $employee = $user->employee;
        $this->assertNotNull($employee);

        // Buat 1 klaim yang sudah di-ACC Pincab tapi BELUM DITARIK / BELUM CAIR
        Reimbursement::create([
            'employee_id' => $employee->id,
            'title' => 'Bensin Antar Dokumen Kas Botania',
            'category' => 'Bensin & Transport AO',
            'date' => now()->toDateString(),
            'amount' => 350000,
            'description' => 'Biaya BBM operasional cabang',
            'status' => 'Approved_Pincab',
        ]);

        // Buat 1 klaim pending
        Reimbursement::create([
            'employee_id' => $employee->id,
            'title' => 'Pembelian Materai Kantor',
            'category' => 'Operasional Kas Cabang',
            'date' => now()->toDateString(),
            'amount' => 120000,
            'description' => 'Materai 10000 12 lembar',
            'status' => 'Pending',
        ]);

        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Belum Ditarik / Siap Cair');
        $response->assertSee('350.000');
        $response->assertSee('Klaim Menunggu Review');
        $response->assertSee('120.000');
        $response->assertSee('Daftar Pengajuan Reimbursement & Status Pencairan Dana');
        $response->assertSee('Bensin Antar Dokumen Kas Botania');
    }

    public function test_navigation_badge_shows_active_reimbursements(): void
    {
        $user = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->actingAs($user);

        $employee = $user->employee;

        Reimbursement::create([
            'employee_id' => $employee->id,
            'title' => 'Klaim Uji Badge',
            'category' => 'Bensin & Transport AO',
            'date' => now()->toDateString(),
            'amount' => 50000,
            'status' => 'Approved_Pincab',
        ]);

        $badge = PortalReimbursementResource::getNavigationBadge();
        $this->assertNotNull($badge);
        $this->assertEquals('1', $badge);
    }

    public function test_admin_dashboard_shows_active_reimbursements(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Klaim Reimbursement Aktif');
    }
}
