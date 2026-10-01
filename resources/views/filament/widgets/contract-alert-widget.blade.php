<x-filament-widgets::widget>
    @php
        $expiringEmployees = $this->getExpiringContracts();
    @endphp

    <style>
        .m3-contract-card {
            border-radius: 24px !important;
            background: #ffffff !important;
            border: 1px solid var(--m3-outline-variant, #B6CAC0) !important;
            padding: 1.5rem !important;
            box-shadow: 0 2px 8px -2px rgba(14, 104, 56, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
            margin-bottom: 1.5rem !important;
            transition: all 0.2s cubic-bezier(0.2, 0, 0, 1);
        }

        :is(.dark) .m3-contract-card {
            background: #141e18 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3) !important;
        }

        .m3-contract-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .m3-contract-head-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .m3-contract-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #ffedd5;
            color: #ea580c;
            flex-shrink: 0;
            transition: transform 0.2s ease;
        }

        :is(.dark) .m3-contract-icon-box {
            background: #431407;
            color: #fb923c;
        }

        .m3-contract-svg {
            width: 22px;
            height: 22px;
        }

        .m3-contract-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #131a15;
            margin: 0;
            letter-spacing: -0.01em;
        }

        :is(.dark) .m3-contract-title {
            color: #e2e8e4;
        }

        .m3-contract-desc {
            font-size: 0.8rem;
            color: #526156;
            margin: 0.2rem 0 0 0;
        }

        :is(.dark) .m3-contract-desc {
            color: #9eaba3;
        }

        .m3-contract-badge-pill {
            font-size: 0.75rem;
            font-weight: 800;
            color: #c2410c;
            background: #ffedd5;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            letter-spacing: 0.3px;
        }

        :is(.dark) .m3-contract-badge-pill {
            color: #fed7aa;
            background: #7c2d12;
        }

        .m3-contract-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 16px;
            background: #DFEAE2;
            border: 1px solid #B6CAC0;
            margin-bottom: 0.65rem;
            transition: all 0.2s ease;
        }

        :is(.dark) .m3-contract-item {
            background: #19261e;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .m3-contract-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        :is(.dark) .m3-contract-item:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
        }

        .m3-eval-btn {
            font-size: 0.8rem;
            font-weight: 700;
            color: #0E6838;
            background: #d1eadb;
            padding: 0.45rem 1rem;
            border-radius: 9999px; /* M3 Pill */
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: all 0.2s ease;
        }

        .m3-eval-btn:hover {
            background: #0E6838;
            color: #ffffff;
            transform: translateY(-1px);
        }

        :is(.dark) .m3-eval-btn {
            color: #a7f3d0;
            background: #064e26;
        }

        :is(.dark) .m3-eval-btn:hover {
            background: #4ade80;
            color: #003a19;
        }
        .m3-contract-emp-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #131A15;
        }

        :is(.dark) .m3-contract-emp-title {
            color: #FFFFFF !important;
        }

        .m3-contract-emp-meta {
            font-size: 0.8rem;
            color: #526156;
            margin-top: 0.25rem;
        }

        :is(.dark) .m3-contract-emp-meta {
            color: #9EABA3 !important;
        }

        .m3-contract-days-label {
            font-size: 0.825rem;
            font-weight: 800;
            color: #131A15;
            margin-top: 0.1rem;
        }

        :is(.dark) .m3-contract-days-label {
            color: #FFFFFF !important;
        }
    </style>

    <div class="m3-contract-card">
        <div class="m3-contract-header">
            <div class="m3-contract-head-left">
                <div class="m3-contract-icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="m3-contract-svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="m3-contract-title">
                        Peringatan Jatuh Tempo Kontrak & Masa Percobaan (60 Hari ke Depan)
                    </h3>
                    <p class="m3-contract-desc">
                        Monitoring otomatis pegawai PKWT / Masa Percobaan yang perlu evaluasi perpanjangan
                    </p>
                </div>
            </div>
            <span class="m3-contract-badge-pill">
                {{ $expiringEmployees->count() }} Pegawai Perlu Evaluasi
            </span>
        </div>

        @if($expiringEmployees->isEmpty())
            <div style="text-align: center; padding: 1.75rem; color: #526156; font-size: 0.875rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;" class="dark:text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="#10b981" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span>Tidak ada kontrak kerja atau masa probation pegawai yang akan berakhir dalam 60 hari ke depan.</span>
            </div>
        @else
            <div>
                @foreach($expiringEmployees as $emp)
                    @php
                        $isProbation = $emp->employment_status === 'Probation';
                        $targetDate = $isProbation 
                            ? ($emp->probation_end_date ?? $emp->end_date)
                            : ($emp->contract_end_date ?? $emp->end_date);
                        
                        $daysLeft = $targetDate ? (int) ceil(now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($targetDate)->startOfDay(), false)) : 0;
                        $isUrgent = $daysLeft <= 15;
                    @endphp
                    <div class="m3-contract-item" style="{{ $isUrgent ? 'border-left: 4px solid #ef4444;' : 'border-left: 4px solid #ea580c;' }}">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span class="m3-contract-emp-title">
                                    {{ $emp->full_name }}
                                </span>
                                <span style="font-size: 0.725rem; background: rgba(14, 104, 56, 0.12); color: #0E6838; padding: 0.15rem 0.6rem; border-radius: 9999px; font-weight: 700; display: inline-flex; align-items: center; gap: 0.3rem;" class="dark:bg-emerald-950 dark:text-emerald-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 12px; height: 12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                    </svg>
                                    <span>{{ $emp->branch?->name }}</span>
                                </span>
                            </div>
                            <div class="m3-contract-emp-meta">
                                {{ $emp->position?->name }} &bull; {{ $emp->department?->name }} &bull; NIP: {{ $emp->nip }}
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="text-align: right;">
                                <div style="font-size: 0.725rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.3px; color: {{ $isUrgent ? '#ef4444' : '#ea580c' }};">
                                    {{ $isProbation ? 'Habis Probation: ' . ($targetDate ? \Carbon\Carbon::parse($targetDate)->format('d M Y') : '-') : 'Habis Kontrak: ' . ($targetDate ? \Carbon\Carbon::parse($targetDate)->format('d M Y') : '-') }}
                                </div>
                                <div class="m3-contract-days-label">
                                    {{ $daysLeft >= 0 ? "Sisa {$daysLeft} Hari Lagi" : "Telah Lewat " . abs($daysLeft) . " Hari" }}
                                </div>
                            </div>
                            <a href="{{ url('/admin/employees/' . $emp->id . '/edit') }}" class="m3-eval-btn">
                                <span>Evaluasi</span>
                                <span>&rarr;</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
