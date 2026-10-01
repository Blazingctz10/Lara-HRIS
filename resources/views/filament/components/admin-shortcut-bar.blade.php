@php
    $unpaidClaimsCount = \App\Models\Reimbursement::whereIn('status', ['Pending', 'Approved_Pincab'])->count();
    $pendingLeaveCount = \App\Models\LeaveRequest::where('status', 'Pending')->count();
    $newApplicantsCount = \App\Models\JobApplicant::where('status', 'Baru')->count();
@endphp

<div class="m3-admin-shortcut-bar" style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: nowrap; overflow-x: auto; padding: 0.2rem 0; scrollbar-width: none;">
    {{-- Shortcut Klaim Reimbursement Belum Cair --}}
    <a
        href="{{ url('/admin/reimbursements') }}"
        title="Klaim Reimbursement yang Belum Dicairkan / Pending"
        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; {{ $unpaidClaimsCount > 0 ? 'background: rgba(243, 122, 32, 0.15); color: #d96512; border: 1px solid rgba(243, 122, 32, 0.4);' : 'background: rgba(0, 0, 0, 0.05); color: #64748b; border: 1px solid rgba(0, 0, 0, 0.08);' }}"
        class="dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-600/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75A.75.75 0 0 1 3 15v.75m0 0v.75m0-1.5h16.5m0-9H3.75m16.5 0v.75A.75.75 0 0 1 21 6h.75m0 0v8.25m0 0h-.75a.75.75 0 0 1-.75-.75V15m0 0v.75m0-1.5a45.083 45.083 0 0 1-16.5 0m16.5 0a45.083 45.083 0 0 0-16.5 0" />
        </svg>
        <span>Klaim Belum Cair</span>
        @if($unpaidClaimsCount > 0)
            <span style="background: #F37A20; color: #ffffff; border-radius: 9999px; padding: 0.05rem 0.45rem; font-size: 0.7rem; font-weight: 800;">
                {{ $unpaidClaimsCount }}
            </span>
        @endif
    </a>

    {{-- Shortcut Cuti Pending --}}
    <a
        href="{{ url('/admin/leave-requests') }}"
        title="Pengajuan Cuti Pegawai yang Menunggu Approval"
        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; {{ $pendingLeaveCount > 0 ? 'background: rgba(14, 104, 56, 0.12); color: #0E6838; border: 1px solid rgba(14, 104, 56, 0.3);' : 'background: rgba(0, 0, 0, 0.05); color: #64748b; border: 1px solid rgba(0, 0, 0, 0.08);' }}"
        class="dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-600/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
        </svg>
        <span>Cuti Pending</span>
        @if($pendingLeaveCount > 0)
            <span style="background: #0E6838; color: #ffffff; border-radius: 9999px; padding: 0.05rem 0.45rem; font-size: 0.7rem; font-weight: 800;">
                {{ $pendingLeaveCount }}
            </span>
        @endif
    </a>

    {{-- Shortcut Pelamar Kerja Baru --}}
    <a
        href="{{ url('/admin/job-applicants') }}"
        title="Pelamar Kerja Baru dari Email cPanel IMAP"
        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; {{ $newApplicantsCount > 0 ? 'background: rgba(59, 130, 246, 0.12); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.3);' : 'background: rgba(0, 0, 0, 0.05); color: #64748b; border: 1px solid rgba(0, 0, 0, 0.08);' }}"
        class="dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-600/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
        </svg>
        <span>Pelamar Baru</span>
        @if($newApplicantsCount > 0)
            <span style="background: #2563eb; color: #ffffff; border-radius: 9999px; padding: 0.05rem 0.45rem; font-size: 0.7rem; font-weight: 800;">
                {{ $newApplicantsCount }}
            </span>
        @endif
    </a>

    {{-- Shortcut Tambah Pegawai --}}
    <a
        href="{{ url('/admin/employees/create') }}"
        title="Daftarkan Pegawai Baru"
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(14, 104, 56, 0.08); color: #0E6838; border: 1px solid rgba(14, 104, 56, 0.2); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-600/20"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        <span>Pegawai</span>
    </a>

    {{-- Shortcut Migrasi Data --}}
    <a
        href="{{ url('/admin/import-data') }}"
        title="Pusat Migrasi Data Excel/CSV"
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(0, 0, 0, 0.05); color: #475569; border: 1px solid rgba(0, 0, 0, 0.08); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
        </svg>
        <span>Migrasi Excel</span>
    </a>

    {{-- Shortcut Backup Data --}}
    <a
        href="{{ url('/admin/backup-data') }}"
        title="Cadangan & Backup Data Sistem"
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(0, 0, 0, 0.05); color: #475569; border: 1px solid rgba(0, 0, 0, 0.08); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
        </svg>
        <span>Backup DB</span>
    </a>
</div>
