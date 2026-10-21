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

        .m3-att-profile-box {
            display: flex;
            align-items: center;
            gap: 1.15rem;
            margin-top: 0.75rem;
        }

        .m3-att-avatar-img {
            width: 62px;
            height: 62px;
            border-radius: 50%;
            border: 2.5px solid rgba(255, 255, 255, 0.45);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            background: rgba(255, 255, 255, 0.15);
            transition: transform 0.2s ease, border-color 0.2s ease;
            flex-shrink: 0;
            display: block;
        }

        .m3-att-avatar-img:hover {
            transform: scale(1.05);
            border-color: #FDB813;
        }

        .m3-att-greeting {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 1.55rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.01em;
        }

        .m3-att-sub {
            font-size: 0.875rem;
            color: #d1fae5;
            margin-top: 0.2rem;
            font-weight: 500;
        }

        .m3-att-meta-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
            margin-top: 0.45rem;
            font-size: 0.75rem;
        }

        .m3-att-chip {
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 0.18rem 0.65rem;
            border-radius: 8px;
            color: #ffffff;
            font-weight: 600;
        }

        .m3-stat-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.85rem;
        }

        .m3-stat-card {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            background: rgba(255, 255, 255, 0.14);
            border: 1px solid rgba(255, 255, 255, 0.22);
            border-radius: 18px;
            padding: 0.85rem 1.25rem;
            min-width: 160px;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .m3-stat-card:hover {
            background: rgba(255, 255, 255, 0.22);
            transform: translateY(-2px);
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
            font-size: 1.2rem;
            font-weight: 800;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            letter-spacing: -0.01em;
            margin-top: 0.1rem;
        }

        .m3-stat-sub {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
            margin-top: 0.1rem;
        }

        .m3-btn-history-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.85rem 1.35rem;
            border-radius: 9999px;
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

        .m3-btn-action-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(253, 184, 19, 0.22);
            color: #fef08a;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 0.85rem 1.35rem;
            border-radius: 9999px;
            text-decoration: none;
            border: 1px solid rgba(253, 184, 19, 0.45);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .m3-btn-action-pill:hover {
            background: #FDB813;
            color: #1e3a24;
            transform: translateY(-2px);
            box-shadow: 0 4px 14px rgba(253, 184, 19, 0.3);
        }

        .m3-btn-icon {
            width: 18px;
            height: 18px;
        }
    </style>

    <div class="m3-att-banner">
        <div class="m3-att-content">
            {{-- Kolom Kiri: Metadata Tanggal & Profil Pegawai --}}
            <div>
                {{-- Baris Pil Tanggal & Lokasi Cabang --}}
                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem;">
                    <span class="m3-badge-pill">
                        <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #FDB813;"></span>
                        {{ now()->translatedFormat('l, d F Y') }}
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

                {{-- Profil & Sapaan Pegawai --}}
                <div class="m3-att-profile-box">
                    <a href="{{ url('/portal/my-profile') }}" title="Lihat & Perbarui Profil Pegawai" style="display: inline-block; text-decoration: none; flex-shrink: 0;">
                        <img
                            src="{{ $employee?->photo_url ? \Illuminate\Support\Facades\Storage::url($employee->photo_url) : asset('images/default-avatar.svg') }}"
                            alt="{{ $employee?->full_name ?? auth()->user()->name }}"
                            class="m3-att-avatar-img"
                        />
                    </a>

                    <div>
                        <div class="m3-att-greeting">
                            Halo, {{ $employee?->full_name ?? auth()->user()->name }}
                        </div>
                        <div class="m3-att-sub">
                            {{ $employee?->position?->name ?? 'Staf' }} &bull; {{ $employee?->department?->name ?? 'Operasional & Layanan Kas' }}
                        </div>
                        <div class="m3-att-meta-chips">
                            <span class="m3-att-chip" style="background: rgba(253, 184, 19, 0.2); border-color: rgba(253, 184, 19, 0.4); color: #fef08a;">
                                NIP: {{ $employee?->nip ?? '-' }}
                            </span>
                            <span class="m3-att-chip">
                                {{ $statusLabel }}
                            </span>
                            @if($tenure)
                                <span class="m3-att-chip" style="background: rgba(16, 185, 129, 0.2); color: #a7f3d0; border-color: rgba(16, 185, 129, 0.35);">
                                    Masa Kerja: {{ $tenure }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Kanan: Ringkasan Hak Cuti & Tombol Akses Riwayat --}}
            <div class="m3-stat-group">
                <!-- Card Sisa Cuti Tahunan (Clickable Shortcut) -->
                <a href="{{ url('/portal/leave-requests') }}" class="m3-stat-card" style="text-decoration: none;" title="Klik untuk melihat riwayat dan pemotongan cuti Anda">
                    <div class="m3-stat-icon-box" style="background: rgba(16, 185, 129, 0.25);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#6ee7b7" class="m3-stat-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                        </svg>
                    </div>
                    <div>
                        <div class="m3-stat-lbl">Sisa Cuti {{ now()->year }}</div>
                        <div class="m3-stat-num">
                            {{ $leaveSummary['annual_remaining'] }} Hari
                        </div>
                        <div class="m3-stat-sub">Hak Cuti Tahunan &bull; Detail &rarr;</div>
                    </div>
                </a>

                <!-- Card Block Leave OJK (Clickable Shortcut) -->
                <a href="{{ url('/portal/leave-requests/create') }}" class="m3-stat-card" style="text-decoration: none;" title="Klik untuk mengajukan permohonan Cuti Mandatori Block Leave OJK">
                    <div class="m3-stat-icon-box" style="background: rgba(243, 122, 32, 0.25);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#FDB813" class="m3-stat-icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                    <div>
                        <div class="m3-stat-lbl">Block Leave OJK</div>
                        <div class="m3-stat-num">
                            {{ $leaveSummary['block_remaining'] }} Hari
                        </div>
                        <div class="m3-stat-sub">Mandatori Bank &bull; Ajukan &rarr;</div>
                    </div>
                </a>

                <!-- Action Button Riwayat Presensi -->
                <a href="{{ url('/portal/attendances') }}" class="m3-btn-history-pill" title="Lihat Rekap dan Riwayat Presensi Bulanan">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="m3-btn-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    <span>Riwayat Presensi</span>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
