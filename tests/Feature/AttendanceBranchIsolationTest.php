<?php

namespace Tests\Feature;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\Attendance;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Tests\TestCase;

class AttendanceBranchIsolationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_branch_manager_can_view_own_branch_attendances_only_without_leaking_other_branches(): void
    {
        $pincab = User::where('email', 'pincab.botania@bprbandaraya.com')->firstOrFail();
        $ownBranchId = $pincab->branch_id;

        $ownEmployee = Employee::where('branch_id', $ownBranchId)->firstOrFail();
        $otherEmployee = Employee::where('branch_id', '!=', $ownBranchId)->firstOrFail();

        $ownAtt = Attendance::create([
            'employee_id' => $ownEmployee->id,
            'date' => today()->toDateString(),
            'clock_in' => '07:55:00',
            'status' => 'Present',
        ]);

        $otherAtt = Attendance::create([
            'employee_id' => $otherEmployee->id,
            'date' => today()->toDateString(),
            'clock_in' => '07:55:00',
            'status' => 'Present',
        ]);

        $this->actingAs($pincab);

        $this->get('/admin/attendances')->assertStatus(200);
        $this->assertFalse(AttendanceResource::canCreate());
        $this->assertFalse(AttendanceResource::canEdit($ownAtt));
        $this->assertFalse(AttendanceResource::canDelete($ownAtt));

        $visibleIds = Attendance::accessibleBy($pincab)->pluck('id');
        $this->assertTrue($visibleIds->contains($ownAtt->id));
        $this->assertFalse($visibleIds->contains($otherAtt->id));
    }

    public function test_branch_manager_without_branch_sees_no_attendances(): void
    {
        $orphanPincab = User::factory()->create([
            'role' => 'branch_manager',
            'branch_id' => null,
        ]);

        $this->actingAs($orphanPincab);
        $this->assertSame(0, Attendance::accessibleBy($orphanPincab)->count());
    }

    public function test_branch_manager_cannot_tamper_matrix_preview_branch_id_parameter(): void
    {
        $pincab = User::where('email', 'pincab.botania@bprbandaraya.com')->firstOrFail();
        $otherBranch = Branch::where('id', '!=', $pincab->branch_id)->firstOrFail();
        $otherEmployee = Employee::where('branch_id', $otherBranch->id)->where('status', 'Active')->firstOrFail();

        $response = $this->actingAs($pincab)->get(route('admin.attendance.matrix-preview', [
            'year' => now()->year,
            'month' => now()->month,
            'branch_id' => $otherBranch->id,
        ]));

        $response->assertStatus(200);
        $response->assertDontSee($otherEmployee->nip);
    }
}

