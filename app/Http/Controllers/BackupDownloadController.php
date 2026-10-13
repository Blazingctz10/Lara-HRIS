<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupDownloadController extends Controller
{
    public function download(Request $request, string $filename): BinaryFileResponse
    {
        // Pastikan hanya super_admin yang berhak mengunduh arsip cadangan
        if (auth()->user()?->role !== 'super_admin') {
            abort(403, 'Akses terbatas untuk IT Administrator.');
        }

        $safeName = basename($filename);
        $backupDir = storage_path('app/backups');
        $path = $backupDir . DIRECTORY_SEPARATOR . $safeName;

        if (! File::exists($path)) {
            abort(404, 'Berkas cadangan tidak ditemukan.');
        }

        return response()->download($path, $safeName);
    }
}
