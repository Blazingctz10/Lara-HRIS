<x-filament-panels::page>
    <style>
        .m3-profile-grid-2col {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.25rem;
            margin-top: 1.25rem;
        }

        @media (max-width: 640px) {
            .m3-profile-grid-2col {
                grid-template-columns: 1fr;
            }
        }

        .m3-profile-subcard {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            border-radius: 16px !important;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        :is(.dark) .m3-profile-subcard {
            background-color: #141E18;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .m3-subcard-head {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            margin-bottom: 0.85rem;
        }

        :is(.dark) .m3-subcard-head {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-subcard-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .m3-subcard-icon svg {
            width: 17px !important;
            height: 17px !important;
            flex-shrink: 0;
        }

        .m3-subcard-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: var(--m3-on-surface, #0f1e14);
            margin: 0;
            line-height: 1.3;
        }

        :is(.dark) .m3-subcard-title {
            color: #FFFFFF !important;
        }

        .m3-subcard-desc {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0.1rem 0 0 0;
        }

        :is(.dark) .m3-subcard-desc {
            color: #94a3b8 !important;
        }

        .m3-profile-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.8rem;
        }

        .m3-profile-table th {
            text-align: left;
            padding: 0.55rem 0.75rem;
            background: rgba(14, 104, 56, 0.05);
            color: #0E6838;
            font-weight: 700;
            font-size: 0.725rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1px solid rgba(14, 104, 56, 0.12);
        }

        :is(.dark) .m3-profile-table th {
            background: rgba(255, 255, 255, 0.03);
            color: #4ADE80;
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-profile-table td {
            padding: 0.55rem 0.75rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            color: #334155;
        }

        .m3-profile-table tr:last-child td {
            border-bottom: none;
        }

        :is(.dark) .m3-profile-table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
        }

        /* PA Bar Chart Clean Layout (Zero Nested Card-in-Card) */
        .m3-pa-strip {
            padding: 0.5rem 0 1.15rem 0;
            margin-bottom: 1rem;
            border-bottom: 1px dashed rgba(148, 163, 184, 0.2);
        }

        :is(.dark) .m3-pa-strip {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-pa-legend {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748B;
            flex-wrap: wrap;
        }

        :is(.dark) .m3-pa-legend {
            color: #94a3b8;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .legend-item .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
        }

        .dot.grade-a { background: #10B981; }
        .dot.grade-b { background: #0E6838; }
        .dot.grade-c { background: #F59E0B; }

        .m3-chart-container {
            display: flex;
            align-items: flex-end;
            gap: 1rem;
            padding: 0.75rem 0.25rem 0 0.25rem;
            overflow-x: auto;
        }

        .m3-chart-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1 1 0;
            min-width: 48px;
            max-width: 64px;
        }

        .m3-chart-track {
            height: 120px;
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            border-bottom: 1.5px solid rgba(14, 104, 56, 0.2);
            padding-bottom: 2px;
        }

        :is(.dark) .m3-chart-track {
            border-bottom-color: rgba(255, 255, 255, 0.15);
        }

        .m3-chart-bar {
            width: 100%;
            border-radius: 6px 6px 0 0;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 0.35rem;
            transition: all 0.2s ease;
            position: relative;
        }

        .m3-chart-bar:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
        }

        .bar-grade-a {
            background: linear-gradient(180deg, #10B981 0%, #0E6838 100%);
        }

        .bar-grade-b {
            background: linear-gradient(180deg, #059669 0%, #064E3B 100%);
        }

        .bar-grade-c {
            background: linear-gradient(180deg, #F59E0B 0%, #B45309 100%);
        }

        .m3-chart-score-val {
            color: #FFFFFF;
            font-size: 0.7rem;
            font-weight: 800;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }

        .m3-chart-labels {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 0.4rem;
            gap: 0.15rem;
        }

        .m3-chart-badge {
            font-size: 0.68rem;
            font-weight: 800;
            padding: 0.1rem 0.45rem;
            border-radius: 9999px !important;
            line-height: 1.2;
        }

        .badge-a {
            background: rgba(16, 185, 129, 0.14);
            color: #065F46;
        }
        :is(.dark) .badge-a {
            background: rgba(16, 185, 129, 0.22);
            color: #6EE7B7;
        }

        .badge-b {
            background: rgba(14, 104, 56, 0.12);
            color: #0E6838;
        }
        :is(.dark) .badge-b {
            background: rgba(52, 211, 153, 0.18);
            color: #A7F3D0;
        }

        .badge-c {
            background: rgba(245, 158, 11, 0.15);
            color: #92400E;
        }
        :is(.dark) .badge-c {
            background: rgba(245, 158, 11, 0.22);
            color: #FCD34D;
        }

        .m3-chart-year-txt {
            font-size: 0.68rem;
            font-weight: 600;
            color: #64748B;
        }

        :is(.dark) .m3-chart-year-txt {
            color: #94A3B8;
        }
    </style>

    <form wire:submit.prevent>
        {{ $this->form }}

        <div style="margin-top: 1.25rem; display: flex; justify-content: flex-end;">
            {{ $this->saveAction }}
        </div>
    </form>

    @php
        $employee = auth()->user()?->employee;
        $families = $employee ? $employee->families : collect();
        $trainings = $employee ? $employee->trainings : collect();
        $appraisals = $employee ? $employee->performanceAppraisals : collect();
    @endphp

    {{-- BARIS 1: DATA KELUARGA & RIWAYAT PELATIHAN (2 Kolom Proporsional) --}}
    <div class="m3-profile-grid-2col">
        {{-- SEKSI 1: ANGGOTA KELUARGA --}}
        <div class="m3-profile-subcard">
            <div class="m3-subcard-head">
                <div class="m3-subcard-icon" style="background: rgba(14, 104, 56, 0.1); color: #0E6838;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </div>
                <div>
                    <h4 class="m3-subcard-title">Data Keluarga Pegawai</h4>
                    <p class="m3-subcard-desc">Tanggungan keluarga & jaminan kesehatan</p>
                </div>
            </div>

            @if($families->isNotEmpty())
                <div style="overflow-x: auto;">
                    <table class="m3-profile-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Hubungan</th>
                                <th>Kelahiran</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($families as $fam)
                                <tr>
                                    <td>
                                        <strong>{{ $fam->name }}</strong>
                                        @if($fam->nik)
                                            <div style="font-size: 0.7rem; opacity: 0.65; font-family: monospace;">NIK: {{ $fam->nik }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span style="display: inline-block; padding: 0.1rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; background: rgba(14, 104, 56, 0.1); color: #0E6838;">
                                            {{ $fam->relationship }}
                                        </span>
                                    </td>
                                    <td>{{ $fam->birth_place ?? '-' }}, {{ $fam->birth_date ? \Carbon\Carbon::parse($fam->birth_date)->format('d M Y') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="font-size: 0.8rem; opacity: 0.65; margin: 0.5rem 0 0 0;">Belum ada arsip data keluarga yang terdaftar.</p>
            @endif
        </div>

        {{-- SEKSI 2: RIWAYAT PELATIHAN --}}
        <div class="m3-profile-subcard">
            <div class="m3-subcard-head">
                <div class="m3-subcard-icon" style="background: rgba(234, 88, 12, 0.12); color: #EA580C;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div>
                    <h4 class="m3-subcard-title">Riwayat Pelatihan & Sertifikasi</h4>
                    <p class="m3-subcard-desc">Pengembangan kompetensi & sertifikasi perbankan</p>
                </div>
            </div>

            @if($trainings->isNotEmpty())
                <div style="overflow-x: auto;">
                    <table class="m3-profile-table">
                        <thead>
                            <tr>
                                <th>Pelatihan</th>
                                <th>Penyelenggara</th>
                                <th>Waktu & Lokasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trainings as $tr)
                                <tr>
                                    <td><strong>{{ $tr->training_name }}</strong></td>
                                    <td>{{ $tr->organizer ?? '-' }}</td>
                                    <td>
                                        <div>{{ $tr->schedule ?? '-' }}</div>
                                        @if($tr->location)
                                            <div style="font-size: 0.7rem; opacity: 0.65;">{{ $tr->location }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="font-size: 0.8rem; opacity: 0.65; margin: 0.5rem 0 0 0;">Belum ada arsip riwayat pelatihan.</p>
            @endif
        </div>
    </div>

    {{-- BARIS 2: PERFORMANCE APPRAISAL (PA) TERPADU (Tanpa Card-in-Card) --}}
    <div class="m3-profile-subcard" style="margin-top: 1.25rem;">
        <div class="m3-subcard-head" style="justify-content: space-between; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.65rem;">
                <div class="m3-subcard-icon" style="background: rgba(14, 104, 56, 0.1); color: #0E6838;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                    </svg>
                </div>
                <div>
                    <h4 class="m3-subcard-title">Performance Appraisal (PA) & Evaluasi Kinerja</h4>
                    <p class="m3-subcard-desc">Tren angka mutu penilaian kinerja tahunan resmi PT BPR Banda Raya</p>
                </div>
            </div>

            @if($appraisals->isNotEmpty())
                <div class="m3-pa-legend">
                    <span class="legend-item"><span class="dot grade-a"></span> Grade A (≥3.75)</span>
                    <span class="legend-item"><span class="dot grade-b"></span> Grade B (3.50 - 3.74)</span>
                    <span class="legend-item"><span class="dot grade-c"></span> Grade C (&lt;3.50)</span>
                </div>
            @endif
        </div>

        @if($appraisals->isNotEmpty())
            @php
                $minScore = $appraisals->min('score') ?? 0;
                $baseline = max(0, min(3.0, floor($minScore * 2) / 2 - 0.2));
                $range = max(0.5, 4.00 - $baseline);
            @endphp
            <div class="m3-pa-strip">
                <div class="m3-chart-container">
                    @foreach($appraisals as $pa)
                        @php
                            $pct = round(min(100, max(28, (($pa->score - $baseline) / $range) * 100)));
                            $isGradeA = $pa->score >= 3.75;
                            $isGradeB = $pa->score >= 3.50 && $pa->score < 3.75;
                        @endphp
                        <div class="m3-chart-col">
                            <div class="m3-chart-track">
                                <div class="m3-chart-bar {{ $isGradeA ? 'bar-grade-a' : ($isGradeB ? 'bar-grade-b' : 'bar-grade-c') }}" 
                                     style="height: {{ $pct }}%;" 
                                     title="Tahun {{ $pa->period_year }}: Skor {{ number_format($pa->score, 2) }} (Grade {{ $pa->letter_grade }})">
                                    <span class="m3-chart-score-val">{{ number_format($pa->score, 2) }}</span>
                                </div>
                            </div>
                            <div class="m3-chart-labels">
                                <span class="m3-chart-badge {{ $isGradeA ? 'badge-a' : ($isGradeB ? 'badge-b' : 'badge-c') }}">
                                    {{ $pa->letter_grade }}
                                </span>
                                <span class="m3-chart-year-txt">
                                    {{ $pa->period_year }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="m3-profile-table">
                    <thead>
                        <tr>
                            <th>Tahun Buku</th>
                            <th>Predikat</th>
                            <th>Angka Mutu</th>
                            <th>Catatan Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appraisals as $pa)
                            <tr>
                                <td><strong>Tahun {{ $pa->period_year }}</strong></td>
                                <td>
                                    <span class="m3-chart-badge {{ $pa->score >= 3.75 ? 'badge-a' : ($pa->score >= 3.50 ? 'badge-b' : 'badge-c') }}">
                                        Grade {{ $pa->letter_grade }}
                                    </span>
                                </td>
                                <td><strong style="font-family: monospace; font-size: 0.875rem;">{{ number_format($pa->score, 2) }}</strong></td>
                                <td>{{ $pa->notes ?? 'Pencapaian target kerja sesuai KPI.' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="font-size: 0.8rem; opacity: 0.65; margin: 0.5rem 0 0 0;">Belum ada arsip riwayat penilaian kinerja (PA).</p>
        @endif
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
