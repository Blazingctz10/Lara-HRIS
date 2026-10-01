@php
    $employee = auth()->user()?->employee;
    $unpaidReimbursements = $employee
        ? \App\Models\Reimbursement::where('employee_id', $employee->id)->where('status', 'Approved_Pincab')->get()
        : collect();
    $unpaidCount = $unpaidReimbursements->count();
    $unpaidSum = (float) $unpaidReimbursements->sum('amount');
@endphp

<div class="m3-portal-shortcut-bar" style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: nowrap; overflow-x: auto; padding: 0.2rem 0; scrollbar-width: none;">
    {{-- Notifikasi Khusus: Reimbursement yang Sudah Di-ACC Pincab tapi Belum Dicairkan --}}
    @if($unpaidCount > 0)
        <a
            href="{{ url('/portal/reimbursements') }}"
            title="Ada dana reimbursement yang sudah disetujui Pincab dan siap dicairkan oleh kasir!"
            style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 800; background: linear-gradient(135deg, #F37A20 0%, #ea580c 100%); color: #ffffff; text-decoration: none; box-shadow: 0 2px 8px rgba(243, 122, 32, 0.35); animation: pulse 2s infinite; white-space: nowrap;"
        >
            <span>💵</span>
            <span>Rp {{ number_format($unpaidSum, 0, ',', '.') }} Siap Dicairkan ({{ $unpaidCount }})</span>
        </a>
    @endif

    {{-- Shortcut Ajukan Cuti --}}
    <a
        href="{{ url('/portal/leave-requests/create') }}"
        title="Ajukan Permohonan Cuti Baru"
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(14, 104, 56, 0.12); color: #0E6838; border: 1px solid rgba(14, 104, 56, 0.3); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-600/30"
    >
        <span>🌴</span>
        <span>Ajukan Cuti</span>
    </a>

    {{-- Shortcut Ajukan Reimbursement --}}
    <a
        href="{{ url('/portal/reimbursements/create') }}"
        title="Ajukan Klaim Reimbursement Operasional"
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(243, 122, 32, 0.12); color: #d96512; border: 1px solid rgba(243, 122, 32, 0.3); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-600/30"
    >
        <span>🧾</span>
        <span>Klaim Reimbursement</span>
    </a>

    {{-- Shortcut Profil Saya --}}
    <a
        href="{{ url('/portal/my-profile') }}"
        title="Lihat & Perbarui Profil Saya"
        style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(0, 0, 0, 0.05); color: #475569; border: 1px solid rgba(0, 0, 0, 0.08); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700/30"
    >
        <span>👤</span>
        <span>Biodata</span>
    </a>
</div>
