<x-filament-widgets::widget>
    @php
        $announcements = $this->getAnnouncements();
    @endphp

    <style>
        .m3-memo-card {
            border-radius: 24px !important;
            background: #ffffff !important;
            border: 1px solid var(--m3-outline-variant, #B6CAC0) !important;
            padding: 1.75rem !important;
            box-shadow: 0 2px 8px -2px rgba(14, 104, 56, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04) !important;
            margin-bottom: 1.5rem !important;
            transition: all 0.25s ease;
        }

        :is(.dark) .m3-memo-card {
            background: #141e18 !important;
            border-color: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.35) !important;
        }

        .m3-memo-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.35rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--m3-outline-variant, #B6CAC0);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        :is(.dark) .m3-memo-head {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .m3-memo-head-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .m3-memo-icon-box {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            background: #d1eadb;
            color: #0E6838;
            flex-shrink: 0;
        }

        :is(.dark) .m3-memo-icon-box {
            background: #0b4f27;
            color: #4ade80;
        }

        .m3-memo-head-icon {
            width: 22px;
            height: 22px;
        }

        .m3-memo-h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #131a15;
            margin: 0;
            letter-spacing: -0.01em;
        }

        :is(.dark) .m3-memo-h3 {
            color: #e2e8e4;
        }

        .m3-memo-desc {
            font-size: 0.8rem;
            color: #526156;
            margin: 0.2rem 0 0 0;
        }

        :is(.dark) .m3-memo-desc {
            color: #9eaba3;
        }

        .m3-memo-count-pill {
            font-size: 0.775rem;
            font-weight: 800;
            color: #0E6838;
            background: #d1eadb;
            padding: 0.35rem 0.95rem;
            border-radius: 9999px;
            letter-spacing: 0.3px;
        }

        :is(.dark) .m3-memo-count-pill {
            color: #bbf7d0;
            background: #0b4f27;
        }

        .m3-memo-item {
            border-radius: 18px;
            padding: 1.25rem 1.45rem;
            margin-bottom: 0.85rem;
            border: 1px solid #B6CAC0;
            background: #DFEAE2;
            transition: all 0.2s ease;
        }

        :is(.dark) .m3-memo-item {
            background: #19261e;
            border-color: rgba(255, 255, 255, 0.06);
        }

        .m3-memo-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.06);
        }

        :is(.dark) .m3-memo-item:hover {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.12);
        }

        /* Card Priority Border Styles */
        .m3-memo-item.urgent {
            border-left: 5px solid #ef4444;
            background: #FEECEB;
        }

        :is(.dark) .m3-memo-item.urgent {
            border-left: 5px solid #ef4444;
            background: #19261e;
            box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.2);
        }

        .m3-memo-item.important {
            border-left: 5px solid #ea580c;
            background: #FEF3E7;
        }

        :is(.dark) .m3-memo-item.important {
            border-left: 5px solid #fb923c;
            background: #19261e;
            box-shadow: inset 0 0 0 1px rgba(243, 122, 32, 0.2);
        }

        .m3-memo-item.normal {
            border-left: 5px solid #0E6838;
            background: #E8F4EC;
        }

        :is(.dark) .m3-memo-item.normal {
            border-left: 5px solid #4ade80;
            background: #19261e;
            box-shadow: inset 0 0 0 1px rgba(74, 222, 128, 0.2);
        }

        /* Tag Badges (M3 Full Pills) */
        .m3-tag-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.725rem;
            font-weight: 800;
            border-radius: 9999px;
            letter-spacing: 0.4px;
        }

        .m3-tag-urgent {
            background: #ef4444;
            color: #ffffff;
        }

        .m3-tag-important {
            background: #ea580c;
            color: #ffffff;
        }

        .m3-tag-normal {
            background: #0E6838;
            color: #ffffff;
        }

        :is(.dark) .m3-tag-normal {
            background: #059669;
            color: #ffffff;
        }

        .m3-tag-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            font-size: 0.725rem;
            font-weight: 600;
            border-radius: 9999px;
            background: rgba(0, 0, 0, 0.05);
            color: #526156;
        }

        :is(.dark) .m3-tag-meta {
            background: rgba(255, 255, 255, 0.08);
            color: #9eaba3;
        }

        .m3-tag-branch {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            font-size: 0.725rem;
            font-weight: 700;
            border-radius: 9999px;
            background: rgba(14, 104, 56, 0.12);
            color: #0E6838;
        }

        :is(.dark) .m3-tag-branch {
            background: rgba(74, 222, 128, 0.15);
            color: #a7f3d0;
        }

        /* Typography */
        .m3-item-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #131a15;
            margin-top: 0.55rem;
            line-height: 1.4;
        }

        :is(.dark) .m3-item-title {
            color: #e2e8e4 !important;
        }

        .m3-item-body {
            font-size: 0.875rem;
            color: #404b44;
            margin-top: 0.4rem;
            line-height: 1.6;
        }

        :is(.dark) .m3-item-body {
            color: #cbd5e1 !important;
        }

        .m3-attach-link {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.825rem;
            font-weight: 700;
            color: #0E6838;
            margin-top: 0.75rem;
            text-decoration: underline;
            transition: color 0.2s;
        }

        :is(.dark) .m3-attach-link {
            color: #4ade80 !important;
        }

        .m3-attach-link:hover {
            color: #ea580c;
        }
    </style>

    <div class="m3-memo-card">
        <!-- Header Section -->
        <div class="m3-memo-head">
            <div class="m3-memo-head-left">
                <div class="m3-memo-icon-box">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="m3-memo-head-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.786.917 1.385.917h.034c.82 0 1.488-.696 1.436-1.514l-.32-4.99m-3.52 2.804c.48-.12.955-.258 1.424-.415M10.34 6.66c.48.12.955.258 1.424.415m0 0a22.25 22.25 0 0 1 3.52 2.804l.32-4.99c.052-.818-.616-1.514-1.436-1.514h-.034c-.599 0-1.138.367-1.385.917a15.42 15.42 0 0 0-.985 2.783Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="m3-memo-h3">
                        Papan Pengumuman & Memo Direksi BPR Banda Raya
                    </h3>
                    <p class="m3-memo-desc">
                        Surat edaran, regulasi kepatuhan OJK, dan informasi internal operasional bank
                    </p>
                </div>
            </div>
            <span class="m3-memo-count-pill">
                {{ $announcements->count() }} Memo Aktif
            </span>
        </div>

        @if($announcements->isEmpty())
            <div style="text-align: center; padding: 2rem; color: #526156; font-size: 0.875rem;" class="dark:text-slate-400">
                Belum ada pengumuman atau memo baru saat ini.
            </div>
        @else
            <div>
                @foreach($announcements as $item)
                    @php
                        $isUrgent = $item->priority === 'urgent';
                        $isImportant = $item->priority === 'important';
                        $cardType = $isUrgent ? 'urgent' : ($isImportant ? 'important' : 'normal');
                    @endphp
                    <div class="m3-memo-item {{ $cardType }}">
                        <!-- Top Badges -->
                        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem;">
                                @if($isUrgent)
                                    <span class="m3-tag-pill m3-tag-urgent">MENDESAK</span>
                                @elseif($isImportant)
                                    <span class="m3-tag-pill m3-tag-important">PENTING</span>
                                @else
                                    <span class="m3-tag-pill m3-tag-normal">INFORMASI</span>
                                @endif

                                <span class="m3-tag-meta">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 12px; height: 12px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    <span>{{ $item->published_at ? $item->published_at->format('d M Y') : '' }}</span>
                                </span>

                                @if($item->branch)
                                    <span class="m3-tag-branch">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                        </svg>
                                        <span>{{ $item->branch->name }}</span>
                                    </span>
                                @else
                                    <span class="m3-tag-branch">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 12px; height: 12px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                        </svg>
                                        <span>Nasional (Semua Cabang)</span>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="m3-item-title">
                            {{ $item->title }}
                        </div>

                        <!-- Content -->
                        <div class="m3-item-body">
                            {!! $item->content !!}
                        </div>

                        <!-- Attachment -->
                        @if($item->attachment_url)
                            <div>
                                <a href="{{ asset('storage/' . $item->attachment_url) }}" target="_blank" class="m3-attach-link">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 16px; height: 16px;">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <span>Unduh Lampiran Surat Edaran (.PDF)</span>
                                </a>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
