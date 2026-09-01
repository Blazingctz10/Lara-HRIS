<x-filament-widgets::widget>
    @php
        $expiringEmployees = $this->getExpiringContracts();
    @endphp

    <style>
        .bpr-contract-box {
            border-radius: 1rem;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1.25rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.25rem;
        }

        :is(.dark) .bpr-contract-box {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .bpr-contract-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .bpr-contract-head-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .bpr-contract-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 0.5rem;
            background: rgba(243, 122, 32, 0.15);
            color: #F37A20;
            flex-shrink: 0;
        }

        :is(.dark) .bpr-contract-icon-box {
            background: rgba(243, 122, 32, 0.25);
            color: #fb923c;
        }

        .bpr-contract-svg {
            width: 20px;
            height: 20px;
            max-width: 20px;
            max-height: 20px;
        }

        .bpr-contract-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        :is(.dark) .bpr-contract-title {
            color: #ffffff;
        }

        .bpr-contract-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0.15rem 0 0 0;
        }

        :is(.dark) .bpr-contract-desc {
            color: #94a3b8;
        }

        .bpr-contract-item {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.85rem 1rem;
            border-radius: 0.6rem;
            background: #f8fafc;
            border: 1px solid rgba(0, 0, 0, 0.06);
            margin-bottom: 0.5rem;
        }

        :is(.dark) .bpr-contract-item {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.06);
        }
    </style>

    <div class="bpr-contract-box">
        <div class="bpr-contract-header">
            <div class="bpr-contract-head-left">
                <div class="bpr-contract-icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="bpr-contract-svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="bpr-contract-title">
                        Peringatan Jatuh Tempo Kontrak & Masa Percobaan (60 Hari ke Depan)
                    </h3>
                    <p class="bpr-contract-desc">
                        Monitoring otomatis pegawai PKWT / Masa Percobaan yang perlu evaluasi perpanjangan
                    </p>
                </div>
            </div>
            <span style="font-size: 0.75rem; font-weight: 700; color: #F37A20; background: rgba(243, 122, 32, 0.12); padding: 0.25rem 0.65rem; border-radius: 9999px;">
                {{ $expiringEmployees->count() }} Pegawai Perlu Evaluasi
            </span>
        </div>

        @if($expiringEmployees->isEmpty())
            <div style="text-align: center; padding: 1.5rem; color: #94a3b8; font-size: 0.85rem;">
                ✅ Tidak ada kontrak kerja atau masa probation pegawai yang akan berakhir dalam 60 hari ke depan.
            </div>
        @else
            <div>
                @foreach($expiringEmployees as $emp)
                    @php
                        $daysLeft = now()->diffInDays($emp->contract_end_date ?? $emp->probation_end_date, false);
                        $isUrgent = $daysLeft <= 15;
                    @endphp
                    <div class="bpr-contract-item" style="{{ $isUrgent ? 'border-left: 4px solid #ef4444;' : 'border-left: 4px solid #F37A20;' }}">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-size: 0.9rem; font-weight: 800; color: #0f172a;" class="dark:text-white">
                                    {{ $emp->full_name }}
                                </span>
                                <span style="font-size: 0.7rem; background: rgba(14, 104, 56, 0.1); color: #0E6838; padding: 0.1rem 0.4rem; border-radius: 4px; font-weight: 700;">
                                    🏢 {{ $emp->branch?->name }}
                                </span>
                            </div>
                            <div style="font-size: 0.775rem; color: #64748b; margin-top: 0.2rem;" class="dark:text-slate-300">
                                {{ $emp->position?->name }} &bull; {{ $emp->department?->name }} &bull; NIP: {{ $emp->nip }}
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="text-align: right;">
                                <div style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: {{ $isUrgent ? '#ef4444' : '#F37A20' }};">
                                    {{ $emp->contract_end_date ? 'Habis Kontrak: ' . $emp->contract_end_date->format('d M Y') : 'Habis Probation: ' . $emp->probation_end_date->format('d M Y') }}
                                </div>
                                <div style="font-size: 0.775rem; font-weight: 800; color: #0f172a;" class="dark:text-white">
                                    {{ $daysLeft >= 0 ? "Sisa {$daysLeft} Hari Lagi" : "Telah Lewat " . abs($daysLeft) . " Hari" }}
                                </div>
                            </div>
                            <a href="{{ url('/admin/employees/' . $emp->id . '/edit') }}" style="font-size: 0.75rem; font-weight: 700; color: #0E6838; background: rgba(14,104,56,0.1); padding: 0.35rem 0.65rem; border-radius: 0.375rem; text-decoration: none;" class="dark:text-emerald-300 dark:bg-emerald-900/40">
                                Evaluasi &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
