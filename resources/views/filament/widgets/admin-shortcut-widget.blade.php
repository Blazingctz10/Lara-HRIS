<x-filament-widgets::widget>
    @php
        $data = $this->getData();
        $role = $data['role'] ?? 'employee';
        $unpaidClaimsCount = $data['unpaidClaimsCount'];
        $pendingLeaveCount = $data['pendingLeaveCount'];
        $newApplicantsCount = $data['newApplicantsCount'];
        $totalUsersCount = $data['totalUsersCount'] ?? 0;
        $dispensationCount = $data['dispensationCount'] ?? 0;
        $overtimePendingCount = $data['overtimePendingCount'] ?? 0;
        $draftPayrollCount = $data['draftPayrollCount'] ?? 0;
    @endphp

    <style>
        .m3-dash-shortcuts-bar {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 0.75rem;
            flex-wrap: wrap;
            padding: 0.65rem 1rem;
            border-radius: 16px;
            background: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            margin-bottom: 0.5rem;
        }

        :is(.dark) .m3-dash-shortcuts-bar {
            background: #111a14;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .m3-shortcut-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
            border: 1px solid var(--chip-border, #e2e8f0);
            background: var(--chip-bg, #f8fafc);
            color: var(--chip-color, #475569);
        }

        .m3-shortcut-chip:hover {
            transform: translateY(-1px);
            filter: brightness(1.04);
        }

        /* Neutral Chip (Migrasi Excel, Backup DB, & 0-count inactive chips) */
        .m3-chip-neutral {
            --chip-bg: #f8fafc;
            --chip-border: #e2e8f0;
            --chip-color: #475569;
        }
        :is(.dark) .m3-chip-neutral {
            --chip-bg: rgba(255, 255, 255, 0.06);
            --chip-border: rgba(255, 255, 255, 0.12);
            --chip-color: #cbd5e1;
        }
        :is(.dark) .m3-chip-neutral:hover {
            --chip-bg: rgba(255, 255, 255, 0.12);
            --chip-color: #ffffff;
        }

        /* Amber / Orange Chip (Klaim Belum Cair) */
        .m3-chip-amber {
            --chip-bg: rgba(243, 122, 32, 0.12);
            --chip-border: rgba(243, 122, 32, 0.35);
            --chip-color: #d96512;
        }
        :is(.dark) .m3-chip-amber {
            --chip-bg: rgba(243, 122, 32, 0.2);
            --chip-border: rgba(251, 146, 60, 0.35);
            --chip-color: #fdba74;
        }

        /* Emerald Green Chip (Cuti Pending & Tambah Pegawai) */
        .m3-chip-emerald {
            --chip-bg: rgba(14, 104, 56, 0.1);
            --chip-border: rgba(14, 104, 56, 0.3);
            --chip-color: #0E6838;
        }
        :is(.dark) .m3-chip-emerald {
            --chip-bg: rgba(14, 104, 56, 0.25);
            --chip-border: rgba(52, 211, 153, 0.3);
            --chip-color: #6ee7b7;
        }

        /* Blue Chip (Pelamar Baru) */
        .m3-chip-blue {
            --chip-bg: rgba(59, 130, 246, 0.12);
            --chip-border: rgba(59, 130, 246, 0.35);
            --chip-color: #2563eb;
        }
        :is(.dark) .m3-chip-blue {
            --chip-bg: rgba(59, 130, 246, 0.2);
            --chip-border: rgba(96, 165, 250, 0.35);
            --chip-color: #93c5fd;
        }

        .m3-chip-count {
            border-radius: 9999px;
            padding: 0.08rem 0.45rem;
            font-size: 0.68rem;
            font-weight: 800;
            line-height: 1;
        }
    </style>

    <div class="m3-dash-shortcuts-bar">
        {{-- Label Pintasan Cepat --}}
        <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
            <div style="width: 26px; height: 26px; border-radius: 8px; background: rgba(14, 104, 56, 0.12); color: #0E6838; display: flex; align-items: center; justify-content: center;" class="dark:bg-emerald-950/60 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                </svg>
            </div>
            <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; color: #475569;" class="dark:text-gray-300">
                Akses Cepat
            </span>
            <div style="width: 1px; height: 18px; background: #cbd5e1; margin: 0 0.15rem;" class="hidden sm:block dark:bg-white/10"></div>
        </div>

        {{-- Group Tombol Pintasan --}}
        <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap; flex: 1; justify-content: flex-start;">
            @if($role === 'super_admin')
                {{-- IT Administrator Shortcuts --}}
                <a href="{{ url('/admin/users') }}" title="Kelola Akun & Hak Akses Pengguna" class="m3-shortcut-chip m3-chip-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span>Kelola Akun IT</span>
                    <span class="m3-chip-count" style="background: #2563eb; color: #ffffff;">{{ $totalUsersCount }}</span>
                </a>

                <a href="{{ url('/admin/backup-data') }}" title="Cadangan Database & Arsip Sistem" class="m3-shortcut-chip m3-chip-neutral">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                    </svg>
                    <span>Backup DB</span>
                </a>

                <a href="{{ url('/admin/attendance-dispensations') }}" title="Logbook Dispensasi & Izin Kehadiran (WA/DM)" class="m3-shortcut-chip m3-chip-emerald">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    <span>Logbook Dispensasi</span>
                    @if($dispensationCount > 0)
                        <span class="m3-chip-count" style="background: #0E6838; color: #ffffff;">{{ $dispensationCount }}</span>
                    @endif
                </a>

                <a href="{{ url('/admin/import-data') }}" title="Migrasi Data Excel / CSV" class="m3-shortcut-chip m3-chip-neutral">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                    </svg>
                    <span>Migrasi Excel</span>
                </a>

                <a href="{{ url('/admin/leave-requests') }}" title="Antrean Cuti Menunggu Persetujuan" class="m3-shortcut-chip {{ $pendingLeaveCount > 0 ? 'm3-chip-emerald' : 'm3-chip-neutral' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                    </svg>
                    <span>Cuti Pending</span>
                    @if($pendingLeaveCount > 0)
                        <span class="m3-chip-count" style="background: #0E6838; color: #ffffff;">{{ $pendingLeaveCount }}</span>
                    @endif
                </a>

                <a href="{{ url('/admin/reimbursements') }}" title="Klaim Reimbursement Pending" class="m3-shortcut-chip {{ $unpaidClaimsCount > 0 ? 'm3-chip-amber' : 'm3-chip-neutral' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75A.75.75 0 0 1 3 15v.75m0 0v.75m0-1.5h16.5m0-9H3.75m16.5 0v.75A.75.75 0 0 1 21 6h.75m0 0v8.25m0 0h-.75a.75.75 0 0 1-.75-.75V15m0 0v.75m0-1.5a45.083 45.083 0 0 1-16.5 0m16.5 0a45.083 45.083 0 0 0-16.5 0" />
                    </svg>
                    <span>Klaim Belum Cair</span>
                    @if($unpaidClaimsCount > 0)
                        <span class="m3-chip-count" style="background: #F37A20; color: #ffffff;">{{ $unpaidClaimsCount }}</span>
                    @endif
                </a>

            @elseif($role === 'direksi')
                {{-- Direksi Executive Shortcuts --}}
                <a href="{{ url('/admin/leave-requests') }}" title="Otorisasi Final Cuti oleh Direksi" class="m3-shortcut-chip m3-chip-emerald">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                    <span>Otorisasi Cuti Direksi</span>
                    @if($pendingLeaveCount > 0)
                        <span class="m3-chip-count" style="background: #0E6838; color: #ffffff;">{{ $pendingLeaveCount }}</span>
                    @endif
                </a>

                <a href="{{ url('/admin/employees') }}" title="Pengawasan Data Pegawai Seluruh Cabang" class="m3-shortcut-chip m3-chip-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <span>Pengawasan Pegawai</span>
                </a>

                <a href="{{ url('/admin/career-movements') }}" title="Riwayat Karir & SK Pengangkatan Direksi" class="m3-shortcut-chip m3-chip-neutral">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                    </svg>
                    <span>Mutasi & SK Karir</span>
                </a>

                <a href="{{ url('/admin/reimbursements') }}" title="Monitoring Pengajuan Reimbursement Bank" class="m3-shortcut-chip {{ $unpaidClaimsCount > 0 ? 'm3-chip-amber' : 'm3-chip-neutral' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75A.75.75 0 0 1 3 15v.75m0 0v.75m0-1.5h16.5m0-9H3.75m16.5 0v.75A.75.75 0 0 1 21 6h.75m0 0v8.25m0 0h-.75a.75.75 0 0 1-.75-.75V15m0 0v.75m0-1.5a45.083 45.083 0 0 1-16.5 0m16.5 0a45.083 45.083 0 0 0-16.5 0" />
                    </svg>
                    <span>Monitoring Klaim</span>
                    @if($unpaidClaimsCount > 0)
                        <span class="m3-chip-count" style="background: #F37A20; color: #ffffff;">{{ $unpaidClaimsCount }}</span>
                    @endif
                </a>

                <a href="{{ url('/admin/announcements/create') }}" title="Terbitkan Surat Edaran / Memo Direksi" class="m3-shortcut-chip m3-chip-neutral">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 0 1-1.44-4.282m3.102.069a18.03 18.03 0 0 1-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 0 1 8.835 2.535M10.34 6.66a23.847 23.847 0 0 0 8.835-2.535m0 0A23.74 23.74 0 0 0 18.75 3c-1.47 0-2.882.265-4.185.75M19.175 4.125l.417.834a3.75 3.75 0 0 1 0 3.352l-.417.834" />
                    </svg>
                    <span>Buat Memo Direksi</span>
                </a>

            @else
                {{-- HR Admin & Pemimpin Cabang Shortcuts --}}
                @if($role === 'hr_admin')
                    <a href="{{ url('/admin/job-applicants') }}" title="Pelamar Kerja Baru" class="m3-shortcut-chip {{ $newApplicantsCount > 0 ? 'm3-chip-blue' : 'm3-chip-neutral' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
                        </svg>
                        <span>Pelamar Baru</span>
                        @if($newApplicantsCount > 0)
                            <span class="m3-chip-count" style="background: #2563eb; color: #ffffff;">{{ $newApplicantsCount }}</span>
                        @endif
                    </a>
                @endif

                <a href="{{ url('/admin/leave-requests') }}" title="Pengajuan Cuti Pegawai yang Menunggu Approval" class="m3-shortcut-chip {{ $pendingLeaveCount > 0 ? 'm3-chip-emerald' : 'm3-chip-neutral' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                    </svg>
                    <span>{{ $role === 'direksi' ? 'Otorisasi Cuti' : 'Verifikasi Cuti' }}</span>
                    @if($pendingLeaveCount > 0)
                        <span class="m3-chip-count" style="background: #0E6838; color: #ffffff;">{{ $pendingLeaveCount }}</span>
                    @endif
                </a>

                <a href="{{ url('/admin/reimbursements') }}" title="Klaim Reimbursement yang Belum Dicairkan / Pending" class="m3-shortcut-chip {{ $unpaidClaimsCount > 0 ? 'm3-chip-amber' : 'm3-chip-neutral' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75A.75.75 0 0 1 3 15v.75m0 0v.75m0-1.5h16.5m0-9H3.75m16.5 0v.75A.75.75 0 0 1 21 6h.75m0 0v8.25m0 0h-.75a.75.75 0 0 1-.75-.75V15m0 0v.75m0-1.5a45.083 45.083 0 0 1-16.5 0m16.5 0a45.083 45.083 0 0 0-16.5 0" />
                    </svg>
                    <span>Klaim Belum Cair</span>
                    @if($unpaidClaimsCount > 0)
                        <span class="m3-chip-count" style="background: #F37A20; color: #ffffff;">{{ $unpaidClaimsCount }}</span>
                    @endif
                </a>

                @if($role === 'hr_admin' || $role === 'super_admin')
                    <a href="{{ url('/admin/attendance-dispensations') }}" title="Logbook Dispensasi Kehadiran HRD & GA" class="m3-shortcut-chip m3-chip-emerald">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                        </svg>
                        <span>Logbook HR & GA</span>
                    </a>
                @endif

                <a href="{{ url('/admin/employees/create') }}" title="Daftarkan Pegawai Baru" class="m3-shortcut-chip m3-chip-emerald">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 13px; height: 13px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Tambah Pegawai</span>
                </a>

                @if($role === 'hr_admin' || $role === 'super_admin' || $role === 'direksi')
                    <a href="{{ url('/admin/payrolls') }}" title="Kelola Penggajian & Rilis Slip Gaji" class="m3-shortcut-chip {{ $draftPayrollCount > 0 ? 'm3-chip-amber' : 'm3-chip-emerald' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75A.75.75 0 0 1 3 15v.75m0 0v.75m0-1.5h16.5m0-9H3.75m16.5 0v.75A.75.75 0 0 1 21 6h.75m0 0v8.25m0 0h-.75a.75.75 0 0 1-.75-.75V15m0 0v.75m0-1.5a45.083 45.083 0 0 1-16.5 0m16.5 0a45.083 45.083 0 0 0-16.5 0" />
                        </svg>
                        <span>Penggajian & Slip</span>
                        @if($draftPayrollCount > 0)
                            <span class="m3-chip-count" style="background: #f59e0b; color: #ffffff;">{{ $draftPayrollCount }} Draf</span>
                        @endif
                    </a>
                @endif

                @if($role === 'hr_admin')
                    <a href="{{ url('/admin/import-data') }}" title="Pusat Migrasi Data Excel/CSV" class="m3-shortcut-chip m3-chip-neutral">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                        </svg>
                        <span>Migrasi Excel</span>
                    </a>
                @endif
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
