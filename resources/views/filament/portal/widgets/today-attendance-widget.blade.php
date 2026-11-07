<x-filament-widgets::widget>
    @php
        $employee = auth()->user()?->employee;
        $branch = $employee?->branch;
        $schedule = $this->getTodaySchedule();
        $statusInfo = $this->getTodayStatusInfo();
        $leaveSummary = $this->getLeaveSummary();
        $tenure = $this->getTenureInfo();
        $statusLabel = $this->getEmploymentStatusLabel();
    @endphp

    <style>
        .m3-att-banner {
            position: relative;
            overflow: hidden;
            border-radius: 24px !important;
            background: linear-gradient(135deg, #094726 0%, #0E6838 45%, #15803d 85%, #0d542d 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.18) !important;
            padding: 2rem 2.25rem !important;
            color: #ffffff !important;
            box-shadow: 0 12px 36px -8px rgba(14, 104, 56, 0.35) !important;
            margin-bottom: 1.5rem !important;
            transition: all 0.25s ease;
        }

        :is(.dark) .m3-att-banner {
            background: linear-gradient(135deg, #052614 0%, #083c20 45%, #0d542d 85%, #051d10 100%) !important;
            border-color: rgba(74, 222, 128, 0.2) !important;
            box-shadow: 0 12px 36px rgba(0, 0, 0, 0.5) !important;
        }

        /* Ambient Decorative Watermark Glow */
        .m3-att-ambient-glow {
            position: absolute;
            top: -60px;
            right: -60px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(253, 184, 19, 0.16) 0%, rgba(14, 104, 56, 0) 70%);
            pointer-events: none;
            z-index: 1;
        }

        .m3-att-ambient-watermark {
            position: absolute;
            right: 2rem;
            bottom: -2rem;
            width: 200px;
            height: 200px;
            opacity: 0.05;
            color: #ffffff;
            pointer-events: none;
            z-index: 1;
        }

        .m3-att-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        @media (min-width: 1100px) {
            .m3-att-content {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        /* Profil Utama & Foto Pegawai Dribbble Style */
        .m3-profile-identity-group {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .m3-profile-avatar-wrap {
            position: relative;
            flex-shrink: 0;
            display: inline-block;
            text-decoration: none;
        }

        /* Avatar Berukuran Besar & Menonjol (Hero Element) */
        .m3-profile-avatar-frame {
            width: 98px;
            height: 98px;
            border-radius: 50%;
            padding: 3.5px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.75) 0%, rgba(253, 184, 19, 0.85) 50%, rgba(255, 255, 255, 0.35) 100%);
            box-shadow: 0 10px 28px -4px rgba(0, 0, 0, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .m3-profile-avatar-wrap:hover .m3-profile-avatar-frame {
            transform: scale(1.05);
        }

        .m3-profile-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 2.5px solid #ffffff;
            object-fit: cover;
            background: #0E6838;
            display: block;
        }

        /* Status Aktif Indicator Dot */
        .m3-avatar-status-dot {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #22c55e;
            border: 2.5px solid #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .m3-profile-salutation {
            font-size: 0.9rem;
            font-weight: 600;
            color: #a7f3d0;
            letter-spacing: 0.3px;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .m3-profile-name {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin: 0;
        }

        .m3-profile-role {
            font-size: 0.95rem;
            color: #d1fae5;
            font-weight: 500;
            margin-top: 0.35rem;
        }

        .m3-profile-meta-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.65rem;
        }

        .m3-profile-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            padding: 0.22rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #ffffff;
            letter-spacing: 0.2px;
        }

        .m3-profile-chip-gold {
            background: rgba(253, 184, 19, 0.18);
            border-color: rgba(253, 184, 19, 0.4);
            color: #fef08a;
        }

        .m3-profile-chip-emerald {
            background: rgba(16, 185, 129, 0.18);
            border-color: rgba(16, 185, 129, 0.35);
            color: #a7f3d0;
        }

        /* Metrik Terbuka (Tanpa Card di dalam Card) */
        .m3-metrics-strip {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .m3-metric-item {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            transition: transform 0.2s ease, opacity 0.2s ease;
            min-width: 120px;
        }

        .m3-metric-item:hover {
            transform: translateY(-2px);
            opacity: 0.95;
        }

        .m3-metric-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #a7f3d0;
            font-weight: 700;
        }

        .m3-metric-val {
            display: flex;
            align-items: baseline;
            gap: 0.35rem;
            margin-top: 0.2rem;
        }

        .m3-metric-num {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 1.85rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .m3-metric-unit {
            font-size: 0.95rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
        }

        .m3-metric-sub {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.725rem;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
            margin-top: 0.25rem;
            transition: color 0.15s ease;
        }

        .m3-metric-item:hover .m3-metric-sub {
            color: #FDB813;
        }

        /* Garis Pemisah Vertikal Halus (Pengganti Batas Card) */
        .m3-metric-divider {
            width: 1px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            flex-shrink: 0;
        }

        @media (max-width: 640px) {
            .m3-metric-divider {
                display: none;
            }
        }

        /* Tombol Aksi Utama Riwayat Presensi */
        .m3-hero-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            background: #ffffff;
            color: #0E6838;
            font-weight: 750;
            font-size: 0.875rem;
            padding: 0.85rem 1.45rem;
            border-radius: 9999px;
            text-decoration: none;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            white-space: nowrap;
            letter-spacing: 0.2px;
        }

        .m3-hero-action-btn:hover {
            background: #FDB813;
            color: #0b381e;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(253, 184, 19, 0.4);
        }

        .m3-hero-action-btn svg {
            width: 19px;
            height: 19px;
            transition: transform 0.2s ease;
        }

        .m3-hero-action-btn:hover svg {
            transform: rotate(15deg);
        }
    </style>

    <div class="m3-att-banner">
        {{-- Ambient Glow Dekoratif --}}
        <div class="m3-att-ambient-glow"></div>
        <svg class="m3-att-ambient-watermark" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>

        <div class="m3-att-content">
            {{-- Bagian Kiri: Profil Pegawai dengan Foto Paling Besar & Menonjol --}}
            <div class="m3-profile-identity-group">
                <a href="{{ url('/portal/my-profile') }}" class="m3-profile-avatar-wrap" title="Lihat & Perbarui Profil Pegawai">
                    <div class="m3-profile-avatar-frame">
                        <img
                            src="{{ $employee?->photo_url ? \Illuminate\Support\Facades\Storage::url($employee->photo_url) : asset('images/default-avatar.svg') }}"
                            alt="{{ $employee?->full_name ?? auth()->user()->name }}"
                            class="m3-profile-avatar-img"
                        />
                    </div>
                    <span class="m3-avatar-status-dot" title="Status: Pegawai Aktif"></span>
                </a>

                <div>
                    <div class="m3-profile-salutation">
                        <span>Selamat Datang</span>
                        <span>👋</span>
                    </div>

                    <h1 class="m3-profile-name">
                        {{ $employee?->full_name ?? auth()->user()->name }}
                    </h1>

                    <div class="m3-profile-role">
                        {{ $employee?->position?->name ?? 'Staf' }} &bull; {{ $employee?->department?->name ?? 'Operasional & Layanan Kas' }}
                    </div>

                    {{-- Metadata Bar: NIP, Status, Masa Kerja, dan Cabang (Semua dalam 1 Lapisan Bersih) --}}
                    <div class="m3-profile-meta-row">
                        <span class="m3-profile-chip m3-profile-chip-gold">
                            NIP: {{ $employee?->nip ?? '-' }}
                        </span>

                        <span class="m3-profile-chip">
                            {{ $statusLabel }}
                        </span>

                        @if($tenure)
                            <span class="m3-profile-chip m3-profile-chip-emerald">
                                Masa Kerja: {{ $tenure }}
                            </span>
                        @endif

                        @if($branch)
                            <span class="m3-profile-chip">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 13px; height: 13px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                <span>{{ $branch->name }}</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Metrik Sisa Cuti Terbuka (Tanpa Card di dalam Card) & Tombol Aksi --}}
            <div class="m3-metrics-strip">
                <!-- Metrik 1: Sisa Cuti Tahunan -->
                <a href="{{ url('/portal/leave-requests') }}" class="m3-metric-item" title="Klik untuk melihat rincian sisa cuti tahunan Anda">
                    <div class="m3-metric-tag">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                        </svg>
                        <span>Sisa Cuti {{ now()->year }}</span>
                    </div>
                    <div class="m3-metric-val">
                        <span class="m3-metric-num">{{ $leaveSummary['annual_remaining'] }}</span>
                        <span class="m3-metric-unit">Hari</span>
                    </div>
                    <div class="m3-metric-sub">
                        <span>Hak Cuti Tahunan</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 11px; height: 11px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>

                <!-- Garis Pemisah Halus -->
                <div class="m3-metric-divider"></div>

                <!-- Metrik 2: Block Leave OJK -->
                <a href="{{ url('/portal/leave-requests/create') }}" class="m3-metric-item" title="Klik untuk mengajukan permohonan Cuti Mandatori Block Leave OJK">
                    <div class="m3-metric-tag">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#FDB813" style="width: 14px; height: 14px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                        <span>Block Leave OJK</span>
                    </div>
                    <div class="m3-metric-val">
                        <span class="m3-metric-num">{{ $leaveSummary['block_remaining'] }}</span>
                        <span class="m3-metric-unit">Hari</span>
                    </div>
                    <div class="m3-metric-sub">
                        <span>Kewajiban OJK</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" style="width: 11px; height: 11px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>

                <!-- Garis Pemisah Halus -->
                <div class="m3-metric-divider"></div>

                <!-- Tombol Aksi Riwayat Presensi -->
                <a href="{{ url('/portal/attendances') }}" class="m3-hero-action-btn" title="Lihat Rekap dan Riwayat Presensi Bulanan">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Riwayat Presensi</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
