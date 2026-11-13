<?php

namespace Tests\Feature;

use App\Filament\Resources\Announcements\AnnouncementResource;
use App\Models\Announcement;
use App\Models\Branch;
use App\Models\User;
use Tests\TestCase;

class AnnouncementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_all_admin_roles_can_access_admin_announcements_and_employee_is_denied(): void
    {
        foreach (['super_admin', 'hr_admin', 'direksi', 'compliance', 'branch_manager'] as $role) {
            $user = User::where('role', $role)->first() ?? User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/admin/announcements')->assertStatus(200);
        }

        $employee = User::where('role', 'employee')->first();
        $this->actingAs($employee)->get('/admin/announcements')->assertStatus(403);
    }

    public function test_compliance_role_sees_draft_and_branch_announcements_in_admin_scope(): void
    {
        $compliance = User::where('role', 'compliance')->first() ?? User::factory()->create(['role' => 'compliance']);
        $branch = Branch::first();

        $draft = Announcement::create([
            'title' => 'Draft Kepatuhan OJK',
            'category' => 'ojk_compliance',
            'priority' => 'important',
            'content' => '<p>Draft internal</p>',
            'branch_id' => $branch?->id,
            'author_id' => $compliance->id,
            'is_active' => false,
            'published_at' => now()->addDays(2)->toDateString(),
        ]);

        $this->actingAs($compliance);

        $this->assertTrue(Announcement::accessibleBy($compliance)->where('id', $draft->id)->exists());
    }

    public function test_direksi_can_edit_own_announcement_only_and_branch_manager_is_read_only(): void
    {
        $direksi = User::where('role', 'direksi')->first();
        $hrAdmin = User::where('role', 'hr_admin')->first();
        $pincab = User::where('role', 'branch_manager')->first();

        $ownMemo = Announcement::create([
            'title' => 'Memo Direksi Sendiri',
            'category' => 'direksi_memo',
            'priority' => 'normal',
            'content' => '<p>Isi memo</p>',
            'author_id' => $direksi->id,
            'is_active' => true,
            'published_at' => today()->toDateString(),
        ]);

        $hrMemo = Announcement::create([
            'title' => 'Memo HRD',
            'category' => 'internal_news',
            'priority' => 'normal',
            'content' => '<p>Isi memo HR</p>',
            'author_id' => $hrAdmin->id,
            'is_active' => true,
            'published_at' => today()->toDateString(),
        ]);

        $this->actingAs($direksi);
        $this->assertTrue(AnnouncementResource::canCreate());
        $this->assertTrue(AnnouncementResource::canEdit($ownMemo));
        $this->assertFalse(AnnouncementResource::canEdit($hrMemo));

        $this->actingAs($pincab);
        $this->assertFalse(AnnouncementResource::canCreate());
        $this->assertFalse(AnnouncementResource::canEdit($ownMemo));
        $this->assertFalse(AnnouncementResource::canDelete($ownMemo));
    }

    public function test_portal_announcements_respects_today_expiry_branch_scope_and_sanitizes_html(): void
    {
        $employee = User::where('role', 'employee')->first();
        $otherBranch = Branch::where('id', '!=', $employee->branch_id)->first();

        $expiringToday = Announcement::create([
            'title' => 'Memo Berakhir Hari Ini',
            'category' => 'internal_news',
            'priority' => 'normal',
            'content' => '<p onclick="alert(1)">Aman</p><a href="javascript:alert(2)">Link</a>',
            'branch_id' => null,
            'is_active' => true,
            'published_at' => today()->subDays(2)->toDateString(),
            'expires_at' => today()->toDateString(),
        ]);

        $expiredYesterday = Announcement::create([
            'title' => 'Memo Kedaluwarsa Kemarin',
            'category' => 'internal_news',
            'priority' => 'normal',
            'content' => '<p>Sudah lewat</p>',
            'branch_id' => null,
            'is_active' => true,
            'published_at' => today()->subDays(5)->toDateString(),
            'expires_at' => today()->subDay()->toDateString(),
        ]);

        $otherBranchMemo = Announcement::create([
            'title' => 'Memo Cabang Lain',
            'category' => 'internal_news',
            'priority' => 'normal',
            'content' => '<p>Khusus cabang lain</p>',
            'branch_id' => $otherBranch?->id,
            'is_active' => true,
            'published_at' => today()->toDateString(),
        ]);

        $this->actingAs($employee)->get('/portal/announcements')->assertStatus(200);

        $visibleIds = Announcement::publishedForUser($employee)->pluck('id');
        $this->assertTrue($visibleIds->contains($expiringToday->id));
        $this->assertFalse($visibleIds->contains($expiredYesterday->id));
        if ($otherBranch) {
            $this->assertFalse($visibleIds->contains($otherBranchMemo->id));
        }

        $sanitized = Announcement::sanitizeContent($expiringToday->content);
        $this->assertStringNotContainsString('onclick', $sanitized);
        $this->assertStringNotContainsString('javascript:', $sanitized);
    }
}
