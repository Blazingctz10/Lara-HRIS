<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeFamily;
use App\Models\EmployeePerformanceAppraisal;
use App\Models\EmployeeTraining;
use App\Models\Position;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class ComprehensiveEmployeeProfileTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_employee_comprehensive_profile_attributes_and_accessors(): void
    {
        $branch = Branch::first();
        $department = Department::first();
        $position = Position::first();

        $employee = Employee::create([
            'nip' => '1101 0064',
            'nik' => '2171122304829010',
            'full_name' => 'Junaidi, A.Md.',
            'email' => 'junaidi@bprbandaraya.com',
            'personal_email' => 'junkincai@gmail.com',
            'phone' => '085265797184',
            'gender' => 'Male',
            'birth_date' => '1982-04-23',
            'birth_place' => 'Kerinci',
            'address' => 'Perum Taman Lestari Blok B1 no.5 – Batu Aji',
            'branch_id' => $branch->id,
            'department_id' => $department->id,
            'position_id' => $position->id,
            'job_effective_date' => '2011-04-25',
            'grade' => 6,
            'grade_effective_date' => '2015-01-01',
            'employment_status' => 'Permanent',
            'join_date' => '2011-01-03',
            'permanent_date' => '2012-01-02',
            'exit_date' => '2022-03-31',
            'exit_reason' => 'Mengundurkan diri',
            'status' => 'Resigned',
            'education_level' => 'D-3',
            'education_major' => 'Teknik Elektronika',
            'education_institution' => 'Univ. Negeri Yogyakarta',
            'ipk' => 2.59,
            'bank_bpr_account' => '01.10.05307',
            'bank_external_name' => 'Bank Mandiri',
            'bank_external_account' => '109-00-1514578-2',
            'npwp' => '15.690.690.1-215.000',
            'bpjs_tk_number' => '08017700462',
            'bpjs_kes_number' => '0001617375611',
            'ptkp_status' => 'K/3',
            'emergency_contact_name' => 'Lolian Putri',
            'emergency_contact_phone' => '081299887766',
            'emergency_contact_relation' => 'Istri',
        ]);

        $this->assertEquals('1101 0064', $employee->nip);
        $this->assertEquals(6, $employee->grade);
        $this->assertEquals('D-3', $employee->education_level);
        $this->assertEquals('2.59', (string) $employee->ipk);
        $this->assertEquals('K/3', $employee->ptkp_status);
        $this->assertEquals('Mengundurkan diri', $employee->exit_reason);

        // Accessor Working Period (2011-01-03 s/d 2022-03-31 = 11 tahun 2 bulan)
        $this->assertStringContainsString('tahun', $employee->working_period);
        $this->assertStringContainsString('bulan', $employee->working_period);

        // Accessor Age
        $this->assertGreaterThanOrEqual(40, $employee->age);

        // Test Families Relation
        $family = EmployeeFamily::create([
            'employee_id' => $employee->id,
            'name' => 'Lolian Putri',
            'nik' => '2171126303819000',
            'relationship' => 'Istri',
            'birth_place' => 'Kerinci',
            'birth_date' => '1981-08-23',
            'gender' => 'Perempuan',
        ]);
        $this->assertCount(1, $employee->families);

        // Test Training Relation
        $training = EmployeeTraining::create([
            'employee_id' => $employee->id,
            'training_name' => 'Penilaian Agunan Kredit BPR',
            'schedule' => '17-Jul-13',
            'organizer' => 'Jakarta Financial Institute',
            'trainer' => 'Bp. Kardi P, SH, M.Ba',
            'location' => 'Jakarta',
        ]);
        $this->assertCount(1, $employee->trainings);

        // Test Performance Appraisal Relation
        $pa = EmployeePerformanceAppraisal::create([
            'employee_id' => $employee->id,
            'period_year' => 2020,
            'letter_grade' => 'B',
            'score' => 3.65,
            'notes' => 'Pencapaian target tahun 2020',
        ]);
        $this->assertCount(1, $employee->performanceAppraisals);
    }

    public function test_admin_can_access_employee_edit_page_with_new_tabs(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        $employee = Employee::first();
        $response = $this->get("/admin/employees/{$employee->id}/edit");
        $response->assertStatus(200);
        $response->assertSee('Data Diri & Penempatan');
        $response->assertSee('Pendidikan & Legalitas');
        $response->assertSee('Keluarga & PTKP');
        $response->assertSee('Riwayat Karir');
        $response->assertSee('Riwayat Pelatihan');
        $response->assertSee('Performance Appraisal');
    }
}
