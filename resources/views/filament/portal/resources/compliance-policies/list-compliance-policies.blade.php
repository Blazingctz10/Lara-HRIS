<x-filament-panels::page>
    @php
        $policies = $this->policies;
        $categoryCounts = $this->categoryCounts;
        $activePolicy = $this->getActivePolicy();
    @endphp

    <style>
        /* === Sleek & Minimalist Policy Repository Styling === */
        .m3-policy-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid var(--m3-outline-variant, #E2E8F0);
        }

        :is(.dark) .m3-policy-header {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-policy-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--m3-on-surface, #0f1e14);
            margin-bottom: 0.25rem;
        }

        :is(.dark) .m3-policy-title {
            color: #FFFFFF;
        }

        .m3-policy-subtitle {
            font-size: 0.875rem;
            opacity: 0.75;
            color: var(--m3-on-surface, #0f1e14);
        }

        :is(.dark) .m3-policy-subtitle {
            color: #cbd5e1;
        }

        /* Filter & Search Bar */
        .m3-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .m3-pills-row {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            flex-wrap: wrap;
        }

        .m3-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1px solid var(--m3-outline-variant, #E2E8F0);
            background: var(--m3-surface, #ffffff);
            color: var(--m3-on-surface, #334155);
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .m3-filter-chip:hover {
            border-color: #0E6838;
            color: #0E6838;
        }

        .m3-filter-chip.active {
            background: #0E6838;
            color: #FFFFFF;
            border-color: #0E6838;
        }

        :is(.dark) .m3-filter-chip {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.1);
            color: #94a3b8;
        }

        :is(.dark) .m3-filter-chip:hover {
            border-color: #4ADE80;
            color: #4ADE80;
        }

        :is(.dark) .m3-filter-chip.active {
            background: #4ADE80;
            color: #0D1410;
            border-color: #4ADE80;
        }

        .m3-search-input-wrap {
            position: relative;
            min-width: 260px;
            max-width: 340px;
            width: 100%;
        }

        .m3-search-field {
            width: 100%;
            border-radius: 9999px;
            border: 1px solid var(--m3-outline-variant, #E2E8F0);
            background: var(--m3-surface, #ffffff);
            color: var(--m3-on-surface, #0f1e14);
            padding: 0.45rem 1rem 0.45rem 2.25rem;
            font-size: 0.825rem;
            transition: all 0.15s ease;
        }

        .m3-search-field:focus {
            outline: none;
            border-color: #0E6838;
            box-shadow: 0 0 0 2px rgba(14, 104, 56, 0.15);
        }

        :is(.dark) .m3-search-field {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
        }

        :is(.dark) .m3-search-field:focus {
            border-color: #4ADE80;
            box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.15);
        }

        .m3-search-ico {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            opacity: 0.5;
            pointer-events: none;
        }

        /* Clean Document Rows */
        .m3-doc-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .m3-doc-item {
            background: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #E2E8F0);
            border-radius: 16px;
            padding: 1.15rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.25rem;
            transition: all 0.15s ease;
        }

        .m3-doc-item:hover {
            border-color: #0E6838;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        :is(.dark) .m3-doc-item {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.06);
        }

        :is(.dark) .m3-doc-item:hover {
            border-color: #4ADE80;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25);
        }

        .m3-doc-left {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            flex: 1;
            min-width: 0;
        }

        .m3-doc-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(14, 104, 56, 0.08);
            color: #0E6838;
        }

        .m3-doc-icon svg {
            width: 20px !important;
            height: 20px !important;
            flex-shrink: 0;
        }

        .m3-btn-pdf svg {
            width: 14px !important;
            height: 14px !important;
            flex-shrink: 0;
        }

        .m3-btn-icon svg {
            width: 16px !important;
            height: 16px !important;
            flex-shrink: 0;
        }

        :is(.dark) .m3-doc-icon {
            background: rgba(74, 222, 128, 0.12);
            color: #4ade80;
        }

        .m3-meta-tag {
            font-size: 0.725rem;
            font-weight: 700;
            padding: 0.15rem 0.55rem;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .m3-meta-tag-apu {
            background: rgba(220, 38, 38, 0.1);
            color: #dc2626;
        }

        :is(.dark) .m3-meta-tag-apu {
            background: rgba(248, 113, 113, 0.15);
            color: #f87171;
        }

        .m3-meta-tag-ethics {
            background: rgba(234, 88, 12, 0.1);
            color: #ea580c;
        }

        :is(.dark) .m3-meta-tag-ethics {
            background: rgba(251, 146, 60, 0.15);
            color: #fb923c;
        }

        .m3-meta-tag-sop {
            background: rgba(14, 104, 56, 0.1);
            color: #0E6838;
        }

        :is(.dark) .m3-meta-tag-sop {
            background: rgba(74, 222, 128, 0.15);
            color: #4ade80;
        }

        .m3-meta-tag-company {
            background: rgba(147, 51, 234, 0.1);
            color: #9333ea;
        }

        :is(.dark) .m3-meta-tag-company {
            background: rgba(192, 132, 252, 0.15);
            color: #c084fc;
        }

        .m3-doc-num {
            font-family: ui-monospace, monospace;
            font-size: 0.725rem;
            opacity: 0.7;
            font-weight: 600;
        }

        .m3-doc-heading {
            font-size: 1rem;
            font-weight: 700;
            color: var(--m3-on-surface, #0f1e14);
            margin: 0.25rem 0 0.2rem;
            line-height: 1.4;
        }

        :is(.dark) .m3-doc-heading {
            color: #FFFFFF;
        }

        .m3-doc-excerpt {
            font-size: 0.825rem;
            opacity: 0.75;
            color: var(--m3-on-surface, #0f1e14);
            line-height: 1.45;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        :is(.dark) .m3-doc-excerpt {
            color: #94a3b8;
        }

        /* Actions on Right */
        .m3-doc-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }

        .m3-btn-read {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(14, 104, 56, 0.08);
            color: #0E6838;
            font-size: 0.785rem;
            font-weight: 700;
            padding: 0.45rem 0.95rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .m3-btn-read:hover {
            background: rgba(14, 104, 56, 0.15);
        }

        :is(.dark) .m3-btn-read {
            background: rgba(74, 222, 128, 0.12);
            color: #4ade80;
        }

        :is(.dark) .m3-btn-read:hover {
            background: rgba(74, 222, 128, 0.2);
        }

        .m3-btn-pdf {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: #0E6838;
            color: #FFFFFF !important;
            font-size: 0.785rem;
            font-weight: 700;
            padding: 0.45rem 1rem;
            border-radius: 9999px;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .m3-btn-pdf:hover {
            background: #094726;
        }

        :is(.dark) .m3-btn-pdf {
            background: #4ADE80;
            color: #0D1410 !important;
        }

        :is(.dark) .m3-btn-pdf:hover {
            background: #38c96e;
        }

        .m3-btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 9999px;
            background: transparent;
            color: var(--m3-on-surface, #475569);
            border: 1px solid var(--m3-outline-variant, #E2E8F0);
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .m3-btn-icon:hover {
            border-color: #0E6838;
            color: #0E6838;
        }

        :is(.dark) .m3-btn-icon {
            border-color: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }

        :is(.dark) .m3-btn-icon:hover {
            border-color: #4ADE80;
            color: #4ADE80;
        }

        @media (max-width: 768px) {
            .m3-doc-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .m3-doc-right {
                width: 100%;
                justify-content: flex-end;
                padding-top: 0.75rem;
                border-top: 1px dashed var(--m3-outline-variant, #E2E8F0);
            }
        }

        /* Clean Modal */
        .m3-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .m3-modal-box {
            background: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #E2E8F0);
            border-radius: 24px;
            max-width: 580px;
            width: 100%;
            padding: 1.75rem 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-height: 85vh;
            overflow-y: auto;
        }

        :is(.dark) .m3-modal-box {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>

    <!-- 1. Sleek Header (Clean, Zero-Clutter) -->
    <div class="m3-policy-header">
        <div>
            <h1 class="m3-policy-title">
                Kebijakan Kepatuhan & SOP Bank
            </h1>
            <p class="m3-policy-subtitle">
                Pedoman resmi, surat edaran direksi, dan regulasi operasional perbankan PT BPR Banda Raya.
            </p>
        </div>

        <div class="m3-search-input-wrap">
            <x-heroicon-o-magnifying-glass class="m3-search-ico" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="m3-search-field"
                placeholder="Cari nomor SK, judul, atau kata kunci..."
            />
        </div>
    </div>

    <!-- 2. Clean Category Pills Toolbar -->
    <div class="m3-toolbar">
        <div class="m3-pills-row">
            <button
                type="button"
                wire:click="setCategory('all')"
                class="m3-filter-chip {{ $selectedCategory === 'all' ? 'active' : '' }}"
            >
                Semua ({{ $categoryCounts['all'] }})
            </button>

            <button
                type="button"
                wire:click="setCategory('apu_ppt')"
                class="m3-filter-chip {{ $selectedCategory === 'apu_ppt' ? 'active' : '' }}"
            >
                APU & PPT ({{ $categoryCounts['apu_ppt'] }})
            </button>

            <button
                type="button"
                wire:click="setCategory('code_of_conduct')"
                class="m3-filter-chip {{ $selectedCategory === 'code_of_conduct' ? 'active' : '' }}"
            >
                Kode Etik & Anti-Fraud ({{ $categoryCounts['code_of_conduct'] }})
            </button>

            <button
                type="button"
                wire:click="setCategory('sop_operation')"
                class="m3-filter-chip {{ $selectedCategory === 'sop_operation' ? 'active' : '' }}"
            >
                SOP Layanan & Kas ({{ $categoryCounts['sop_operation'] }})
            </button>

            <button
                type="button"
                wire:click="setCategory('company_regulation')"
                class="m3-filter-chip {{ $selectedCategory === 'company_regulation' ? 'active' : '' }}"
            >
                Peraturan Perusahaan ({{ $categoryCounts['company_regulation'] }})
            </button>
        </div>
    </div>

    <!-- 3. Minimalist Document List -->
    @if($policies->isEmpty())
        <div style="background: var(--m3-surface, #ffffff); border: 1px dashed var(--m3-outline-variant, #E2E8F0); border-radius: 16px; padding: 2.5rem 1.5rem; text-align: center;">
            <p style="font-size: 0.9rem; opacity: 0.7; margin: 0;">
                Tidak ada dokumen kebijakan yang sesuai dengan pencarian Anda.
            </p>
        </div>
    @else
        <div class="m3-doc-list">
            @foreach($policies as $policy)
                @php
                    $badgeClass = match($policy->category) {
                        'apu_ppt' => 'm3-meta-tag-apu',
                        'code_of_conduct' => 'm3-meta-tag-ethics',
                        'sop_operation' => 'm3-meta-tag-sop',
                        default => 'm3-meta-tag-company',
                    };
                @endphp

                <div class="m3-doc-item">
                    <!-- Left: Icon & Text Info -->
                    <div class="m3-doc-left">
                        <div class="m3-doc-icon">
                            @if($policy->category === 'apu_ppt')
                                <x-heroicon-o-shield-check class="w-5 h-5" />
                            @elseif($policy->category === 'code_of_conduct')
                                <x-heroicon-o-scale class="w-5 h-5" />
                            @elseif($policy->category === 'sop_operation')
                                <x-heroicon-o-banknotes class="w-5 h-5" />
                            @else
                                <x-heroicon-o-document-text class="w-5 h-5" />
                            @endif
                        </div>

                        <div style="min-width: 0; flex: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span class="m3-meta-tag {{ $badgeClass }}">
                                    {{ $policy->category_label }}
                                </span>
                                <span class="m3-doc-num">{{ $policy->document_number }}</span>
                                <span style="font-size: 0.725rem; opacity: 0.6;">&bull; Berlaku: {{ $policy->effective_date->format('d M Y') }}</span>
                            </div>

                            <div class="m3-doc-heading">
                                {{ $policy->title }}
                            </div>

                            @if($policy->summary)
                                <div class="m3-doc-excerpt">
                                    {{ $policy->summary }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Right: Refined Action Buttons -->
                    <div class="m3-doc-right">
                        <button
                            type="button"
                            wire:click="openDetail({{ $policy->id }})"
                            class="m3-btn-read"
                            title="Lihat ringkasan lengkap"
                        >
                            Ringkasan
                        </button>

                        <a
                            href="{{ $policy->attachment_url }}"
                            target="_blank"
                            class="m3-btn-pdf"
                            title="Buka dokumen PDF resmi"
                        >
                            <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                            Buka PDF
                        </a>

                        <a
                            href="{{ $policy->attachment_url }}"
                            download
                            class="m3-btn-icon"
                            title="Unduh PDF"
                        >
                            <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 4. Refined Detail Modal -->
    @if($activePolicy)
        <div class="m3-modal-backdrop" wire:click.self="closeDetail">
            <div class="m3-modal-box">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
                    <span class="m3-doc-num" style="font-size: 0.8rem; font-weight: 700;">
                        {{ $activePolicy->document_number }}
                    </span>

                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="m3-btn-icon"
                        title="Tutup dialog"
                    >
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>

                <h3 style="font-size: 1.15rem; font-weight: 800; line-height: 1.4; margin-bottom: 0.75rem; color: var(--m3-on-surface, #0f1e14);">
                    {{ $activePolicy->title }}
                </h3>

                <div style="font-size: 0.8rem; opacity: 0.75; margin-bottom: 1.25rem;">
                    Kategori: <strong>{{ $activePolicy->category_label }}</strong> &bull; Efektif: <strong>{{ $activePolicy->effective_date->format('d M Y') }}</strong> &bull; Lingkup: <strong>{{ $activePolicy->target_audience_label }}</strong>
                </div>

                <div style="background: var(--m3-surface-container, rgba(0,0,0,0.03)); border-radius: 12px; padding: 1rem 1.15rem; font-size: 0.85rem; line-height: 1.6; color: var(--m3-on-surface, #0f1e14); margin-bottom: 1.5rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.7; margin-bottom: 0.35rem;">
                        Ringkasan Pokok:
                    </div>
                    {!! nl2br(e($activePolicy->summary ?? 'Tidak ada ringkasan tertulis.')) !!}
                </div>

                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 0.5rem;">
                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="m3-btn-read"
                    >
                        Tutup
                    </button>

                    <a
                        href="{{ $activePolicy->attachment_url }}"
                        target="_blank"
                        class="m3-btn-pdf"
                    >
                        <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                        Buka Dokumen PDF
                    </a>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
