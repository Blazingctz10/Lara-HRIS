<div class="bpr-slip-root space-y-5 text-slate-800 dark:text-slate-100" style="font-variant-numeric: tabular-nums;">
    <style>
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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b-2 border-[#0E6838] dark:border-emerald-500">
        <div class="flex items-start gap-3">
            <div class="w-11 h-11 rounded-2xl bg-[#0E6838]/10 dark:bg-emerald-500/15 text-[#0E6838] dark:text-emerald-400 flex items-center justify-center shrink-0 font-black text-base tracking-tight border border-[#0E6838]/20">
                BPR
            </div>
            <div>
                <h2 class="text-lg font-extrabold tracking-tight text-[#0E6838] dark:text-emerald-400 leading-tight">
                    PT BPR BANDA RAYA
                </h2>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    Slip Gaji &amp; Penghasilan Resmi Pegawai
                </p>
                <p class="text-xs font-bold text-slate-700 dark:text-slate-200 mt-0.5">
                    Periode: {{ $record->period_label }}
                </p>
            </div>
        </div>

        <div class="flex flex-col sm:items-end gap-1.5">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="window.print()"
                    class="bpr-slip-no-print inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 dark:text-slate-200 transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18.75 7.034H5.25" />
                    </svg>
                    Cetak / PDF
                </button>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $record->status === 'Paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300 border border-amber-200 dark:border-amber-800' }}">
                    {{ $record->status === 'Paid' ? '✓ DIBAYARKAN (LUNAS)' : 'DRAF / BELUM DIRILIS' }}
                </span>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">
                Tanggal Rilis: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $record->payment_date ? \Carbon\Carbon::parse($record->payment_date)->translatedFormat('d F Y') : '-' }}</span>
            </p>
        </div>
    </div>

    {{-- Identitas Pegawai --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3.5 p-4 rounded-2xl bg-[#F4F7F5] dark:bg-slate-800/60 border border-[#D5E0D9] dark:border-slate-700/80 text-xs">
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">NIP Pegawai</span>
            <span class="font-mono font-bold text-slate-800 dark:text-slate-100">{{ $record->employee?->nip ?? '-' }}</span>
        </div>
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Nama Lengkap</span>
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $record->employee?->full_name ?? '-' }}</span>
        </div>
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Jabatan &amp; Grade</span>
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $record->employee?->position?->name ?? '-' }} (Grade {{ $record->employee?->grade ?? '-' }})</span>
        </div>
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Unit Kantor</span>
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $record->employee?->branch?->name ?? 'Kantor Pusat' }}</span>
        </div>
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Status PTKP</span>
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $record->ptkp_status_used ?? ($record->employee?->ptkp_status ?? 'TK/0') }}</span>
        </div>
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">No. Rekening BPR</span>
            <span class="font-mono font-bold text-slate-800 dark:text-slate-100">{{ $record->employee?->bank_bpr_account ?? ($record->employee?->bank_account_number ?? '-') }}</span>
        </div>
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Lembur Disetujui</span>
            <span class="font-bold text-slate-800 dark:text-slate-100">{{ $record->overtime_hours ?? 0 }} Jam Kerja</span>
        </div>
        <div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400 block">Keterlambatan</span>
            <span class="font-bold {{ ($record->late_count ?? 0) > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-800 dark:text-slate-100' }}">{{ $record->late_count ?? 0 }} Kali</span>
        </div>
    </div>

    {{-- Rincian Penghasilan vs Potongan --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        {{-- Kolom Penerimaan --}}
        <div class="rounded-2xl border border-[#D5E0D9] dark:border-slate-700/80 bg-[#F9FBFA] dark:bg-slate-900/70 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-4 py-2.5 bg-[#0E6838]/10 dark:bg-emerald-500/15 border-b border-[#D5E0D9] dark:border-slate-700/80">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-[#0E6838] dark:text-emerald-400">
                        A. Penghasilan &amp; Penerimaan
                    </h3>
                </div>
                <div class="divide-y divide-slate-200/70 dark:divide-slate-800 px-4 text-xs">
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Gaji Pokok</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-100">Rp {{ number_format($record->basic_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Tunjangan Jabatan</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-100">Rp {{ number_format($record->position_allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Tunjangan Transport</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-100">Rp {{ number_format($record->transport_allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Uang Makan</span>
                        <span class="font-semibold text-slate-800 dark:text-slate-100">Rp {{ number_format($record->meal_allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Uang Lembur (Overtime)</span>
                        <span class="font-semibold text-[#0E6838] dark:text-emerald-400">+ Rp {{ number_format($record->overtime_pay, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Pencairan Reimbursement</span>
                        <span class="font-semibold text-[#0E6838] dark:text-emerald-400">+ Rp {{ number_format($record->reimbursement_pay, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center px-4 py-3 bg-[#0E6838]/5 dark:bg-emerald-500/10 border-t border-[#D5E0D9] dark:border-slate-700/80 font-bold text-xs sm:text-sm text-[#0E6838] dark:text-emerald-300">
                <span>Total Penghasilan Bruto</span>
                <span>Rp {{ number_format($record->total_earnings, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Kolom Potongan --}}
        <div class="rounded-2xl border border-[#D5E0D9] dark:border-slate-700/80 bg-[#F9FBFA] dark:bg-slate-900/70 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-4 py-2.5 bg-rose-500/10 dark:bg-rose-500/15 border-b border-[#D5E0D9] dark:border-slate-700/80">
                    <h3 class="text-xs font-extrabold uppercase tracking-wider text-rose-700 dark:text-rose-400">
                        B. Potongan &amp; Kewajiban
                    </h3>
                </div>
                <div class="divide-y divide-slate-200/70 dark:divide-slate-800 px-4 text-xs">
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">BPJS Ketenagakerjaan (JHT 2% + JP 1%)</span>
                        <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->bpjs_tk_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">BPJS Kesehatan (1%)</span>
                        <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->bpjs_kes_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Pajak Penghasilan (PPh 21 TER)</span>
                        <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->pph21_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Denda Keterlambatan Presensi</span>
                        <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->late_penalty_deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-2.5">
                        <span class="text-slate-600 dark:text-slate-400">Potongan Pinjaman / Kasbon</span>
                        <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->loan_deduction, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-between items-center px-4 py-3 bg-rose-500/5 dark:bg-rose-500/10 border-t border-[#D5E0D9] dark:border-slate-700/80 font-bold text-xs sm:text-sm text-rose-700 dark:text-rose-400">
                <span>Total Potongan</span>
                <span>- Rp {{ number_format($record->deductions, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- Take-Home Pay (Gaji Bersih) --}}
    <div class="p-5 rounded-2xl bg-[#0E6838] dark:bg-emerald-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3 border border-[#084022] dark:border-emerald-700">
        <div>
            <span class="text-[11px] text-emerald-100 block uppercase tracking-wider font-semibold">
                Gaji Bersih Diterima (Take-Home Pay)
            </span>
            <p class="text-2xl sm:text-3xl font-black tracking-tight mt-0.5">
                Rp {{ number_format($record->net_salary, 0, ',', '.') }}
            </p>
        </div>
        <div class="sm:text-right text-xs text-emerald-100/90 space-y-0.5">
            <p class="font-semibold text-white">Transfer Internal Rekening BPR Banda Raya</p>
            <p class="font-mono text-[11px]">No. Rek: {{ $record->employee?->bank_bpr_account ?? ($record->employee?->bank_account_number ?? '-') }}</p>
            <p class="text-[10px] text-emerald-200/80">Diterbitkan secara digital oleh Human Capital PT BPR Banda Raya</p>
        </div>
    </div>
</div>
