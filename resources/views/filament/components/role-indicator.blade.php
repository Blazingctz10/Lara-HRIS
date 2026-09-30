@php
    $user = auth()->user();
    if (! $user) return;

    $panelId = filament()->getCurrentPanel()->getId();
    $role = $user->role;
    $employee = $user->employee;
    $branchName = $user->branch?->name ?? $employee?->branch?->name ?? 'Kantor Pusat';

    // Role metadata & M3 badge colors
    $roleConfig = match($role) {
        'super_admin' => [
            'label' => 'Administrator IT',
            'bg' => 'rgba(202, 138, 4, 0.15)',
            'color' => '#ca8a04',
            'border' => 'rgba(202, 138, 4, 0.35)',
            'icon' => 'heroicon-m-command-line',
        ],
        'hr_admin' => [
            'label' => 'Human Capital / HRD',
            'bg' => 'rgba(234, 88, 12, 0.15)',
            'color' => '#ea580c',
            'border' => 'rgba(234, 88, 12, 0.35)',
            'icon' => 'heroicon-m-user-group',
        ],
        'branch_manager' => [
            'label' => 'Pemimpin Cabang',
            'bg' => 'rgba(59, 130, 246, 0.15)',
            'color' => '#3b82f6',
            'border' => 'rgba(59, 130, 246, 0.35)',
            'icon' => 'heroicon-m-building-office-2',
        ],
        default => [
            'label' => 'Karyawan / Staf',
            'bg' => 'rgba(14, 104, 56, 0.15)',
            'color' => '#0E6838',
            'border' => 'rgba(14, 104, 56, 0.35)',
            'icon' => 'heroicon-m-identification',
        ],
    };

    $canAccessAdmin = in_array($role, ['super_admin', 'hr_admin', 'branch_manager']);
@endphp

<div class="m3-role-bar" style="display: flex; align-items: center; gap: 0.6rem; margin-right: 0.75rem;">
    <!-- Active Role Badge -->
    <div style="
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.3rem 0.85rem;
        border-radius: 9999px;
        background: {{ $roleConfig['bg'] }};
        color: {{ $roleConfig['color'] }};
        border: 1px solid {{ $roleConfig['border'] }};
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.3px;
        white-space: nowrap;
    ">
        <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $roleConfig['color'] }};"></span>
        <span>{{ $roleConfig['label'] }}</span>
        <span style="opacity: 0.5;">&bull;</span>
        <span style="font-weight: 600; opacity: 0.9;">{{ $branchName }}</span>
    </div>

    <!-- Quick Switcher to Admin / Portal for Privileged Roles -->
    @if($canAccessAdmin)
        @if($panelId === 'portal')
            <a href="{{ url('/admin') }}" style="
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.3rem 0.75rem;
                border-radius: 9999px;
                background: rgba(234, 88, 12, 0.12);
                color: #ea580c;
                border: 1px solid rgba(234, 88, 12, 0.25);
                font-size: 0.725rem;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.2s ease;
                white-space: nowrap;
            " onmouseover="this.style.background='#ea580c'; this.style.color='#ffffff';" onmouseout="this.style.background='rgba(234, 88, 12, 0.12)'; this.style.color='#ea580c';">
                <span>Panel HRD</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 12px; height: 12px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        @else
            <a href="{{ url('/portal') }}" style="
                display: inline-flex;
                align-items: center;
                gap: 0.35rem;
                padding: 0.3rem 0.75rem;
                border-radius: 9999px;
                background: rgba(14, 104, 56, 0.12);
                color: #0E6838;
                border: 1px solid rgba(14, 104, 56, 0.25);
                font-size: 0.725rem;
                font-weight: 700;
                text-decoration: none;
                transition: all 0.2s ease;
                white-space: nowrap;
            " onmouseover="this.style.background='#0E6838'; this.style.color='#ffffff';" onmouseout="this.style.background='rgba(14, 104, 56, 0.12)'; this.style.color='#0E6838';">
                <span>Portal Karyawan</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" style="width: 12px; height: 12px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        @endif
    @endif
</div>
