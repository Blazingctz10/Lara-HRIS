<?php

namespace App\Http\Controllers;

use App\Services\DataMigrationService;
use App\Support\BprExcelExport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TemplateDownloadController extends Controller
{
    /**
     * Download template CSV Formulir CV Biodata Karyawan Resmi BPR Banda Raya
     */
    public function downloadEmployeeBprCv(): StreamedResponse
    {
        $this->checkAdminAccess();

        $service = app(DataMigrationService::class);
        $csv = $service->generateEmployeeBprCvTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_cv_karyawan_bpr_bandaraya.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Download template CSV Master Pegawai via Native HTTP GET
     */
    public function downloadEmployee(): StreamedResponse
    {
        $this->checkAdminAccess();

        $service = app(DataMigrationService::class);
        $csv = $service->generateEmployeeTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_migrasi_pegawai_bpr.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Download template Excel (.xlsx) Master Pegawai via Native HTTP GET
     */
    public function downloadEmployeeXls(): StreamedResponse
    {
        $this->checkAdminAccess();

        return BprExcelExport::stream(
            'template_migrasi_pegawai_bpr.xlsx',
            'Template Pegawai',
            'TEMPLATE MIGRASI DATA MASTER PEGAWAI',
            'Format Impor Resmi Lara-HRIS',
            [
                ['label' => 'NIP'],
                ['label' => 'NIK'],
                ['label' => 'Nama_Lengkap'],
                ['label' => 'Email'],
                ['label' => 'Nomor_Telepon'],
                ['label' => 'Jenis_Kelamin'],
                ['label' => 'Kantor_Cabang'],
                ['label' => 'Jabatan'],
                ['label' => 'Status_Kerja'],
                ['label' => 'Tanggal_Bergabung'],
                ['label' => 'No_Rekening_BPR'],
            ],
            [
                [
                    ['value' => '2601 0001', 'class' => 'td-nip'],
                    ['value' => '21710101990001', 'class' => 'td-text'],
                    ['value' => 'BUDI SANTOSO', 'class' => 'td-bold'],
                    ['value' => 'budi.santoso@bprbandaraya.com'],
                    ['value' => '081270001122', 'class' => 'td-text'],
                    ['value' => 'Laki-laki', 'class' => 'td-center'],
                    ['value' => 'Kantor Pusat'],
                    ['value' => 'Account Officer'],
                    ['value' => 'Permanent', 'class' => 'td-center'],
                    ['value' => '2024-01-15', 'class' => 'td-center'],
                    ['value' => '01.10.00123', 'class' => 'td-text td-center'],
                ],
            ],
        );
    }

    /**
     * Download template CSV Log Absensi via Native HTTP GET
     */
    public function downloadAttendance(): StreamedResponse
    {
        $this->checkAdminAccess();

        $service = app(DataMigrationService::class);
        $csv = $service->generateAttendanceTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_impor_absensi_bpr.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Download template Excel (.xlsx) Log Absensi via Native HTTP GET
     */
    public function downloadAttendanceXls(): StreamedResponse
    {
        $this->checkAdminAccess();

        return BprExcelExport::stream(
            'template_impor_absensi_bpr.xlsx',
            'Template Absensi',
            'TEMPLATE IMPOR LOG PRESENSI HARIAN',
            'Format Impor Resmi Lara-HRIS',
            [
                ['label' => 'NIP'],
                ['label' => 'Tanggal'],
                ['label' => 'Jam_Masuk'],
                ['label' => 'Jam_Pulang'],
                ['label' => 'Status'],
                ['label' => 'Catatan'],
            ],
            [
                [
                    ['value' => '2601 0001', 'class' => 'td-nip'],
                    ['value' => '2026-11-02', 'class' => 'td-center'],
                    ['value' => '07:55', 'class' => 'td-center'],
                    ['value' => '17:05', 'class' => 'td-center'],
                    ['value' => 'Present', 'class' => 'td-center'],
                    ['value' => 'Hadir normal'],
                ],
            ],
        );
    }

    /**
     * Download template CSV Recruitment Tracker & Pelamar Kerja
     */
    public function downloadRecruitmentTracker(): StreamedResponse
    {
        $this->checkAdminAccess();

        $service = app(DataMigrationService::class);
        $csv = $service->generateRecruitmentTrackerTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_recruitment_tracker_bpr.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    protected function checkAdminAccess(): void
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['super_admin', 'hr_admin'])) {
            abort(403, 'Akses terbatas untuk Administrator dan HR.');
        }
    }
}
