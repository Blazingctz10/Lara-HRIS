<x-filament-widgets::widget>
    @php
        $employee = auth()->user()?->employee;
        $branch = $employee?->branch;
        $attendance = $this->getTodayAttendance();
    @endphp

    <style>
        .m3-att-banner {
            position: relative;
            overflow: hidden;
            border-radius: 24px !important;
            background: linear-gradient(135deg, #0E6838 0%, #15803d 55%, #0b4a27 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            padding: 1.75rem !important;
            color: #ffffff !important;
            box-shadow: 0 8px 24px -4px rgba(14, 104, 56, 0.3) !important;
            margin-bottom: 1.5rem !important;
            transition: all 0.25s ease;
        }

        :is(.dark) .m3-att-banner {
            background: linear-gradient(135deg, #09381e 0%, #0e4e2a 55%, #052614 100%) !important;
            border-color: rgba(74, 222, 128, 0.2) !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45) !important;
        }

        .m3-att-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        @media (min-width: 1024px) {
            .m3-att-content {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .m3-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.35rem 0.95rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.18);
            font-size: 0.775rem;
            font-weight: 700;
            letter-spacing: 0.4px;
            color: #ffffff;
        }

        .m3-badge-branch {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.12);
            font-size: 0.775rem;
            font-weight: 600;
            color: #bbf7d0;
        }

        .m3-badge-device {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            background: rgba(253, 184, 19, 0.2);
            border: 1px solid rgba(253, 184, 19, 0.4);
            font-size: 0.725rem;
            font-weight: 700;
            color: #fef08a;
            margin-top: 0.5rem;
        }

        .m3-att-greeting {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            margin-top: 0.65rem;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }

        .m3-att-sub {
            font-size: 0.875rem;
            color: #d1fae5;
            margin-top: 0.25rem;
        }

        .m3-stat-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .m3-stat-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 18px;
            padding: 0.85rem 1.25rem;
            min-width: 155px;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .m3-stat-card:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .m3-stat-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.22);
            flex-shrink: 0;
        }

        .m3-stat-icon {
            width: 22px;
            height: 22px;
        }

        .m3-stat-lbl {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #a7f3d0;
            font-weight: 700;
        }

        .m3-stat-num {
            font-size: 1.15rem;
            font-weight: 800;
            color: #ffffff;
            font-family: monospace;
            letter-spacing: 0.5px;
            margin-top: 0.1rem;
        }

        .m3-btn-history-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.85rem 1.4rem;
            border-radius: 9999px; /* M3 Full Pill */
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.35);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .m3-btn-history-pill:hover {
            background: #ffffff;
            color: #0E6838;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }

        .m3-btn-icon {
            width: 18px;
            height: 18px;
        }
    </style>

    <div class="m3-att-banner">
        <div class="m3-att-content">
            <div>
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem;">
                    <span class="m3-badge-pill">
                        <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #FDB813;"></span>
                        PRESENSI &bull; {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                    @if($branch)
                        <span class="m3-badge-branch">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                            </svg>
                            <span>{{ $branch->name }}</span>
                        </span>
                    @endif
                </div>

                <div class="m3-att-greeting">
                    Halo, {{ $employee?->full_name ?? auth()->user()->name }}
                </div>
                <div class="m3-att-sub">
                    {{ $employee?->position?->name ?? 'Staf' }} &bull; {{ $employee?->department?->name ?? 'Operasional' }} &bull; NIP: {{ $employee?->nip ?? '-' }}
                </div>

                <div class="m3-badge-device">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                    </svg>
                    <span>Terhubung Mesin Presensi Solution X105 Cabang</span>
                </div>
            </div>

            <div class="m3-stat-group">
                <!-- Box Jam Masuk -->
                <div class="m3-stat-card">
                    <div class="m3-stat-icon-box" style="background: rgba(16, 185, 129, 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#6ee7b7" class="m3-stat-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </div>
                    <div>
                        <div class="m3-stat-lbl">Masuk (In)</div>
                        <div class="m3-stat-num">
                            {{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '--:--' }}
                        </div>
                    </div>
                </div>

                <!-- Box Jam Pulang -->
                <div class="m3-stat-card">
                    <div class="m3-stat-icon-box" style="background: rgba(243, 122, 32, 0.3);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#FDB813" class="m3-stat-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </div>
                    <div>
                        <div class="m3-stat-lbl">Pulang (Out)</div>
                        <div class="m3-stat-num">
                            {{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '--:--' }}
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <a href="{{ url('/portal/attendances') }}" class="m3-btn-history-pill">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="m3-btn-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Riwayat Presensi</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
