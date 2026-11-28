<div class="bpr-slip-root">
    <style>
        .bpr-slip-root {
            font-family: var(--bento-font, "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif);
            font-variant-numeric: tabular-nums;
            color: var(--bento-text, #0f172a);
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
            line-height: 1.45;
        }

        .bpr-slip-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding-bottom: 0.9rem;
            border-bottom: 1.5px solid #cbd5e1;
        }
        :is(.dark) .bpr-slip-header {
            border-bottom-color: rgba(255, 255, 255, 0.16);
        }

        .bpr-slip-brand {
            display: flex;
            align-items: center;
            gap: 0.9rem;
        }

        .bpr-slip-logo-img {
            height: 40px;
            width: auto;
            max-width: 210px;
            object-fit: contain;
            flex-shrink: 0;
        }
        :is(.dark) .bpr-slip-logo-img {
            background: rgba(255, 255, 255, 0.95);
            padding: 4px 10px;
            border-radius: 8px;
        }

        .bpr-slip-doc-meta {
            border-left: 1px solid var(--bento-border-strong, #cbd5e1);
            padding-left: 0.9rem;
        }

        .bpr-slip-title {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            color: var(--bento-text, #0f172a);
        }

        .bpr-slip-period {
            margin: 0.15rem 0 0;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--bento-text-muted, #475569);
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
            padding: 0.375rem 0.85rem;
            border-radius: 9999px;
            font-family: inherit;
            font-size: 0.75rem;
            font-weight: 600;
            background: transparent;
            color: var(--bento-text, #0f172a);
            border: 1px solid var(--bento-border-strong, #94a3b8);
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .bpr-slip-print-btn:hover {
            background: var(--bento-surface-hover, #f1f5f9);
        }
        .bpr-slip-print-btn svg {
            width: 14px;
            height: 14px;
            flex-shrink: 0;
        }

        .bpr-slip-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            border: 1px solid #cbd5e1;
            color: var(--bento-text, #0f172a);
            background: transparent;
        }
        :is(.dark) .bpr-slip-badge {
            border-color: rgba(255, 255, 255, 0.16);
        }

        .bpr-slip-meta {
            margin: 0;
            font-size: 0.72rem;
            color: var(--bento-text-muted, #64748b);
        }

        .bpr-slip-info-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.8rem 1rem;
            padding: 0.9rem 1rem;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: transparent;
        }
        :is(.dark) .bpr-slip-info-grid {
            border-color: rgba(255, 255, 255, 0.14);
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
            margin-bottom: 0.12rem;
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
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            background: transparent;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        :is(.dark) .bpr-slip-card {
            border-color: rgba(255, 255, 255, 0.14);
        }

        .bpr-slip-card-head {
            padding: 0.65rem 1rem;
            border-bottom: 1px solid #cbd5e1;
            font-size: 0.72rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--bento-text, #0f172a);
        }
        :is(.dark) .bpr-slip-card-head {
            border-bottom-color: rgba(255, 255, 255, 0.14);
        }

        .bpr-slip-rows {
            padding: 0.2rem 1rem;
        }
        .bpr-slip-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(148, 163, 184, 0.22);
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

        .bpr-slip-card-foot {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.7rem 1rem;
            border-top: 1px solid #cbd5e1;
            font-size: 0.82rem;
            font-weight: 800;
            color: var(--bento-text, #0f172a);
        }
        :is(.dark) .bpr-slip-card-foot {
            border-top-color: rgba(255, 255, 255, 0.14);
        }

        .bpr-slip-net-box {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 12px;
            border: 1.5px solid #475569;
            background: transparent;
        }
        :is(.dark) .bpr-slip-net-box {
            border-color: rgba(255, 255, 255, 0.25);
        }
        .bpr-slip-net-label {
            display: block;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--bento-text-muted, #475569);
        }
        .bpr-slip-net-amount {
            margin: 0.15rem 0 0;
            font-size: 1.55rem;
            font-weight: 900;
            letter-spacing: -0.02em;
            color: var(--bento-text, #0f172a);
        }
        .bpr-slip-net-right {
            text-align: right;
            font-size: 0.74rem;
            color: var(--bento-text-muted, #475569);
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
            .bpr-slip-doc-meta {
                border-left: none;
                padding-left: 0;
            }
        }

        @media print {
            body * { visibility: hidden !important; }
            .bpr-slip-root, .bpr-slip-root * {
                visibility: visible !important;
                color: #0f172a !important;
                border-color: #334155 !important;
                background: #ffffff !important;
            }
            .bpr-slip-root {
                position: fixed !important;
                inset: 0 !important;
                padding: 24px !important;
                z-index: 999999 !important;
            }
            .bpr-slip-no-print { display: none !important; }
        }
    </style>

    {{-- Kop Slip Gaji Resmi PT BPR Banda Raya --}}
    <div class="bpr-slip-header">
        <div class="bpr-slip-brand">
            <img src="{{ asset('images/logo-bpr-horizontal.png') }}" alt="PT BPR Banda Raya" class="bpr-slip-logo-img">
            <div class="bpr-slip-doc-meta">
                <h2 class="bpr-slip-title">Slip Gaji Pegawai</h2>
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
                <span class="bpr-slip-badge">
                    {{ $record->status === 'Paid' ? 'DIBAYARKAN (LUNAS)' : 'DRAF / BELUM DIRILIS' }}
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
        <div class="bpr-slip-card">
            <div>
                <div class="bpr-slip-card-head">A. Penghasilan &amp; Penerimaan</div>
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
                        <span class="bpr-slip-row-val">+ Rp {{ number_format($record->overtime_pay, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="bpr-slip-card-foot">
                <span>Total Penghasilan Bruto</span>
                <span>Rp {{ number_format($record->total_earnings, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="bpr-slip-card">
            <div>
                <div class="bpr-slip-card-head">B. Potongan &amp; Kewajiban</div>
                <div class="bpr-slip-rows">
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">BPJS Ketenagakerjaan (JHT 2% + JP 1%)</span>
                        <span class="bpr-slip-row-val">- Rp {{ number_format($record->bpjs_tk_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">BPJS Kesehatan (1%)</span>
                        <span class="bpr-slip-row-val">- Rp {{ number_format($record->bpjs_kes_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Pajak Penghasilan (PPh 21 TER)</span>
                        <span class="bpr-slip-row-val">- Rp {{ number_format($record->pph21_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Denda Keterlambatan Presensi</span>
                        <span class="bpr-slip-row-val">- Rp {{ number_format($record->late_penalty_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="bpr-slip-row">
                        <span class="bpr-slip-row-label">Potongan Pinjaman / Kasbon</span>
                        <span class="bpr-slip-row-val">- Rp {{ number_format($record->loan_deduction, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="bpr-slip-card-foot">
                <span>Total Potongan</span>
                <span>- Rp {{ number_format($record->deductions, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Take-Home Pay (Gaji Bersih) --}}
    <div class="bpr-slip-net-box">
        <div>
            <span class="bpr-slip-net-label">Gaji Bersih Diterima (Take-Home Pay)</span>
            <p class="bpr-slip-net-amount">Rp {{ number_format($record->net_salary, 0, ',', '.') }}</p>
        </div>
        <div class="bpr-slip-net-right">
            <p><strong>Transfer Internal Rekening PT BPR Banda Raya</strong></p>
            <p class="bpr-slip-mono">No. Rek: {{ $record->employee?->bank_bpr_account ?? ($record->employee?->bank_account_number ?? '-') }}</p>
            <p style="font-size: 0.66rem;">Dokumen ini sah dan diterbitkan secara digital oleh Human Capital PT BPR Banda Raya</p>
        </div>
    </div>
</div>
