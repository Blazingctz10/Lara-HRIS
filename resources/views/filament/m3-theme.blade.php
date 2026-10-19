<!-- Apple VisionOS / macOS Liquid Glass System with High-Precision Light & Dark Ergonomics -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">

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
}

.fi-sidebar-item-btn:hover {
    background: var(--glass-surface-hover) !important;
    color: var(--glass-text-main) !important;
    border-color: var(--glass-border) !important;
    box-shadow: var(--glass-shadow-sm) !important;
    transform: translateX(3px);
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
.fi-sidebar-item.fi-active > .fi-sidebar-item-btn > span,
.fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-label,
.fi-sidebar-item-btn[aria-current="page"] > span,
.fi-sidebar-item.fi-active .fi-sidebar-item-btn span,
.fi-sidebar-item-btn[aria-current="page"] span {
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
:is(.dark) .fi-sidebar-item.fi-active > .fi-sidebar-item-btn > span,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] .fi-sidebar-item-label,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] > span,
:is(.dark) .fi-sidebar-item.fi-active .fi-sidebar-item-btn span,
:is(.dark) .fi-sidebar-item-btn[aria-current="page"] span {
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
    overflow-x: visible !important;
}

.fi-topbar {
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
    backdrop-filter: var(--glass-blur-md) !important;
    -webkit-backdrop-filter: var(--glass-blur-md) !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow-md) !important;
    transition: box-shadow 0.3s cubic-bezier(0.16, 1, 0.3, 1), transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.3s ease !important;
}

.fi-section:not(.fi-section-not-contained):hover,
.fi-card:hover {
    border-color: var(--glass-border-strong) !important;
    box-shadow: var(--glass-shadow-lg) !important;
}

/* Stat Cards */
.fi-wi-stats-overview-stat {
    border-radius: 22px !important;
    background: var(--glass-surface) !important;
    backdrop-filter: var(--glass-blur-md) !important;
    -webkit-backdrop-filter: var(--glass-blur-md) !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow-md) !important;
    padding: 1.35rem 1.6rem !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.fi-wi-stats-overview-stat:hover {
    box-shadow: var(--glass-shadow-lg) !important;
    transform: translateY(-3px) scale(1.01);
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
    border-radius: 24px !important;
    overflow: visible !important;
    border: 1px solid var(--glass-border) !important;
    background: var(--glass-surface) !important;
    backdrop-filter: var(--glass-blur-md) !important;
    -webkit-backdrop-filter: var(--glass-blur-md) !important;
    box-shadow: var(--glass-shadow-md) !important;
}

.fi-ta-content {
    border-radius: 0 0 24px 24px !important;
    overflow-x: auto !important;
}

.fi-ta-content,
.fi-ta-table,
.fi-ta-table > thead,
.fi-ta-table > tbody {
    background: transparent !important;
}

.fi-ta-header-cell {
    background: rgba(228, 239, 232, 0.95) !important;
    color: var(--glass-text-main) !important;
    font-weight: 800 !important;
    font-size: 0.78rem !important;
    letter-spacing: 0.6px !important;
    text-transform: uppercase !important;
    border-bottom: 1px solid var(--glass-border) !important;
    padding: 1rem 1.15rem !important;
}

:is(.dark) .fi-ta-header-cell {
    background: rgba(14, 22, 17, 0.85) !important;
    color: var(--glass-text-secondary) !important;
    border-bottom-color: var(--glass-border-subtle) !important;
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

.fi-ta-search-field .fi-input-wrp {
    border-radius: 9999px !important;
}

.fi-pagination-item-btn {
    border-radius: 9999px !important;
    font-weight: 600 !important;
    transition: all 0.2s ease !important;
}

.fi-pagination-item-active .fi-pagination-item-btn {
    background: var(--glass-primary) !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px var(--glass-primary-glow) !important;
}

/* =============================================================================
   6. APPLE SPECULAR BUTTONS & CAPSULES
   ============================================================================= */
.fi-btn {
    border-radius: 9999px !important;
    font-weight: 700 !important;
    font-size: 0.875rem !important;
    letter-spacing: -0.01em !important;
    padding: 0.6rem 1.45rem !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    border: 1px solid transparent !important;
}

.fi-btn-size-sm {
    padding: 0.4rem 1rem !important;
    font-size: 0.8rem !important;
}

.fi-btn-size-lg {
    padding: 0.8rem 2rem !important;
    font-size: 0.95rem !important;
}

/* Primary Button */
.fi-btn-color-primary {
    background: linear-gradient(135deg, #0E6838 0%, #09502A 100%) !important;
    color: #FFFFFF !important;
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
    box-shadow: 0 4px 14px var(--glass-primary-glow), inset 0 1px 1px rgba(255, 255, 255, 0.5) !important;
}

:is(.dark) .fi-btn-color-primary {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%) !important;
    color: #022C15 !important;
    font-weight: 800 !important;
    border: 1px solid rgba(255, 255, 255, 0.4) !important;
    box-shadow: 0 4px 16px rgba(16, 185, 129, 0.4), inset 0 1px 1px rgba(255, 255, 255, 0.6) !important;
}

.fi-btn-color-primary:hover {
    transform: translateY(-2px) scale(1.02) !important;
    box-shadow: 0 8px 24px var(--glass-primary-glow), inset 0 1px 1px rgba(255, 255, 255, 0.6) !important;
    filter: brightness(1.08) !important;
}

.fi-btn-color-primary:active {
    transform: translateY(0) scale(0.99) !important;
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

/* =============================================================================
   8. APPLE BADGES & STATUS CHIPS
   ============================================================================= */
.fi-badge {
    border-radius: 9999px !important;
    font-size: 0.76rem !important;
    font-weight: 700 !important;
    padding: 0.32rem 0.85rem !important;
    letter-spacing: 0.2px !important;
    backdrop-filter: var(--glass-blur-sm) !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04) !important;
}

:is(.dark) .fi-badge {
    border-color: rgba(255, 255, 255, 0.12) !important;
}

/* =============================================================================
   9. VISIONOS FROSTED MODALS & DIALOGS
   ============================================================================= */
.fi-modal-close-overlay {
    background: rgba(7, 13, 9, 0.65) !important;
    backdrop-filter: blur(16px) saturate(180%) !important;
    -webkit-backdrop-filter: blur(16px) saturate(180%) !important;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}

.fi-modal-window {
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
    min-width: 20rem !important;
    max-width: 28rem !important;
    width: max-content !important;
    border-radius: 20px !important;
    border: 1px solid var(--glass-border) !important;
    box-shadow: var(--glass-shadow-lg) !important;
    background: var(--glass-surface) !important;
    backdrop-filter: var(--glass-blur-lg) !important;
    -webkit-backdrop-filter: var(--glass-blur-lg) !important;
    padding: 0.6rem !important;
    z-index: 100 !important;
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
</style>
