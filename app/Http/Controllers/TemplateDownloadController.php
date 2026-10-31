<?php

namespace App\Http\Controllers;

use App\Services\DataMigrationService;
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
     * Download template Excel (.xls) Master Pegawai via Native HTTP GET
     */
    public function downloadEmployeeXls(): StreamedResponse
    {
        $this->checkAdminAccess();

        $service = app(DataMigrationService::class);
        $xls = $service->generateEmployeeTemplateXls();

        return response()->streamDownload(function () use ($xls) {
            echo "\xEF\xBB\xBF" . $xls;
        }, 'template_migrasi_pegawai_bpr.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
        ]);
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
     * Download template Excel (.xls) Log Absensi via Native HTTP GET
     */
    public function downloadAttendanceXls(): StreamedResponse
    {
        $this->checkAdminAccess();

        $service = app(DataMigrationService::class);
        $xls = $service->generateAttendanceTemplateXls();

        return response()->streamDownload(function () use ($xls) {
            echo "\xEF\xBB\xBF" . $xls;
        }, 'template_impor_absensi_bpr.xls', [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
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
