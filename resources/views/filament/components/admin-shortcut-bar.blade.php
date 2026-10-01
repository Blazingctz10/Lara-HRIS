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
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; {{ $unpaidClaimsCount > 0 ? 'background: rgba(243, 122, 32, 0.15); color: #d96512; border: 1px solid rgba(243, 122, 32, 0.4);' : 'background: rgba(0, 0, 0, 0.05); color: #64748b; border: 1px solid rgba(0, 0, 0, 0.08);' }}"
        class="dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-600/30"
    >
        <span>💰</span>
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
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; {{ $pendingLeaveCount > 0 ? 'background: rgba(14, 104, 56, 0.12); color: #0E6838; border: 1px solid rgba(14, 104, 56, 0.3);' : 'background: rgba(0, 0, 0, 0.05); color: #64748b; border: 1px solid rgba(0, 0, 0, 0.08);' }}"
        class="dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-600/30"
    >
        <span>🏖️</span>
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
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; transition: all 0.2s ease; white-space: nowrap; {{ $newApplicantsCount > 0 ? 'background: rgba(59, 130, 246, 0.12); color: #2563eb; border: 1px solid rgba(59, 130, 246, 0.3);' : 'background: rgba(0, 0, 0, 0.05); color: #64748b; border: 1px solid rgba(0, 0, 0, 0.08);' }}"
        class="dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-600/30"
    >
        <span>📥</span>
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
        style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(14, 104, 56, 0.08); color: #0E6838; border: 1px solid rgba(14, 104, 56, 0.2); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-emerald-950/30 dark:text-emerald-300 dark:border-emerald-600/20"
    >
        <span>➕</span>
        <span>Pegawai</span>
    </a>

    {{-- Shortcut Migrasi Data --}}
    <a
        href="{{ url('/admin/import-data') }}"
        title="Pusat Migrasi Data Excel/CSV"
        style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(0, 0, 0, 0.05); color: #475569; border: 1px solid rgba(0, 0, 0, 0.08); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700/30"
    >
        <span>⚡</span>
        <span>Migrasi Excel</span>
    </a>

    {{-- Shortcut Backup Data --}}
    <a
        href="{{ url('/admin/backup-data') }}"
        title="Cadangan & Backup Data Sistem"
        style="display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(0, 0, 0, 0.05); color: #475569; border: 1px solid rgba(0, 0, 0, 0.08); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700/30"
    >
        <span>💾</span>
        <span>Backup DB</span>
    </a>
</div>
