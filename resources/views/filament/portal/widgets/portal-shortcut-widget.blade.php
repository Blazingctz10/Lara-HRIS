<x-filament-widgets::widget>
    @php
        $data = $this->getData();
        $unpaidCount = $data['unpaidCount'];
        $unpaidSum = $data['unpaidSum'];
    @endphp

    <style>
        .m3-portal-dash-strip {
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

        :is(.dark) .m3-portal-dash-strip {
            background: #111a14;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .m3-portal-action-pill {
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
            border: 1px solid var(--pill-border, #e2e8f0);
            background: var(--pill-bg, #f8fafc);
            color: var(--pill-color, #475569);
        }

        .m3-portal-action-pill:hover {
            transform: translateY(-1px);
            filter: brightness(1.04);
        }

        /* Emerald Green Pill (Ajukan Cuti) */
        .m3-pill-emerald {
            --pill-bg: rgba(14, 104, 56, 0.1);
            --pill-border: rgba(14, 104, 56, 0.3);
            --pill-color: #0E6838;
        }
        :is(.dark) .m3-pill-emerald {
            --pill-bg: rgba(14, 104, 56, 0.25);
            --pill-border: rgba(52, 211, 153, 0.3);
            --pill-color: #6ee7b7;
        }

        /* Amber / Orange Pill (Klaim Reimbursement) */
        .m3-pill-amber {
            --pill-bg: rgba(243, 122, 32, 0.12);
            --pill-border: rgba(243, 122, 32, 0.35);
            --pill-color: #d96512;
        }
        :is(.dark) .m3-pill-amber {
            --pill-bg: rgba(243, 122, 32, 0.2);
            --pill-border: rgba(251, 146, 60, 0.35);
            --pill-color: #fdba74;
        }

        /* Neutral Pill (Profil Saya) */
        .m3-pill-neutral {
            --pill-bg: #f8fafc;
            --pill-border: #e2e8f0;
            --pill-color: #475569;
        }
        :is(.dark) .m3-pill-neutral {
            --pill-bg: rgba(255, 255, 255, 0.06);
            --pill-border: rgba(255, 255, 255, 0.12);
            --pill-color: #cbd5e1;
        }
        :is(.dark) .m3-pill-neutral:hover {
            --pill-bg: rgba(255, 255, 255, 0.12);
            --pill-color: #ffffff;
        }
    </style>

    <div class="m3-portal-dash-strip">
        @if($unpaidCount > 0)
            {{-- Notifikasi Dana Reimbursement Siap Dicairkan --}}
            <div style="display: flex; align-items: center; gap: 0.55rem; margin-right: auto; min-width: 260px;">
                <div style="width: 28px; height: 28px; border-radius: 8px; background: rgba(243, 122, 32, 0.15); color: #F37A20; display: flex; align-items: center; justify-content: center; flex-shrink: 0;" class="dark:bg-amber-950/60 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 15px; height: 15px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div style="font-size: 0.82rem; color: #1e293b;" class="dark:text-gray-200">
                    <span style="font-weight: 800; color: #d96512;" class="dark:text-amber-400">
                        Rp {{ number_format($unpaidSum, 0, ',', '.') }} Siap Dicairkan
                    </span>
                    <span style="color: #64748b; font-size: 0.75rem;" class="dark:text-gray-400">
                        ({{ $unpaidCount }} klaim disetujui Pincab)
                    </span>
                </div>
            </div>
        @else
            {{-- Label Akses Cepat Karyawan --}}
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0;">
                <div style="width: 26px; height: 26px; border-radius: 8px; background: rgba(14, 104, 56, 0.12); color: #0E6838; display: flex; align-items: center; justify-content: center;" class="dark:bg-emerald-950/60 dark:text-emerald-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                    </svg>
                </div>
                <span style="font-size: 0.75rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; color: #475569;" class="dark:text-gray-300">
                    Akses Cepat Karyawan
                </span>
                <div style="width: 1px; height: 18px; background: #cbd5e1; margin: 0 0.15rem;" class="hidden sm:block dark:bg-white/10"></div>
            </div>
        @endif

        {{-- Group Tombol Pintasan Karyawan --}}
        <div style="display: flex; align-items: center; gap: 0.45rem; flex-wrap: wrap;">
            {{-- Ajukan Cuti --}}
            <a
                href="{{ url('/portal/leave-requests/create') }}"
                title="Ajukan Permohonan Cuti Baru"
                class="m3-portal-action-pill m3-pill-emerald"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                </svg>
                <span>Ajukan Cuti</span>
            </a>

            {{-- Klaim Reimbursement --}}
            <a
                href="{{ url('/portal/reimbursements/create') }}"
                title="Ajukan Klaim Reimbursement Operasional"
                class="m3-portal-action-pill m3-pill-amber"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.75H4.5a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 004.5 21h15a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0019.5 3z" />
                </svg>
                <span>Klaim Reimbursement</span>
            </a>

            {{-- Profil Saya --}}
            <a
                href="{{ url('/portal/my-profile') }}"
                title="Lihat & Perbarui Profil Saya"
                class="m3-portal-action-pill m3-pill-neutral"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                </svg>
                <span>Profil Saya</span>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
