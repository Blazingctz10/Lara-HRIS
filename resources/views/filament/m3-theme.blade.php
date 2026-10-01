<!-- Google Fonts: Plus Jakarta Sans for Material 3 Expressive -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* =============================================================================
   PT BPR BANDA RAYA - CORE HRIS
   MATERIAL 3 EXPRESSIVE DESIGN SYSTEM (LIGHT & DARK MODES)
   High Reader Ergonomics, Zero Eye Strain, Tactile Tonal Containers
   Unified Font: Plus Jakarta Sans
   ============================================================================= */

:root {
    --m3-font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --m3-font-display: 'Plus Jakarta Sans', var(--m3-font-sans);

    /* Light Mode: Soft Tonal Ergonomics (No harsh 100% white glare) */
    --m3-bg: #F4F6F4;
    --m3-surface: #FFFFFF;
    --m3-surface-dim: #EDF2EE;
    --m3-surface-bright: #FFFFFF;
    --m3-surface-container-lowest: #FFFFFF;
    --m3-surface-container-low: #F0F5F2;
    --m3-surface-container: #E8EFEA;
    --m3-surface-container-high: #E0E9E3;
    --m3-surface-container-highest: #D7E2DB;

    /* Text & Legibility */
    --m3-on-surface: #131A15;
    --m3-on-surface-variant: #404B44;
    --m3-outline: #707C74;
    --m3-outline-variant: #C0CDC5;

    /* Corporate Accents */
    --m3-primary: #0E6838;
    --m3-on-primary: #FFFFFF;
    --m3-primary-container: #D1EADB;
    --m3-on-primary-container: #03361B;

    --m3-secondary: #EA580C;
    --m3-on-secondary: #FFFFFF;
    --m3-secondary-container: #FFEDD5;
    --m3-on-secondary-container: #7C2D12;

    --m3-tertiary: #CA8A04;
    --m3-on-tertiary: #FFFFFF;
    --m3-tertiary-container: #FEF08A;
    --m3-on-tertiary-container: #713F12;

    /* Elevation */
    --m3-elevation-1: 0 1px 3px 1px rgba(0, 0, 0, 0.06), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
    --m3-elevation-2: 0 4px 14px 0 rgba(14, 104, 56, 0.08), 0 2px 4px 0 rgba(0, 0, 0, 0.03);
    --m3-elevation-3: 0 8px 24px -4px rgba(14, 104, 56, 0.12), 0 4px 8px -2px rgba(0, 0, 0, 0.04);
}

.dark {
    /* Dark Mode: Deep Soothing Slate-Emerald (No eye-fatiguing #000000) */
    --m3-bg: #0D1410;
    --m3-surface: #111A14;
    --m3-surface-dim: #0B110D;
    --m3-surface-bright: #1A261F;
    --m3-surface-container-lowest: #0F1612;
    --m3-surface-container-low: #141E18;
    --m3-surface-container: #19261E;
    --m3-surface-container-high: #203026;
    --m3-surface-container-highest: #283B30;

    /* Text & Legibility */
    --m3-on-surface: #E2E8E4;
    --m3-on-surface-variant: #9EABA3;
    --m3-outline: #7A8980;
    --m3-outline-variant: #304036;

    /* Corporate Accents (Vibrant & Ergonomic for Dark Surface) */
    --m3-primary: #4ADE80;
    --m3-on-primary: #003A19;
    --m3-primary-container: #0B4F27;
    --m3-on-primary-container: #BBF7D0;

    --m3-secondary: #FB923C;
    --m3-on-secondary: #541C00;
    --m3-secondary-container: #7C2D12;
    --m3-on-secondary-container: #FED7AA;

    --m3-tertiary: #FACC15;
    --m3-on-tertiary: #422200;
    --m3-tertiary-container: #713F12;
    --m3-on-tertiary-container: #FEF08A;

    /* Elevation */
    --m3-elevation-1: 0 2px 6px rgba(0, 0, 0, 0.35);
    --m3-elevation-2: 0 6px 18px rgba(0, 0, 0, 0.45);
    --m3-elevation-3: 0 12px 32px rgba(0, 0, 0, 0.55);
}

/* Base Document Typography */
html, body {
    font-family: var(--m3-font-sans) !important;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
}

/* Background Application */
body.fi-body {
    background-color: var(--m3-bg) !important;
    color: var(--m3-on-surface) !important;
}

/* =============================================================================
   1. FILAMENT SIDEBAR (M3 Navigation Drawer with Ergonomic Pills)
   ============================================================================= */
.fi-sidebar {
    background-color: var(--m3-surface-container-low) !important;
    border-right: 1px solid var(--m3-outline-variant) !important;
    transition: all 0.25s cubic-bezier(0.2, 0, 0, 1);
}

.fi-sidebar-header {
    border-bottom: 1px solid var(--m3-outline-variant) !important;
    background-color: var(--m3-surface-container-low) !important;
    padding-top: 1.25rem !important;
    padding-bottom: 1.25rem !important;
}

/* Nav Item Styling */
.fi-sidebar-item-btn {
    border-radius: 9999px !important; /* M3 Expressive Pill */
    padding: 0.65rem 1rem !important;
    margin: 0.15rem 0.6rem !important;
    font-weight: 600 !important;
    font-size: 0.86rem !important;
    transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
    color: var(--m3-on-surface-variant) !important;
}

.fi-sidebar-item-btn:hover {
    background-color: var(--m3-surface-container-high) !important;
    color: var(--m3-on-surface) !important;
    transform: translateX(2px);
}

/* Active Nav Item: Expressive Primary Tonal Container */
.fi-sidebar-item-active .fi-sidebar-item-btn,
.fi-sidebar-item-btn[aria-current="page"] {
    background-color: var(--m3-primary-container) !important;
    color: var(--m3-on-primary-container) !important;
    font-weight: 700 !important;
    box-shadow: var(--m3-elevation-1) !important;
}

.fi-sidebar-item-active .fi-sidebar-item-icon {
    color: var(--m3-on-primary-container) !important;
}

.fi-sidebar-group-label {
    font-size: 0.72rem !important;
    letter-spacing: 0.8px !important;
    text-transform: uppercase !important;
    font-weight: 800 !important;
    color: var(--m3-outline) !important;
    padding-left: 1.2rem !important;
}

/* =============================================================================
   2. FILAMENT TOPBAR & TOP NAVIGATION
   ============================================================================= */
.fi-topbar-ctn {
    overflow-x: visible !important;
}

.fi-topbar {
    background-color: var(--m3-surface-container-lowest) !important;
    border-bottom: 1px solid var(--m3-outline-variant) !important;
    box-shadow: var(--m3-elevation-1) !important;
    overflow: visible !important;
    padding-right: 1.5rem !important;
    box-sizing: border-box !important;
}

.fi-topbar nav {
    background-color: transparent !important;
    overflow: visible !important;
    padding-right: 0.5rem !important;
}

.fi-topbar-nav-groups {
    flex-shrink: 1 !important;
    min-width: 0 !important;
    overflow-x: auto !important;
    scrollbar-width: none !important;
}

.fi-topbar-nav-groups::-webkit-scrollbar {
    display: none !important;
}

.fi-topbar-end {
    margin-left: auto !important;
    flex-shrink: 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.75rem !important;
    padding-right: 0.5rem !important;
}

.fi-user-menu {
    flex-shrink: 0 !important;
    display: flex !important;
    align-items: center !important;
}

.fi-user-menu-trigger {
    flex-shrink: 0 !important;
    min-width: 2.25rem !important;
    min-height: 2.25rem !important;
}

.fi-topbar-item-btn {
    border-radius: 9999px !important;
    font-weight: 600 !important;
    transition: all 0.2s ease !important;
}

.fi-topbar-item-active .fi-topbar-item-btn {
    background-color: var(--m3-primary-container) !important;
    color: var(--m3-on-primary-container) !important;
    font-weight: 700 !important;
}

/* =============================================================================
   3. SECTIONS, CARDS & CONTAINERS (M3 Expressive Squircle 24px)
   ============================================================================= */
/* Filament Stats Overview Widget wrapper: transparent so each stat card renders standalone without outer nested box */
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

/* Genuine Section Cards (Forms, InfoLists, contained Sections) */
.fi-section:not(.fi-section-not-contained),
.fi-card {
    border-radius: 24px !important; /* M3 Expressive Card Radius */
    background-color: var(--m3-surface) !important;
    border: 1px solid var(--m3-outline-variant) !important;
    box-shadow: var(--m3-elevation-1) !important;
    transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease !important;
}

.fi-section:not(.fi-section-not-contained):hover,
.fi-card:hover {
    box-shadow: var(--m3-elevation-2) !important;
}

/* Standalone Stat Cards: Direct, Clean M3 Expressive Cards */
.fi-wi-stats-overview-stat {
    border-radius: 20px !important;
    background-color: var(--m3-surface) !important;
    border: 1px solid var(--m3-outline-variant) !important;
    box-shadow: var(--m3-elevation-1) !important;
    transition: box-shadow 0.2s ease, transform 0.2s ease, border-color 0.2s ease !important;
}

.fi-wi-stats-overview-stat:hover {
    box-shadow: var(--m3-elevation-2) !important;
    transform: translateY(-2px);
}

.fi-section-header {
    border-bottom: 1px solid var(--m3-outline-variant) !important;
    padding: 1.25rem 1.5rem !important;
}

.fi-section-header-title {
    font-family: var(--m3-font-display) !important;
    font-weight: 700 !important;
    font-size: 1.15rem !important;
    color: var(--m3-on-surface) !important;
}

.fi-section-header-description {
    color: var(--m3-on-surface-variant) !important;
    font-size: 0.85rem !important;
}

/* Stat Overview Cards */
.fi-wi-stats-overview-stat {
    padding: 1.25rem 1.5rem !important;
}

.fi-wi-stats-overview-stat-label {
    font-size: 0.8rem !important;
    font-weight: 600 !important;
    color: var(--m3-on-surface-variant) !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}

.fi-wi-stats-overview-stat-value {
    font-family: var(--m3-font-display) !important;
    font-weight: 800 !important;
    font-size: 1.85rem !important;
    color: var(--m3-on-surface) !important;
    margin-top: 0.35rem !important;
}

/* =============================================================================
   4. DATA TABLES (Clean, Ergonomic Rows & Tonal Headers)
   ============================================================================= */
.fi-ta-ctn {
    border-radius: 24px !important;
    overflow: hidden !important;
    border: 1px solid var(--m3-outline-variant) !important;
    background-color: var(--m3-surface) !important;
    box-shadow: var(--m3-elevation-1) !important;
}

.fi-ta-header-cell {
    background-color: var(--m3-surface-container-low) !important;
    color: var(--m3-on-surface-variant) !important;
    font-weight: 700 !important;
    font-size: 0.78rem !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    border-bottom: 1px solid var(--m3-outline-variant) !important;
    padding: 0.95rem 1rem !important;
}

.fi-ta-row {
    transition: background-color 0.15s ease !important;
    border-bottom: 1px solid var(--m3-outline-variant) !important;
}

.fi-ta-row:hover {
    background-color: var(--m3-surface-container-low) !important;
}

.fi-ta-cell {
    padding: 0.95rem 1rem !important;
    font-size: 0.875rem !important;
    color: var(--m3-on-surface) !important;
}

/* Table Search Input */
.fi-ta-search-field .fi-input-wrp {
    border-radius: 9999px !important; /* M3 Pill Search */
}

/* Table Pagination Buttons */
.fi-pagination-item-btn {
    border-radius: 9999px !important;
    font-weight: 600 !important;
}

.fi-pagination-item-active .fi-pagination-item-btn {
    background-color: var(--m3-primary) !important;
    color: var(--m3-on-primary) !important;
}

/* =============================================================================
   5. BUTTONS & CHIPS (M3 Full Pill Shapes)
   ============================================================================= */
.fi-btn {
    border-radius: 9999px !important; /* M3 Pill Button */
    font-weight: 700 !important;
    font-size: 0.86rem !important;
    padding: 0.6rem 1.35rem !important;
    transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
    letter-spacing: 0.2px !important;
}

.fi-btn-size-sm {
    padding: 0.4rem 1rem !important;
    font-size: 0.8rem !important;
}

.fi-btn-size-lg {
    padding: 0.8rem 1.85rem !important;
    font-size: 0.95rem !important;
}

/* Primary Button Styling */
.fi-btn-color-primary {
    background-color: var(--m3-primary) !important;
    color: var(--m3-on-primary) !important;
    box-shadow: 0 2px 8px rgba(14, 104, 56, 0.25) !important;
}

.fi-btn-color-primary:hover {
    filter: brightness(1.1) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 14px rgba(14, 104, 56, 0.35) !important;
}

.fi-btn-color-primary:active {
    transform: translateY(0) !important;
}

/* Outlined / Gray Buttons */
.fi-btn-color-gray {
    border-color: var(--m3-outline-variant) !important;
    color: var(--m3-on-surface) !important;
}

.fi-btn-color-gray:hover {
    background-color: var(--m3-surface-container-high) !important;
}

/* =============================================================================
   6. FORM INPUTS & CONTROLS (Squircle 14px with Gentle Optical Focus)
   ============================================================================= */
.fi-input-wrp {
    border-radius: 14px !important; /* M3 Squircle Input */
    background-color: var(--m3-surface-container-lowest) !important;
    border: 1px solid var(--m3-outline-variant) !important;
    box-shadow: none !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

.fi-input-wrp:focus-within {
    border-color: var(--m3-primary) !important;
    box-shadow: 0 0 0 2px var(--m3-primary-container) !important;
}

.fi-input {
    font-size: 0.9rem !important;
    color: var(--m3-on-surface) !important;
    padding: 0.65rem 0.85rem !important;
}

.fi-fo-field-wrp-label {
    font-weight: 600 !important;
    font-size: 0.85rem !important;
    color: var(--m3-on-surface) !important;
    margin-bottom: 0.35rem !important;
}

/* =============================================================================
   7. BADGES & CHIPS (Full Pill with Soft Tonal Accents)
   ============================================================================= */
.fi-badge {
    border-radius: 9999px !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    padding: 0.3rem 0.75rem !important;
    letter-spacing: 0.3px !important;
}

/* =============================================================================
   8. MODALS & DIALOGS (M3 Expressive Squircle 28px)
   ============================================================================= */
.fi-modal-window {
    border-radius: 28px !important;
    border: 1px solid var(--m3-outline-variant) !important;
    background-color: var(--m3-surface) !important;
    box-shadow: var(--m3-elevation-3) !important;
    overflow: hidden !important;
}

.fi-modal-header {
    border-bottom: 1px solid var(--m3-outline-variant) !important;
    padding: 1.5rem !important;
}

.fi-modal-footer {
    border-top: 1px solid var(--m3-outline-variant) !important;
    background-color: var(--m3-surface-container-low) !important;
    padding: 1.25rem 1.5rem !important;
}

/* =============================================================================
   9. TABS (M3 Pill Indicator)
   ============================================================================= */
.fi-tabs {
    border-bottom: 1px solid var(--m3-outline-variant) !important;
}

.fi-tabs-item-btn {
    border-radius: 9999px !important;
    font-weight: 600 !important;
    padding: 0.5rem 1.25rem !important;
    transition: all 0.2s ease !important;
    color: var(--m3-on-surface-variant) !important;
}

.fi-tabs-item-active .fi-tabs-item-btn {
    background-color: var(--m3-primary-container) !important;
    color: var(--m3-on-primary-container) !important;
    font-weight: 700 !important;
}

/* =============================================================================
   10. DROPDOWNS & USER MENU (M3 Surface Container & Full Name Display)
   ============================================================================= */
.fi-dropdown-panel {
    min-width: 20rem !important;
    max-width: 28rem !important;
    width: max-content !important;
    border-radius: 20px !important;
    border: 1px solid var(--m3-outline-variant) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12) !important;
    background-color: var(--m3-surface) !important;
    padding: 0.5rem !important;
}

:is(.dark) .fi-dropdown-panel {
    background-color: var(--m3-surface-container-low) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 14px 36px rgba(0, 0, 0, 0.5) !important;
}

.fi-dropdown-header {
    padding: 0.75rem 1rem !important;
    border-bottom: 1px solid var(--m3-outline-variant) !important;
    margin-bottom: 0.35rem !important;
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
    color: var(--m3-on-surface) !important;
    line-height: 1.35 !important;
}

.fi-dropdown-list-item-btn,
.fi-dropdown-list-item-link {
    border-radius: 14px !important;
    padding: 0.65rem 1rem !important;
    transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
    color: var(--m3-on-surface) !important;
}

.fi-dropdown-list-item-btn:hover,
.fi-dropdown-list-item-link:hover {
    background-color: var(--m3-surface-container-high) !important;
    transform: translateX(2px);
}

.fi-theme-switcher {
    border-radius: 14px !important;
    background-color: var(--m3-surface-container) !important;
    padding: 0.25rem !important;
    margin: 0.35rem 0.5rem !important;
    border: 1px solid var(--m3-outline-variant) !important;
}

.fi-theme-switcher-btn {
    border-radius: 10px !important;
    transition: all 0.2s ease !important;
}

/* =============================================================================
   11. MATERIAL 3 EXPRESSIVE MODALS & DIALOG POPUPS
   Tactile 28px Containers, Frosted Scrim, M3 Action Pills & Elevated Toasts
   ============================================================================= */

/* Modal Scrim / Backdrop */
.fi-modal-close-overlay {
    background-color: rgba(13, 20, 16, 0.68) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    transition: all 0.25s cubic-bezier(0.2, 0, 0, 1) !important;
}

/* Modal Window Container */
.fi-modal-window {
    border-radius: 28px !important;
    background-color: var(--m3-surface) !important;
    border: 1px solid var(--m3-outline-variant) !important;
    box-shadow: 0 24px 64px -12px rgba(0, 0, 0, 0.25), 0 8px 24px rgba(14, 104, 56, 0.08) !important;
    overflow: hidden !important;
    transition: all 0.25s cubic-bezier(0.2, 0, 0, 1) !important;
}

:is(.dark) .fi-modal-window {
    background-color: var(--m3-surface-container-high) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 28px 72px -12px rgba(0, 0, 0, 0.65), 0 0 0 1px rgba(255, 255, 255, 0.05) !important;
}

/* Slide-over Modals (Panel Slide from Edge) */
.fi-modal-window.fi-modal-slide-over {
    border-top-left-radius: 28px !important;
    border-bottom-left-radius: 28px !important;
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

/* Modal Header */
.fi-modal-header {
    padding: 1.5rem 1.75rem 1.15rem !important;
    border-bottom: 1px solid var(--m3-outline-variant) !important;
    background-color: var(--m3-surface) !important;
}

:is(.dark) .fi-modal-header {
    background-color: var(--m3-surface-container-high) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

.fi-modal-heading {
    font-family: var(--m3-font-display) !important;
    font-size: 1.25rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.015em !important;
    color: var(--m3-on-surface) !important;
}

.fi-modal-description {
    font-size: 0.875rem !important;
    color: var(--m3-on-surface-variant) !important;
    line-height: 1.5 !important;
    margin-top: 0.35rem !important;
}

/* Modal Close Button */
.fi-modal-close-btn {
    border-radius: 9999px !important;
    padding: 0.5rem !important;
    color: var(--m3-on-surface-variant) !important;
    transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
}

.fi-modal-close-btn:hover {
    background-color: var(--m3-surface-container-highest) !important;
    color: var(--m3-on-surface) !important;
    transform: rotate(90deg);
}

/* Modal Content / Body */
.fi-modal-content {
    padding: 1.5rem 1.75rem !important;
    color: var(--m3-on-surface) !important;
}

/* Modal Footer & Action Buttons */
.fi-modal-footer {
    padding: 1.15rem 1.75rem !important;
    border-top: 1px solid var(--m3-outline-variant) !important;
    background-color: var(--m3-surface-container-low) !important;
}

:is(.dark) .fi-modal-footer {
    background-color: var(--m3-surface-container) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

.fi-modal-footer .fi-btn {
    border-radius: 9999px !important; /* M3 Action Pill */
    font-weight: 700 !important;
    font-size: 0.875rem !important;
    padding: 0.55rem 1.35rem !important;
    transition: all 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
    letter-spacing: 0.1px !important;
}

.fi-modal-footer .fi-btn:hover {
    transform: translateY(-1px) !important;
}

/* Danger Modal Action (Confirm Delete) */
.fi-modal-footer .fi-btn-color-danger {
    background-color: #DC2626 !important;
    color: #FFFFFF !important;
    box-shadow: 0 4px 14px rgba(220, 38, 38, 0.3) !important;
}

.fi-modal-footer .fi-btn-color-danger:hover {
    background-color: #B91C1C !important;
    box-shadow: 0 6px 18px rgba(220, 38, 38, 0.45) !important;
}

/* Secondary/Cancel Modal Action */
.fi-modal-footer .fi-btn-color-gray {
    background-color: transparent !important;
    border: 1px solid var(--m3-outline) !important;
    color: var(--m3-on-surface) !important;
}

.fi-modal-footer .fi-btn-color-gray:hover {
    background-color: var(--m3-surface-container-high) !important;
}

/* Confirmation Dialog Icon Badge */
.fi-modal-icon-bg {
    border-radius: 18px !important;
    padding: 0.75rem !important;
}

/* Notifications & Toast Popups */
.fi-no-notification {
    border-radius: 20px !important;
    background-color: var(--m3-surface) !important;
    border: 1px solid var(--m3-outline-variant) !important;
    box-shadow: var(--m3-elevation-3) !important;
    padding: 1rem 1.25rem !important;
}

:is(.dark) .fi-no-notification {
    background-color: var(--m3-surface-container-high) !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    box-shadow: 0 16px 36px rgba(0, 0, 0, 0.5) !important;
}

.fi-no-notification-title {
    font-family: var(--m3-font-sans) !important;
    font-weight: 800 !important;
    font-size: 0.925rem !important;
    color: var(--m3-on-surface) !important;
}

.fi-no-notification-date,
.fi-no-notification-body {
    font-size: 0.825rem !important;
    color: var(--m3-on-surface-variant) !important;
    line-height: 1.45 !important;
}

/* Custom Scrollbar for Smooth Ergonomics */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--m3-bg);
}

::-webkit-scrollbar-thumb {
    background: var(--m3-outline-variant);
    border-radius: 9999px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--m3-outline);
}
</style>
