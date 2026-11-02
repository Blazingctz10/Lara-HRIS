<div
    x-show="! $store.sidebar.isOpen"
    class="fi-sidebar-collapsed-brand-ctn"
    x-cloak
>
    <a
        href="{{ filament()->getHomeUrl() ?? url('/') }}"
        class="flex items-center justify-center w-full h-full"
        title="{{ filament()->getBrandName() ?? 'BPR Banda Raya' }}"
    >
        <img
            src="{{ asset('images/logo-tight.png') }}"
            alt="{{ filament()->getBrandName() ?? 'BPR Banda Raya' }}"
            class="fi-sidebar-collapsed-brand-logo"
        />
    </a>
</div>
