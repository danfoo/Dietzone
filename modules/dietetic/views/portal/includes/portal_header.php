<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Portail Patient</title>

    <!-- CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

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

        /* Mobile Hamburger Menu */
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
            top: 70px;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Mobile Menu Panel */
        .mobile-menu-panel {
            position: fixed;
            top: 70px;
            right: 0;
            width: 280px;
            max-width: 85%;
            height: calc(100vh - 70px);
            background: white;
            box-shadow: -4px 0 12px rgba(0, 0, 0, 0.1);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            z-index: 1000;
            overflow-y: auto;
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
</head>
<body>
    <!-- Portal Header -->
    <div class="portal-header">
        <div class="portal-header-content">
            <!-- Logo -->
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="portal-logo">
                <?php
                $logo_path = get_option('company_logo_dark');
                if (!$logo_path || !file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                    $logo_path = get_option('company_logo');
                }

                if ($logo_path && file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                ?>
                    <img src="<?php echo base_url('uploads/company/' . $logo_path); ?>" alt="<?php echo get_option('companyname'); ?>">
                <?php } else { ?>
                    <div class="portal-logo-text">
                        <i class="fa fa-heartbeat"></i>
                        <span><?php echo get_option('companyname') ? get_option('companyname') : 'Dietetic'; ?></span>
                    </div>
                <?php } ?>
            </a>

            <!-- Desktop Navigation -->
            <nav class="portal-nav-desktop">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="<?php echo !isset($active_page) || $active_page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fa fa-home"></i> Accueil
                </a>
                <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="<?php echo isset($active_page) && $active_page == 'meal_plans' ? 'active' : ''; ?>">
                    <i class="fa fa-cutlery"></i> Repas
                </a>
                <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
                <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="<?php echo isset($active_page) && $active_page == 'food_surveys' ? 'active' : ''; ?>">
                    <i class="fa fa-clipboard-list"></i> Enquêtes
                </a>
                <?php } ?>
                <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="<?php echo isset($active_page) && $active_page == 'measurements' ? 'active' : ''; ?>">
                    <i class="fa fa-heartbeat"></i> Mesures
                </a>
                <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="<?php echo isset($active_page) && $active_page == 'dietitians' ? 'active' : ''; ?>">
                    <i class="fa fa-user-md"></i> Diététicien
                </a>
                <?php if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) { ?>
                <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="<?php echo isset($active_page) && $active_page == 'notifications' ? 'active' : ''; ?>">
                    <i class="fa fa-bell"></i> Notifications
                </a>
                <?php } ?>
                <a href="<?php echo site_url('clients/profile'); ?>" class="<?php echo isset($active_page) && $active_page == 'profile' ? 'active' : ''; ?>">
                    <i class="fa fa-user"></i> Profil
                </a>
            </nav>

            <!-- Hamburger Menu (Mobile) -->
            <div class="hamburger-menu" id="hamburgerMenu">
                <div class="hamburger-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu Panel -->
    <div class="mobile-menu-panel" id="mobileMenuPanel">
        <div class="mobile-menu-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="mobile-menu-item <?php echo !isset($active_page) || $active_page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'meal_plans' ? 'active' : ''; ?>">
                <i class="fa fa-cutlery"></i>
                <span>Plans de Repas</span>
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'food_surveys' ? 'active' : ''; ?>">
                <i class="fa fa-clipboard-list"></i>
                <span>Enquêtes Alimentaires</span>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'measurements' ? 'active' : ''; ?>">
                <i class="fa fa-heartbeat"></i>
                <span>Mes Mesures</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'dietitians' ? 'active' : ''; ?>">
                <i class="fa fa-user-md"></i>
                <span>Mon Diététicien</span>
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) { ?>
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'notifications' ? 'active' : ''; ?>">
                <i class="fa fa-bell"></i>
                <span>Notifications</span>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('clients/profile'); ?>" class="mobile-menu-item <?php echo isset($active_page) && $active_page == 'profile' ? 'active' : ''; ?>">
                <i class="fa fa-user"></i>
                <span>Mon Profil</span>
            </a>
            <a href="<?php echo site_url('authentication/logout'); ?>" class="mobile-menu-item">
                <i class="fa fa-sign-out"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="bottom-nav-item <?php echo !isset($active_page) || $active_page == 'dashboard' ? 'active' : ''; ?>">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'meal_plans' ? 'active' : ''; ?>">
                <i class="fa fa-cutlery"></i>
                <span>Repas</span>
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'food_surveys' ? 'active' : ''; ?>">
                <i class="fa fa-clipboard-list"></i>
                <span>Enquêtes</span>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'measurements' ? 'active' : ''; ?>">
                <i class="fa fa-plus-circle"></i>
                <span>Mesure</span>
            </a>
            <a href="<?php echo site_url('clients/profile'); ?>" class="bottom-nav-item <?php echo isset($active_page) && $active_page == 'profile' ? 'active' : ''; ?>">
                <i class="fa fa-user"></i>
                <span>Profil</span>
            </a>
        </div>
    </nav>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

    <script>
        // Hamburger Menu Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerMenu = document.getElementById('hamburgerMenu');
            const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');
            const mobileMenuPanel = document.getElementById('mobileMenuPanel');

            function toggleMenu() {
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

            hamburgerMenu.addEventListener('click', toggleMenu);
            mobileMenuOverlay.addEventListener('click', toggleMenu);

            // Touch feedback for mobile
            document.querySelectorAll('.bottom-nav-item, .mobile-menu-item').forEach(function(element) {
                element.addEventListener('touchstart', function() {
                    this.style.opacity = '0.7';
                });
                element.addEventListener('touchend', function() {
                    this.style.opacity = '';
                });
            });

            // Close menu on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileMenuPanel.classList.contains('active')) {
                    toggleMenu();
                }
            });
        });
    </script>
