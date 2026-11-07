<x-filament-panels::page>
    <style>
        .m3-profile-subcard {
            background-color: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #e2e8f0);
            border-radius: 20px;
            padding: 1.5rem 1.75rem;
            margin-top: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        :is(.dark) .m3-profile-subcard {
            background-color: #111a14;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        }

        .m3-profile-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            margin-top: 0.85rem;
        }

        .m3-profile-table th {
            text-align: left;
            padding: 0.65rem 0.85rem;
            background: rgba(14, 104, 56, 0.06);
            color: #0E6838;
            font-weight: 700;
            border-bottom: 1px solid rgba(14, 104, 56, 0.15);
        }

        :is(.dark) .m3-profile-table th {
            background: rgba(255, 255, 255, 0.04);
            color: #4ade80;
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }

        .m3-profile-table td {
            padding: 0.65rem 0.85rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #334155;
        }

        :is(.dark) .m3-profile-table td {
            border-bottom-color: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
        }

        /* PA Bar Chart Precision System (Harvyn High-Contrast Dynamic Baseline) */
        .m3-pa-chart-card {
            background: #FFFFFF;
            border: 1px solid var(--m3-outline-variant, #E2E8F0);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-top: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        :is(.dark) .m3-pa-chart-card {
            background: #0B1713;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .m3-pa-chart-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        :is(.dark) .m3-pa-chart-header {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-pa-chart-title {
            font-size: 0.8125rem;
            font-weight: 700;
            color: #0E6838;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin: 0;
        }

        :is(.dark) .m3-pa-chart-title {
            color: #34D399;
        }

        .m3-pa-chart-sub {
            font-size: 0.75rem;
            color: #64748B;
        }

        .m3-pa-legend {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748B;
        }

        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .legend-item .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .dot.grade-a { background: #10B981; }
        .dot.grade-b { background: #0E6838; }
        .dot.grade-c { background: #F59E0B; }

        .m3-chart-container {
            display: flex;
            align-items: flex-end;
            gap: 1.25rem;
            padding: 0.5rem 0.25rem 0 0.25rem;
            overflow-x: auto;
        }

        .m3-chart-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1 1 0;
            min-width: 52px;
            max-width: 72px;
        }

        .m3-chart-track {
            height: 150px;
            width: 100%;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            border-bottom: 2px solid rgba(14, 104, 56, 0.2);
            padding-bottom: 2px;
        }

        :is(.dark) .m3-chart-track {
            border-bottom-color: rgba(255, 255, 255, 0.15);
        }

        .m3-chart-bar {
            width: 100%;
            border-radius: 8px 8px 0 0;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 0.4rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            cursor: pointer;
        }

        .m3-chart-bar:hover {
            filter: brightness(1.12);
            transform: translateY(-2px);
        }

        .bar-grade-a {
            background: linear-gradient(180deg, #10B981 0%, #0E6838 100%);
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
        }

        .bar-grade-b {
            background: linear-gradient(180deg, #059669 0%, #064E3B 100%);
            box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
        }

        .bar-grade-c {
            background: linear-gradient(180deg, #F59E0B 0%, #B45309 100%);
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.2);
        }

        .m3-chart-score-val {
            color: #FFFFFF;
            font-size: 0.75rem;
            font-weight: 800;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            letter-spacing: -0.02em;
        }

        .m3-chart-labels {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 0.5rem;
            gap: 0.2rem;
        }

        .m3-chart-badge {
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.1rem 0.45rem;
            border-radius: 6px;
            line-height: 1.2;
        }

        .badge-a {
            background: rgba(16, 185, 129, 0.15);
            color: #065F46;
        }
        :is(.dark) .badge-a {
            background: rgba(16, 185, 129, 0.25);
            color: #6EE7B7;
        }

        .badge-b {
            background: rgba(14, 104, 56, 0.12);
            color: #0E6838;
        }
        :is(.dark) .badge-b {
            background: rgba(52, 211, 153, 0.2);
            color: #A7F3D0;
        }

        .badge-c {
            background: rgba(245, 158, 11, 0.15);
            color: #92400E;
        }
        :is(.dark) .badge-c {
            background: rgba(245, 158, 11, 0.25);
            color: #FCD34D;
        }

        .m3-chart-year-txt {
            font-size: 0.7rem;
            font-weight: 600;
            color: #64748B;
        }

        :is(.dark) .m3-chart-year-txt {
            color: #94A3B8;
        }
    </style>

    <form wire:submit.prevent>
        {{ $this->form }}

        <div style="margin-top: 1.75rem; display: flex; justify-content: flex-end;">
            {{ $this->saveAction }}
        </div>
    </form>

    @php
        $employee = auth()->user()?->employee;
        $families = $employee ? $employee->families : collect();
        $trainings = $employee ? $employee->trainings : collect();
        $appraisals = $employee ? $employee->performanceAppraisals : collect();
    @endphp

    {{-- SEKSI 1: ANGGOTA KELUARGA --}}
    <div class="m3-profile-subcard">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div style="width: 32px; height: 32px; border-radius: 10px; background: rgba(14, 104, 56, 0.12); color: #0E6838; display: flex; align-items: center; justify-content: center;" class="dark:bg-emerald-950/60 dark:text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 800; color: #1e293b;" class="dark:text-gray-100">Data Keluarga Pegawai</h4>
                <p style="font-size: 0.78rem; color: #64748b;" class="dark:text-gray-400">Data tanggungan keluarga untuk jaminan kesehatan & tunjangan keluarga</p>
            </div>
        </div>

        @if($families->isNotEmpty())
            <div style="overflow-x: auto;">
                <table class="m3-profile-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Anggota Keluarga</th>
                            <th>NIK (16 Digit)</th>
                            <th>Hubungan</th>
                            <th>Tempat & Tanggal Lahir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($families as $idx => $fam)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td><strong>{{ $fam->name }}</strong></td>
                                <td>{{ $fam->nik ?? '-' }}</td>
                                <td>
                                    <span style="display: inline-block; padding: 0.15rem 0.5rem; border-radius: 6px; font-size: 0.72rem; font-weight: 700; background: rgba(14, 104, 56, 0.1); color: #0E6838;" class="dark:bg-emerald-950/40 dark:text-emerald-300">
                                        {{ $fam->relationship }}
                                    </span>
                                </td>
                                <td>{{ $fam->birth_place ?? '-' }}, {{ $fam->birth_date ? \Carbon\Carbon::parse($fam->birth_date)->format('d-M-Y') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="font-size: 0.85rem; color: #94a3b8; font-style: italic; margin-top: 0.75rem;">Belum ada arsip data keluarga yang terdaftar.</p>
        @endif
    </div>

    {{-- SEKSI 2: RIWAYAT PELATIHAN --}}
    <div class="m3-profile-subcard">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div style="width: 32px; height: 32px; border-radius: 10px; background: rgba(243, 122, 32, 0.15); color: #F37A20; display: flex; align-items: center; justify-content: center;" class="dark:bg-amber-950/60 dark:text-amber-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 800; color: #1e293b;" class="dark:text-gray-100">Riwayat Pelatihan & Sertifikasi (Training History)</h4>
                <p style="font-size: 0.78rem; color: #64748b;" class="dark:text-gray-400">Daftar rekam jejak pengembangan kompetensi & sertifikasi perbankan</p>
            </div>
        </div>

        @if($trainings->isNotEmpty())
            <div style="overflow-x: auto;">
                <table class="m3-profile-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelatihan</th>
                            <th>Jadwal / Waktu</th>
                            <th>Penyelenggara</th>
                            <th>Pemateri</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($trainings as $idx => $tr)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td><strong>{{ $tr->training_name }}</strong></td>
                                <td>{{ $tr->schedule ?? '-' }}</td>
                                <td>{{ $tr->organizer ?? '-' }}</td>
                                <td>{{ $tr->trainer ?? '-' }}</td>
                                <td>{{ $tr->location ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="font-size: 0.85rem; color: #94a3b8; font-style: italic; margin-top: 0.75rem;">Belum ada arsip riwayat pelatihan.</p>
        @endif
    </div>

    {{-- SEKSI 3: PERFORMANCE APPRAISAL (PA) & GRAFIK --}}
    <div class="m3-profile-subcard">
        <div style="display: flex; align-items: center; gap: 0.6rem;">
            <div style="width: 32px; height: 32px; border-radius: 10px; background: rgba(59, 130, 246, 0.15); color: #2563eb; display: flex; align-items: center; justify-content: center;" class="dark:bg-blue-950/60 dark:text-blue-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 18px; height: 18px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
            </div>
            <div>
                <h4 style="font-size: 1.05rem; font-weight: 800; color: #1e293b;" class="dark:text-gray-100">Performance Appraisal (PA) & Evaluasi Kinerja</h4>
                <p style="font-size: 0.78rem; color: #64748b;" class="dark:text-gray-400">Riwayat angka mutu penilaian kinerja tahunan resmi PT BPR Banda Raya</p>
            </div>
        </div>

        @if($appraisals->isNotEmpty())
            {{-- Visualisasi Grafik Batang Performance Appraisal --}}
            @php
                $minScore = $appraisals->min('score') ?? 0;
                $maxScore = $appraisals->max('score') ?? 4.00;
                // Skala dinamis: baseline adaptif agar perbedaan nilai tahunan (misal 3.51 vs 3.73 vs 3.93) terlihat nyata dan proporsional
                $baseline = max(0, min(3.0, floor($minScore * 2) / 2 - 0.2));
                $range = max(0.5, 4.00 - $baseline);
            @endphp
            <div class="m3-pa-chart-card">
                <div class="m3-pa-chart-header">
                    <div>
                        <p class="m3-pa-chart-title">Grafik Tren Angka Mutu Kinerja (Skala 0 - 4.00)</p>
                        <span class="m3-pa-chart-sub">Perbandingan capaian nilai tahunan pegawai secara komparatif</span>
                    </div>
                    <div class="m3-pa-legend">
                        <span class="legend-item"><span class="dot grade-a"></span> Grade A (≥3.75)</span>
                        <span class="legend-item"><span class="dot grade-b"></span> Grade B (3.50 - 3.74)</span>
                        <span class="legend-item"><span class="dot grade-c"></span> Grade C (&lt;3.50)</span>
                    </div>
                </div>

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

            {{-- Tabel Rincian PA --}}
            <div style="overflow-x: auto;">
                <table class="m3-profile-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Periode Penilaian (Tahun Buku)</th>
                            <th>Nilai Huruf</th>
                            <th>Angka Mutu</th>
                            <th>Catatan Evaluasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appraisals as $idx => $pa)
                            <tr>
                                <td>{{ $idx + 1 }}</td>
                                <td><strong>Tahun {{ $pa->period_year }}</strong></td>
                                <td>
                                    <span style="display: inline-block; padding: 0.15rem 0.55rem; border-radius: 6px; font-size: 0.75rem; font-weight: 800; {{ $pa->letter_grade === 'A' ? 'background: rgba(14, 104, 56, 0.15); color: #0E6838;' : 'background: rgba(59, 130, 246, 0.15); color: #2563eb;' }}">
                                        Grade {{ $pa->letter_grade }}
                                    </span>
                                </td>
                                <td><strong style="font-family: monospace; font-size: 0.95rem;">{{ number_format($pa->score, 2) }}</strong></td>
                                <td>{{ $pa->notes ?? 'Pencapaian target kerja sesuai KPI.' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="font-size: 0.85rem; color: #94a3b8; font-style: italic; margin-top: 0.75rem;">Belum ada arsip riwayat penilaian kinerja (PA).</p>
        @endif
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>
