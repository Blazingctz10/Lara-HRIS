<?php

namespace Tests\Feature;

use App\Filament\Resources\Attendances\Pages\ListAttendances;
use App\Models\Attendance;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class AttendanceGroupingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh --seed');
    }

    public function test_attendance_list_renders_collapsible_groups_and_tabs_identically_to_career_movements(): void
    {
        $admin = User::where('role', 'super_admin')->first();
        $this->actingAs($admin);

        $response = $this->get('/admin/attendances');
        $response->assertStatus(200);

        Livewire::test(ListAttendances::class)
            ->assertSuccessful()
            ->assertSee('Pegawai Aktif')
            ->assertSee('Hari Ini')
            ->assertSee('Terlambat & Denda')
            ->assertSee('Alumni / Resign')
            ->assertSee('Semua Log');
    }
}
