<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/login', [AuthController::class, 'showLoginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/showcase/{design?}', function ($design = 'material3') {
    $branches = \App\Models\Branch::withCount('employees')->get();
    $totalEmployees = \App\Models\Employee::count();
    $todayAttendances = \App\Models\Attendance::whereDate('date', now())->count();
    $pendingLeaves = \App\Models\LeaveRequest::where('status', 'Pending')->count();
    $totalPayrolls = \App\Models\Payroll::sum('net_salary');
    $recentEmployees = \App\Models\Employee::with(['branch', 'position', 'department'])->latest()->take(6)->get();

    return view('showcase', compact('branches', 'totalEmployees', 'todayAttendances', 'pendingLeaves', 'totalPayrolls', 'recentEmployees', 'design'));
})->name('showcase');

