<!-- Early Theme Initializer & UI Stacking Controller (Robust Light/Dark/System Sync) -->
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

        // Active Field & Section Ancestor Chain Elevation System (guarantees opened dropdowns/calendars are always above following sections)
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
                    if (node.contains(popup)) {
                        keep = true;
                    }
                });
                if (!keep) {
                    node.classList.remove('fi-elevated-active');
                }
            });
        }

        document.addEventListener('focusin', function(e) {
            clearElevation();
            elevateChain(e.target);
        }, true);

        document.addEventListener('click', function(e) {
            setTimeout(function() {
                clearElevation();
                const activeTriggers = document.querySelectorAll(
                    '[aria-expanded="true"], .choices.is-open, .choices__list--dropdown.is-active, .fi-fo-date-time-picker-panel:not([style*="display: none"]), .fi-dropdown-panel:not([style*="display: none"])'
                );
                activeTriggers.forEach(el => {
                    elevateChain(el);
                });
                if (document.activeElement && document.activeElement !== document.body) {
                    elevateChain(document.activeElement);
                }
            }, 10);
        }, true);
    })();
</script>

<!-- Apple VisionOS / macOS Liquid Glass System with High-Precision Light & Dark Ergonomics -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet"></noscript>

<style>
/* =============================================================================
   PT BPR BANDA RAYA - CORE HRIS
   APPLE LIQUID GLASS DESIGN SYSTEM (HIGH-CONTRAST LIGHT & DARK EDITIONS)
   - Solves Light-Mode Washed-Out Edge Blending (Razor-sharp perimeter definition)
   - Preserves Dark-Mode VisionOS Luminous Obsidian Styling
   - Complete --m3-* and --glass-* token harmonization
   - Specular Top-Rim Light Bevel (Inset 1px 0)
   - Ergonomics First: Uncompromised WCAG AAA Readability
   ============================================================================= */

:root {
    /* Fonts */
    --m3-font-sans: -apple-system, BlinkMacSystemFont, "SF Pro Display", "SF Pro Text", "Plus Jakarta Sans", system-ui, sans-serif;
    --m3-font-display: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Plus Jakarta Sans", sans-serif;
    --glass-font-sans: var(--m3-font-sans);
    --glass-font-display: var(--m3-font-display);

    /* =========================================================================
       LIGHT MODE: TACTILE APPLE FROSTED GLASS (CRISP EDGES, ZERO WASHOUT)
       ========================================================================= */
    --glass-bg-mesh: 
        radial-gradient(at 0% 0%, rgba(14, 104, 56, 0.16) 0px, transparent 50%),
        radial-gradient(at 100% 0%, rgba(243, 122, 32, 0.12) 0px, transparent 45%),
        radial-gradient(at 50% 100%, rgba(253, 184, 19, 0.09) 0px, transparent 55%),
        radial-gradient(at 100% 100%, rgba(14, 104, 56, 0.14) 0px, transparent 50%),
        linear-gradient(145deg, #DFEAE3 0%, #D5E3DA 50%, #DCE8E0 100%);

    --m3-bg: #DCE8E0;

    /* Light Mode Glass Surfaces */
    --glass-surface: rgba(255, 255, 255, 0.88);
    --glass-surface-solid: #FFFFFF;
    --glass-surface-subtle: rgba(235, 244, 238, 0.92);
    --glass-surface-hover: #FFFFFF;
    --glass-surface-active: rgba(220, 237, 226, 0.95);

    /* M3 compatibility tokens */
    --m3-surface: var(--glass-surface);
    --m3-surface-dim: rgba(215, 228, 220, 0.85);
    --m3-surface-bright: #FFFFFF;
    --m3-surface-container-lowest: rgba(255, 255, 255, 0.75);
    --m3-surface-container-low: rgba(238, 245, 240, 0.88);
    --m3-surface-container: rgba(228, 239, 232, 0.92);
    --m3-surface-container-high: rgba(218, 232, 224, 0.94);
    --m3-surface-container-highest: rgba(206, 224, 213, 0.96);

    /* Razor-Sharp Light Mode Borders (Fixes Invisible Borders) */
    --glass-border: rgba(14, 104, 56, 0.18);
    --glass-border-subtle: rgba(14, 104, 56, 0.10);
    --glass-border-strong: rgba(14, 104, 56, 0.35);

    /* Text & Legibility: Deep Obsidian & Charcoal (Zero Eye-Strain, AAA Contrast) */
    --glass-text-main: #0B1910;
    --glass-text-secondary: #1F3326;
    --glass-text-muted: #4B6354;
    --m3-on-surface: var(--glass-text-main);
    --m3-on-surface-variant: var(--glass-text-secondary);
    --m3-outline: rgba(14, 104, 56, 0.35);
    --m3-outline-variant: rgba(14, 104, 56, 0.18);

    /* Corporate Accents (PT BPR Banda Raya Deep Emerald) */
    --glass-primary: #0E6838;
    --glass-primary-glow: rgba(14, 104, 56, 0.25);
    --glass-primary-container: rgba(14, 104, 56, 0.14);
    --glass-on-primary: #FFFFFF;
    --glass-on-primary-container: #03361B;
    --m3-primary: var(--glass-primary);
    --m3-on-primary: var(--glass-on-primary);
    --m3-primary-container: #CCE8D7;
    --m3-on-primary-container: var(--glass-on-primary-container);

    --glass-warning: #EA580C;
    --glass-warning-glow: rgba(234, 88, 12, 0.22);
    --glass-amber: #CA8A04;
    --m3-secondary: var(--glass-warning);
    --m3-on-secondary: #FFFFFF;
    --m3-secondary-container: #FFEDD5;
    --m3-on-secondary-container: #7C2D12;

    --m3-tertiary: #CA8A04;
    --m3-on-tertiary: #FFFFFF;
    --m3-tertiary-container: #FEF08A;
    --m3-on-tertiary-container: #713F12;

    /* Apple Ambient Depth Shadows with Specular Top Bevel */
    --glass-shadow-sm: 0 2px 8px -1px rgba(14, 104, 56, 0.08), 0 1px 3px 0 rgba(0, 0, 0, 0.04), inset 0 1px 0 0 rgba(255, 255, 255, 0.95);
    --glass-shadow-md: 0 10px 28px -4px rgba(14, 104, 56, 0.10), 0 4px 12px -2px rgba(0, 0, 0, 0.05), inset 0 1px 1px 0 rgba(255, 255, 255, 0.95);
    --glass-shadow-lg: 0 20px 48px -8px rgba(14, 104, 56, 0.15), 0 8px 24px -4px rgba(0, 0, 0, 0.08), inset 0 1px 2px 0 rgba(255, 255, 255, 1);
    --glass-shadow-modal: 0 32px 72px -12px rgba(0, 0, 0, 0.25), 0 16px 32px -4px rgba(14, 104, 56, 0.15), inset 0 1px 2px 0 rgba(255, 255, 255, 0.95);

    --m3-elevation-1: var(--glass-shadow-sm);
    --m3-elevation-2: var(--glass-shadow-md);
    --m3-elevation-3: var(--glass-shadow-lg);

    /* Blur & Refraction */
    --glass-blur-sm: blur(12px) saturate(180%);
    --glass-blur-md: blur(24px) saturate(190%);
    --glass-blur-lg: blur(36px) saturate(200%);
}

/* =============================================================================
   DARK MODE: VISIONOS LUMINOUS OBSIDIAN FROSTED GLASS
   ============================================================================= */
.dark {
    --glass-bg-mesh: 
        radial-gradient(at 10% 12%, rgba(14, 104, 56, 0.24) 0px, transparent 50%),
        radial-gradient(at 90% 10%, rgba(243, 122, 32, 0.14) 0px, transparent 45%),
        radial-gradient(at 50% 90%, rgba(74, 222, 128, 0.12) 0px, transparent 55%),
        radial-gradient(at 85% 75%, rgba(14, 104, 56, 0.18) 0px, transparent 50%),
        linear-gradient(145deg, #070D09 0%, #0B130E 50%, #060B08 100%);

    --m3-bg: #0D1410;
    --glass-surface: rgba(18, 28, 22, 0.76);
    --glass-surface-solid: #121C16;
    --glass-surface-subtle: rgba(14, 22, 17, 0.7);
    --glass-surface-hover: rgba(28, 42, 34, 0.88);
    --glass-surface-active: rgba(11, 79, 39, 0.55);

    --m3-surface: var(--glass-surface);
    --m3-surface-dim: rgba(11, 17, 13, 0.7);
    --m3-surface-bright: rgba(26, 38, 31, 0.85);
    --m3-surface-container-lowest: rgba(15, 22, 18, 0.65);
    --m3-surface-container-low: rgba(20, 30, 24, 0.72);
    --m3-surface-container: rgba(25, 38, 30, 0.78);
    --m3-surface-container-high: rgba(32, 48, 38, 0.84);
    --m3-surface-container-highest: rgba(40, 59, 48, 0.9);

    /* Apple Specular Dark Edge Rim */
    --glass-border: rgba(255, 255, 255, 0.14);
    --glass-border-subtle: rgba(255, 255, 255, 0.08);
    --glass-border-strong: rgba(74, 222, 128, 0.35);

    /* Text & Legibility */
    --glass-text-main: #F4F7F5;
    --glass-text-secondary: #CBD8D0;
    --glass-text-muted: #8E9F94;
    --m3-on-surface: var(--glass-text-main);
    --m3-on-surface-variant: var(--glass-text-secondary);
    --m3-outline: rgba(255, 255, 255, 0.2);
    --m3-outline-variant: rgba(255, 255, 255, 0.1);

    /* Corporate Accents */
    --glass-primary: #34D399;
    --glass-primary-glow: rgba(52, 211, 153, 0.32);
    --glass-primary-container: rgba(14, 104, 56, 0.45);
    --glass-on-primary: #022C15;
    --glass-on-primary-container: #A7F3D0;
    --m3-primary: var(--glass-primary);
    --m3-on-primary: var(--glass-on-primary);
    --m3-primary-container: #0B4F27;
    --m3-on-primary-container: var(--glass-on-primary-container);

    --glass-warning: #FB923C;
    --glass-warning-glow: rgba(251, 146, 60, 0.28);
    --glass-amber: #FBBF24;
    --m3-secondary: var(--glass-warning);
    --m3-on-secondary: #541C00;
    --m3-secondary-container: #7C2D12;
    --m3-on-secondary-container: #FED7AA;

    --m3-tertiary: #FACC15;
    --m3-on-tertiary: #422200;
    --m3-tertiary-container: #713F12;
    --m3-on-tertiary-container: #FEF08A;

    /* Elevation */
    --glass-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.4);
    --glass-shadow-md: 0 12px 36px -4px rgba(0, 0, 0, 0.6), inset 0 1px 1px 0 rgba(255, 255, 255, 0.12);
    --glass-shadow-lg: 0 24px 60px -8px rgba(0, 0, 0, 0.75), inset 0 1px 1px 0 rgba(255, 255, 255, 0.18);
    --glass-shadow-modal: 0 36px 84px -10px rgba(0, 0, 0, 0.88), 0 0 0 1px rgba(255, 255, 255, 0.1), inset 0 1px 2px 0 rgba(255, 255, 255, 0.2);

    --m3-elevation-1: var(--glass-shadow-sm);
    --m3-elevation-2: var(--glass-shadow-md);
    --m3-elevation-3: var(--glass-shadow-lg);

    --glass-blur-sm: blur(14px) saturate(190%);
    --glass-blur-md: blur(28px) saturate(200%);
    --glass-blur-lg: blur(40px) saturate(210%);
}

/* =============================================================================
   1. GLOBAL CANVAS & RESET TAILWIND CLASHING RINGS
   ============================================================================= */
html, body {
    font-family: var(--glass-font-sans) !important;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-rendering: optimizeLegibility;
}

body.fi-body {
    background: var(--glass-bg-mesh) !important;
    background-attachment: fixed !important;
    background-size: cover !important;
    color: var(--glass-text-main) !important;
    min-height: 100vh;
}

/* Neutralize Tailwind Ring Clashes (prevents double outlines) */
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
   2. APPLE FROSTED GLASS SIDEBAR & ACTIVE NAVIGATION
   ============================================================================= */
.fi-sidebar {
    background: var(--glass-surface-subtle) !important;
    backdrop-filter: var(--glass-blur-md) !important;
    -webkit-backdrop-filter: var(--glass-blur-md) !important;
    border-right: 1px solid var(--glass-border) !important;
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.04) !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.fi-sidebar-header {
    background: transparent !important;
    border-bottom: 1px solid var(--glass-border) !important;
    padding-top: 1.25rem !important;
    padding-bottom: 1.25rem !important;
}

/* Sidebar Nav Item: Apple Floating Capsule */
.fi-sidebar-item-btn {
    border-radius: 14px !important;
    padding: 0.65rem 0.95rem !important;
    margin: 0.2rem 0.65rem !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    letter-spacing: -0.01em !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    color: var(--glass-text-secondary) !important;
    border: 1px solid transparent !important;
    box-sizing: border-box !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
}

.fi-sidebar-open .fi-sidebar-item-label {
    flex: 1 1 auto !important;
    min-width: 0 !important;
    white-space: nowrap !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
}

.fi-sidebar-open .fi-sidebar-item-badge-ctn {
    flex-shrink: 0 !important;
    margin-left: auto !important;
}

.fi-sidebar-item-btn:hover {
    background: var(--glass-surface-hover) !important;
    color: var(--glass-text-main) !important;
    border-color: var(--glass-border) !important;
    box-shadow: var(--glass-shadow-sm) !important;
}

.fi-sidebar.fi-sidebar-open .fi-sidebar-item-btn:hover {
    transform: translateX(3px);
}

/* =============================================================================
   COLLAPSED SIDEBAR (ICON-ONLY MODE) - SYMMETRICAL SQUIRCLE ICONS
   Fixes cut-off active capsules & asymmetrical clipping against sidebar border
   ============================================================================= */
.fi-sidebar:not(.fi-sidebar-open) {
    width: 4.75rem !important;
    min-width: 4.75rem !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-grouped-border {
    display: none !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav {
    padding-inline: 0 !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    overflow-x: hidden !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav-groups {
    margin-inline: 0 !important;
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group {
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-group-items {
    width: 100% !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item {
    width: 100% !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn {
    width: 2.75rem !important;
    height: 2.75rem !important;
    min-width: 2.75rem !important;
    max-width: 2.75rem !important;
    padding: 0 !important;
    margin: 0.25rem auto !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    border-radius: 14px !important;
    border: 1px solid transparent !important;
    box-sizing: border-box !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-btn:hover {
    transform: scale(1.08) !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-item-icon {
    margin: 0 !important;
    justify-self: center !important;
    align-self: center !important;
}

/* Collapsed Sidebar: clean header without empty boxes or redundant duplicate logos */
.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header-ctn,
.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-header {
    display: none !important;
    height: 0 !important;
    padding: 0 !important;
    border: none !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-nav {
    padding-top: 1rem !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-footer {
    margin-inline: 0 !important;
    padding-inline: 0 !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
}

.fi-sidebar:not(.fi-sidebar-open) .fi-sidebar-database-notifications-btn,
.fi-sidebar:not(.fi-sidebar-open) .fi-user-menu-trigger {
    width: 2.75rem !important;
    height: 2.75rem !important;
    padding: 0 !important;
    margin: 0.25rem auto !important;
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    border-radius: 14px !important;
}

/* Active Nav Item: Explicit Pure White Text & Icon on Emerald Glass Capsule */
.fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
.fi-sidebar-item.fi-active .fi-sidebar-item-btn,
.fi-sidebar-item-btn[aria-current="page"] {
    background: linear-gradient(135deg, #0E6838 0%, #084D28 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    box-shadow: 0 4px 14px -1px rgba(14, 104, 56, 0.45), inset 0 1px 1px rgba(255, 255, 255, 0.5) !important;
}

.fi-sidebar-item.fi-active .fi-sidebar-item-label,
.fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label,
.fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-label {
    color: #FFFFFF !important;
    -webkit-text-fill-color: #FFFFFF !important;
    font-weight: 700 !important;
}

.fi-sidebar-item.fi-active .fi-sidebar-item-icon,
.fi-sidebar-item.fi-active > .fi-sidebar-item-btn > svg,
.fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-icon,
.fi-sidebar-item-btn[aria-current="page"] > svg,
.fi-sidebar-item.fi-active .fi-sidebar-item-btn svg,
.fi-sidebar-item-btn[aria-current="page"] svg {
    color: #FFFFFF !important;
    stroke: #FFFFFF !important;
    fill: none !important;
}

/* Dark Mode Active Nav */
:is(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn,
:is(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-btn,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] {
    background: linear-gradient(135deg, rgba(52, 211, 153, 0.28), rgba(14, 104, 56, 0.45)) !important;
    border: 1px solid rgba(52, 211, 153, 0.45) !important;
    box-shadow: 0 6px 20px -2px rgba(52, 211, 153, 0.25), inset 0 1px 1px rgba(255, 255, 255, 0.2) !important;
}

:is(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-label,
:is(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > .fi-sidebar-item-label,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-label {
    color: #A7F3D0 !important;
    -webkit-text-fill-color: #A7F3D0 !important;
    font-weight: 700 !important;
}

:is(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-icon,
:is(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > svg,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-icon,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] > svg,
:is(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-btn svg,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] svg {
    color: #34D399 !important;
    stroke: #34D399 !important;
}

/* High-Contrast Sidebar Badge System (Crisp, Highly Readable & No Invisible Text) */
.fi-sidebar-item .fi-badge,
.fi-sidebar-item .fi-sidebar-item-badge-ctn .fi-badge {
    border-radius: 9999px !important;
    padding: 0.15rem 0.55rem !important;
    font-size: 0.72rem !important;
    font-weight: 800 !important;
    line-height: 1.2 !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border-width: 1px !important;
}

/* Badge on Active Emerald Capsule (Sidebar) - Solid White Pill with Deep Emerald Text */
.fi-sidebar-item.fi-active .fi-badge,
.fi-sidebar-item.fi-active .fi-sidebar-item-badge-ctn .fi-badge,
.fi-sidebar-item-btn[aria-current="page"] .fi-badge {
    background: #FFFFFF !important;
    border-color: rgba(255, 255, 255, 0.9) !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.16) !important;
}

.fi-sidebar-item.fi-active .fi-badge,
.fi-sidebar-item.fi-active .fi-badge span,
.fi-sidebar-item.fi-active .fi-badge .fi-badge-label,
.fi-sidebar-item.fi-active .fi-badge .fi-badge-label-ctn,
.fi-sidebar-item-btn[aria-current="page"] .fi-badge,
.fi-sidebar-item-btn[aria-current="page"] .fi-badge span,
.fi-sidebar-item-btn[aria-current="page"] .fi-badge .fi-badge-label,
.fi-sidebar-item-btn[aria-current="page"] .fi-badge .fi-badge-label-ctn {
    color: #0E6838 !important;
    -webkit-text-fill-color: #0E6838 !important;
    font-weight: 800 !important;
}

/* Dark mode active item badge */
:is(.dark) .fi-sidebar-item.fi-active .fi-badge,
:is(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-badge-ctn .fi-badge,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-badge {
    background: #A7F3D0 !important;
    border-color: #34D399 !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.3) !important;
}

:is(.dark) .fi-sidebar-item.fi-active .fi-badge,
:is(.dark) .fi-sidebar-item.fi-active .fi-badge span,
:is(.dark) .fi-sidebar-item.fi-active .fi-badge .fi-badge-label,
:is(.dark) .fi-sidebar-item.fi-active .fi-badge .fi-badge-label-ctn,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-badge,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-badge span,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-badge .fi-badge-label,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-badge .fi-badge-label-ctn {
    color: #064E3B !important;
    -webkit-text-fill-color: #064E3B !important;
    font-weight: 800 !important;
}

/* Badge on Inactive Item (Light Mode) - Amber Pill with Deep Brown Text */
.fi-sidebar-item:not(.fi-active) .fi-badge,
.fi-sidebar-item:not(.fi-active) .fi-sidebar-item-badge-ctn .fi-badge {
    background: #FEF3C7 !important;
    border-color: #FCD34D !important;
}

.fi-sidebar-item:not(.fi-active) .fi-badge,
.fi-sidebar-item:not(.fi-active) .fi-badge span,
.fi-sidebar-item:not(.fi-active) .fi-badge .fi-badge-label,
.fi-sidebar-item:not(.fi-active) .fi-badge .fi-badge-label-ctn,
.fi-sidebar-item:not(.fi-active) .fi-sidebar-item-badge-ctn .fi-badge span {
    color: #92400E !important;
    -webkit-text-fill-color: #92400E !important;
    font-weight: 800 !important;
}

/* Badge on Inactive Item (Dark Mode) */
:is(.dark) .fi-sidebar-item:not(.fi-active) .fi-badge,
:is(.dark) .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-badge-ctn .fi-badge {
    background: rgba(245, 158, 11, 0.22) !important;
    border-color: rgba(245, 158, 11, 0.45) !important;
}

:is(.dark) .fi-sidebar-item:not(.fi-active) .fi-badge,
:is(.dark) .fi-sidebar-item:not(.fi-active) .fi-badge span,
:is(.dark) .fi-sidebar-item:not(.fi-active) .fi-badge .fi-badge-label,
:is(.dark) .fi-sidebar-item:not(.fi-active) .fi-badge .fi-badge-label-ctn,
:is(.dark) .fi-sidebar-item:not(.fi-active) .fi-sidebar-item-badge-ctn .fi-badge span {
    color: #FBBF24 !important;
    -webkit-text-fill-color: #FBBF24 !important;
    font-weight: 800 !important;
}

.fi-sidebar-group-label {
    font-size: 0.72rem !important;
    letter-spacing: 0.8px !important;
    text-transform: uppercase !important;
    font-weight: 800 !important;
    color: var(--glass-text-muted) !important;
    padding-left: 1.25rem !important;
    margin-top: 0.5rem !important;
}

/* =============================================================================
   3. APPLE FROSTED GLASS TOPBAR
   ============================================================================= */
.fi-topbar-ctn {
    position: sticky !important;
    top: 0 !important;
    z-index: 99990 !important;
    overflow: visible !important;
    overflow-x: visible !important;
    overflow-y: visible !important;
}

.fi-topbar {
    position: relative !important;
    z-index: 99991 !important;
    background: var(--glass-surface) !important;
    backdrop-filter: var(--glass-blur-md) !important;
    -webkit-backdrop-filter: var(--glass-blur-md) !important;
    border-bottom: 1px solid var(--glass-border) !important;
    box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05) !important;
    overflow: visible !important;
    padding-right: 1.5rem !important;
    box-sizing: border-box !important;
}

:is(.dark) .fi-topbar {
    background: var(--glass-surface-subtle) !important;
    border-bottom: 1px solid var(--glass-border) !important;
    box-shadow: 0 6px 28px rgba(0, 0, 0, 0.45) !important;
}

.fi-topbar nav {
    background: transparent !important;
}

.fi-topbar-start,
.fi-topbar-end,
.fi-user-menu,
.fi-user-menu-trigger,
.fi-topbar-database-notifications-btn {
    position: relative !important;
    z-index: 99992 !important;
}

.fi-topbar .fi-dropdown-panel,
.fi-user-menu .fi-dropdown-panel {
    z-index: 99999 !important;
    box-shadow: 0 20px 48px -4px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(14, 104, 56, 0.25) !important;
}

:is(.dark) .fi-topbar .fi-dropdown-panel,
:is(.dark) .fi-user-menu .fi-dropdown-panel {
    box-shadow: 0 24px 56px -4px rgba(0, 0, 0, 0.85), 0 0 0 1px rgba(255, 255, 255, 0.15) !important;
}

.fi-topbar-end {
    margin-left: auto !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.85rem !important;
}

.fi-topbar-item-btn {
    border-radius: 9999px !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    transition: all 0.2s ease !important;
}

.fi-topbar-item.fi-active .fi-topbar-item-btn,
.fi-topbar-item-btn[aria-current="page"] {
    background: linear-gradient(135deg, #0E6838 0%, #084D28 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.35) !important;
    box-shadow: 0 4px 12px rgba(14, 104, 56, 0.35) !important;
}

.fi-topbar-item.fi-active .fi-topbar-item-label,
.fi-topbar-item-btn[aria-current="page"] .fi-topbar-item-label,
.fi-topbar-item.fi-active .fi-topbar-item-btn span,
.fi-topbar-item-btn[aria-current="page"] span {
    color: #FFFFFF !important;
    -webkit-text-fill-color: #FFFFFF !important;
    font-weight: 700 !important;
}

.fi-topbar-item.fi-active .fi-topbar-item-icon,
.fi-topbar-item-btn[aria-current="page"] .fi-topbar-item-icon,
.fi-topbar-item.fi-active .fi-topbar-item-btn svg,
.fi-topbar-item-btn[aria-current="page"] svg {
    color: #FFFFFF !important;
    stroke: #FFFFFF !important;
}

:is(.dark) .fi-topbar-item.fi-active .fi-topbar-item-btn,
:is(.dark) .fi-topbar-item-btn[aria-current="page"] {
    background: rgba(52, 211, 153, 0.25) !important;
    color: #A7F3D0 !important;
}

:is(.dark) .fi-topbar-item.fi-active .fi-topbar-item-label,
:is(.dark) .fi-topbar-item-btn[aria-current="page"] .fi-topbar-item-label,
:is(.dark) .fi-topbar-item.fi-active .fi-topbar-item-btn span,
:is(.dark) .fi-topbar-item-btn[aria-current="page"] span {
    color: #A7F3D0 !important;
    -webkit-text-fill-color: #A7F3D0 !important;
}

/* =============================================================================
   4. SECTIONS, CARDS & CONTAINERS (CRISP APPLE SQUIRCLES)
   ============================================================================= */
.fi-wi-stats-overview,
.fi-wi-stats-overview > .fi-section,
.fi-wi-stats-overview .fi-section-content-ctn,
.fi-wi-stats-overview .fi-section-content {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
}

.fi-section:not(.fi-section-not-contained),
.fi-card {
    border-radius: 24px !important;
    background: var(--glass-surface) !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow-md) !important;
    transition: box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease !important;
    position: relative !important;
    overflow: visible !important;
}

/* Prevent container elements from clipping floating popovers, datepickers, and select dropdowns */
.fi-section,
.fi-section-content-ctn,
.fi-section-content,
.fi-sc-section,
.fi-sc-component,
.fi-sc-grid,
.fi-sc-tabs-content,
.fi-sc-tabs-tab,
.fi-grid,
.fi-grid-col,
.fi-fo-field-wrp,
.fi-fo-field-wrp-ctn,
.fi-fo-field-content-col,
.fi-fo-field-content,
.fi-fo-select,
.fi-fo-select-wrp,
.fi-select-input-ctn,
.fi-fo-date-time-picker,
.fi-tabs,
.fi-tabs-content,
.choices {
    overflow: visible !important;
}

.fi-input-wrp,
.fi-input-wrp:has(.choices),
.fi-input-wrp:has(.fi-dropdown-panel),
.fi-input-wrp:has(.fi-fo-date-time-picker-panel),
.fi-input-wrp:has([x-ref="panel"]),
.fi-fo-select .fi-input-wrp,
.fi-fo-date-time-picker .fi-input-wrp {
    overflow: visible !important;
}

/* All components baseline positioning */
.fi-grid > .fi-grid-col,
.fi-sc > .fi-grid-col,
.fi-sc-grid > .fi-grid-col,
.fi-tabs-content > .fi-grid > .fi-grid-col,
.fi-sc-tabs-tab > .fi-grid > .fi-grid-col,
.fi-sc-tabs-tab > .fi-sc > .fi-grid-col,
.fi-sc > .fi-sc-component,
.fi-sc > div,
.fi-sc-section,
.fi-section {
    position: relative !important;
}

/* Dynamic Priority Elevation: Ensures any active/opened dropdown or calendar floats completely OVER following sections */
.fi-elevated-active,
.fi-grid-col:focus-within,
.fi-grid-col:has(:focus),
.fi-grid-col:has([aria-expanded="true"]),
.fi-grid-col:has(.choices.is-open),
.fi-grid-col:has(.choices__list--dropdown.is-active),
.fi-grid-col:has(.fi-dropdown-panel:not([style*="display: none"])),
.fi-grid-col:has(.fi-fo-date-time-picker-panel:not([style*="display: none"])),
.fi-grid-col:has([x-ref="panel"]:not([style*="display: none"])),
.fi-sc-component:focus-within,
.fi-sc-component:has(:focus),
.fi-sc-component:has([aria-expanded="true"]),
.fi-sc-component:has(.choices.is-open),
.fi-sc-component:has(.fi-dropdown-panel:not([style*="display: none"])),
.fi-sc-component:has(.fi-fo-date-time-picker-panel:not([style*="display: none"])),
.fi-sc-section:focus-within,
.fi-sc-section:has(:focus),
.fi-sc-section:has([aria-expanded="true"]),
.fi-sc-section:has(.choices.is-open),
.fi-sc-section:has(.choices__list--dropdown.is-active),
.fi-sc-section:has(.fi-dropdown-panel:not([style*="display: none"])),
.fi-sc-section:has(.fi-fo-date-time-picker-panel:not([style*="display: none"])),
.fi-section:not(.fi-section-not-contained):focus-within,
.fi-section:not(.fi-section-not-contained):has(:focus),
.fi-section:not(.fi-section-not-contained):has([aria-expanded="true"]),
.fi-section:not(.fi-section-not-contained):has(.choices.is-open),
.fi-section:not(.fi-section-not-contained):has(.choices__list--dropdown.is-active),
.fi-section:not(.fi-section-not-contained):has(.fi-dropdown-panel:not([style*="display: none"])),
.fi-section:not(.fi-section-not-contained):has(.fi-fo-date-time-picker-panel:not([style*="display: none"])),
.fi-fo-field-wrp:focus-within,
.fi-fo-field-wrp:has([aria-expanded="true"]),
.fi-input-wrp:focus-within,
.fi-input-wrp:has([aria-expanded="true"]) {
    position: relative !important;
    z-index: 9999 !important;
}

/* Dropdown & Datepicker Popovers: Topmost Z-Index Layer */
.fi-dropdown-panel,
.fi-fo-date-time-picker-panel,
.fi-popover-content,
[x-popover],
.choices__list--dropdown {
    position: absolute !important;
    z-index: 999999 !important;
    background: #FFFFFF !important;
    border: 1px solid rgba(14, 104, 56, 0.2) !important;
    border-radius: 16px !important;
    padding: 0.4rem !important;
    box-shadow: 0 16px 40px -4px rgba(0, 0, 0, 0.18), 0 0 0 1px rgba(14, 104, 56, 0.15) !important;
}

:is(.dark) .choices__list--dropdown {
    background: #0f1c14 !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    box-shadow: 0 20px 48px -4px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.12) !important;
}

.choices__list--dropdown .choices__item {
    border-radius: 10px !important;
    padding: 0.6rem 0.85rem !important;
    font-size: 0.875rem !important;
    color: var(--glass-text-main) !important;
    transition: all 0.15s ease !important;
}

.choices__list--dropdown .choices__item--selectable.is-highlighted,
.choices__list--dropdown .choices__item--selectable:hover {
    background: rgba(14, 104, 56, 0.1) !important;
    color: #0E6838 !important;
}

:is(.dark) .choices__list--dropdown .choices__item--selectable.is-highlighted,
:is(.dark) .choices__list--dropdown .choices__item--selectable:hover {
    background: rgba(52, 211, 153, 0.18) !important;
    color: #34D399 !important;
}

.choices__list--dropdown .choices__input {
    background: var(--glass-surface-subtle) !important;
    border-radius: 10px !important;
    border: 1px solid var(--glass-border) !important;
    color: var(--glass-text-main) !important;
    padding: 0.5rem 0.75rem !important;
    margin-bottom: 0.4rem !important;
}

.fi-dropdown-panel,
.fi-fo-date-time-picker-panel,
.fi-popover-content,
[x-popover] {
    position: absolute !important;
    z-index: 999999 !important;
    background: #FFFFFF !important;
    box-shadow: 0 16px 40px -4px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(14, 104, 56, 0.2) !important;
}

:is(.dark) .fi-dropdown-panel,
:is(.dark) .fi-fo-date-time-picker-panel,
:is(.dark) .fi-popover-content {
    background: #0f1c14 !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    box-shadow: 0 20px 48px -4px rgba(0, 0, 0, 0.75), 0 0 0 1px rgba(255, 255, 255, 0.12) !important;
}

.fi-section:not(.fi-section-not-contained):hover,
.fi-card:hover {
    border-color: var(--glass-border-strong) !important;
    box-shadow: var(--glass-shadow-lg) !important;
}

/* Stat Cards (Harvyn High-Performance Surface) */
.fi-wi-stats-overview-stat {
    border-radius: 16px !important;
    background: #FFFFFF !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    padding: 1.25rem 1.5rem !important;
    transition: all 0.2s ease !important;
}

:is(.dark) .fi-wi-stats-overview-stat {
    background: #0B1713 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
}

.fi-wi-stats-overview-stat:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
    transform: translateY(-2px);
    border-color: var(--glass-border-strong) !important;
}

.fi-wi-stats-overview-stat-label {
    font-size: 0.8rem !important;
    font-weight: 700 !important;
    color: var(--glass-text-muted) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.6px !important;
}

.fi-wi-stats-overview-stat-value {
    font-family: var(--glass-font-display) !important;
    font-weight: 800 !important;
    font-size: 1.95rem !important;
    letter-spacing: -0.025em !important;
    color: var(--glass-text-main) !important;
    margin-top: 0.4rem !important;
}

.fi-section-header {
    border-bottom: 1px solid var(--glass-border-subtle) !important;
    padding: 1.25rem 1.65rem !important;
    background: rgba(255, 255, 255, 0.4) !important;
}

:is(.dark) .fi-section-header {
    background: rgba(255, 255, 255, 0.03) !important;
    border-bottom-color: var(--glass-border-subtle) !important;
}

.fi-section-header-title {
    font-family: var(--glass-font-display) !important;
    font-weight: 800 !important;
    font-size: 1.2rem !important;
    letter-spacing: -0.02em !important;
    color: var(--glass-text-main) !important;
}

.fi-section-header-description {
    color: var(--glass-text-muted) !important;
    font-size: 0.875rem !important;
    line-height: 1.5 !important;
}

/* =============================================================================
   5. DATA TABLES: RAZOR-SHARP ERGONOMICS (NO WASHED-OUT CELLS)
   ============================================================================= */
.fi-ta-ctn {
    border-radius: 16px !important;
    overflow: hidden !important;
    border: 1px solid var(--glass-border) !important;
    background: #FFFFFF !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
}

:is(.dark) .fi-ta-ctn {
    background: #0B1713 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
}

.fi-ta-content {
    border-radius: 0 0 16px 16px !important;
    overflow-x: auto !important;
    width: 100% !important;
}

.fi-ta-table {
    width: 100% !important;
    table-layout: auto !important;
}

.fi-ta-content,
.fi-ta-table,
.fi-ta-table > thead,
.fi-ta-table > tbody {
    background: transparent !important;
}

.fi-ta-header-cell {
    background: #F8FAFC !important;
    color: #64748B !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
    letter-spacing: 0.05em !important;
    text-transform: uppercase !important;
    border-bottom: 1px solid var(--glass-border) !important;
    padding: 0.75rem 1rem !important;
}

:is(.dark) .fi-ta-header-cell {
    background: rgba(15, 23, 42, 0.6) !important;
    color: #94A3B8 !important;
    border-bottom-color: rgba(255, 255, 255, 0.08) !important;
}

.fi-ta-row {
    background: transparent !important;
    transition: background 0.18s ease !important;
    border-bottom: 1px solid var(--glass-border-subtle) !important;
}

.fi-ta-row:hover,
.fi-ta-row.fi-striped:hover {
    background: rgba(14, 104, 56, 0.06) !important;
}

:is(.dark) .fi-ta-row:hover,
:is(.dark) .fi-ta-row.fi-striped:hover {
    background: rgba(255, 255, 255, 0.06) !important;
}

.fi-ta-row.fi-striped {
    background: rgba(235, 244, 238, 0.45) !important;
}

:is(.dark) .fi-ta-row.fi-striped {
    background: rgba(255, 255, 255, 0.02) !important;
}

.fi-ta-cell {
    padding: 1rem 1.15rem !important;
    font-size: 0.875rem !important;
    color: var(--glass-text-main) !important;
}

/* Table Grouping Header Rows (Harvyn Workforce Clean Collapsible Headers) */
.fi-ta-group-header-row {
    background: #F8FAFC !important;
    border-bottom: 1px solid var(--glass-border) !important;
    border-top: 1px solid var(--glass-border) !important;
    cursor: pointer !important;
    transition: all 0.15s ease !important;
}

:is(.dark) .fi-ta-group-header-row {
    background: rgba(15, 23, 42, 0.6) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

.fi-ta-group-header-row:hover {
    background: #F1F5F9 !important;
}

:is(.dark) .fi-ta-group-header-row:hover {
    background: rgba(255, 255, 255, 0.06) !important;
}

.fi-ta-group-header-cell {
    padding: 0.75rem 1rem !important;
}

.fi-ta-group-heading {
    font-size: 0.875rem !important;
    font-weight: 700 !important;
    color: var(--glass-text-main) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.fi-ta-group-description {
    font-size: 0.785rem !important;
    font-weight: 500 !important;
    color: var(--glass-text-secondary) !important;
    margin-left: 0.5rem !important;
}

.fi-ta-search-field .fi-input-wrp {
    border-radius: 9999px !important;
}

.fi-pagination-items {
    border-radius: 10px !important;
    overflow: hidden !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
}

.fi-pagination-item-btn {
    border-radius: 0 !important;
    font-weight: 600 !important;
    transition: all 0.15s ease !important;
    padding: 0.45rem 0.85rem !important;
}

.fi-pagination-item.fi-active .fi-pagination-item-btn,
.fi-active .fi-pagination-item-btn,
.fi-pagination-item-active .fi-pagination-item-btn,
.fi-pagination-item-btn[aria-current="page"] {
    background: #0E6838 !important;
    color: #FFFFFF !important;
    font-weight: 800 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

:is(.dark) .fi-pagination-item.fi-active .fi-pagination-item-btn,
:is(.dark) .fi-active .fi-pagination-item-btn,
:is(.dark) .fi-pagination-item-active .fi-pagination-item-btn,
:is(.dark) .fi-pagination-item-btn[aria-current="page"] {
    background: #10B981 !important;
    color: #022C15 !important;
    font-weight: 800 !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

/* =============================================================================
   6. APPLE SPECULAR BUTTONS & CAPSULES
   ============================================================================= */
.fi-btn {
    border-radius: 12px !important;
    font-weight: 700 !important;
    font-size: 0.875rem !important;
    letter-spacing: -0.01em !important;
    padding: 0.6rem 1.45rem !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    border: 1px solid transparent !important;
    --tw-ring-shadow: none !important;
    --tw-ring-offset-shadow: none !important;
    --tw-ring-color: transparent !important;
}

.fi-btn-size-sm {
    padding: 0.4rem 1rem !important;
    font-size: 0.8rem !important;
    border-radius: 10px !important;
}

.fi-btn-size-lg {
    padding: 0.8rem 2rem !important;
    font-size: 0.95rem !important;
    border-radius: 14px !important;
}

/* Primary Button */
.fi-btn-color-primary {
    background: #0E6838 !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 2px 6px rgba(14, 104, 56, 0.25), 0 1px 2px rgba(0, 0, 0, 0.04) !important;
}

:is(.dark) .fi-btn-color-primary {
    background: #10B981 !important;
    color: #022C15 !important;
    font-weight: 800 !important;
    border: 1px solid rgba(255, 255, 255, 0.12) !important;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3) !important;
}

.fi-btn-color-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 12px rgba(14, 104, 56, 0.35) !important;
    filter: brightness(1.05) !important;
}

:is(.dark) .fi-btn-color-primary:hover {
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45) !important;
    filter: brightness(1.05) !important;
}

.fi-btn-color-primary:active {
    transform: translateY(0) !important;
}

/* Secondary / Gray Button */
.fi-btn-color-gray {
    background: var(--glass-surface-hover) !important;
    backdrop-filter: var(--glass-blur-sm) !important;
    border: 1px solid var(--glass-border) !important;
    color: var(--glass-text-main) !important;
    box-shadow: var(--glass-shadow-sm) !important;
}

.fi-btn-color-gray:hover {
    background: #FFFFFF !important;
    border-color: var(--glass-border-strong) !important;
    transform: translateY(-1px) !important;
}

/* =============================================================================
   7. FORM INPUTS: SOLID WHITE IN LIGHT MODE (CRISP CONTRAST)
   ============================================================================= */
.fi-input-wrp {
    border-radius: 14px !important;
    background: #FFFFFF !important;
    border: 1px solid rgba(14, 104, 56, 0.22) !important;
    --tw-ring-color: transparent !important;
    --tw-ring-shadow: 0 0 #0000 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

:is(.dark) .fi-input-wrp {
    background: rgba(14, 22, 17, 0.85) !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.3) !important;
}

.fi-input-wrp:focus-within {
    border-color: #0E6838 !important;
    background: #FFFFFF !important;
    box-shadow: 0 0 0 3.5px rgba(14, 104, 56, 0.2), 0 2px 8px rgba(14, 104, 56, 0.1) !important;
}

:is(.dark) .fi-input-wrp:focus-within {
    background: rgba(20, 32, 24, 0.95) !important;
    border-color: #34D399 !important;
    box-shadow: 0 0 0 3.5px rgba(52, 211, 153, 0.28) !important;
}

.fi-input {
    font-size: 0.9rem !important;
    color: var(--glass-text-main) !important;
    padding: 0.7rem 0.95rem !important;
}

.fi-fo-field-wrp-label {
    font-weight: 700 !important;
    font-size: 0.85rem !important;
    letter-spacing: -0.01em !important;
    color: var(--glass-text-secondary) !important;
    margin-bottom: 0.4rem !important;
}

/* Fix native select options dropdown */
select option {
    background-color: #FFFFFF !important;
    color: #0B1910 !important;
}

:is(.dark) select option {
    background-color: #121C16 !important;
    color: #F4F7F5 !important;
}

/* Placeholders & Affixes Contrast Safeguards */
.fi-input::placeholder,
input::placeholder,
textarea::placeholder {
    color: var(--glass-text-muted) !important;
    opacity: 0.75 !important;
}

.fi-input-wrp-prefix,
.fi-input-wrp-suffix,
.fi-input-wrp-prefix span,
.fi-input-wrp-suffix span {
    color: var(--glass-text-muted) !important;
    font-weight: 700 !important;
}

/* =============================================================================
   8. APPLE BADGES & STATUS CHIPS (CRISP CONTRAST IN BOTH LIGHT & DARK)
   ============================================================================= */
.fi-badge {
    border-radius: 9999px !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
    padding: 0.32rem 0.85rem !important;
    letter-spacing: 0.2px !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
}

/* Light mode badges */
.fi-badge.fi-color-success {
    background: #D1FAE5 !important;
    color: #065F46 !important;
    border-color: #A7F3D0 !important;
}
.fi-badge.fi-color-warning {
    background: #FEF3C7 !important;
    color: #92400E !important;
    border-color: #FDE68A !important;
}
.fi-badge.fi-color-danger {
    background: #FEE2E2 !important;
    color: #991B1B !important;
    border-color: #FECACA !important;
}
.fi-badge.fi-color-info,
.fi-badge.fi-color-primary {
    background: #CCE8D7 !important;
    color: #064E26 !important;
    border-color: #A7F3D0 !important;
}
.fi-badge.fi-color-gray {
    background: #E2E8F0 !important;
    color: #1E293B !important;
    border-color: #CBD5E1 !important;
}

/* Dark mode badges */
:is(.dark) .fi-badge.fi-color-success {
    background: rgba(16, 185, 129, 0.2) !important;
    color: #6EE7B7 !important;
    border-color: rgba(52, 211, 153, 0.35) !important;
}
:is(.dark) .fi-badge.fi-color-warning {
    background: rgba(245, 158, 11, 0.2) !important;
    color: #FCD34D !important;
    border-color: rgba(251, 191, 36, 0.35) !important;
}
:is(.dark) .fi-badge.fi-color-danger {
    background: rgba(239, 68, 68, 0.2) !important;
    color: #FCA5A5 !important;
    border-color: rgba(248, 113, 113, 0.35) !important;
}
:is(.dark) .fi-badge.fi-color-info,
:is(.dark) .fi-badge.fi-color-primary {
    background: rgba(14, 104, 56, 0.35) !important;
    color: #A7F3D0 !important;
    border-color: rgba(52, 211, 153, 0.4) !important;
}
:is(.dark) .fi-badge.fi-color-gray {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #E2E8F0 !important;
    border-color: rgba(255, 255, 255, 0.15) !important;
}

.fi-badge span,
.fi-badge .fi-badge-label {
    color: inherit !important;
    -webkit-text-fill-color: inherit !important;
    font-weight: 700 !important;
}

/* =============================================================================
   HARVYN HRIS SEGMENTED TABS (CLEAN, PRECISE & COHESIVE LIGHT/DARK CONTRAST)
   ============================================================================= */
.fi-sc-tabs {
    border-radius: 14px !important;
    border: 1px solid var(--glass-border) !important;
    background: #FFFFFF !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    overflow: hidden !important;
}

:is(.dark) .fi-sc-tabs {
    background: #0B1713 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
}

.fi-tabs {
    background: #F8FAFC !important;
    border-bottom: 1px solid var(--glass-border) !important;
    border-top: none !important;
    border-left: none !important;
    border-right: none !important;
    border-radius: 12px 12px 0 0 !important;
    padding: 0.45rem 0.65rem !important;
    display: flex !important;
    align-items: center !important;
    overflow-x: auto !important;
    gap: 0.35rem !important;
}

:is(.dark) .fi-tabs {
    background: rgba(15, 23, 42, 0.45) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.fi-sc-tabs-tablist {
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 0 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.35rem !important;
    box-shadow: none !important;
}

.fi-tabs-item,
.fi-sc-tabs-tablist button {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.45rem !important;
    padding: 0.45rem 0.95rem !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    line-height: 1.25 !important;
    border-radius: 8px !important;
    border: 1px solid transparent !important;
    margin: 0 !important;
    margin-bottom: 0 !important;
    color: var(--glass-text-secondary) !important;
    box-shadow: none !important;
    transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1) !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    background: transparent !important;
}

.fi-tabs-item svg,
.fi-tabs-item .fi-icon,
.fi-tabs-item .fi-tabs-item-icon,
.fi-sc-tabs-tablist button svg {
    width: 1.125rem !important;
    height: 1.125rem !important;
    flex-shrink: 0 !important;
    color: var(--glass-text-secondary) !important;
    stroke: currentColor !important;
    fill: none !important;
    transition: color 0.15s ease !important;
}

/* Inactive Tab Hover */
.fi-tabs-item:not(.fi-active):hover,
.fi-sc-tabs-tablist button:not(.fi-active):hover {
    color: var(--glass-text-main) !important;
    background: #FFFFFF !important;
    border-color: var(--glass-border) !important;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
}

:is(.dark) .fi-tabs-item:not(.fi-active):hover,
:is(.dark) .fi-sc-tabs-tablist button:not(.fi-active):hover {
    color: #F8FAFC !important;
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.fi-tabs-item:not(.fi-active):hover svg,
.fi-tabs-item:not(.fi-active):hover .fi-icon,
.fi-tabs-item:not(.fi-active):hover .fi-tabs-item-icon,
.fi-sc-tabs-tablist button:not(.fi-active):hover svg {
    color: var(--glass-text-main) !important;
    stroke: currentColor !important;
}

/* Active Segmented Pill - BPR Corporate Emerald (Harvyn Precision) */
.fi-tabs-item.fi-active,
.fi-tabs-item[aria-selected="true"],
.fi-sc-tabs-tablist button.fi-active,
.fi-sc-tabs-tablist button[aria-selected="true"] {
    background: #0E6838 !important;
    color: #FFFFFF !important;
    -webkit-text-fill-color: #FFFFFF !important;
    font-weight: 700 !important;
    border: 1px solid #084D28 !important;
    box-shadow: 0 1px 3px rgba(14, 104, 56, 0.3), 0 1px 2px rgba(0, 0, 0, 0.06) !important;
}

.fi-tabs-item.fi-active .fi-tabs-item-label,
.fi-tabs-item[aria-selected="true"] .fi-tabs-item-label,
.fi-sc-tabs-tablist button.fi-active span,
.fi-sc-tabs-tablist button[aria-selected="true"] span {
    color: #FFFFFF !important;
    -webkit-text-fill-color: #FFFFFF !important;
    font-weight: 700 !important;
}

/* Active Tab Icon: Pure Crisp White */
.fi-tabs-item.fi-active svg,
.fi-tabs-item.fi-active .fi-icon,
.fi-tabs-item.fi-active .fi-tabs-item-icon,
.fi-tabs-item[aria-selected="true"] svg,
.fi-tabs-item[aria-selected="true"] .fi-icon,
.fi-tabs-item[aria-selected="true"] .fi-tabs-item-icon,
.fi-sc-tabs-tablist button.fi-active svg,
.fi-sc-tabs-tablist button[aria-selected="true"] svg {
    color: #FFFFFF !important;
    stroke: currentColor !important;
    fill: none !important;
}

/* Dark Mode Active Pill */
:is(.dark) .fi-tabs-item.fi-active,
:is(.dark) .fi-tabs-item[aria-selected="true"],
:is(.dark) .fi-sc-tabs-tablist button.fi-active,
:is(.dark) .fi-sc-tabs-tablist button[aria-selected="true"] {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.25), rgba(5, 150, 105, 0.4)) !important;
    color: #34D399 !important;
    -webkit-text-fill-color: #34D399 !important;
    border: 1px solid rgba(52, 211, 153, 0.45) !important;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.2) !important;
}

:is(.dark) .fi-tabs-item.fi-active .fi-tabs-item-label,
:is(.dark) .fi-tabs-item[aria-selected="true"] .fi-tabs-item-label,
:is(.dark) .fi-sc-tabs-tablist button.fi-active span,
:is(.dark) .fi-sc-tabs-tablist button[aria-selected="true"] span {
    color: #34D399 !important;
    -webkit-text-fill-color: #34D399 !important;
    font-weight: 700 !important;
}

:is(.dark) .fi-tabs-item.fi-active svg,
:is(.dark) .fi-tabs-item.fi-active .fi-icon,
:is(.dark) .fi-tabs-item.fi-active .fi-tabs-item-icon,
:is(.dark) .fi-tabs-item[aria-selected="true"] svg,
:is(.dark) .fi-tabs-item[aria-selected="true"] .fi-icon,
:is(.dark) .fi-tabs-item[aria-selected="true"] .fi-tabs-item-icon,
:is(.dark) .fi-sc-tabs-tablist button.fi-active svg,
:is(.dark) .fi-sc-tabs-tablist button[aria-selected="true"] svg {
    color: #34D399 !important;
    stroke: currentColor !important;
    fill: none !important;
}

/* =============================================================================
   9. VISIONOS FROSTED MODALS & DIALOGS
   ============================================================================= */
.fi-modal {
    z-index: 999990 !important;
}

.fi-modal-close-overlay {
    z-index: 999991 !important;
    background: rgba(7, 13, 9, 0.65) !important;
    backdrop-filter: blur(16px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.fi-modal-window-ctn {
    z-index: 999995 !important;
}

.fi-modal-window {
    z-index: 999999 !important;
    border-radius: 28px !important;
    background: var(--glass-surface) !important;
    backdrop-filter: var(--glass-blur-lg) !important;
    -webkit-backdrop-filter: var(--glass-blur-lg) !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow-modal) !important;
    overflow: hidden !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

:is(.dark) .fi-modal-window {
    background: rgba(18, 28, 22, 0.88) !important;
}

.fi-modal-header {
    padding: 1.6rem 1.85rem 1.25rem !important;
    border-bottom: 1px solid var(--glass-border-subtle) !important;
    background: rgba(255, 255, 255, 0.45) !important;
}

:is(.dark) .fi-modal-header {
    background: rgba(255, 255, 255, 0.04) !important;
}

.fi-modal-heading {
    font-family: var(--glass-font-display) !important;
    font-size: 1.3rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.02em !important;
    color: var(--glass-text-main) !important;
}

.fi-modal-description {
    font-size: 0.875rem !important;
    color: var(--glass-text-muted) !important;
    line-height: 1.5 !important;
    margin-top: 0.35rem !important;
}

.fi-modal-close-btn {
    border-radius: 9999px !important;
    padding: 0.5rem !important;
    transition: all 0.2s ease !important;
}

.fi-modal-close-btn:hover {
    background: var(--glass-surface-hover) !important;
    transform: rotate(90deg);
}

.fi-modal-content {
    padding: 1.6rem 1.85rem !important;
    color: var(--glass-text-main) !important;
}

.fi-modal-footer {
    padding: 1.25rem 1.85rem !important;
    border-top: 1px solid var(--glass-border-subtle) !important;
    background: rgba(244, 248, 245, 0.7) !important;
}

:is(.dark) .fi-modal-footer {
    background: rgba(14, 22, 17, 0.75) !important;
}

/* =============================================================================
   10. DROPDOWNS & USER MENU
   ============================================================================= */
.fi-dropdown-panel {
    min-width: 18rem;
    max-width: 28rem;
    width: max-content;
    border-radius: 20px !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow-lg) !important;
    background: var(--glass-surface) !important;
    backdrop-filter: var(--glass-blur-lg) !important;
    -webkit-backdrop-filter: var(--glass-blur-lg) !important;
    padding: 0.6rem !important;
    z-index: 99999 !important;
}

/* Select Form Input Dropdown Panel: naturally match input container */
.fi-input-wrp .fi-dropdown-panel,
.fi-select-input-ctn .fi-dropdown-panel,
.fi-fo-select .fi-dropdown-panel {
    min-width: 100% !important;
    max-width: 100% !important;
    width: 100% !important;
    box-sizing: border-box !important;
}

/* Table Filters Dropdown Panel: ensure proper padding, height, and no button cutoff */
.fi-ta-filters-dropdown .fi-dropdown-panel {
    min-width: 22rem !important;
    max-width: 32rem !important;
    width: auto !important;
    padding: 1.25rem 1.35rem 1.5rem !important;
    max-height: min(85vh, 600px) !important;
    overflow-y: auto !important;
    overflow-x: hidden !important;
}

.fi-ta-filters {
    display: flex !important;
    flex-direction: column !important;
    gap: 1.15rem !important;
}

.fi-ta-filters-actions-ctn {
    margin-top: 1rem !important;
    padding-top: 0.85rem !important;
    padding-bottom: 0.25rem !important;
    border-top: 1px solid var(--glass-border-subtle) !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
}

.fi-ta-filters-actions-ctn .fi-btn {
    border-radius: 12px !important;
    width: 100% !important;
    justify-content: center !important;
}

.fi-dropdown-header {
    padding: 0.75rem 1rem !important;
    border-bottom: 1px solid var(--glass-border-subtle) !important;
    margin-bottom: 0.4rem !important;
}

.fi-dropdown-header span,
.fi-dropdown-header > span,
.fi-dropdown-header .fi-dropdown-header-label,
.fi-dropdown-list-item-label,
.fi-user-menu-item-label,
.fi-user-menu-trigger-text {
    white-space: normal !important;
    overflow: visible !important;
    text-overflow: unset !important;
    word-break: break-word !important;
    font-weight: 700 !important;
    font-size: 0.9rem !important;
    color: var(--glass-text-main) !important;
    line-height: 1.35 !important;
}

.fi-dropdown-list-item-btn,
.fi-dropdown-list-item-link {
    border-radius: 12px !important;
    padding: 0.65rem 1rem !important;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1) !important;
    color: var(--glass-text-main) !important;
}

.fi-dropdown-list-item-btn:hover,
.fi-dropdown-list-item-link:hover {
    background: var(--glass-surface-hover) !important;
    transform: translateX(2px);
}

.fi-theme-switcher {
    border-radius: 14px !important;
    background: var(--glass-surface-subtle) !important;
    backdrop-filter: var(--glass-blur-sm) !important;
    padding: 0.25rem !important;
    margin: 0.4rem 0.5rem !important;
    border: 1px solid var(--glass-border-subtle) !important;
}

.fi-theme-switcher-btn {
    border-radius: 10px !important;
    transition: all 0.2s ease !important;
}

/* =============================================================================
   11. APPLE FLOATING TOASTS & NOTIFICATIONS
   ============================================================================= */
.fi-no-notification {
    border-radius: 20px !important;
    background: var(--glass-surface) !important;
    backdrop-filter: var(--glass-blur-lg) !important;
    -webkit-backdrop-filter: var(--glass-blur-lg) !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow-lg) !important;
    padding: 1.1rem 1.35rem !important;
}

.fi-no-notification-title {
    font-family: var(--glass-font-sans) !important;
    font-weight: 800 !important;
    font-size: 0.95rem !important;
    letter-spacing: -0.01em !important;
    color: var(--glass-text-main) !important;
}

.fi-no-notification-date,
.fi-no-notification-body {
    font-size: 0.85rem !important;
    color: var(--glass-text-muted) !important;
    line-height: 1.45 !important;
}

/* =============================================================================
   12. ULTRA-SMOOTH APPLE SCROLLBAR
   ============================================================================= */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: var(--glass-border-strong);
    border-radius: 9999px;
    border: 2px solid transparent;
    background-clip: padding-box;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--glass-primary);
}

/* =============================================================================
   13. MODAL SVG & CONTENT SAFEGUARDS (PREVENTS GIGANTIC UNSTYLED SVGS)
   ============================================================================= */
.fi-modal-content svg,
.fi-modal-window svg {
    display: inline-block;
    vertical-align: middle;
    flex-shrink: 0;
}
.fi-modal-content svg.w-4, .fi-modal-window svg.w-4 { width: 1rem !important; height: 1rem !important; min-width: 1rem !important; max-width: 1rem !important; }
.fi-modal-content svg.w-5, .fi-modal-window svg.w-5 { width: 1.25rem !important; height: 1.25rem !important; min-width: 1.25rem !important; max-width: 1.25rem !important; }
.fi-modal-content svg.w-6, .fi-modal-window svg.w-6 { width: 1.5rem !important; height: 1.5rem !important; min-width: 1.5rem !important; max-width: 1.5rem !important; }
.fi-modal-content svg.w-8, .fi-modal-window svg.w-8 { width: 2rem !important; height: 2rem !important; min-width: 2rem !important; max-width: 2rem !important; }
.fi-modal-content svg:not([class*="w-"]):not([width]) {
    width: 1.25rem !important;
    height: 1.25rem !important;
    max-width: 1.25rem !important;
    max-height: 1.25rem !important;
}

/* Ensure flex/grid/spacing inside custom modals works reliably */
.fi-modal-content .flex { display: flex !important; }
.fi-modal-content .grid { display: grid !important; }
.fi-modal-content .items-center { align-items: center !important; }
.fi-modal-content .items-start { align-items: flex-start !important; }
.fi-modal-content .justify-between { justify-content: space-between !important; }
.fi-modal-content .justify-center { justify-content: center !important; }
.fi-modal-content .gap-2 { gap: 0.5rem !important; }
.fi-modal-content .gap-3 { gap: 0.75rem !important; }
.fi-modal-content .gap-4 { gap: 1rem !important; }
.fi-modal-content .space-y-3 > * + * { margin-top: 0.75rem !important; }
.fi-modal-content .space-y-4 > * + * { margin-top: 1rem !important; }
.fi-modal-content .space-y-6 > * + * { margin-top: 1.5rem !important; }

/* =============================================================================
   14. HEADER ACTION BUTTONS INTERACTION SAFEGUARD
   ============================================================================= */
.fi-header-actions-ctn {
    position: relative !important;
    z-index: 10 !important;
    pointer-events: auto !important;
}

.fi-header-actions-ctn .fi-btn,
.fi-header-actions-ctn a.fi-btn,
.fi-header-actions-ctn a.fi-ac-btn-action {
    cursor: pointer !important;
    pointer-events: auto !important;
    user-select: none !important;
}
</style>
