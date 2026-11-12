<?php

namespace Tests\Feature;

use App\Filament\Widgets\BankStatsOverview;
use App\Filament\Widgets\RecentCompliancePoliciesWidget;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class RoleBasedDashboardTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_compliance_officer_dashboard_shows_compliance_metrics_and_not_hr_leave_stats(): void
    {
        $complianceUser = User::where('role', 'compliance')->first();
        if (!$complianceUser) {
            $complianceUser = User::factory()->create([
                'role' => 'compliance',
                'name' => 'Petugas Kepatuhan',
                'email' => 'compliance@bprbandaraya.com',
            ]);
        }

        $this->actingAs($complianceUser);

        $this->assertTrue(BankStatsOverview::canView());
        $this->assertTrue(RecentCompliancePoliciesWidget::canView());

        Livewire::test(BankStatsOverview::class)
            ->assertSee('Katalog Kebijakan & SOP')
            ->assertSee('Regulasi APU & PPT (OJK)')
            ->assertSee('SOP Operasional & Kas')
            ->assertSee('Surat Edaran Aktif')
            ->assertDontSee('Total Pegawai Aktif')
            ->assertDontSee('Pengajuan Cuti Berjalan');

        Livewire::test(RecentCompliancePoliciesWidget::class)
            ->assertSee('Daftar Kebijakan Kepatuhan, Regulasi OJK & SOP Bank Terkini')
            ->assertSee('SK.DIR/001/BPR-BR/2026')
            ->assertSee('Pedoman Penerapan Program Anti Pencucian Uang');
    }

    public function test_hr_admin_dashboard_shows_hr_metrics(): void
    {
        $hrUser = User::where('role', 'hr_admin')->first();
        $this->actingAs($hrUser);

        $this->assertTrue(BankStatsOverview::canView());
        $this->assertFalse(RecentCompliancePoliciesWidget::canView());

        Livewire::test(BankStatsOverview::class)
            ->assertSee('Total Pegawai Aktif')
            ->assertSee('Verifikasi Cuti Pending')
            ->assertSee('Pelamar Baru')
            ->assertDontSee('Katalog Kebijakan & SOP');
    }

    public function test_branch_manager_dashboard_shows_branch_specific_metrics(): void
    {
        $pincabUser = User::where('role', 'branch_manager')->first();
        $this->actingAs($pincabUser);

        $this->assertTrue(BankStatsOverview::canView());
        $this->assertFalse(RecentCompliancePoliciesWidget::canView());

        Livewire::test(BankStatsOverview::class)
            ->assertSee('Pegawai Cabang Aktif')
            ->assertSee('Presensi Cabang Hari Ini')
            ->assertSee('Persetujuan Cuti Cabang')
            ->assertSee('Klaim Reimbursement Cabang')
            ->assertDontSee('Total Akun Sistem');
    }

    public function test_direksi_dashboard_shows_executive_metrics(): void
    {
        $direksiUser = User::where('role', 'direksi')->first();
        $this->actingAs($direksiUser);

        $this->assertTrue(BankStatsOverview::canView());
        $this->assertTrue(RecentCompliancePoliciesWidget::canView());

        Livewire::test(BankStatsOverview::class)
            ->assertSee('Total Pegawai BPR')
            ->assertSee('Otorisasi Cuti Direksi')
            ->assertSee('Beban Klaim Operasional');
    }

    public function test_super_admin_dashboard_shows_it_admin_metrics(): void
    {
        $superAdmin = User::where('role', 'super_admin')->first();
        $this->actingAs($superAdmin);

        $this->assertTrue(BankStatsOverview::canView());
        $this->assertTrue(RecentCompliancePoliciesWidget::canView());

        Livewire::test(BankStatsOverview::class)
            ->assertSee('Total Akun Sistem')
            ->assertSee('Master Pegawai')
            ->assertSee('Log Presensi Masuk Hari Ini');
    }
}
