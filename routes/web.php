<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Cetak Formulir Cuti Resmi BPR Banda Raya
Route::middleware('auth')->get('/leave-requests/{leaveRequest}/print', [\App\Http\Controllers\LeavePrintController::class, 'printForm'])->name('leave-requests.print');

Route::middleware('auth')->prefix('admin')->group(function () {
    Route::get('/download-template/employee', [\App\Http\Controllers\TemplateDownloadController::class, 'downloadEmployee'])->name('admin.download-template.employee');
    Route::get('/download-template/attendance', [\App\Http\Controllers\TemplateDownloadController::class, 'downloadAttendance'])->name('admin.download-template.attendance');
    Route::get('/backup/download/{filename}', [\App\Http\Controllers\BackupDownloadController::class, 'download'])->name('admin.backup.download');
});

