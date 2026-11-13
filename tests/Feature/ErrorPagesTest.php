<?php

namespace Tests\Feature;

use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_404_page_renders_custom_m3_error_layout(): void
    {
        $response = $this->get('/halaman-tidak-ditemukan-404-xyz');

        $response->assertStatus(404);
        $response->assertSee('SYS404');
        $response->assertSee('Halaman Tidak Ditemukan');
    }

    public function test_custom_error_views_render_correct_codes_and_messages(): void
    {
        $view419 = view('errors.419')->render();
        $this->assertStringContainsString('SYS419', $view419);
        $this->assertStringContainsString('Sesi Formulir Berakhir (CSRF)', $view419);

        $view503 = view('errors.503')->render();
        $this->assertStringContainsString('SYS503', $view503);
        $this->assertStringContainsString('Sistem Sedang dalam Pemeliharaan Terjadwal', $view503);

        $view429 = view('errors.429')->render();
        $this->assertStringContainsString('SYS429', $view429);
        $this->assertStringContainsString('Batas Permintaan Akses Terlampaui', $view429);

        $view403 = view('errors.403')->render();
        $this->assertStringContainsString('SYS403', $view403);
    }
}
