<x-filament-widgets::widget>
    @php
        $data = $this->getData();
        $unpaidClaimsCount = $data['unpaidClaimsCount'];
        $pendingLeaveCount = $data['pendingLeaveCount'];
        $newApplicantsCount = $data['newApplicantsCount'];
    @endphp

    <style>
        .m3-dash-shortcuts-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
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
            gap: 0.4rem;
            padding: 0.32rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
            border: 1px solid transparent;
        }

        .m3-shortcut-chip:hover {
            transform: translateY(-1px);
        }

        .m3-chip-count {
            border-radius: 9999px;
            padding: 0.05rem 0.4rem;
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
            {{-- 1. Klaim Belum Cair --}}
            <a
                href="{{ url('/admin/reimbursements') }}"
                title="Klaim Reimbursement yang Belum Dicairkan / Pending"
                class="m3-shortcut-chip {{ $unpaidClaimsCount > 0 ? 'dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-600/30' : 'dark:bg-white/5 dark:text-gray-400 dark:border-white/5' }}"
                style="{{ $unpaidClaimsCount > 0 ? 'background: rgba(243, 122, 32, 0.12); color: #d96512; border-color: rgba(243, 122, 32, 0.35);' : 'background: #f8fafc; color: #64748b; border-color: #e2e8f0;' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6H2.25m0 0v8.25m0 0h.75A.75.75 0 0 1 3 15v.75m0 0v.75m0-1.5h16.5m0-9H3.75m16.5 0v.75A.75.75 0 0 1 21 6h.75m0 0v8.25m0 0h-.75a.75.75 0 0 1-.75-.75V15m0 0v.75m0-1.5a45.083 45.083 0 0 1-16.5 0m16.5 0a45.083 45.083 0 0 0-16.5 0" />
                </svg>
                <span>Klaim Belum Cair</span>
                @if($unpaidClaimsCount > 0)
                    <span class="m3-chip-count" style="background: #F37A20; color: #ffffff;">
                        {{ $unpaidClaimsCount }}
                    </span>
                @endif
            </a>

            {{-- 2. Cuti Pending --}}
            <a
                href="{{ url('/admin/leave-requests') }}"
                title="Pengajuan Cuti Pegawai yang Menunggu Approval"
                class="m3-shortcut-chip {{ $pendingLeaveCount > 0 ? 'dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-600/30' : 'dark:bg-white/5 dark:text-gray-400 dark:border-white/5' }}"
                style="{{ $pendingLeaveCount > 0 ? 'background: rgba(14, 104, 56, 0.12); color: #0E6838; border-color: rgba(14, 104, 56, 0.35);' : 'background: #f8fafc; color: #64748b; border-color: #e2e8f0;' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                </svg>
                <span>Cuti Pending</span>
                @if($pendingLeaveCount > 0)
                    <span class="m3-chip-count" style="background: #0E6838; color: #ffffff;">
                        {{ $pendingLeaveCount }}
                    </span>
                @endif
            </a>

            {{-- 3. Pelamar Baru --}}
            <a
                href="{{ url('/admin/job-applicants') }}"
                title="Pelamar Kerja Baru"
                class="m3-shortcut-chip {{ $newApplicantsCount > 0 ? 'dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-600/30' : 'dark:bg-white/5 dark:text-gray-400 dark:border-white/5' }}"
                style="{{ $newApplicantsCount > 0 ? 'background: rgba(59, 130, 246, 0.12); color: #2563eb; border-color: rgba(59, 130, 246, 0.35);' : 'background: #f8fafc; color: #64748b; border-color: #e2e8f0;' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 14px; height: 14px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.765Z" />
                </svg>
                <span>Pelamar Baru</span>
                @if($newApplicantsCount > 0)
                    <span class="m3-chip-count" style="background: #2563eb; color: #ffffff;">
                        {{ $newApplicantsCount }}
                    </span>
                @endif
            </a>

            {{-- 4. Tambah Pegawai --}}
            <a
                href="{{ url('/admin/employees/create') }}"
                title="Daftarkan Pegawai Baru"
                class="m3-shortcut-chip dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-600/20"
                style="background: rgba(14, 104, 56, 0.06); color: #0E6838; border-color: rgba(14, 104, 56, 0.2);"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 13px; height: 13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <span>+ Pegawai</span>
            </a>

            {{-- 5. Migrasi Excel --}}
            <a
                href="{{ url('/admin/import-data') }}"
                title="Pusat Migrasi Data Excel/CSV"
                class="m3-shortcut-chip dark:bg-white/5 dark:text-gray-300 dark:border-white/10"
                style="background: #f8fafc; color: #475569; border-color: #e2e8f0;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
                <span>Migrasi Excel</span>
            </a>

            {{-- 6. Backup DB --}}
            <a
                href="{{ url('/admin/backup-data') }}"
                title="Cadangan & Backup Data Sistem"
                class="m3-shortcut-chip dark:bg-white/5 dark:text-gray-300 dark:border-white/10"
                style="background: #f8fafc; color: #475569; border-color: #e2e8f0;"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
                </svg>
                <span>Backup DB</span>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
