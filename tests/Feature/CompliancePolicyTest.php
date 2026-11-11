<?php

namespace Tests\Feature;

use App\Models\CompliancePolicy;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompliancePolicyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_super_admin_can_access_compliance_policies_admin(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $response = $this->actingAs($admin)->get('/admin/compliance-policies');
        $response->assertStatus(200);
        $response->assertSee('SK.DIR/001/BPR-BR/2026');
    }

    public function test_direksi_can_view_policies_but_cannot_create(): void
    {
        $direksi = User::where('role', 'direksi')->first();
        $this->actingAs($direksi);

        $this->get('/admin/compliance-policies')->assertStatus(200);
        $this->get('/admin/compliance-policies/create')->assertStatus(403);
    }

    public function test_employee_can_view_compliance_catalog_in_portal(): void
    {
        $employee = User::where('role', 'employee')->first();
        $response = $this->actingAs($employee)->get('/portal/compliance-policies');
        $response->assertStatus(200);
        $response->assertSee('Pedoman Penerapan Program Anti Pencucian Uang');
    }

    public function test_regular_employee_cannot_access_admin_compliance_policies(): void
    {
        $employee = User::where('role', 'employee')->first();
        $response = $this->actingAs($employee)->get('/admin/compliance-policies');
        $response->assertStatus(403);
    }

    public function test_target_audience_scoping_hides_executive_only_from_regular_employee(): void
    {
        $policyExecutive = CompliancePolicy::create([
            'document_number' => 'SK.DIR/999/TEST/2026',
            'title' => 'Kebijakan Khusus Dewan Direksi & Komisaris',
            'category' => 'other',
            'attachment_path' => 'compliance-policies/sample-kebijakan-bpr.pdf',
            'effective_date' => now(),
            'target_audience' => 'executive_only',
            'status' => 'active',
        ]);

        $employee = User::where('role', 'employee')->first();
        $visiblePolicies = CompliancePolicy::accessibleBy($employee)->pluck('id');

        $this->assertNotContains($policyExecutive->id, $visiblePolicies);

        // Super Admin & Direksi CAN see it
        $superAdmin = User::where('role', 'super_admin')->first();
        $this->assertContains($policyExecutive->id, CompliancePolicy::accessibleBy($superAdmin)->pluck('id'));

        $direksi = User::where('role', 'direksi')->first();
        $this->assertContains($policyExecutive->id, CompliancePolicy::accessibleBy($direksi)->pluck('id'));
    }

    public function test_archived_policy_is_hidden_from_regular_employee(): void
    {
        $archived = CompliancePolicy::create([
            'document_number' => 'SK.DIR/000/ARCHIVED/2025',
            'title' => 'Kebijakan Lama Yang Sudah Ditarik',
            'category' => 'other',
            'attachment_path' => 'compliance-policies/sample-kebijakan-bpr.pdf',
            'effective_date' => '2025-01-01',
            'target_audience' => 'all',
            'status' => 'archived',
        ]);

        $employee = User::where('role', 'employee')->first();
        $visiblePolicies = CompliancePolicy::accessibleBy($employee)->pluck('id');

        $this->assertNotContains($archived->id, $visiblePolicies);
    }
}

