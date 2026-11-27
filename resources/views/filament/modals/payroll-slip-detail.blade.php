<div class="bpr-slip-root">
    <style>
        .bpr-slip-root {
            font-family: "Inter", "Segoe UI", system-ui, -apple-system, sans-serif;
            font-variant-numeric: tabular-nums;
            color: var(--bento-text, #0f172a);
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
            line-height: 1.45;
        }

        .bpr-slip-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #0E6838;
        }
        :is(.dark) .bpr-slip-header {
            border-bottom-color: #4ADE80;
        }

        .bpr-slip-brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .bpr-slip-logo {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(14, 104, 56, 0.12);
            border: 1px solid rgba(14, 104, 56, 0.25);
            color: #0E6838;
            font-weight: 900;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            letter-spacing: -0.02em;
        }
        :is(.dark) .bpr-slip-logo {
            background: rgba(74, 222, 128, 0.14);
            border-color: rgba(74, 222, 128, 0.3);
            color: #4ADE80;
        }

        .bpr-slip-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 800;
            color: #0E6838;
            letter-spacing: -0.01em;
        }
        :is(.dark) .bpr-slip-title {
            color: #4ADE80;
        }

        .bpr-slip-subtitle {
            margin: 0.1rem 0 0;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--bento-text-muted, #64748b);
        }

        .bpr-slip-period {
            margin: 0.2rem 0 0;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--bento-text, #1e293b);
        }

        .bpr-slip-status-wrap {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.35rem;
        }

        .bpr-slip-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .bpr-slip-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .bpr-slip-print-btn:hover {
            background: #e2e8f0;
        }
        :is(.dark) .bpr-slip-print-btn {
            background: #1e293b;
            color: #e2e8f0;
            border-color: rgba(255, 255, 255, 0.14);
        }
        :is(.dark) .bpr-slip-print-btn:hover {
            background: #334155;
        }
        .bpr-slip-print-btn svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .bpr-slip-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.35rem 0.8rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .bpr-slip-badge-paid {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .bpr-slip-badge-draft {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }
        :is(.dark) .bpr-slip-badge-paid {
            background: rgba(16, 185, 129, 0.18);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.35);
        }
        :is(.dark) .bpr-slip-badge-draft {
            background: rgba(245, 158, 11, 0.18);
            color: #fcd34d;
            border-color: rgba(245, 158, 11, 0.35);
        }

        .bpr-slip-meta {
            margin: 0;
            font-size: 0.72rem;
            color: var(--bento-text-muted, #64748b);
        }

        .bpr-slip-info-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem 1rem;
            padding: 1rem;
            border-radius: 14px;
            background: var(--bento-chrome, #f4f7f5);
            border: 1px solid var(--bento-border, #d5e0d9);
        }
        @media (max-width: 720px) {
            .bpr-slip-info-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        .bpr-slip-info-label {
            display: block;
            font-size: 0.68rem;
            color: var(--bento-text-muted, #64748b);
            margin-bottom: 0.15rem;
        }
        .bpr-slip-info-val {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--bento-text, #0f172a);
        }
        .bpr-slip-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        }

        .bpr-slip-cols {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }
        @media (max-width: 720px) {
            .bpr-slip-cols {
                grid-template-columns: 1fr;
            }
        }

        .bpr-slip-card {
            border-radius: 16px;
            border: 1px solid var(--bento-border, #d5e0d9);
            background: var(--bento-surface, #f9fbfa);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .bpr-slip-card-head-earn {
            padding: 0.65rem 1rem;
            background: rgba(14, 104, 56, 0.1);
            border-bottom: 1px solid var(--bento-border, #d5e0d9);
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #0E6838;
        }
        :is(.dark) .bpr-slip-card-head-earn {
            background: rgba(74, 222, 128, 0.14);
            color: #4ADE80;
        }

        .bpr-slip-card-head-deduct {
            padding: 0.65rem 1rem;
            background: rgba(225, 29, 72, 0.09);
            border-bottom: 1px solid var(--bento-border, #d5e0d9);
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #be123c;
        }
        :is(.dark) .bpr-slip-card-head-deduct {
            background: rgba(251, 113, 133, 0.14);
            color: #fda4af;
        }

        .bpr-slip-rows {
            padding: 0.25rem 1rem;
        }
        .bpr-slip-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.55rem 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.18);
            font-size: 0.78rem;
        }
        .bpr-slip-row:last-child {
            border-bottom: none;
        }
        .bpr-slip-row-label {
            color: var(--bento-text-muted, #475569);
        }
        .bpr-slip-row-val {
            font-weight: 600;
            color: var(--bento-text, #0f172a);
            white-space: nowrap;
        }
        .bpr-slip-val-plus {
            color: #0E6838;
        }
        :is(.dark) .bpr-slip-val-plus {
            color: #4ADE80;
        }
        .bpr-slip-val-minus {
            color: #be123c;
        }
        :is(.dark) .bpr-slip-val-minus {
            color: #fda4af;
        }

        .bpr-slip-card-foot-earn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: rgba(14, 104, 56, 0.07);
            border-top: 1px solid var(--bento-border, #d5e0d9);
            font-size: 0.84rem;
            font-weight: 800;
            color: #0E6838;
        }
        :is(.dark) .bpr-slip-card-foot-earn {
            background: rgba(74, 222, 128, 0.1);
            color: #4ADE80;
        }

        .bpr-slip-card-foot-deduct {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 1rem;
            background: rgba(225, 29, 72, 0.06);
            border-top: 1px solid var(--bento-border, #d5e0d9);
            font-size: 0.84rem;
            font-weight: 800;
            color: #be123c;
        }
        :is(.dark) .bpr-slip-card-foot-deduct {
            background: rgba(251, 113, 133, 0.1);
            color: #fda4af;
        }

        .bpr-slip-net-banner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.15rem 1.35rem;
            border-radius: 16px;
            background: linear-gradient(135deg, #0E6838 0%, #094D29 100%);
            color: #ffffff;
            border: 1px solid #084022;
        }
        .bpr-slip-net-label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            color: #bbf7d0;
        }
        .bpr-slip-net-amount {
            margin: 0.15rem 0 0;
            font-size: 1.65rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: #ffffff;
        }
        .bpr-slip-net-right {
            text-align: right;
            font-size: 0.74rem;
            color: #dcfce7;
        }
        .bpr-slip-net-right p {
            margin: 0.12rem 0;
        }
        @media (max-width: 640px) {
            .bpr-slip-net-right {
                text-align: left;
            }
            .bpr-slip-status-wrap {
                align-items: flex-start;
            }
        }

        @media print {
            body * { visibility: hidden !important; }
            .bpr-slip-root, .bpr-slip-root * { visibility: visible !important; }
            .bpr-slip-root {
                position: fixed !important;
                inset: 0 !important;
                padding: 24px !important;
                background: #ffffff !important;
                color: #0f172a !important;
                z-index: 999999 !important;
            }
            .bpr-slip-no-print { display: none !important; }
        }
    </style>

    {{-- Header Kop Slip Gaji Resmi PT BPR Banda Raya --}}
    <div class="bpr-slip-header">
        <div class="bpr-slip-brand">
            <div class="bpr-slip-logo">BPR</div>
            <div>
                <h2 class="bpr-slip-title">PT BPR BANDA RAYA</h2>
                <p class="bpr-slip-subtitle">Slip Gaji &amp; Penghasilan Resmi Pegawai</p>
                <p class="bpr-slip-period">Periode: {{ $record->period_label }}</p>
            </div>
        </div>

        <div class="bpr-slip-status-wrap">
            <div class="bpr-slip-actions">
                <button type="button" onclick="window.print()" class="bpr-slip-print-btn bpr-slip-no-print">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 7.034H5.25" />
                    </svg>
                    Cetak / PDF
                </button>
                <span class="bpr-slip-badge {{ $record->status === 'Paid' ? 'bpr-slip-badge-paid' : 'bpr-slip-badge-draft' }}">
                    {{ $record->status === 'Paid' ? '✓ DIBAYARKAN (LUNAS)' : 'DRAF / BELUM DIRILIS' }}
                </span>
            </div>
            <p class="bpr-slip-meta">
                Tanggal Rilis: <strong>{{ $record->payment_date ? \Carbon\Carbon::parse($record->payment_date)->translatedFormat('d F Y') : '-' }}</strong>
            </p>
        </div>
    </div>

    {{-- Identitas Pegawai --}}
    <div class="bpr-slip-info-grid">
        <div>
            <span class="bpr-slip-info-label">NIP Pegawai</span>
            <span class="bpr-slip-info-val bpr-slip-mono">{{ $record->employee?->nip ?? '-' }}</span>
        </div>
        <div>
            <span class="bpr-slip-info-label">Nama Lengkap</span>
            <span class="bpr-slip-info-val">{{ $record->employee?->full_name ?? '-' }}</span>
        </div>
        <div>
            <span class="bpr-slip-info-label">Jabatan &amp; Grade</span>
            <span class="bpr-slip-info-val">{{ $record->employee?->position?->name ?? '-' }} (Grade {{ $record->employee?->grade ?? '-' }})</span>
        </div>
        <div>
            <span class="bpr-slip-info-label">Unit Kantor</span>
            <span class="bpr-slip-info-val">{{ $record->employee?->branch?->name ?? 'Kantor Pusat' }}</span>
        </div>
        <div>
            <span class="bpr-slip-info-label">Status PTKP</span>
            <span class="bpr-slip-info-val">{{ $record->ptkp_status_used ?? ($record->employee?->ptkp_status ?? 'TK/0') }}</span>
        </div>
        <div>
            <span class="bpr-slip-info-label">No. Rekening BPR</span>
            <span class="bpr-slip-info-val bpr-slip-mono">{{ $record->employee?->bank_bpr_account ?? ($record->employee?->bank_account_number ?? '-') }}</span>
        </div>
        <div>
            <span class="bpr-slip-info-label">Lembur Disetujui</span>
            <span class="bpr-slip-info-val">{{ $record->overtime_hours ?? 0 }} Jam Kerja</span>
        </div>
        <div>
            <span class="bpr-slip-info-label">Keterlambatan</span>
            <span class="bpr-slip-info-val">{{ $record->late_count ?? 0 }} Kali</span>
        </div>
    </div>

    {{-- Rincian Penghasilan vs Potongan --}}
    <div class="bpr-slip-cols">
        {{-- Kolom Penerimaan --}}
        <div class="bpr-slip-card">
            <div>
                <div class="bpr-slip-card-head-earn">A. Penghasilan &amp; Penerimaan</div>
                <div class="bpr-slip-rows">
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Gaji Pokok</span>
                        <span class="bpr-slip-row-val">Rp {{ number_format($record->basic_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Tunjangan Jabatan</span>
                        <span class="bpr-slip-row-val">Rp {{ number_format($record->position_allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Tunjangan Transport</span>
                        <span class="bpr-slip-row-val">Rp {{ number_format($record->transport_allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Uang Makan</span>
                        <span class="bpr-slip-row-val">Rp {{ number_format($record->meal_allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Uang Lembur (Overtime)</span>
                        <span class="bpr-slip-row-val bpr-slip-val-plus">+ Rp {{ number_format($record->overtime_pay, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Pencairan Reimbursement</span>
                        <span class="bpr-slip-row-val bpr-slip-val-plus">+ Rp {{ number_format($record->reimbursement_pay, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="bpr-slip-card-foot-earn">
                <span>Total Penghasilan Bruto</span>
                <span>Rp {{ number_format($record->total_earnings, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Kolom Potongan --}}
        <div class="bpr-slip-card">
            <div>
                <div class="bpr-slip-card-head-deduct">B. Potongan &amp; Kewajiban</div>
                <div class="bpr-slip-rows">
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">BPJS Ketenagakerjaan (JHT 2% + JP 1%)</span>
                        <span class="bpr-slip-row-val bpr-slip-val-minus">- Rp {{ number_format($record->bpjs_tk_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">BPJS Kesehatan (1%)</span>
                        <span class="bpr-slip-row-val bpr-slip-val-minus">- Rp {{ number_format($record->bpjs_kes_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Pajak Penghasilan (PPh 21 TER)</span>
                        <span class="bpr-slip-row-val bpr-slip-val-minus">- Rp {{ number_format($record->pph21_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Denda Keterlambatan Presensi</span>
                        <span class="bpr-slip-row-val bpr-slip-val-minus">- Rp {{ number_format($record->late_penalty_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Potongan Pinjaman / Kasbon</span>
                        <span class="bpr-slip-row-val bpr-slip-val-minus">- Rp {{ number_format($record->loan_deduction, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="bpr-slip-card-foot-deduct">
                <span>Total Potongan</span>
                <span>- Rp {{ number_format($record->deductions, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Take-Home Pay (Gaji Bersih) --}}
    <div class="bpr-slip-net-banner">
        <div>
            <span class="bpr-slip-net-label">Gaji Bersih Diterima (Take-Home Pay)</span>
            <p class="bpr-slip-net-amount">Rp {{ number_format($record->net_salary, 0, ',', '.') }}</p>
        </div>
        <div class="bpr-slip-net-right">
            <p><strong>Transfer Internal Rekening BPR Banda Raya</strong></p>
            <p class="bpr-slip-mono">No. Rek: {{ $record->employee?->bank_bpr_account ?? ($record->employee?->bank_account_number ?? '-') }}</p>
            <p style="opacity: 0.85; font-size: 0.66rem;">Diterbitkan secara digital oleh Human Capital PT BPR Banda Raya</p>
        </div>
    </div>
</div>
