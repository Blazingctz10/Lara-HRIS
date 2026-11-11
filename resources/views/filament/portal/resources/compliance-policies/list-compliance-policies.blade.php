<x-filament-panels::page>
    @php
        $policies = $this->policies;
        $categoryCounts = $this->categoryCounts;
        $activePolicy = $this->getActivePolicy();
    @endphp

    <style>
        /* === Material 3 Expressive Policy Library Styling === */
        .m3-policy-hero {
            background: linear-gradient(135deg, rgba(14, 104, 56, 0.08) 0%, rgba(20, 30, 24, 0.02) 100%);
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            border-radius: 24px;
            padding: 2rem 2.25rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 2px 10px -2px rgba(14, 104, 56, 0.08);
        }

        :is(.dark) .m3-policy-hero {
            background: linear-gradient(135deg, rgba(74, 222, 128, 0.08) 0%, #141e18 100%);
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        }

        .m3-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            background: rgba(14, 104, 56, 0.12);
            color: #0E6838;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.85rem;
        }

        :is(.dark) .m3-hero-badge {
            background: rgba(74, 222, 128, 0.15);
            color: #4ade80;
        }

        .m3-hero-title {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
            color: var(--m3-on-surface, #0f1e14);
        }

        :is(.dark) .m3-hero-title {
            color: #FFFFFF;
        }

        .m3-hero-desc {
            font-size: 0.925rem;
            opacity: 0.85;
            line-height: 1.6;
            max-width: 780px;
            color: var(--m3-on-surface, #0f1e14);
        }

        :is(.dark) .m3-hero-desc {
            color: #e2e8e4;
        }

        .m3-hero-stats {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px dashed var(--m3-outline-variant, #b6cac0);
            flex-wrap: wrap;
        }

        :is(.dark) .m3-hero-stats {
            border-top-color: rgba(255, 255, 255, 0.1);
        }

        .m3-stat-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* === Search & Filter Bar === */
        .m3-filter-toolbar {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .m3-search-box {
            position: relative;
            max-width: 600px;
            width: 100%;
        }

        .m3-search-input {
            width: 100%;
            border-radius: 9999px;
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            background: var(--m3-surface, #ffffff);
            color: var(--m3-on-surface, #0f1e14);
            padding: 0.75rem 1.25rem 0.75rem 2.85rem;
            font-size: 0.9rem;
            font-weight: 500;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
            transition: all 0.2s ease;
        }

        .m3-search-input:focus {
            outline: none;
            border-color: #0E6838;
            box-shadow: 0 0 0 3px rgba(14, 104, 56, 0.18);
        }

        :is(.dark) .m3-search-input {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.12);
            color: #FFFFFF;
        }

        :is(.dark) .m3-search-input:focus {
            border-color: #4ADE80;
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        .m3-search-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            opacity: 0.6;
            pointer-events: none;
        }

        .m3-filter-pills {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .m3-pill-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.45rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            background: var(--m3-surface, #ffffff);
            color: var(--m3-on-surface, #0f1e14);
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .m3-pill-btn:hover {
            border-color: #0E6838;
            background: rgba(14, 104, 56, 0.05);
            transform: translateY(-1px);
        }

        .m3-pill-btn.active {
            background: #0E6838;
            color: #FFFFFF;
            border-color: #0E6838;
            box-shadow: 0 2px 6px rgba(14, 104, 56, 0.25);
        }

        :is(.dark) .m3-pill-btn {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }

        :is(.dark) .m3-pill-btn:hover {
            border-color: #4ADE80;
            background: rgba(74, 222, 128, 0.1);
            color: #FFFFFF;
        }

        :is(.dark) .m3-pill-btn.active {
            background: #4ADE80;
            color: #0D1410;
            border-color: #4ADE80;
        }

        .m3-pill-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            padding: 0.1rem 0.45rem;
            border-radius: 9999px;
            background: rgba(0, 0, 0, 0.08);
        }

        .m3-pill-btn.active .m3-pill-count {
            background: rgba(255, 255, 255, 0.25);
            color: #FFFFFF;
        }

        :is(.dark) .m3-pill-btn.active .m3-pill-count {
            background: rgba(0, 0, 0, 0.25);
            color: #0D1410;
        }

        /* === Policy Cards Grid === */
        .m3-policy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 1.5rem;
        }

        @media (max-width: 640px) {
            .m3-policy-grid {
                grid-template-columns: 1fr;
            }
        }

        .m3-policy-card {
            background: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            border-radius: 20px;
            padding: 1.5rem 1.65rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 2px 8px -2px rgba(14, 104, 56, 0.06), 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .m3-policy-card:hover {
            transform: translateY(-4px);
            border-color: #0E6838;
            box-shadow: 0 8px 24px -4px rgba(14, 104, 56, 0.15);
        }

        :is(.dark) .m3-policy-card {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
        }

        :is(.dark) .m3-policy-card:hover {
            border-color: #4ADE80;
            box-shadow: 0 8px 24px -4px rgba(74, 222, 128, 0.2);
        }

        .m3-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            gap: 0.75rem;
        }

        .m3-category-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .m3-badge-apu {
            background: rgba(220, 38, 38, 0.12);
            color: #dc2626;
        }

        :is(.dark) .m3-badge-apu {
            background: rgba(248, 113, 113, 0.2);
            color: #f87171;
        }

        .m3-badge-ethics {
            background: rgba(234, 88, 12, 0.12);
            color: #ea580c;
        }

        :is(.dark) .m3-badge-ethics {
            background: rgba(251, 146, 60, 0.2);
            color: #fb923c;
        }

        .m3-badge-sop {
            background: rgba(14, 104, 56, 0.12);
            color: #0E6838;
        }

        :is(.dark) .m3-badge-sop {
            background: rgba(74, 222, 128, 0.2);
            color: #4ade80;
        }

        .m3-badge-risk {
            background: rgba(59, 130, 246, 0.12);
            color: #2563eb;
        }

        :is(.dark) .m3-badge-risk {
            background: rgba(96, 165, 250, 0.2);
            color: #60a5fa;
        }

        .m3-badge-company {
            background: rgba(147, 51, 234, 0.12);
            color: #9333ea;
        }

        :is(.dark) .m3-badge-company {
            background: rgba(192, 132, 252, 0.2);
            color: #c084fc;
        }

        .m3-doc-number {
            display: inline-block;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            font-weight: 700;
            color: #0E6838;
            background: rgba(14, 104, 56, 0.08);
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            margin-bottom: 0.65rem;
            letter-spacing: 0.2px;
        }

        :is(.dark) .m3-doc-number {
            color: #4ade80;
            background: rgba(74, 222, 128, 0.12);
        }

        .m3-card-title {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1.4;
            margin-bottom: 0.75rem;
            color: var(--m3-on-surface, #0f1e14);
            letter-spacing: -0.01em;
        }

        :is(.dark) .m3-card-title {
            color: #FFFFFF;
        }

        .m3-summary-box {
            background: var(--m3-surface-container, #f0f5f2);
            border-left: 3px solid #0E6838;
            border-radius: 0 12px 12px 0;
            padding: 0.75rem 0.85rem;
            font-size: 0.825rem;
            line-height: 1.5;
            opacity: 0.9;
            margin-bottom: 1.15rem;
            color: var(--m3-on-surface, #0f1e14);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        :is(.dark) .m3-summary-box {
            background: #19261E;
            border-left-color: #4ADE80;
            color: #cbd5e1;
        }

        .m3-card-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.75rem;
            opacity: 0.75;
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px dashed var(--m3-outline-variant, #b6cac0);
        }

        :is(.dark) .m3-card-meta {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-card-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .m3-action-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #0E6838;
            color: #FFFFFF !important;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.55rem 1.15rem;
            border-radius: 9999px;
            text-decoration: none;
            transition: all 0.15s ease;
            box-shadow: 0 2px 6px rgba(14, 104, 56, 0.2);
            border: none;
            cursor: pointer;
        }

        .m3-action-btn-primary:hover {
            background: #094726;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(14, 104, 56, 0.3);
        }

        :is(.dark) .m3-action-btn-primary {
            background: #4ADE80;
            color: #0D1410 !important;
        }

        :is(.dark) .m3-action-btn-primary:hover {
            background: #38c96e;
        }

        .m3-action-btn-tonal {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(14, 104, 56, 0.1);
            color: #0E6838;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 0.55rem 1rem;
            border-radius: 9999px;
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .m3-action-btn-tonal:hover {
            background: rgba(14, 104, 56, 0.18);
            transform: translateY(-1px);
        }

        :is(.dark) .m3-action-btn-tonal {
            background: rgba(74, 222, 128, 0.15);
            color: #4ade80;
        }

        :is(.dark) .m3-action-btn-tonal:hover {
            background: rgba(74, 222, 128, 0.25);
        }

        .m3-action-btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 9999px;
            background: rgba(0, 0, 0, 0.05);
            color: var(--m3-on-surface, #0f1e14);
            border: none;
            cursor: pointer;
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .m3-action-btn-icon:hover {
            background: rgba(14, 104, 56, 0.15);
            color: #0E6838;
            transform: translateY(-1px);
        }

        :is(.dark) .m3-action-btn-icon {
            background: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
        }

        :is(.dark) .m3-action-btn-icon:hover {
            background: rgba(74, 222, 128, 0.2);
            color: #4ade80;
        }

        /* === Detail Modal === */
        .m3-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .m3-modal-container {
            background: var(--m3-surface, #ffffff);
            border: 1px solid var(--m3-outline-variant, #b6cac0);
            border-radius: 28px !important;
            max-width: 650px;
            width: 100%;
            padding: 2rem 2.25rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.25);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        :is(.dark) .m3-modal-container {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: 0 12px 48px rgba(0, 0, 0, 0.6);
        }

        /* === Empty State === */
        .m3-empty-state {
            background: var(--m3-surface, #ffffff);
            border: 1px dashed var(--m3-outline-variant, #b6cac0);
            border-radius: 24px;
            padding: 3.5rem 2rem;
            text-align: center;
            margin-top: 1.5rem;
        }

        :is(.dark) .m3-empty-state {
            background: #141e18;
            border-color: rgba(255, 255, 255, 0.1);
        }
    </style>

    <!-- 1. Hero Banner: Pusat Kebijakan & SOP Kepatuhan -->
    <div class="m3-policy-hero">
        <div class="m3-hero-badge">
            <x-heroicon-o-shield-check class="w-4 h-4" />
            Kepatuhan & Tata Kelola PT BPR Banda Raya
        </div>

        <h1 class="m3-hero-title">
            Katalog Kebijakan Kepatuhan & SOP Bank
        </h1>

        <p class="m3-hero-desc">
            Seluruh pedoman resmi, surat edaran direksi, prosedur anti-fraud, dan standar operasional perbankan yang wajib dipahami serta dipedomani oleh seluruh insan BPR Banda Raya dalam menjalankan tugas operasional sehari-hari.
        </p>

        <div class="m3-hero-stats">
            <div class="m3-stat-item">
                <x-heroicon-o-document-duplicate class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                <span>{{ $categoryCounts['all'] }} Kebijakan Resmi Berlaku</span>
            </div>
            <div class="m3-stat-item">
                <x-heroicon-o-building-office-2 class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                <span>Berlaku Kantor Pusat & Seluruh Cabang</span>
            </div>
            <div class="m3-stat-item">
                <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #16a34a;"></span>
                <span>Standar Kepatuhan OJK Terverifikasi</span>
            </div>
        </div>
    </div>

    <!-- 2. Search & Category Filters -->
    <div class="m3-filter-toolbar">
        <div class="m3-search-box">
            <x-heroicon-o-magnifying-glass class="m3-search-icon" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                class="m3-search-input"
                placeholder="Cari berdasarkan nomor SK, judul SOP, atau kata kunci..."
            />
        </div>

        <div class="m3-filter-pills">
            <button
                type="button"
                wire:click="setCategory('all')"
                class="m3-pill-btn {{ $selectedCategory === 'all' ? 'active' : '' }}"
            >
                Semua Kebijakan
                <span class="m3-pill-count">{{ $categoryCounts['all'] }}</span>
            </button>

            <button
                type="button"
                wire:click="setCategory('apu_ppt')"
                class="m3-pill-btn {{ $selectedCategory === 'apu_ppt' ? 'active' : '' }}"
            >
                APU & PPT (Regulasi OJK)
                <span class="m3-pill-count">{{ $categoryCounts['apu_ppt'] }}</span>
            </button>

            <button
                type="button"
                wire:click="setCategory('code_of_conduct')"
                class="m3-pill-btn {{ $selectedCategory === 'code_of_conduct' ? 'active' : '' }}"
            >
                Kode Etik & Anti-Fraud
                <span class="m3-pill-count">{{ $categoryCounts['code_of_conduct'] }}</span>
            </button>

            <button
                type="button"
                wire:click="setCategory('sop_operation')"
                class="m3-pill-btn {{ $selectedCategory === 'sop_operation' ? 'active' : '' }}"
            >
                SOP Layanan & Kas
                <span class="m3-pill-count">{{ $categoryCounts['sop_operation'] }}</span>
            </button>

            <button
                type="button"
                wire:click="setCategory('company_regulation')"
                class="m3-pill-btn {{ $selectedCategory === 'company_regulation' ? 'active' : '' }}"
            >
                Peraturan Perusahaan
                <span class="m3-pill-count">{{ $categoryCounts['company_regulation'] }}</span>
            </button>
        </div>
    </div>

    <!-- 3. Policy Cards Grid -->
    @if($policies->isEmpty())
        <div class="m3-empty-state">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: rgba(14, 104, 56, 0.1); color: #0E6838; margin: 0 auto 1rem; display: flex; align-items: center; justify-content: center;">
                <x-heroicon-o-shield-check class="w-8 h-8" />
            </div>
            <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 0.35rem;">
                Tidak Ada Dokumen Kebijakan Ditemukan
            </h3>
            <p style="font-size: 0.875rem; opacity: 0.75; max-width: 480px; margin: 0 auto;">
                Tidak ditemukan dokumen kebijakan yang cocok dengan kriteria pencarian atau kategori yang Anda pilih.
            </p>
        </div>
    @else
        <div class="m3-policy-grid">
            @foreach($policies as $policy)
                @php
                    $badgeClass = match($policy->category) {
                        'apu_ppt' => 'm3-badge-apu',
                        'code_of_conduct' => 'm3-badge-ethics',
                        'sop_operation' => 'm3-badge-sop',
                        'risk_management' => 'm3-badge-risk',
                        default => 'm3-badge-company',
                    };
                @endphp

                <div class="m3-policy-card">
                    <div>
                        <!-- Card Header: Category & Active status -->
                        <div class="m3-card-top">
                            <span class="m3-category-badge {{ $badgeClass }}">
                                @if($policy->category === 'apu_ppt')
                                    <x-heroicon-o-shield-exclamation class="w-3.5 h-3.5" />
                                @elseif($policy->category === 'code_of_conduct')
                                    <x-heroicon-o-scale class="w-3.5 h-3.5" />
                                @elseif($policy->category === 'sop_operation')
                                    <x-heroicon-o-banknotes class="w-3.5 h-3.5" />
                                @else
                                    <x-heroicon-o-book-open class="w-3.5 h-3.5" />
                                @endif
                                {{ $policy->category_label }}
                            </span>

                            <span style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.725rem; font-weight: 700; color: #16a34a;">
                                <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #16a34a;"></span>
                                Berlaku
                            </span>
                        </div>

                        <!-- Document Number -->
                        <div class="m3-doc-number">
                            {{ $policy->document_number }}
                        </div>

                        <!-- Title -->
                        <h2 class="m3-card-title">
                            {{ $policy->title }}
                        </h2>

                        <!-- Summary preview -->
                        @if($policy->summary)
                            <div class="m3-summary-box">
                                {{ $policy->summary }}
                            </div>
                        @endif
                    </div>

                    <div>
                        <!-- Metadata Bar -->
                        <div class="m3-card-meta">
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <x-heroicon-o-calendar class="w-4 h-4 opacity-75" />
                                Berlaku: {{ $policy->effective_date->format('d M Y') }}
                            </span>

                            <span style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <x-heroicon-o-users class="w-4 h-4 opacity-75" />
                                {{ $policy->target_audience_label }}
                            </span>
                        </div>

                        <!-- Actions Bar -->
                        <div class="m3-card-actions">
                            <button
                                type="button"
                                wire:click="openDetail({{ $policy->id }})"
                                class="m3-action-btn-tonal"
                                title="Lihat intisari dan rincian kebijakan"
                            >
                                <x-heroicon-o-information-circle class="w-4 h-4" />
                                Ringkasan
                            </button>

                            <a
                                href="{{ $policy->attachment_url }}"
                                target="_blank"
                                class="m3-action-btn-primary"
                                title="Buka berkas PDF resmi di tab baru"
                            >
                                <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                                Buka PDF
                            </a>

                            <a
                                href="{{ $policy->attachment_url }}"
                                download
                                class="m3-action-btn-icon"
                                title="Unduh Berkas PDF"
                            >
                                <x-heroicon-o-arrow-down-tray class="w-4 h-4" />
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- 4. Interactive Detail Modal (Material 3 Expressive Container) -->
    @if($activePolicy)
        <div class="m3-modal-backdrop" wire:click.self="closeDetail">
            <div class="m3-modal-container">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                    <div class="m3-doc-number" style="margin-bottom: 0;">
                        {{ $activePolicy->document_number }}
                    </div>

                    <button
                        type="button"
                        wire:click="closeDetail"
                        class="m3-action-btn-icon"
                        title="Tutup dialog"
                    >
                        <x-heroicon-o-x-mark class="w-5 h-5" />
                    </button>
                </div>

                <h2 style="font-size: 1.35rem; font-weight: 800; line-height: 1.4; margin-bottom: 1rem; color: var(--m3-on-surface, #0f1e14);">
                    {{ $activePolicy->title }}
                </h2>

                <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 1.25rem;">
                    <span class="m3-category-badge m3-badge-sop">
                        {{ $activePolicy->category_label }}
                    </span>
                    <span style="font-size: 0.8rem; opacity: 0.8;">
                        &bull; Efektif: <strong>{{ $activePolicy->effective_date->format('d M Y') }}</strong>
                    </span>
                    <span style="font-size: 0.8rem; opacity: 0.8;">
                        &bull; Lingkup: <strong>{{ $activePolicy->target_audience_label }}</strong>
                    </span>
                </div>

                <div style="background: var(--m3-surface-container, #f0f5f2); border-radius: 16px; padding: 1.25rem 1.35rem; margin-bottom: 1.75rem; font-size: 0.875rem; line-height: 1.65; color: var(--m3-on-surface, #0f1e14);">
                    <div style="font-weight: 700; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.75; margin-bottom: 0.5rem;">
                        Intisari & Ketentuan Utama Regulasi:
                    </div>
                    {!! nl2br(e($activePolicy->summary ?? 'Tidak ada ringkasan khusus untuk kebijakan ini.')) !!}
                </div>

                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="font-size: 0.775rem; opacity: 0.75;">
                        Dokumen resmi PT BPR Banda Raya
                    </div>

                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                        <button
                            type="button"
                            wire:click="closeDetail"
                            class="m3-action-btn-tonal"
                        >
                            Tutup
                        </button>

                        <a
                            href="{{ $activePolicy->attachment_url }}"
                            target="_blank"
                            class="m3-action-btn-primary"
                        >
                            <x-heroicon-o-arrow-top-right-on-square class="w-4 h-4" />
                            Buka PDF Lengkap
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
