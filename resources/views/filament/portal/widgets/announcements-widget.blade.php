<x-filament-widgets::widget>
    @php
        $announcements = $this->getAnnouncements();
    @endphp

    <style>
        .bpr-memo-box {
            border-radius: 1rem;
            background: #ffffff;
            border: 1px solid rgba(0, 0, 0, 0.08);
            padding: 1.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        :is(.dark) .bpr-memo-box {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
        }

        .bpr-memo-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        :is(.dark) .bpr-memo-head {
            border-bottom-color: rgba(255, 255, 255, 0.08);
        }

        .bpr-memo-head-left {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .bpr-memo-icon-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 0.75rem;
            background: rgba(14, 104, 56, 0.15);
            color: #0E6838;
            flex-shrink: 0;
        }

        :is(.dark) .bpr-memo-icon-circle {
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .bpr-memo-head-icon {
            width: 22px;
            height: 22px;
            max-width: 22px;
            max-height: 22px;
        }

        .bpr-memo-h3 {
            font-size: 1.1rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.01em;
        }

        :is(.dark) .bpr-memo-h3 {
            color: #ffffff;
        }

        .bpr-memo-desc {
            font-size: 0.8rem;
            color: #64748b;
            margin: 0.15rem 0 0 0;
        }

        :is(.dark) .bpr-memo-desc {
            color: #94a3b8;
        }

        .bpr-memo-count-badge {
            font-size: 0.8rem;
            font-weight: 700;
            color: #0E6838;
            background: rgba(14, 104, 56, 0.12);
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            border: 1px solid rgba(14, 104, 56, 0.25);
        }

        :is(.dark) .bpr-memo-count-badge {
            color: #34d399;
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(52, 211, 153, 0.3);
        }

        .bpr-item-card {
            border-radius: 0.75rem;
            padding: 1.15rem 1.35rem;
            margin-bottom: 0.85rem;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        :is(.dark) .bpr-item-card {
            background: #1e293b;
            border-color: rgba(255, 255, 255, 0.08);
        }

        .bpr-item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px -3px rgba(0, 0, 0, 0.1);
        }

        :is(.dark) .bpr-item-card:hover {
            box-shadow: 0 6px 20px -3px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.15);
        }

        /* Card Priority Border Styles */
        .bpr-item-card.urgent {
            border-left: 5px solid #ef4444;
            background: #fff5f5;
        }

        :is(.dark) .bpr-item-card.urgent {
            border-left: 5px solid #ef4444;
            background: #1e293b;
            box-shadow: inset 0 0 0 1px rgba(239, 68, 68, 0.2);
        }

        .bpr-item-card.important {
            border-left: 5px solid #F37A20;
            background: #fffbf5;
        }

        :is(.dark) .bpr-item-card.important {
            border-left: 5px solid #F37A20;
            background: #1e293b;
            box-shadow: inset 0 0 0 1px rgba(243, 122, 32, 0.2);
        }

        .bpr-item-card.normal {
            border-left: 5px solid #0E6838;
            background: #f4fdf7;
        }

        :is(.dark) .bpr-item-card.normal {
            border-left: 5px solid #10b981;
            background: #1e293b;
            box-shadow: inset 0 0 0 1px rgba(16, 185, 129, 0.2);
        }

        /* Tag Badges */
        .tag-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.65rem;
            font-size: 0.725rem;
            font-weight: 800;
            border-radius: 0.375rem;
            letter-spacing: 0.4px;
        }

        .tag-urgent {
            background: #ef4444;
            color: #ffffff;
        }

        .tag-important {
            background: #F37A20;
            color: #ffffff;
        }

        .tag-normal {
            background: #0E6838;
            color: #ffffff;
        }

        :is(.dark) .tag-normal {
            background: #059669;
            color: #ffffff;
        }

        .tag-meta {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.55rem;
            font-size: 0.725rem;
            font-weight: 600;
            border-radius: 0.375rem;
            background: rgba(0, 0, 0, 0.05);
            color: #475569;
        }

        :is(.dark) .tag-meta {
            background: rgba(255, 255, 255, 0.1);
            color: #cbd5e1;
        }

        .tag-branch {
            display: inline-flex;
            align-items: center;
            padding: 0.2rem 0.55rem;
            font-size: 0.725rem;
            font-weight: 700;
            border-radius: 0.375rem;
            background: rgba(14, 104, 56, 0.1);
            color: #0E6838;
        }

        :is(.dark) .tag-branch {
            background: rgba(52, 211, 153, 0.15);
            color: #6ee7b7;
        }

        /* Typography */
        .bpr-item-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            margin-top: 0.5rem;
            line-height: 1.4;
        }

        :is(.dark) .bpr-item-title {
            color: #ffffff !important;
        }

        .bpr-item-body {
            font-size: 0.85rem;
            color: #334155;
            margin-top: 0.35rem;
            line-height: 1.6;
        }

        :is(.dark) .bpr-item-body {
            color: #e2e8f0 !important;
        }

        .bpr-attach-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            font-weight: 700;
            color: #0E6838;
            margin-top: 0.65rem;
            text-decoration: underline;
            transition: color 0.2s;
        }

        :is(.dark) .bpr-attach-link {
            color: #34d399 !important;
        }

        .bpr-attach-link:hover {
            color: #F37A20;
        }
    </style>

    <div class="bpr-memo-box">
        <!-- Header Section -->
        <div class="bpr-memo-head">
            <div class="bpr-memo-head-left">
                <div class="bpr-memo-icon-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" class="bpr-memo-head-icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 1 1 0-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.786.917 1.385.917h.034c.82 0 1.488-.696 1.436-1.514l-.32-4.99m-3.52 2.804c.48-.12.955-.258 1.424-.415M10.34 6.66c.48.12.955.258 1.424.415m0 0a22.25 22.25 0 0 1 3.52 2.804l.32-4.99c.052-.818-.616-1.514-1.436-1.514h-.034c-.599 0-1.138.367-1.385.917a15.42 15.42 0 0 0-.985 2.783Z" />
                    </svg>
                </div>
                <div>
                    <h3 class="bpr-memo-h3">
                        Papan Pengumuman & Memo Direksi BPR Banda Raya
                    </h3>
                    <p class="bpr-memo-desc">
                        Surat edaran, regulasi kepatuhan OJK, dan informasi internal operasional bank
                    </p>
                </div>
            </div>
            <span class="bpr-memo-count-badge">
                {{ $announcements->count() }} Memo Aktif
            </span>
        </div>

        @if($announcements->isEmpty())
            <div style="text-align: center; padding: 2rem; color: #94a3b8; font-size: 0.875rem;">
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
                    <div class="bpr-item-card {{ $cardType }}">
                        <!-- Top Badges -->
                        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.5rem;">
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem;">
                                @if($isUrgent)
                                    <span class="tag-pill tag-urgent">🚨 MENDESAK</span>
                                @elseif($isImportant)
                                    <span class="tag-pill tag-important">⚠️ PENTING</span>
                                @else
                                    <span class="tag-pill tag-normal">ℹ️ INFORMASI</span>
                                @endif

                                <span class="tag-meta">
                                    🗓️ {{ $item->published_at ? $item->published_at->format('d M Y') : '' }}
                                </span>

                                @if($item->branch)
                                    <span class="tag-branch">
                                        📍 {{ $item->branch->name }}
                                    </span>
                                @else
                                    <span class="tag-branch">
                                        🌐 Nasional (Semua Cabang)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Title -->
                        <div class="bpr-item-title">
                            {{ $item->title }}
                        </div>

                        <!-- Content -->
                        <div class="bpr-item-body">
                            {!! $item->content !!}
                        </div>

                        <!-- Attachment -->
                        @if($item->attachment_url)
                            <div>
                                <a href="{{ asset('storage/' . $item->attachment_url) }}" target="_blank" class="bpr-attach-link">
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
