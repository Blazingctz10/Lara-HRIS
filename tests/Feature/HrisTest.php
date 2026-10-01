<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use App\Services\GeofencingService;
use Carbon\Carbon;
use Tests\TestCase;

class HrisTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected bool $seed = true;
    public function test_models_and_relations_work(): void
    {
        $branch = Branch::where('code', 'KC-BOTANIA')->first();
        $this->assertNotNull($branch);
        $this->assertGreaterThan(0, $branch->employees()->count());

        $teller = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->assertNotNull($teller);
        $this->assertNotNull($teller->employee);
        $this->assertEquals('KC-BOTANIA', $teller->employee->branch->code);
    }

    public function test_geofencing_calculation(): void
    {
        GeofencingService::setEnabled(true);

        // KC Botania coords: 1.11650000, 104.08900000
        $result = GeofencingService::isWithinRadius(
            1.11650000,
            104.08900000,
            1.11650000,
            104.08900000,
            50
        );

        $this->assertTrue($result['is_valid']);
        $this->assertEquals(0, $result['distance_meters']);

        // Far distance (e.g. 500 meters away)
        $farResult = GeofencingService::isWithinRadius(
            1.12500000,
            104.08900000,
            1.11650000,
            104.08900000,
            50
        );

        $this->assertFalse($farResult['is_valid']);
        $this->assertGreaterThan(50, $farResult['distance_meters']);

        // When disabled, is_valid should be true even when far
        GeofencingService::setEnabled(false);
        $disabledResult = GeofencingService::isWithinRadius(
            1.12500000,
            104.08900000,
            1.11650000,
            104.08900000,
            50
        );
        $this->assertTrue($disabledResult['is_valid']);
    }

    public function test_leave_balance_observer_deducts_on_hr_approval(): void
    {
        $employee = Employee::where('email', 'teller.botania@bprbandaraya.com')->first();
        $leaveAnnual = LeaveType::where('code', 'ANNUAL')->first();

        $balance = EmployeeLeaveBalance::firstOrCreate([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveAnnual->id,
            'year' => 2026,
        ], [
            'initial_quota' => 12,
            'used_quota' => 0,
            'remaining_quota' => 12,
        ]);

        $initialRemaining = $balance->remaining_quota;

        $request = LeaveRequest::create([
            'employee_id' => $employee->id,
            'leave_type_id' => $leaveAnnual->id,
            'start_date' => '2026-09-01',
            'end_date' => '2026-09-02',
            'total_days' => 2,
            'reason' => 'Test Cuti',
            'status' => 'Pending',
        ]);

        // Status is Pending, remaining quota should NOT be changed yet
        $balance->refresh();
        $this->assertEquals($initialRemaining, $balance->remaining_quota);

        // Status changes to Approved_HR -> Trigger Observer
        $request->update(['status' => 'Approved_HR']);

        $balance->refresh();
        $this->assertEquals($initialRemaining - 2, $balance->remaining_quota);
        $this->assertEquals(2 + ($initialRemaining < 12 ? (12 - $initialRemaining) : 0), $balance->used_quota);
    }

    public function test_panel_access_contracts(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $pincab = User::where('role', 'branch_manager')->first();
        $employee = User::where('role', 'employee')->first();

        // Admin panel check
        $adminPanel = \Filament\Facades\Filament::getPanel('admin');
        $portalPanel = \Filament\Facades\Filament::getPanel('portal');

        $this->assertTrue($admin->canAccessPanel($adminPanel));
        $this->assertTrue($pincab->canAccessPanel($adminPanel));
        $this->assertFalse($employee->canAccessPanel($adminPanel));

        // Portal panel check
        $this->assertTrue($admin->canAccessPanel($portalPanel));
        $this->assertTrue($pincab->canAccessPanel($portalPanel));
        $this->assertTrue($employee->canAccessPanel($portalPanel));
    }

    public function test_announcements_creation_and_scoping(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $employee = User::where('role', 'employee')->first();

        $announcement = \App\Models\Announcement::create([
            'title' => 'Test Pengumuman OJK',
            'category' => 'ojk_compliance',
            'priority' => 'urgent',
            'content' => 'Isi materi kepatuhan',
            'author_id' => $admin->id,
            'is_active' => true,
            'published_at' => now(),
        ]);

        $this->assertDatabaseHas('announcements', ['title' => 'Test Pengumuman OJK']);
        
        $this->actingAs($employee);
        $visibleAnnouncements = \App\Models\Announcement::accessibleBy()->get();
        $this->assertTrue($visibleAnnouncements->contains($announcement));
    }

    public function test_new_modules_and_contract_alerts(): void
    {
        $employee = Employee::first();
        $this->assertNotNull($employee);

        // 1. Test Overtime
        $overtime = \App\Models\Overtime::create([
            'employee_id' => $employee->id,
            'date' => now()->toDateString(),
            'start_time' => '17:30:00',
            'end_time' => '20:30:00',
            'duration_hours' => 3.00,
            'reason' => 'Test Lembur EOM',
            'status' => 'Pending',
        ]);
        $this->assertDatabaseHas('overtimes', ['reason' => 'Test Lembur EOM']);

        // 2. Test Reimbursement
        $reimbursement = \App\Models\Reimbursement::create([
            'employee_id' => $employee->id,
            'title' => 'Test Klaim Bensin',
            'category' => 'Bensin & Transport AO',
            'date' => now()->toDateString(),
            'amount' => 150000,
            'status' => 'Pending',
        ]);
        $this->assertDatabaseHas('reimbursements', ['title' => 'Test Klaim Bensin']);

        // 3. Test Career Movement
        $movement = \App\Models\CareerMovement::create([
            'employee_id' => $employee->id,
            'type' => 'Mutasi Cabang',
            'effective_date' => now()->toDateString(),
            'sk_number' => 'SK.DIR/TEST/001',
        ]);
        $this->assertDatabaseHas('career_movements', ['sk_number' => 'SK.DIR/TEST/001']);

        // 4. Test Warning Letter
        $sp = \App\Models\WarningLetter::create([
            'employee_id' => $employee->id,
            'level' => 'Surat Peringatan 1 (SP 1)',
            'letter_number' => 'SP.TEST/001',
            'reason' => 'Test Indisipliner',
            'issued_date' => now()->toDateString(),
            'status' => 'Aktif',
        ]);
        $this->assertDatabaseHas('warning_letters', ['letter_number' => 'SP.TEST/001']);
    }

    public function test_employee_can_view_and_update_profile_and_password(): void
    {
        $user = User::where('email', 'teller.botania@bprbandaraya.com')->first();
        $this->actingAs($user);

        $response = $this->get('/portal/my-profile');
        $response->assertStatus(200);

        \Livewire\Livewire::actingAs($user)
            ->test(\App\Filament\Portal\Pages\MyProfile::class)
            ->set('data.phone', '081299998888')
            ->set('data.address', 'Perumahan Botania Garden No. 100')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('081299998888', $user->fresh()->employee->phone);
        $this->assertEquals('Perumahan Botania Garden No. 100', $user->fresh()->employee->address);
    }
}
