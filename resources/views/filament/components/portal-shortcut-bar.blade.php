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
            style="display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.35rem 0.85rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 800; background: linear-gradient(135deg, #F37A20 0%, #ea580c 100%); color: #ffffff; text-decoration: none; box-shadow: 0 2px 8px rgba(243, 122, 32, 0.35); animation: pulse 2s infinite; white-space: nowrap;"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 15px; height: 15px;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span>Rp {{ number_format($unpaidSum, 0, ',', '.') }} Siap Dicairkan ({{ $unpaidCount }})</span>
        </a>
    @endif

    {{-- Shortcut Ajukan Cuti --}}
    <a
        href="{{ url('/portal/leave-requests/create') }}"
        title="Ajukan Permohonan Cuti Baru"
        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(14, 104, 56, 0.12); color: #0E6838; border: 1px solid rgba(14, 104, 56, 0.3); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-600/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
        </svg>
        <span>Ajukan Cuti</span>
    </a>

    {{-- Shortcut Ajukan Reimbursement --}}
    <a
        href="{{ url('/portal/reimbursements/create') }}"
        title="Ajukan Klaim Reimbursement Operasional"
        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(243, 122, 32, 0.12); color: #d96512; border: 1px solid rgba(243, 122, 32, 0.3); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-600/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.75H4.5a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 004.5 21h15a2.25 2.25 0 002.25-2.25V5.25A2.25 2.25 0 0019.5 3z" />
        </svg>
        <span>Klaim Reimbursement</span>
    </a>

    {{-- Shortcut Profil Saya --}}
    <a
        href="{{ url('/portal/my-profile') }}"
        title="Lihat & Perbarui Profil Saya"
        style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: rgba(0, 0, 0, 0.05); color: #475569; border: 1px solid rgba(0, 0, 0, 0.08); transition: all 0.2s ease; white-space: nowrap;"
        class="dark:bg-slate-800/40 dark:text-slate-300 dark:border-slate-700/30"
    >
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 15px; height: 15px;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
        </svg>
        <span>Biodata</span>
    </a>
</div>
