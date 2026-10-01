<?php

namespace Tests\Feature;

use App\Models\JobApplicant;
use App\Models\User;
use App\Services\ImapRecruitmentService;
use Tests\TestCase;

class RecruitmentImapTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_sample_applicants_generation(): void
    {
        $service = app(ImapRecruitmentService::class);
        $count = $service->generateSampleApplicants();

        $this->assertGreaterThanOrEqual(1, $count);
        $this->assertDatabaseHas('job_applicants', [
            'email' => 'mega.utami@gmail.com',
            'position_applied' => 'Customer Service (CS)',
            'status' => 'Baru',
        ]);
    }

    public function test_admin_can_access_recruitment_page_and_view_applicants(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        // Buat sample applicant
        $service = app(ImapRecruitmentService::class);
        $service->generateSampleApplicants();

        $response = $this->get('/admin/job-applicants');
        $response->assertStatus(200);
        $response->assertSee('Rekrutmen & Pelamar');
        $response->assertSee('Mega Utami');
    }

    public function test_applicant_status_can_be_updated(): void
    {
        $applicant = JobApplicant::create([
            'name' => 'Budi Pratama',
            'email' => 'budi.pratama@gmail.com',
            'phone' => '081234567890',
            'subject' => 'Lamaran Kerja - Teller',
            'position_applied' => 'Teller Kas & Kliring',
            'status' => 'Baru',
            'email_date' => now(),
        ]);

        $applicant->update([
            'status' => 'Interview',
            'notes' => 'Kandidat lolos seleksi berkas, dijadwalkan wawancara offline.',
        ]);

        $this->assertDatabaseHas('job_applicants', [
            'id' => $applicant->id,
            'status' => 'Interview',
            'notes' => 'Kandidat lolos seleksi berkas, dijadwalkan wawancara offline.',
        ]);
    }
}
