<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Portail Patient</title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
        }

        body {
            background: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            min-height: 100vh;
            padding-top: 70px; /* Space for fixed header */
            padding-bottom: 75px; /* Space for fixed footer */
        }

        /* ============================================
           HEADER MAGNIFIQUE
           ============================================ */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            box-shadow: 0 4px 20px rgba(1, 128, 123, 0.3);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .header-logo:hover {
            transform: scale(1.05);
        }

        .header-logo img {
            height: 45px;
            width: auto;
            filter: brightness(0) invert(1);
        }

        .header-logo-text {
            font-size: 22px;
            font-weight: 700;
            color: white;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo-text i {
            font-size: 28px;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            25% { transform: scale(1.1); }
            50% { transform: scale(1); }
        }

        /* Hamburger Menu Button */
        .hamburger-btn {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            transition: all 0.3s;
            backdrop-filter: blur(10px);
        }

        .hamburger-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .hamburger-btn:active {
            transform: scale(0.95);
        }

        .hamburger-line {
            width: 28px;
            height: 3px;
            background: white;
            border-radius: 3px;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        .hamburger-btn.active .hamburger-line:nth-child(1) {
            transform: translateY(8px) rotate(45deg);
        }

        .hamburger-btn.active .hamburger-line:nth-child(2) {
            opacity: 0;
            transform: translateX(20px);
        }

        .hamburger-btn.active .hamburger-line:nth-child(3) {
            transform: translateY(-8px) rotate(-45deg);
        }

        /* Menu Overlay */
        .menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        /* Slide Menu */
        .slide-menu {
            position: fixed;
            top: 0;
            right: -350px;
            width: 320px;
            height: 100vh;
            background: white;
            z-index: 1001;
            transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
        }

        .slide-menu.active {
            right: 0;
        }

        .menu-header {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            padding: 30px 20px;
            color: white;
            text-align: center;
        }

        .menu-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 700;
        }

        .menu-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        .menu-items {
            padding: 15px 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 16px 20px;
            color: #2c3e50;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
            position: relative;
        }

        .menu-item:hover {
            background: linear-gradient(90deg, rgba(1, 128, 123, 0.1) 0%, transparent 100%);
            border-left-color: #01807B;
            padding-left: 25px;
        }

        .menu-item.active {
            background: linear-gradient(90deg, rgba(1, 128, 123, 0.15) 0%, transparent 100%);
            border-left-color: #01807B;
            font-weight: 600;
        }

        .menu-item i {
            font-size: 22px;
            width: 30px;
            text-align: center;
            color: #01807B;
        }

        .menu-item span {
            flex: 1;
            font-size: 16px;
        }

        .menu-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
            margin: 10px 20px;
        }

        /* ============================================
           FOOTER MAGNIFIQUE
           ============================================ */
        .app-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 75px;
            background: white;
            border-top: 3px solid #01807B;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0 10px;
        }

        .footer-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 5px;
            padding: 10px 5px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            position: relative;
        }

        .footer-item:hover {
            background: rgba(1, 128, 123, 0.05);
        }

        .footer-item:active {
            transform: scale(0.95);
        }

        .footer-item.active {
            color: #01807B;
        }

        .footer-item.active::before {
            content: '';
            position: absolute;
            top: -3px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 3px;
            background: #01807B;
            border-radius: 0 0 3px 3px;
        }

        .footer-icon {
            font-size: 24px;
            transition: all 0.3s;
        }

        .footer-item.active .footer-icon {
            transform: scale(1.1);
            color: #01807B;
        }

        .footer-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .footer-item.active .footer-label {
            font-weight: 700;
        }

        /* Content Container */
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        @media (max-width: 768px) {
            .content-container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER MAGNIFIQUE -->
    <header class="app-header">
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="header-logo">
            <?php
            $logo_path = get_option('company_logo_dark');
            if (!$logo_path || !file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                $logo_path = get_option('company_logo');
            }

            if ($logo_path && file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
            ?>
                <img src="<?php echo base_url('uploads/company/' . $logo_path); ?>" alt="<?php echo get_option('companyname'); ?>">
            <?php } else { ?>
                <div class="header-logo-text">
                    <i class="fa fa-heartbeat"></i>
                    <span><?php echo get_option('companyname') ? get_option('companyname') : 'DietSenegal'; ?></span>
                </div>
            <?php } ?>
        </a>

        <button class="hamburger-btn" id="menuToggle">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
    </header>

    <!-- MENU OVERLAY -->
    <div class="menu-overlay" id="menuOverlay"></div>

    <!-- SLIDE MENU -->
    <nav class="slide-menu" id="slideMenu">
        <div class="menu-header">
            <h3><?php echo isset($client->company) && $client->company ? htmlspecialchars($client->company) : (isset($patient->client->company) && $patient->client->company ? htmlspecialchars($patient->client->company) : 'Mon Compte'); ?></h3>
            <p>Portail Patient</p>
        </div>

        <div class="menu-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="menu-item <?php echo (!isset($active_page) || $active_page == 'dashboard') ? 'active' : ''; ?>">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'meal_plans') ? 'active' : ''; ?>">
                <i class="fa fa-cutlery"></i>
                <span>Plans de Repas</span>
            </a>

            <?php
            // Check if food surveys feature is enabled
            $CI_menu = &get_instance();
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_food_surveys')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'food_surveys') ? 'active' : ''; ?>">
                <i class="fa fa-list-alt"></i>
                <span>Enquêtes Alimentaires</span>
            </a>
            <?php } ?>

            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'add_measurement') ? 'active' : ''; ?>">
                <i class="fa fa-plus-circle"></i>
                <span>Ajouter une Mesure</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/measurements'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'measurements') ? 'active' : ''; ?>">
                <i class="fa fa-line-chart"></i>
                <span>Mes Mesures</span>
            </a>

            <div class="menu-divider"></div>

            <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'consultations') ? 'active' : ''; ?>">
                <i class="fa fa-calendar"></i>
                <span>Mes Consultations</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'my_dietitians') ? 'active' : ''; ?>">
                <i class="fa fa-user-md"></i>
                <span>Mon Diététicien</span>
            </a>

            <div class="menu-divider"></div>

            <a href="<?php echo site_url('clients/profile'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'profile') ? 'active' : ''; ?>">
                <i class="fa fa-user"></i>
                <span>Mon Profil</span>
            </a>

            <a href="<?php echo site_url('authentication/logout'); ?>" class="menu-item">
                <i class="fa fa-sign-out"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </nav>

    <div class="content-container">
