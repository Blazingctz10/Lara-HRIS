<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rute Cetak Formulir Cuti Resmi BPR Banda Raya (Diproteksi Auth & Throttle 30 req/min)
Route::middleware(['auth', 'throttle:30,1'])->get('/leave-requests/{leaveRequest}/print', [\App\Http\Controllers\LeavePrintController::class, 'printForm'])->name('leave-requests.print');

Route::middleware(['auth', 'throttle:60,1'])->prefix('admin')->group(function () {
    Route::get('/download-template/employee', [\App\Http\Controllers\TemplateDownloadController::class, 'downloadEmployee'])->name('admin.download-template.employee');
    Route::get('/download-template/employee-xls', [\App\Http\Controllers\TemplateDownloadController::class, 'downloadEmployeeXls'])->name('admin.download-template.employee-xls');
    Route::get('/download-template/attendance', [\App\Http\Controllers\TemplateDownloadController::class, 'downloadAttendance'])->name('admin.download-template.attendance');
    Route::get('/download-template/attendance-xls', [\App\Http\Controllers\TemplateDownloadController::class, 'downloadAttendanceXls'])->name('admin.download-template.attendance-xls');
    Route::get('/backup/download/{filename}', [\App\Http\Controllers\BackupDownloadController::class, 'download'])->name('admin.backup.download');
    Route::get('/attendance/matrix-preview', [\App\Http\Controllers\AttendanceReportController::class, 'previewMatrix'])->name('admin.attendance.matrix-preview');
    Route::get('/attendance/export-matrix', [\App\Http\Controllers\AttendanceReportController::class, 'exportMatrix'])->name('admin.attendance.export-matrix');
});

