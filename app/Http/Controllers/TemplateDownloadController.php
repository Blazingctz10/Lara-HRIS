<?php

namespace App\Http\Controllers;

use App\Services\DataMigrationService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TemplateDownloadController extends Controller
{
    /**
     * Download template CSV Master Pegawai via Native HTTP GET
     */
    public function downloadEmployee(): StreamedResponse
    {
        $service = app(DataMigrationService::class);
        $csv = $service->generateEmployeeTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_migrasi_pegawai_bpr.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Download template CSV Log Absensi via Native HTTP GET
     */
    public function downloadAttendance(): StreamedResponse
    {
        $service = app(DataMigrationService::class);
        $csv = $service->generateAttendanceTemplateCsv();

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_impor_absensi_bpr.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
