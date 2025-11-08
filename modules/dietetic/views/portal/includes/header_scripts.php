<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
// Hamburger Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerMenu = document.getElementById('hamburgerMenu');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
    const mobileMenuPanel = document.getElementById('mobileMenuPanel');

    function toggleMenu() {
        if (!hamburgerMenu || !mobileMenuOverlay || !mobileMenuPanel) return;

        hamburgerMenu.classList.toggle('active');
        mobileMenuOverlay.classList.toggle('active');
        mobileMenuPanel.classList.toggle('active');

        // Prevent body scroll when menu is open
        if (mobileMenuPanel.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = '';
        }
    }

    if (hamburgerMenu) {
        hamburgerMenu.addEventListener('click', toggleMenu);
    }

    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', toggleMenu);
    }

    // Close menu on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileMenuPanel && mobileMenuPanel.classList.contains('active')) {
            toggleMenu();
        }
    });

    // Touch feedback for mobile
    document.querySelectorAll('.bottom-nav-item, .mobile-menu-item').forEach(function(element) {
        element.addEventListener('touchstart', function() {
            this.style.opacity = '0.7';
        });
        element.addEventListener('touchend', function() {
            this.style.opacity = '';
        });
    });
});
</script>
