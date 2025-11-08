<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: #f8f9fa;
    padding-top: 70px;
    padding-bottom: env(safe-area-inset-bottom, 70px);
}

/* Portal Header */
.portal-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    z-index: 1000;
    padding: 0;
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
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 998;
}

.mobile-menu-overlay.active {
    opacity: 1;
    visibility: visible;
}

/* Mobile Menu Panel */
.mobile-menu-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: 280px;
    max-width: 85%;
    height: 100vh;
    background: white;
    box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
    transform: translateX(100%);
    transition: transform 0.3s ease;
    z-index: 999;
    overflow-y: auto;
    padding-top: 60px;
}

.mobile-menu-panel.active {
    transform: translateX(0);
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
    display: flex;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: white;
    border-top: 1px solid #e9ecef;
    box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.08);
    z-index: 1000;
    padding: 8px 0 env(safe-area-inset-bottom, 8px) 0;
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
    body {
        padding-bottom: 0;
    }

    .portal-nav-desktop {
        display: flex !important;
    }

    .hamburger-menu {
        display: none;
    }

    .bottom-nav {
        display: none;
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
