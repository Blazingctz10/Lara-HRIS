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
            border-radius: 16px !important;
            background: linear-gradient(135deg, #094726 0%, #0E6838 60%, #147a42 100%) !important;
            border: 1px solid rgba(255, 255, 255, 0.14) !important;
            padding: 1.35rem 1.75rem !important;
            color: #ffffff !important;
            box-shadow: 0 6px 20px -4px rgba(14, 104, 56, 0.22) !important;
            margin-bottom: 0.75rem !important;
        }

        :is(.dark) .m3-att-banner {
            background: linear-gradient(135deg, #072916 0%, #0a3f22 60%, #0E6838 100%) !important;
            border-color: rgba(74, 222, 128, 0.2) !important;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4) !important;
        }

        .m3-att-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        @media (min-width: 1080px) {
            .m3-att-content {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }

        /* Profil Utama: Proporsional & Ringkas */
        .m3-profile-identity-group {
            display: flex;
            align-items: center;
            gap: 1.1rem;
        }

        .m3-profile-avatar-simple {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 2.5px solid rgba(255, 255, 255, 0.85);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            object-fit: cover;
            background: #0E6838;
            display: block;
            flex-shrink: 0;
            transition: transform 0.2s ease, border-color 0.2s ease;
        }

        .m3-profile-avatar-simple:hover {
            transform: scale(1.03);
            border-color: #ffffff;
        }

        .m3-profile-salutation {
            font-size: 0.775rem;
            font-weight: 600;
            color: #a7f3d0;
            letter-spacing: 0.2px;
            margin-bottom: 0.1rem;
        }

        .m3-profile-name {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 1.35rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.2;
            letter-spacing: -0.015em;
            margin: 0;
        }

        .m3-profile-role {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.92);
            font-weight: 600;
            margin-top: 0.2rem;
        }

        .m3-profile-meta-line {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.775rem;
            color: #d1fae5;
            font-weight: 500;
            margin-top: 0.3rem;
            line-height: 1.35;
        }

        .m3-meta-bullet {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.7rem;
        }

        /* Metrik Terbuka (Tanpa Card di dalam Card) */
        .m3-metrics-strip {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.25rem;
        }

        .m3-metric-item {
            display: flex;
            flex-direction: column;
            text-decoration: none;
            transition: transform 0.15s ease, opacity 0.15s ease;
            min-width: 105px;
        }

        .m3-metric-item:hover {
            transform: translateY(-1px);
            opacity: 0.95;
        }

        .m3-metric-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #a7f3d0;
            font-weight: 700;
        }

        .m3-metric-tag svg {
            width: 13px !important;
            height: 13px !important;
            flex-shrink: 0;
        }

        .m3-metric-val {
            display: flex;
            align-items: baseline;
            gap: 0.3rem;
            margin-top: 0.15rem;
        }

        .m3-metric-num {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            font-size: 1.45rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1.05;
            letter-spacing: -0.02em;
        }

        .m3-metric-unit {
            font-size: 0.825rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.85);
        }

        .m3-metric-sub {
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.75);
            font-weight: 500;
            margin-top: 0.15rem;
            transition: color 0.15s ease;
        }

        .m3-metric-sub svg {
            width: 10px !important;
            height: 10px !important;
            flex-shrink: 0;
        }

        .m3-metric-item:hover .m3-metric-sub {
            color: #FACC15;
        }

        .m3-metric-divider {
            width: 1px;
            height: 38px;
            background: rgba(255, 255, 255, 0.18);
            flex-shrink: 0;
        }

        @media (max-width: 640px) {
            .m3-metric-divider {
                display: none;
            }
        }

        /* Tombol Aksi Utama */
        .m3-hero-action-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #ffffff;
            color: #0E6838;
            font-weight: 700;
            font-size: 0.8rem;
            padding: 0.55rem 1.1rem;
            border-radius: 9999px;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.15s ease;
            white-space: nowrap;
            cursor: pointer;
            border: none;
        }

        .m3-hero-action-btn:hover {
            background: #FACC15;
            color: #0b381e;
            transform: translateY(-1px);
        }

        .m3-hero-action-btn-ghost {
            background: rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.25);
            box-shadow: none;
        }

        .m3-hero-action-btn-ghost:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #ffffff;
        }

        .m3-hero-action-btn svg {
            width: 15px !important;
            height: 15px !important;
            flex-shrink: 0;
        }

        /* Modal Saldo Cuti Bersih */
        .m3-leave-modal-backdrop {
            position: fixed;
            inset: 0;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(4px);
            padding: 1rem;
        }

        .m3-leave-modal-box {
            background: #ffffff;
            color: #0f1e14;
            border-radius: 28px !important;
            width: 100%;
            max-width: 540px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }

        :is(.dark) .m3-leave-modal-box {
            background: #141E18;
            color: #f1f5f9;
            border-color: rgba(74, 222, 128, 0.2);
        }

        .m3-leave-modal-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.7rem 1rem;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        :is(.dark) .m3-leave-modal-row {
            background: #19261E;
            border-color: rgba(255, 255, 255, 0.08);
        }
    </style>

    <div class="m3-att-banner">
        <div class="m3-att-content">
            {{-- Bagian Kiri: Profil Pegawai Ringkas --}}
            <div class="m3-profile-identity-group">
                <a href="{{ url('/portal/my-profile') }}" title="Lihat & Perbarui Profil Pegawai" style="text-decoration: none; flex-shrink: 0;">
                    <img
                        src="{{ $employee ? $employee->avatar_url : (auth()->user()?->getFilamentAvatarUrl() ?? asset('images/default-avatar.svg')) }}"
                        alt="{{ $employee?->full_name ?? auth()->user()->name }}"
                        class="m3-profile-avatar-simple"
                        loading="eager"
                        decoding="async"
                        width="64"
                        height="64"
                    />
                </a>

                <div>
                    <div class="m3-profile-salutation">
                        Selamat Datang,
                    </div>

                    <h1 class="m3-profile-name">
                        {{ $employee?->full_name ?? auth()->user()->name }}
                    </h1>

                    <div class="m3-profile-role">
                        {{ $employee?->position?->name ?? 'Staf' }} &bull; {{ $employee?->department?->name ?? 'Operasional & Layanan Kas' }}
                    </div>

                    <div class="m3-profile-meta-line">
                        <span>NIP: {{ $employee?->nip ?? '-' }}</span>
                        <span class="m3-meta-bullet">&bull;</span>
                        <span>{{ $statusLabel }}</span>
                        @if($tenure)
                            <span class="m3-meta-bullet">&bull;</span>
                            <span>Masa Kerja: {{ $tenure }}</span>
                        @endif
                        @if($branch)
                            <span class="m3-meta-bullet">&bull;</span>
                            <span>{{ $branch->name }}</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Bagian Kanan: Metrik Sisa Cuti & Aksi --}}
            <div class="m3-metrics-strip" x-data="{ openBalancesModal: false }">
                <!-- Metrik 1: Sisa Cuti Tahunan -->
                <a href="{{ url('/portal/leave-requests') }}" class="m3-metric-item" title="Sisa hak cuti tahunan {{ now()->year }}">
                    <div class="m3-metric-tag">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                        </svg>
                        <span>Cuti Tahunan</span>
                    </div>
                    <div class="m3-metric-val">
                        <span class="m3-metric-num">{{ $leaveSummary['annual_remaining'] }}</span>
                        <span class="m3-metric-unit">Hari</span>
                    </div>
                    <div class="m3-metric-sub">
                        <span>{{ $leaveSummary['annual_used'] > 0 ? $leaveSummary['annual_used'] . ' Hari Terpakai' : 'Tersedia Penuh' }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                        </svg>
                    </div>
                </a>

                <div class="m3-metric-divider"></div>

                @if($leaveSummary['sick_used'] > 0)
                    <a href="{{ url('/portal/leave-requests') }}" class="m3-metric-item" title="Sisa hak cuti sakit dengan surat dokter">
                        <div class="m3-metric-tag" style="color: #fca5a5;">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#f87171">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                            </svg>
                            <span>Cuti Sakit</span>
                        </div>
                        <div class="m3-metric-val">
                            <span class="m3-metric-num">{{ $leaveSummary['sick_remaining'] }}</span>
                            <span class="m3-metric-unit">Hari</span>
                        </div>
                        <div class="m3-metric-sub" style="color: #fecaca;">
                            <span>{{ $leaveSummary['sick_used'] }} Hari Terpakai</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>
                @else
                    <a href="{{ url('/portal/leave-requests/create') }}" class="m3-metric-item" title="Mandatori 5 hari kerja berurutan OJK">
                        <div class="m3-metric-tag">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#FACC15">
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
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </a>
                @endif

                <div class="m3-metric-divider"></div>

                <!-- Tombol Aksi -->
                <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                    <button
                        type="button"
                        @click="openBalancesModal = true"
                        class="m3-hero-action-btn m3-hero-action-btn-ghost"
                        title="Buka rincian lengkap seluruh saldo cuti Anda"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <span>Semua Saldo Cuti</span>
                    </button>

                    <a href="{{ url('/portal/attendances') }}" class="m3-hero-action-btn" title="Lihat Rekap dan Riwayat Presensi Bulanan">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        <span>Riwayat Presensi</span>
                    </a>
                </div>

                <!-- Modal Rincian Lengkap Saldo Cuti Pegawai -->
                <div
                    x-show="openBalancesModal"
                    x-cloak
                    class="m3-leave-modal-backdrop"
                    @keydown.escape.window="openBalancesModal = false"
                >
                    <div
                        @click.outside="openBalancesModal = false"
                        class="m3-leave-modal-box"
                    >
                        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid rgba(148, 163, 184, 0.15); display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 800; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#0E6838" style="width: 20px; height: 20px; flex-shrink: 0;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                                    </svg>
                                    Rincian Saldo Cuti ({{ now()->year }})
                                </h3>
                                <p style="font-size: 0.775rem; margin: 0.15rem 0 0 0; opacity: 0.7;">
                                    {{ $employee?->full_name ?? auth()->user()->name }} &bull; NIP: {{ $employee?->nip ?? '-' }}
                                </p>
                            </div>
                            <button
                                type="button"
                                @click="openBalancesModal = false"
                                style="background: none; border: none; font-size: 1.35rem; cursor: pointer; color: inherit; opacity: 0.6; padding: 0.25rem;"
                            >
                                &times;
                            </button>
                        </div>

                        <div style="padding: 1.15rem 1.5rem; max-height: 60vh; overflow-y: auto;">
                            @if(isset($leaveSummary['balances']) && $leaveSummary['balances']->isNotEmpty())
                                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                                    @foreach($leaveSummary['balances'] as $bal)
                                        <div class="m3-leave-modal-row">
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.875rem;">
                                                    {{ $bal->leaveType?->name ?? 'Cuti' }}
                                                </div>
                                                <div style="font-size: 0.75rem; opacity: 0.7; margin-top: 0.1rem;">
                                                    Kuota Awal: {{ $bal->initial_quota }} hari &bull; Terpakai: <span style="font-weight: 700; color: {{ $bal->used_quota > 0 ? '#EA580C' : 'inherit' }}">{{ $bal->used_quota }} hari</span>
                                                </div>
                                            </div>
                                            <div style="text-align: right;">
                                                <div style="font-size: 1.05rem; font-weight: 800; color: #0E6838;">
                                                    {{ $bal->remaining_quota }} <span style="font-size: 0.75rem; font-weight: 600;">Hari</span>
                                                </div>
                                                <div style="font-size: 0.68rem; opacity: 0.65;">Sisa Kuota</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p style="text-align: center; opacity: 0.7; font-size: 0.85rem; padding: 1.25rem 0;">
                                    Belum ada kuota saldo cuti tercatat untuk tahun ini.
                                </p>
                            @endif

                            @if($leaveSummary['recent_leave'])
                                <div style="margin-top: 1rem; padding: 0.8rem 1rem; border-radius: 12px; background: rgba(14, 104, 56, 0.08); border: 1px dashed rgba(14, 104, 56, 0.3);">
                                    <div style="font-size: 0.72rem; font-weight: 700; color: #0E6838; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Permohonan Cuti Terakhir
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 0.25rem;">
                                        <div style="font-size: 0.825rem; font-weight: 600;">
                                            {{ $leaveSummary['recent_leave']->leaveType?->name }} ({{ $leaveSummary['recent_leave']->total_days }} Hari)
                                        </div>
                                        <span style="display: inline-flex; align-items: center; padding: 0.15rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; background: #0E6838; color: #ffffff;">
                                            {{ match($leaveSummary['recent_leave']->status) {
                                                'Approved_Direksi' => 'Disetujui Direksi',
                                                'Approved_HR', 'Approved_HRD' => 'Disetujui HRD',
                                                'Approved_Komisaris' => 'Disetujui Komisaris',
                                                'Rejected' => 'Ditolak',
                                                default => $leaveSummary['recent_leave']->status
                                            } }}
                                        </span>
                                    </div>
                                    <div style="font-size: 0.725rem; opacity: 0.75; margin-top: 0.15rem;">
                                        Periode: {{ \Carbon\Carbon::parse($leaveSummary['recent_leave']->start_date)->format('d M Y') }} s.d. {{ \Carbon\Carbon::parse($leaveSummary['recent_leave']->end_date)->format('d M Y') }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div style="padding: 0.9rem 1.5rem; border-top: 1px solid rgba(148, 163, 184, 0.15); display: flex; justify-content: flex-end; gap: 0.6rem;">
                            <a
                                href="{{ url('/portal/leave-requests/create') }}"
                                style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1.1rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 700; background: #0E6838; color: #ffffff; text-decoration: none;"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 14px; height: 14px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Ajukan Cuti Baru
                            </a>
                            <button
                                type="button"
                                @click="openBalancesModal = false"
                                style="padding: 0.5rem 1.1rem; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; background: transparent; border: 1px solid rgba(148, 163, 184, 0.3); cursor: pointer; color: inherit;"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
