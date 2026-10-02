<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Tests\TestCase;

class ComprehensiveResourceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }
    public function test_all_admin_pages_and_tables(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        if (!$admin) {
            $admin = User::factory()->create(['role' => 'super_admin']);
        }
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $resources = [
            \App\Filament\Resources\Branches\Pages\ListBranches::class,
            \App\Filament\Resources\Branches\Pages\CreateBranch::class,
            \App\Filament\Resources\Departments\Pages\ListDepartments::class,
            \App\Filament\Resources\Departments\Pages\CreateDepartment::class,
            \App\Filament\Resources\Positions\Pages\ListPositions::class,
            \App\Filament\Resources\Positions\Pages\CreatePosition::class,
            \App\Filament\Resources\Employees\Pages\ListEmployees::class,
            \App\Filament\Resources\Employees\Pages\CreateEmployee::class,
            \App\Filament\Resources\Attendances\Pages\ListAttendances::class,
            \App\Filament\Resources\Attendances\Pages\CreateAttendance::class,
            \App\Filament\Resources\LeaveTypes\Pages\ListLeaveTypes::class,
            \App\Filament\Resources\LeaveTypes\Pages\CreateLeaveType::class,
            \App\Filament\Resources\LeaveRequests\Pages\ListLeaveRequests::class,
            \App\Filament\Resources\LeaveRequests\Pages\CreateLeaveRequest::class,
            \App\Filament\Resources\Announcements\Pages\ListAnnouncements::class,
            \App\Filament\Resources\Announcements\Pages\CreateAnnouncement::class,
            \App\Filament\Resources\FingerprintDevices\Pages\ListFingerprintDevices::class,
            \App\Filament\Resources\FingerprintDevices\Pages\CreateFingerprintDevice::class,
            \App\Filament\Resources\Overtimes\Pages\ListOvertimes::class,
            \App\Filament\Resources\Overtimes\Pages\CreateOvertime::class,
            \App\Filament\Resources\Reimbursements\Pages\ListReimbursements::class,
            \App\Filament\Resources\Reimbursements\Pages\CreateReimbursement::class,
            \App\Filament\Resources\CareerMovements\Pages\ListCareerMovements::class,
            \App\Filament\Resources\CareerMovements\Pages\CreateCareerMovement::class,
            \App\Filament\Resources\WarningLetters\Pages\ListWarningLetters::class,
            \App\Filament\Resources\WarningLetters\Pages\CreateWarningLetter::class,
            \App\Filament\Resources\JobApplicants\Pages\ListJobApplicants::class,
            \App\Filament\Resources\JobApplicants\Pages\CreateJobApplicant::class,
            \App\Filament\Resources\Users\Pages\ListUsers::class,
            \App\Filament\Resources\Users\Pages\CreateUser::class,
        ];

        foreach ($resources as $pageClass) {
            Livewire::test($pageClass)->assertSuccessful();
        }
    }

    public function test_all_portal_pages(): void
    {
        $employee = User::where('role', 'employee')->first();
        $this->actingAs($employee);
        Filament::setCurrentPanel(Filament::getPanel('portal'));

        $portalPages = [
            \App\Filament\Portal\Resources\Attendances\Pages\ListAttendances::class,
            \App\Filament\Portal\Resources\LeaveRequests\Pages\ListLeaveRequests::class,
            \App\Filament\Portal\Resources\LeaveRequests\Pages\CreateLeaveRequest::class,
            \App\Filament\Portal\Resources\Reimbursements\Pages\ListReimbursements::class,
            \App\Filament\Portal\Resources\Reimbursements\Pages\CreateReimbursement::class,
            \App\Filament\Portal\Resources\Overtimes\Pages\ListOvertimes::class,
            \App\Filament\Portal\Resources\Overtimes\Pages\CreateOvertime::class,
        ];

        foreach ($portalPages as $pageClass) {
            Livewire::test($pageClass)->assertSuccessful();
        }
    }
}
