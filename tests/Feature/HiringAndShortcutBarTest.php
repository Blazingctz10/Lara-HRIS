<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Employee;
use App\Models\JobApplicant;
use App\Models\Position;
use App\Models\Reimbursement;
use App\Models\User;
use App\Services\GeofencingService;
use Tests\TestCase;

class HiringAndShortcutBarTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_geofencing_is_disabled_by_default_and_allows_attendance(): void
    {
        // Secara default geofencing harus nonaktif
        $this->assertFalse(GeofencingService::isEnabled());

        // Walaupun jarak 100 km, is_valid harus tetap true karena dinonaktifkan
        $result = GeofencingService::isWithinRadius(
            1.50000000,
            104.50000000,
            1.11650000,
            104.08900000,
            50
        );

        $this->assertTrue($result['is_valid']);
        $this->assertFalse($result['geofencing_enabled']);
    }

    public function test_hired_applicant_automatically_becomes_active_employee(): void
    {
        $applicant = JobApplicant::create([
            'name' => 'Fajar Pratama, S.Kom.',
            'email' => 'fajar.pratama@gmail.com',
            'phone' => '081299887766',
            'position_applied' => 'Customer Service (CS)',
            'subject' => 'Lamaran Kerja CS',
            'status' => 'Baru',
            'email_date' => now(),
        ]);

        $branch = Branch::where('name', 'LIKE', '%Botania%')->first();
        $position = Position::where('name', 'LIKE', '%Customer Service%')->first();

        // Eksekusi promoteToEmployee
        $employee = $applicant->promoteToEmployee([
            'branch_id' => $branch->id,
            'position_id' => $position->id,
            'employment_status' => 'Probation',
            'join_date' => now()->toDateString(),
        ]);

        $this->assertInstanceOf(Employee::class, $employee);
        $this->assertEquals('Fajar Pratama, S.Kom.', $employee->full_name);
        $this->assertStringStartsWith('BNK-', $employee->nip);
        $this->assertEquals('Active', $employee->status);
        $this->assertEquals('Probation', $employee->employment_status);

        // Pastikan akun user login terbuat dengan domain .com
        $this->assertDatabaseHas('users', [
            'id' => $employee->user_id,
            'role' => 'employee',
        ]);
        $this->assertStringEndsWith('@bprbandaraya.com', $employee->email);

        // Pastikan kuota cuti tahunan awal 12 hari aktif
        $this->assertDatabaseHas('employee_leave_balances', [
            'employee_id' => $employee->id,
            'remaining_quota' => 12,
        ]);

        // Pastikan status pelamar menjadi Diterima
        $applicant->refresh();
        $this->assertEquals('Diterima', $applicant->status);
        $this->assertStringContainsString($employee->nip, $applicant->notes);
    }

    public function test_admin_shortcut_bar_renders_on_admin_panel(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Klaim Belum Cair');
        $response->assertSee('Cuti Pending');
        $response->assertSee('Pelamar Baru');
        $response->assertSee('Migrasi Excel');
        $response->assertSee('Backup DB');
    }

    public function test_portal_shortcut_bar_renders_unpaid_reimbursement_alert(): void
    {
        $user = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->actingAs($user);

        // Buat reimbursement disetujui tapi belum dicairkan
        Reimbursement::create([
            'employee_id' => $user->employee->id,
            'title' => 'Klaim Bensin Botania',
            'category' => 'Bensin & Transport AO',
            'date' => now()->toDateString(),
            'amount' => 275000,
            'status' => 'Approved_Pincab',
        ]);

        $response = $this->get('/portal');
        $response->assertStatus(200);
        $response->assertSee('Siap Dicairkan');
        $response->assertSee('275.000');
        $response->assertSee('Ajukan Cuti');
        $response->assertSee('Klaim Reimbursement');
    }
}
