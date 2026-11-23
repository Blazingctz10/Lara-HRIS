<!-- Early Theme Initializer & UI Stacking Controller -->
<script>
    (function() {
        const theme = localStorage.getItem('theme') || localStorage.getItem('hris_theme') || 'system';
        let isDark = false;
        if (theme === 'dark') {
            isDark = true;
        } else if (theme === 'system') {
            isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        }
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function elevateChain(target) {
            if (!target || !target.closest) return;
            let curr = target;
            while (curr && curr !== document.body) {
                if (curr.classList && (
                    curr.classList.contains('fi-section') ||
                    curr.classList.contains('fi-sc-section') ||
                    curr.classList.contains('fi-sc-component') ||
                    curr.classList.contains('fi-grid-col') ||
                    curr.classList.contains('fi-input-wrp') ||
                    curr.classList.contains('fi-fo-field-wrp') ||
                    curr.classList.contains('fi-fo-field-wrp-ctn')
                )) {
                    curr.classList.add('fi-elevated-active');
                }
                curr = curr.parentElement;
            }
        }

        function clearElevation() {
            const activePopups = document.querySelectorAll(
                '[aria-expanded="true"], .choices.is-open, .choices__list--dropdown.is-active, .fi-fo-date-time-picker-panel:not([style*="display: none"]), .fi-dropdown-panel:not([style*="display: none"])'
            );
            document.querySelectorAll('.fi-elevated-active').forEach(node => {
                let keep = false;
                if (document.activeElement && document.activeElement !== document.body && node.contains(document.activeElement)) {
                    keep = true;
                }
                activePopups.forEach(popup => {
                    if (node.contains(popup)) keep = true;
                });
                if (!keep) node.classList.remove('fi-elevated-active');
            });
        }

        document.addEventListener('focusin', function(e) {
            clearElevation();
            elevateChain(e.target);
        }, true);

        document.addEventListener('click', function() {
            setTimeout(function() {
                clearElevation();
                document.querySelectorAll(
                    '[aria-expanded="true"], .choices.is-open, .choices__list--dropdown.is-active, .fi-fo-date-time-picker-panel:not([style*="display: none"]), .fi-dropdown-panel:not([style*="display: none"])'
                ).forEach(el => elevateChain(el));
                if (document.activeElement && document.activeElement !== document.body) {
                    elevateChain(document.activeElement);
                }
            }, 10);
        }, true);
    })();
</script>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* =============================================================================
   PT BPR BANDA RAYA — UNIVERSAL CLEAN BENTO SAAS DESIGN SYSTEM
   Single Source of Truth for Light & Dark Mode across /admin and /portal
   ============================================================================= */

:root {
    color-scheme: light dark;

    --bento-font: "Plus Jakarta Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", system-ui, sans-serif;

    /* Light Mode Surfaces */
    --bento-bg: #F6F8F7;
    --bento-surface: #FFFFFF;
    --bento-surface-subtle: #F1F5F3;
    --bento-surface-hover: #EBF1ED;
    --bento-border: #E4EAE6;
    --bento-border-strong: #CBD7CF;

    /* Light Mode Text */
    --bento-text: #0F1713;
    --bento-text-secondary: #3B4D42;
    --bento-text-muted: #64748B;

    /* Brand Accents */
    --bento-primary: #0E6838;
    --bento-primary-hover: #0B532C;
    --bento-primary-soft: rgba(14, 104, 56, 0.08);
    --bento-on-primary: #FFFFFF;
    --bento-warning: #EA580C;
    --bento-warning-soft: rgba(234, 88, 12, 0.09);
    --bento-amber: #CA8A04;

    /* Elevation */
    --bento-shadow-card: 0 1px 2px rgba(15, 23, 19, 0.04), 0 1px 1px rgba(15, 23, 19, 0.02);
    --bento-shadow-hover: 0 4px 12px -2px rgba(15, 23, 19, 0.06);
    --bento-shadow-overlay: 0 16px 36px -8px rgba(15, 23, 19, 0.14), 0 4px 12px -2px rgba(15, 23, 19, 0.06);

    /* Token Aliases for Custom Widgets */
    --m3-bg: var(--bento-bg);
    --m3-surface: var(--bento-surface);
    --m3-surface-container: var(--bento-surface-subtle);
    --m3-surface-container-low: var(--bento-surface-subtle);
    --m3-on-surface: var(--bento-text);
    --m3-on-surface-variant: var(--bento-text-secondary);
    --m3-outline: var(--bento-border-strong);
    --m3-outline-variant: var(--bento-border);
    --m3-primary: var(--bento-primary);
    --m3-on-primary: var(--bento-on-primary);
    --m3-secondary: var(--bento-warning);
    --m3-tertiary: var(--bento-amber);
}

.dark {
    /* Dark Mode Surfaces */
    --bento-bg: #0B100D;
    --bento-surface: #131B16;
    --bento-surface-subtle: #19241D;
    --bento-surface-hover: #202E25;
    --bento-border: rgba(255, 255, 255, 0.08);
    --bento-border-strong: rgba(74, 222, 128, 0.28);

    /* Dark Mode Text */
    --bento-text: #F3F6F4;
    --bento-text-secondary: #C5D1C9;
    --bento-text-muted: #8A9A90;

    /* Brand Accents */
    --bento-primary: #4ADE80;
    --bento-primary-hover: #22C55E;
    --bento-primary-soft: rgba(74, 222, 128, 0.12);
    --bento-on-primary: #052E16;
    --bento-warning: #FB923C;
    --bento-warning-soft: rgba(251, 146, 60, 0.14);
    --bento-amber: #FACC15;

    /* Elevation */
    --bento-shadow-card: 0 1px 3px rgba(0, 0, 0, 0.35);
    --bento-shadow-hover: 0 6px 16px -4px rgba(0, 0, 0, 0.45);
    --bento-shadow-overlay: 0 20px 44px -8px rgba(0, 0, 0, 0.65);
}

/* =============================================================================
   1. BASE TYPOGRAPHY & CANVAS
   ============================================================================= */
html, body {
    font-family: var(--bento-font) !important;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

body.fi-body {
    background: var(--bento-bg) !important;
    color: var(--bento-text) !important;
    min-height: 100vh;
}

.fi-wi-stats-overview-stat-value,
.fi-ta-cell,
.tabular-nums {
    font-variant-numeric: tabular-nums;
}

/* Remove double Tailwind rings on Filament containers */
.fi-section,
.fi-card,
.fi-ta-ctn,
.fi-modal-window,
.fi-dropdown-panel,
.fi-wi-stats-overview-stat {
    --tw-ring-color: transparent !important;
    --tw-ring-shadow: 0 0 #0000 !important;
    --tw-ring-offset-shadow: 0 0 #0000 !important;
}

/* =============================================================================
   2. SIDEBAR & TOPBAR (Clean, Flat, Ergonomic)
   ============================================================================= */
.fi-sidebar {
    background: var(--bento-surface) !important;
    border-right: 1px solid var(--bento-border) !important;
    box-shadow: none !important;
}

.fi-sidebar-header {
    background: transparent !important;
    border-bottom: 1px solid var(--bento-border) !important;
}

.fi-sidebar-item-btn {
    border-radius: 12px !important;
    padding: 0.55rem 0.85rem !important;
    margin: 0.12rem 0.5rem !important;
    transition: background-color 0.15s ease, color 0.15s ease !important;
}

.fi-sidebar-item-btn:hover {
    background: var(--bento-surface-subtle) !important;
}

.fi-sidebar-item-active > .fi-sidebar-item-btn {
    background: var(--bento-primary-soft) !important;
    color: var(--bento-primary) !important;
    font-weight: 700 !important;
}

.fi-sidebar-item-active > .fi-sidebar-item-btn svg {
    color: var(--bento-primary) !important;
}

.fi-topbar {
    background: var(--bento-surface) !important;
    border-bottom: 1px solid var(--bento-border) !important;
    box-shadow: none !important;
}

.fi-topbar-ctn {
    overflow-x: visible !important;
}

.fi-topbar-end,
.fi-user-menu {
    flex-shrink: 0 !important;
}

.fi-dropdown-header span {
    white-space: normal !important;
    word-break: break-word !important;
}

/* =============================================================================
   3. BENTO CARDS, SECTIONS, STATS & TABLES (Unified 16px Radius)
   ============================================================================= */
.fi-section,
.fi-card,
.fi-ta-ctn,
.fi-wi-stats-overview-stat,
.fi-wi-chart {
    background: var(--bento-surface) !important;
    border: 1px solid var(--bento-border) !important;
    border-radius: 16px !important;
    box-shadow: var(--bento-shadow-card) !important;
}

.fi-wi-stats-overview-stat {
    transition: border-color 0.15s ease, box-shadow 0.15s ease !important;
}

.fi-wi-stats-overview-stat:hover {
    border-color: var(--bento-border-strong) !important;
    box-shadow: var(--bento-shadow-hover) !important;
}

.fi-section.fi-collapsed .fi-section-header {
    border-bottom: none !important;
}

/* Table Header & Rows */
.fi-ta-header-ctn,
.fi-ta-thead {
    background: var(--bento-surface-subtle) !important;
}

.fi-ta-row:hover {
    background: var(--bento-surface-subtle) !important;
}

/* =============================================================================
   4. INPUTS, BUTTONS, BADGES & MODALS
   ============================================================================= */
.fi-input-wrp {
    border-radius: 12px !important;
    border-color: var(--bento-border-strong) !important;
    background: var(--bento-surface) !important;
}

.fi-input-wrp:focus-within {
    border-color: var(--bento-primary) !important;
    box-shadow: 0 0 0 3px var(--bento-primary-soft) !important;
}

.fi-btn,
.fi-badge,
.fi-tabs-item {
    border-radius: 9999px !important;
}

.fi-tabs {
    background: var(--bento-surface) !important;
    border: 1px solid var(--bento-border) !important;
    border-radius: 16px !important;
    padding: 0.35rem !important;
    box-shadow: var(--bento-shadow-card) !important;
}

.fi-modal-window {
    background: var(--bento-surface) !important;
    border: 1px solid var(--bento-border) !important;
    border-radius: 24px !important;
    box-shadow: var(--bento-shadow-overlay) !important;
}

.fi-dropdown-panel {
    background: var(--bento-surface) !important;
    border: 1px solid var(--bento-border) !important;
    border-radius: 16px !important;
    box-shadow: var(--bento-shadow-overlay) !important;
}

/* =============================================================================
   5. ELEVATION & NOTIFICATION TOASTS
   ============================================================================= */
.fi-elevated-active {
    position: relative !important;
    z-index: 60 !important;
}

.fi-no {
    z-index: 999999 !important;
    top: 4.75rem !important;
}

@media (forced-colors: active) {
    .fi-section,
    .fi-card,
    .fi-ta-ctn,
    .fi-wi-stats-overview-stat,
    .fi-modal-window,
    .fi-dropdown-panel {
        border: 1px solid ButtonBorder !important;
    }
}
</style>
