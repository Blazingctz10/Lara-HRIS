<x-filament-widgets::widget>
    @php
        $announcements = $this->getAnnouncements();
    @endphp

    <style>
        .m3-memo-card {
            border-radius: 16px !important;
            background: var(--m3-surface, #ffffff) !important;
            border: 1px solid var(--m3-outline-variant, #e2e8f0) !important;
            padding: 1.25rem 1.5rem !important;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
            margin-bottom: 1rem !important;
            content-visibility: auto;
            contain-intrinsic-size: auto 320px;
        }

        @supports not (content-visibility: auto) {
            .m3-memo-card {
                contain: layout style paint;
            }
        }

        :is(.dark) .m3-memo-card {
            background: #141E18 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
        }

        .m3-memo-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0.9rem;
            border-bottom: 1px solid var(--m3-outline-variant, #e2e8f0);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        :is(.dark) .m3-memo-head {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-memo-head-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .m3-memo-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(14, 104, 56, 0.1);
            color: #0E6838;
            flex-shrink: 0;
        }

        .m3-memo-icon-box svg {
            width: 18px !important;
            height: 18px !important;
            flex-shrink: 0;
        }

        :is(.dark) .m3-memo-icon-box {
            background: rgba(74, 222, 128, 0.12);
            color: #4ADE80;
        }

        .m3-memo-h3 {
            font-size: 0.975rem;
            font-weight: 800;
            color: var(--m3-on-surface, #0f1e14);
            margin: 0;
            letter-spacing: -0.01em;
        }

        :is(.dark) .m3-memo-h3 {
            color: #FFFFFF;
        }

        .m3-memo-desc {
            font-size: 0.775rem;
            color: #64748b;
            margin: 0.1rem 0 0 0;
        }

        :is(.dark) .m3-memo-desc {
            color: #94a3b8;
        }

        .m3-memo-count-pill {
            font-size: 0.725rem;
            font-weight: 700;
            color: #0E6838;
            background: rgba(14, 104, 56, 0.1);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px !important;
        }

        :is(.dark) .m3-memo-count-pill {
            color: #4ADE80;
            background: rgba(74, 222, 128, 0.12);
        }

        /* Clean Divider List (Zero Card-in-Card Clutter) */
        .m3-memo-list {
            display: flex;
            flex-direction: column;
        }

        .m3-memo-row {
            padding: 1rem 0.25rem;
            border-bottom: 1px solid rgba(148, 163, 184, 0.15);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .m3-memo-row:last-child {
            border-bottom: none;
            padding-bottom: 0.25rem;
        }

        :is(.dark) .m3-memo-row {
            border-bottom-color: rgba(255, 255, 255, 0.06);
        }

        /* Priority Pills */
        .m3-tag-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.15rem 0.55rem;
            font-size: 0.68rem;
            font-weight: 800;
            border-radius: 9999px !important;
            letter-spacing: 0.3px;
        }

        .m3-tag-urgent {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
        }
        :is(.dark) .m3-tag-urgent {
            background: rgba(248, 113, 113, 0.16);
            color: #f87171;
        }

        .m3-tag-important {
            background: rgba(234, 88, 12, 0.12);
            color: #ea580c;
        }
        :is(.dark) .m3-tag-important {
            background: rgba(251, 146, 60, 0.16);
            color: #fb923c;
        }

        .m3-tag-normal {
            background: rgba(14, 104, 56, 0.1);
            color: #0E6838;
        }
        :is(.dark) .m3-tag-normal {
            background: rgba(74, 222, 128, 0.14);
            color: #4ADE80;
        }

        .m3-memo-meta-text {
            font-size: 0.725rem;
            color: #64748b;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        :is(.dark) .m3-memo-meta-text {
            color: #94a3b8;
        }

        .m3-item-title {
            font-size: 0.925rem;
            font-weight: 700;
            color: var(--m3-on-surface, #0f1e14);
            margin-top: 0.3rem;
            line-height: 1.35;
        }

        :is(.dark) .m3-item-title {
            color: #FFFFFF !important;
        }

        .m3-item-body {
            font-size: 0.825rem;
            color: #475569;
            margin-top: 0.25rem;
            line-height: 1.55;
        }

        :is(.dark) .m3-item-body {
            color: #cbd5e1 !important;
        }

        .m3-attach-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #0E6838;
            background: rgba(14, 104, 56, 0.08);
            padding: 0.35rem 0.8rem;
            border-radius: 9999px !important;
            text-decoration: none;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.15s ease;
        }

        .m3-attach-btn svg {
            width: 14px !important;
            height: 14px !important;
            flex-shrink: 0;
        }

        .m3-attach-btn:hover {
            background: rgba(14, 104, 56, 0.15);
        }

        .m3-attach-btn:focus-visible {
            outline: 3px solid #0E6838;
            outline-offset: 2px;
        }

        :is(.dark) .m3-attach-btn {
            color: #4ADE80 !important;
            background: rgba(74, 222, 128, 0.12);
        }

        :is(.dark) .m3-attach-btn:focus-visible {
            outline-color: #4ADE80;
        }

        @media (forced-colors: active) {
            .m3-memo-card,
            .m3-attach-btn {
                border: 1px solid ButtonBorder !important;
            }
            .m3-attach-btn:focus-visible {
                outline: 3px solid Highlight;
            }
        }

        @media (max-width: 640px) {
            .m3-memo-row {
                flex-direction: column;
            }
        }
    </style>

    <div class="m3-memo-card">
        <!-- Header Section -->
        <div class="m3-memo-head">
            <div class="m3-memo-head-left">
                <div class="m3-memo-icon-box">
                    <x-heroicon-o-megaphone />
                </div>
                <div>
                    <h3 class="m3-memo-h3">
                        Papan Pengumuman & Memo Internal
                    </h3>
                    <p class="m3-memo-desc">
                        Informasi terbaru dan edaran resmi PT BPR Banda Raya
                    </p>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span class="m3-memo-count-pill">
                    {{ $announcements->count() }} Memo Aktif
                </span>
                <a href="{{ url('/portal/announcements') }}" class="m3-attach-btn">
                    <span>Lihat Semua</span>
                    <x-heroicon-o-arrow-right aria-hidden="true" />
                </a>
            </div>
        </div>

        @if($announcements->isEmpty())
            <div style="text-align: center; padding: 1.75rem 1rem; opacity: 0.7; font-size: 0.85rem;">
                Belum ada pengumuman atau memo baru saat ini.
            </div>
        @else
            <div class="m3-memo-list">
                @foreach($announcements as $item)
                    @php
                        $isUrgent = $item->priority === 'urgent';
                        $isImportant = $item->priority === 'important';
                    @endphp
                    <div class="m3-memo-row">
                        <div style="min-width: 0; flex: 1;">
                            <!-- Top Meta Line -->
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.45rem;">
                                @if($isUrgent)
                                    <span class="m3-tag-pill m3-tag-urgent">Mendesak</span>
                                @elseif($isImportant)
                                    <span class="m3-tag-pill m3-tag-important">Penting</span>
                                @else
                                    <span class="m3-tag-pill m3-tag-normal">Info</span>
                                @endif

                                <span class="m3-memo-meta-text">
                                    {{ $item->published_at ? $item->published_at->format('d M Y') : '' }}
                                    &bull;
                                    {{ $item->branch ? $item->branch->name : 'Seluruh Cabang' }}
                                </span>
                            </div>

                            <!-- Title -->
                            <div class="m3-item-title">
                                {{ $item->title }}
                            </div>

                            <!-- Content -->
                            <div class="m3-item-body">
                                {!! \App\Models\Announcement::sanitizeContent($item->content) !!}
                            </div>
                        </div>

                        <!-- Attachment Action -->
                        @if($item->attachment_url)
                            <div style="margin-top: 0.25rem;">
                                <a href="{{ asset('storage/' . $item->attachment_url) }}" target="_blank" rel="noopener noreferrer" class="m3-attach-btn">
                                    <x-heroicon-o-arrow-down-tray aria-hidden="true" />
                                    <span>Lampiran PDF</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
