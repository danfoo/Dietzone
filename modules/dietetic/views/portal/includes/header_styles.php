<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
/* Portal Patient Specific Styles - Scoped to avoid conflicts */

body.dietetic-portal-page {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    background: #f8f9fa !important;
    padding-top: 70px !important;
    padding-bottom: 70px !important;
}

@supports (padding: env(safe-area-inset-bottom)) {
    body.dietetic-portal-page {
        padding-bottom: calc(70px + env(safe-area-inset-bottom)) !important;
    }
}

/* Portal Header */
.portal-header {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    background: white !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
    z-index: 9999 !important;
    padding: 0 !important;
    margin: 0 !important;
}

.portal-header-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 20px;
    gap: 20px;
}

.portal-logo {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #01807B;
    font-weight: 700;
    font-size: 18px;
}

.portal-logo img {
    max-height: 40px;
    max-width: 150px;
    object-fit: contain;
}

.portal-logo-text {
    display: flex;
    align-items: center;
    gap: 8px;
}

.portal-logo-text i {
    font-size: 24px;
}

/* Desktop Navigation */
.portal-nav-desktop {
    display: none;
    gap: 8px;
    align-items: center;
    flex: 1;
    justify-content: center;
}

.portal-nav-desktop a {
    padding: 10px 18px;
    color: #495057;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    white-space: nowrap;
}

.portal-nav-desktop a:hover {
    background: #f8f9fa;
    color: #01807B;
}

.portal-nav-desktop a.active {
    background: #01807B;
    color: white;
}

.portal-nav-desktop a i {
    font-size: 16px;
}

/* Hamburger Menu */
.hamburger-menu {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.hamburger-menu:hover {
    background: #f8f9fa;
}

.hamburger-icon {
    width: 28px;
    height: 24px;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.hamburger-icon span {
    display: block;
    height: 3px;
    background: #01807B;
    border-radius: 3px;
    transition: all 0.3s ease;
}

.hamburger-menu.active .hamburger-icon span:nth-child(1) {
    transform: translateY(10.5px) rotate(45deg);
}

.hamburger-menu.active .hamburger-icon span:nth-child(2) {
    opacity: 0;
}

.hamburger-menu.active .hamburger-icon span:nth-child(3) {
    transform: translateY(-10.5px) rotate(-45deg);
}

/* Mobile Menu Overlay */
.mobile-menu-overlay {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: rgba(0, 0, 0, 0.5) !important;
    opacity: 0 !important;
    visibility: hidden !important;
    transition: all 0.3s ease !important;
    z-index: 99998 !important;
}

.mobile-menu-overlay.active {
    opacity: 1 !important;
    visibility: visible !important;
}

/* Mobile Menu Panel */
.mobile-menu-panel {
    position: fixed !important;
    top: 0 !important;
    right: 0 !important;
    width: 280px !important;
    max-width: 85% !important;
    height: 100vh !important;
    background: white !important;
    box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1) !important;
    transform: translateX(100%) !important;
    transition: transform 0.3s ease !important;
    z-index: 99999 !important;
    overflow-y: auto !important;
    padding-top: 60px !important;
}

.mobile-menu-panel.active {
    transform: translateX(0) !important;
}

.mobile-menu-items {
    padding: 20px 0;
}

.mobile-menu-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px 25px;
    color: #495057;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s ease;
    border-left: 4px solid transparent;
}

.mobile-menu-item:hover {
    background: #f8f9fa;
    color: #01807B;
}

.mobile-menu-item.active {
    background: #e8f5f4;
    color: #01807B;
    border-left-color: #01807B;
}

.mobile-menu-item i {
    font-size: 20px;
    width: 24px;
    text-align: center;
}

/* Bottom Navigation (Mobile) */
.bottom-nav {
    display: flex !important;
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    background: white !important;
    border-top: 1px solid #e9ecef !important;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08) !important;
    z-index: 9998 !important;
    padding: 8px 0 8px 0 !important;
    margin: 0 !important;
}

@supports (padding: env(safe-area-inset-bottom)) {
    .bottom-nav {
        padding-bottom: env(safe-area-inset-bottom) !important;
    }
}

.bottom-nav-items {
    display: flex;
    justify-content: space-around;
    align-items: center;
    max-width: 600px;
    margin: 0 auto;
    width: 100%;
}

.bottom-nav-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding: 8px;
    color: #6c757d;
    text-decoration: none;
    transition: all 0.2s ease;
    border-radius: 12px;
    min-width: 60px;
    position: relative;
}

.bottom-nav-item.active {
    color: #01807B;
}

.bottom-nav-item i {
    font-size: 22px;
}

.bottom-nav-item.active i {
    transform: scale(1.1);
}

.bottom-nav-item span {
    font-size: 11px;
    font-weight: 600;
}

.bottom-nav-item.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 32px;
    height: 3px;
    background: #01807B;
    border-radius: 0 0 3px 3px;
}

/* Desktop Breakpoint */
@media (min-width: 992px) {
    body.dietetic-portal-page {
        padding-bottom: 0 !important;
    }

    .portal-nav-desktop {
        display: flex !important;
    }

    .hamburger-menu {
        display: none !important;
    }

    .bottom-nav {
        display: none !important;
    }

    .mobile-menu-overlay,
    .mobile-menu-panel {
        display: none !important;
    }
}

/* Touch Feedback */
.mobile-menu-item:active,
.bottom-nav-item:active {
    transform: scale(0.95);
}

@media (max-width: 375px) {
    .portal-logo img {
        max-height: 32px;
        max-width: 120px;
    }

    .portal-logo-text {
        font-size: 16px;
    }

    .bottom-nav-item span {
        font-size: 10px;
    }

    .bottom-nav-item i {
        font-size: 20px;
    }
}
</style>
