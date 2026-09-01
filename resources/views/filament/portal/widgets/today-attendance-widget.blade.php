<x-filament-widgets::widget>
    @php
        $employee = auth()->user()?->employee;
        $branch = $employee?->branch;
        $attendance = $this->getTodayAttendance();
    @endphp

    <style>
        .bpr-att-banner {
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
            background: linear-gradient(135deg, #0E6838 0%, #115e34 50%, #062e19 100%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 1.5rem;
            color: #ffffff;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            margin-bottom: 1.25rem;
        }

        .bpr-att-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        @media (min-width: 1024px) {
            .bpr-att-content {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        .bpr-badge-date {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.3rem 0.85rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(8px);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #ffffff;
        }

        .bpr-badge-branch {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.1);
            font-size: 0.75rem;
            color: #a7f3d0;
            margin-left: 0.5rem;
        }

        .bpr-badge-finger {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            background: rgba(243, 122, 32, 0.25);
            border: 1px solid rgba(243, 122, 32, 0.4);
            font-size: 0.7rem;
            font-weight: 700;
            color: #ffedd5;
            margin-top: 0.35rem;
        }

        .bpr-att-title {
            font-size: 1.4rem;
            font-weight: 800;
            margin-top: 0.5rem;
            color: #ffffff;
            line-height: 1.2;
        }

        .bpr-att-subtitle {
            font-size: 0.85rem;
            color: #d1fae5;
            margin-top: 0.25rem;
        }

        .bpr-stat-boxes {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.85rem;
        }

        .bpr-stat-card {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 0.75rem;
            padding: 0.65rem 1.1rem;
            min-width: 145px;
        }

        .bpr-stat-icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }

        .bpr-stat-icon {
            width: 20px;
            height: 20px;
            max-width: 20px;
            max-height: 20px;
        }

        .bpr-stat-label {
            font-size: 0.675rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #a7f3d0;
            font-weight: 700;
        }

        .bpr-stat-val {
            font-size: 1.05rem;
            font-weight: 800;
            color: #ffffff;
            font-family: monospace;
        }

        .bpr-btn-history {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.875rem;
            padding: 0.85rem 1.25rem;
            border-radius: 0.75rem;
            text-decoration: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .bpr-btn-history:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            color: #ffffff;
        }

        .bpr-btn-icon {
            width: 18px;
            height: 18px;
            max-width: 18px;
            max-height: 18px;
        }
    </style>

    <div class="bpr-att-banner">
        <div class="bpr-att-content">
            <div>
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.25rem;">
                    <span class="bpr-badge-date">
                        <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #FDB813;"></span>
                        PRESENSI &bull; {{ now()->translatedFormat('l, d F Y') }}
                    </span>
                    @if($branch)
                        <span class="bpr-badge-branch">
                            🏢 {{ $branch->name }}
                        </span>
                    @endif
                </div>

                <div class="bpr-att-title">
                    Halo, {{ $employee?->full_name ?? auth()->user()->name }}
                </div>
                <div class="bpr-att-subtitle">
                    {{ $employee?->position?->name ?? 'Staf' }} &bull; {{ $employee?->department?->name ?? 'Operasional' }} &bull; NIP: {{ $employee?->nip ?? '-' }}
                </div>

                <div class="bpr-badge-finger">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33" />
                    </svg>
                    <span>Terhubung Mesin Fingerprint Solution X105 Cabang</span>
                </div>
            </div>

            <div class="bpr-stat-boxes">
                <!-- Box Jam Masuk -->
                <div class="bpr-stat-card">
                    <div class="bpr-stat-icon-wrapper" style="background: rgba(16, 185, 129, 0.25);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#6ee7b7" class="bpr-stat-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </div>
                    <div>
                        <div class="bpr-stat-label">Masuk (In)</div>
                        <div class="bpr-stat-val">
                            {{ $attendance?->clock_in ? \Carbon\Carbon::parse($attendance->clock_in)->format('H:i') : '--:--' }}
                        </div>
                    </div>
                </div>

                <!-- Box Jam Pulang -->
                <div class="bpr-stat-card">
                    <div class="bpr-stat-icon-wrapper" style="background: rgba(243, 122, 32, 0.25);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#FDB813" class="bpr-stat-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </div>
                    <div>
                        <div class="bpr-stat-label">Pulang (Out)</div>
                        <div class="bpr-stat-val">
                            {{ $attendance?->clock_out ? \Carbon\Carbon::parse($attendance->clock_out)->format('H:i') : '--:--' }}
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <a href="{{ url('/portal/attendances') }}" class="bpr-btn-history">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="bpr-btn-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Lihat Riwayat Presensi</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
