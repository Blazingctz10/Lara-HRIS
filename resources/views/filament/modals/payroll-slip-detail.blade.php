<div class="p-6 bg-white dark:bg-slate-900 rounded-xl text-slate-800 dark:text-slate-100">
    <!-- Header Resmi Slip Gaji BPR -->
    <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
        <div>
            <h2 class="text-xl font-bold tracking-tight text-emerald-700 dark:text-emerald-400">PT BPR BANDA RAYA</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">SLIP GAJI DAN PENGHASILAN PEGAWAI RESMI</p>
            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Periode: {{ $record->period_label }}</p>
        </div>
        <div class="text-right">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $record->status === 'Paid' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' }}">
                {{ $record->status === 'Paid' ? '✓ DIBAYARKAN (LUNAS)' : 'DRAF / BELUM DIRILIS' }}
            </span>
            <p class="text-xs text-slate-400 mt-1">Tanggal: {{ $record->payment_date ? \Carbon\Carbon::parse($record->payment_date)->isoFormat('D MMMM Y') : '-' }}</p>
        </div>
    </div>

    <!-- Data Pribadi Pegawai -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-lg bg-slate-50 dark:bg-slate-800/50 mb-6 text-xs">
        <div>
            <span class="text-slate-400 block">NIP Pegawai</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->employee?->nip ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block">Nama Lengkap</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->employee?->full_name ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block">Jabatan & Grade</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->employee?->position?->name ?? '-' }} (Grade {{ $record->employee?->grade ?? '-' }})</span>
        </div>
        <div>
            <span class="text-slate-400 block">Unit Kerja</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->employee?->branch?->name ?? 'Kantor Pusat' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block">Status PTKP Pajak</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->ptkp_status_used ?? ($record->employee?->ptkp_status ?? 'TK/0') }}</span>
        </div>
        <div>
            <span class="text-slate-400 block">Rekening Payroll BPR</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->employee?->bank_bpr_account ?? ($record->employee?->bank_account_number ?? '-') }}</span>
        </div>
        <div>
            <span class="text-slate-400 block">Lembur Disetujui</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->overtime_hours ?? 0 }} Jam Kerja</span>
        </div>
        <div>
            <span class="text-slate-400 block">Presensi Telat</span>
            <span class="font-bold text-slate-700 dark:text-slate-200">{{ $record->late_count ?? 0 }} Kali</span>
        </div>
    </div>

    <!-- Rincian Penghasilan vs Potongan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Kolom Penerimaan (Earnings) -->
        <div class="border border-slate-200 dark:border-slate-800 rounded-lg p-4">
            <h3 class="text-sm font-bold text-emerald-700 dark:text-emerald-400 border-b border-slate-200 dark:border-slate-800 pb-2 mb-3">
                A. PENGHASILAN & PENERIMAAN (EARNINGS)
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Gaji Pokok</span>
                    <span class="font-semibold">Rp {{ number_format($record->basic_salary, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Tunjangan Jabatan</span>
                    <span class="font-semibold">Rp {{ number_format($record->position_allowance, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Tunjangan Transport</span>
                    <span class="font-semibold">Rp {{ number_format($record->transport_allowance, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Uang Makan (Meal Allowance)</span>
                    <span class="font-semibold">Rp {{ number_format($record->meal_allowance, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Uang Lembur (Overtime)</span>
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">+ Rp {{ number_format($record->overtime_pay, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Pencairan Klaim Reimbursement</span>
                    <span class="font-semibold text-emerald-600 dark:text-emerald-400">+ Rp {{ number_format($record->reimbursement_pay, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-slate-800 font-bold text-sm text-slate-800 dark:text-slate-100">
                    <span>Total Penghasilan Bruto</span>
                    <span>Rp {{ number_format($record->total_earnings, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Kolom Potongan (Deductions) -->
        <div class="border border-slate-200 dark:border-slate-800 rounded-lg p-4">
            <h3 class="text-sm font-bold text-rose-700 dark:text-rose-400 border-b border-slate-200 dark:border-slate-800 pb-2 mb-3">
                B. POTONGAN (DEDUCTIONS)
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">BPJS Ketenagakerjaan (JHT 2% + JP 1%)</span>
                    <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->bpjs_tk_deduction, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">BPJS Kesehatan (1%)</span>
                    <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->bpjs_kes_deduction, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Pajak Penghasilan (PPh 21 TER)</span>
                    <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->pph21_deduction, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Denda Keterlambatan Presensi</span>
                    <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->late_penalty_deduction, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">Potongan Pinjaman / Kasbon</span>
                    <span class="font-semibold text-rose-600 dark:text-rose-400">- Rp {{ number_format($record->loan_deduction, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between pt-2 border-t border-slate-200 dark:border-slate-800 font-bold text-sm text-rose-700 dark:text-rose-400">
                    <span>Total Potongan</span>
                    <span>- Rp {{ number_format($record->deductions, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Take Home Pay (Gaji Bersih) -->
    <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white flex flex-col md:flex-row items-center justify-between shadow-lg">
        <div>
            <span class="text-xs text-emerald-100 block uppercase tracking-wider font-semibold">GAJI BERSIH YANG DITERIMA (TAKE HOME PAY)</span>
            <h1 class="text-2xl font-black tracking-tight">Rp {{ number_format($record->net_salary, 0, ',', '.') }}</h1>
        </div>
        <div class="text-right mt-2 md:mt-0 text-xs text-emerald-100">
            <p>Dibayarkan melalui transfer internal rekening BPR Banda Raya</p>
            <p class="font-mono text-[10px] mt-0.5">Dokumen ini sah dan diterbitkan secara digital oleh Human Capital BPR</p>
        </div>
    </div>
</div>
