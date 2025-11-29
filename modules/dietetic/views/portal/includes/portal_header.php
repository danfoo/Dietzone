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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Josefin+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            -webkit-tap-highlight-color: rgba(0,0,0,0);
        }

        body {
            background: #f8f9fa;
            font-family: 'Josefin Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', sans-serif;
            min-height: 100vh;
            padding-top: 60px; /* Space for fixed header */
            padding-bottom: 60px; /* Space for fixed footer */
        }

        /* ============================================
           HEADER MAGNIFIQUE
           ============================================ */
        .app-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: #f8f9fa;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }


        /* Hamburger Menu Button - Mobile App Style */
        .hamburger-btn {
            width: 44px;
            height: 44px;
            background: transparent;
            border: none;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            transition: all 0.3s;
            padding: 8px;
        }

        .hamburger-btn:hover {
            background: #e9ecef;
        }

        .hamburger-btn:active {
            transform: scale(0.9);
        }

        .hamburger-btn i {
            font-size: 24px;
            color: #2c3e50;
            transition: all 0.3s;
        }

        .hamburger-btn.active i {
            transform: rotate(90deg);
        }

        /* Notification Button */
        .notification-btn {
            width: 44px;
            height: 44px;
            background: transparent;
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .notification-btn:hover {
            background: #f8f9fa;
        }

        .notification-btn:active {
            transform: scale(0.9);
        }

        .notification-btn i {
            font-size: 22px;
            color: #2c3e50;
        }

        .notification-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ff4757;
            color: white;
            font-size: 10px;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            box-shadow: 0 2px 4px rgba(255, 71, 87, 0.3);
        }

        /* Header Profile Button */
        .header-profile-btn {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #01807B, #F3911D, #01807B);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            margin-right: auto; /* Push to the left */
            padding: 2px;
            position: relative;
        }

        .header-profile-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            padding: 2px;
            background: linear-gradient(135deg, #01807B, #F3911D, #4CAF50, #01807B);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 1;
            animation: rotate-gradient 3s linear infinite;
        }

        @keyframes rotate-gradient {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        .header-profile-btn-inner {
            width: 40px;
            height: 40px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .header-profile-btn:hover {
            transform: scale(1.05);
            text-decoration: none;
        }

        .header-profile-btn:active {
            transform: scale(0.95);
        }

        .header-profile-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .header-profile-initials {
            font-size: 14px;
            font-weight: 700;
            color: #01807B;
            text-transform: uppercase;
        }

        /* Header Logo */
        .header-logo {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            height: 40px;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .header-logo img {
            height: 100%;
            max-width: 120px;
            object-fit: contain;
        }

        .header-logo-text {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 700;
            color: #01807B;
        }

        .header-logo-text i {
            font-size: 24px;
        }

        .header-logo-text span {
            display: none;
        }

        @media (min-width: 480px) {
            .header-logo-text span {
                display: inline;
            }
        }

        /* Notification Panel */
        .notification-panel {
            position: fixed;
            top: 60px;
            right: -350px;
            width: 320px;
            max-height: calc(100vh - 120px);
            background: white;
            z-index: 999;
            transition: right 0.3s ease;
            box-shadow: -4px 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 12px 0 0 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .notification-panel.active {
            right: 0;
        }

        .notification-panel-header {
            background: #01807B;
            color: white;
            padding: 16px 20px;
            font-size: 18px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .notification-panel-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .mark-all-read-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .mark-all-read-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .mark-all-read-btn:active {
            transform: scale(0.95);
        }

        .mark-all-read-btn.hidden {
            display: none;
        }

        .notification-panel-close {
            background: transparent;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .notification-panel-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .notification-filters {
            display: flex;
            gap: 8px;
            padding: 12px 12px 8px 12px;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .filter-btn {
            flex: 1;
            padding: 8px 12px;
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            color: #6c757d;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn:hover {
            border-color: #01807B;
            color: #01807B;
        }

        .filter-btn.active {
            background: #01807B;
            border-color: #01807B;
            color: white;
        }

        .filter-btn:active {
            transform: scale(0.95);
        }

        .notification-panel-content {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .notification-item {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 14px;
            margin-bottom: 10px;
            border-left: 4px solid #01807B;
            transition: all 0.2s;
            position: relative;
        }

        .notification-item:hover {
            background: #e9ecef;
            transform: translateX(-4px);
        }

        .notification-item.unread {
            background: #e8f5f4;
            border-left-color: #ff4757;
        }

        .notification-item-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .notification-item-icon {
            width: 36px;
            height: 36px;
            background: #01807B;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .notification-item-title {
            flex: 1;
            font-weight: 600;
            font-size: 14px;
            color: #2c3e50;
        }

        .notification-item-time {
            font-size: 11px;
            color: #6c757d;
        }

        .notification-item-message {
            font-size: 13px;
            color: #495057;
            line-height: 1.4;
            padding-left: 46px;
        }

        .notification-item-delete {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 24px;
            height: 24px;
            background: transparent;
            border: none;
            color: #6c757d;
            cursor: pointer;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s;
            opacity: 0;
        }

        .notification-item:hover .notification-item-delete {
            opacity: 1;
        }

        .notification-item-delete:hover {
            background: #ff4757;
            color: white;
            transform: scale(1.1);
        }

        .notification-item-delete:active {
            transform: scale(0.9);
        }

        .notification-date-separator {
            padding: 12px 12px 8px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #01807B;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: linear-gradient(90deg, #01807B 0%, transparent 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            position: sticky;
            top: 0;
            background-color: #fff;
            z-index: 1;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notification-date-separator:before {
            content: '';
            height: 2px;
            flex: 1;
            background: linear-gradient(90deg, #01807B 0%, transparent 100%);
        }

        .notification-date-separator span {
            background: linear-gradient(135deg, #01807B 0%, #026660 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .notification-empty {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .notification-empty i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .notification-panel-footer {
            border-top: 1px solid #e9ecef;
            padding: 12px;
        }

        .notification-settings-link {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 8px;
            color: #01807B;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .notification-settings-link:hover {
            background: #e9ecef;
            color: #01807B;
            text-decoration: none;
        }

        .notification-settings-link i {
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .notification-panel {
                width: 100%;
                right: -100%;
                border-radius: 0;
            }
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
            max-height: 100vh;
            background: white;
            z-index: 1003;
            transition: right 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch; /* Smooth scrolling sur iOS */
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
            padding: 15px 0 80px 0; /* Padding en bas pour accès facile au dernier élément sur mobile */
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
            height: 50px;
            background: white;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
            z-index: 1000;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 0 5px;
        }

        .footer-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            padding: 8px 3px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            position: relative;
        }

        .footer-item:hover {
            background: rgba(1, 128, 123, 0.05);
        }

        .footer-item:active {
            transform: scale(0.92);
        }

        .footer-item.active {
            color: #01807B;
        }

        .footer-item.active::before {
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

        .footer-icon {
            font-size: 20px;
            transition: all 0.2s;
        }

        .footer-item.active .footer-icon {
            transform: scale(1.08);
            color: #01807B;
        }

        .footer-label {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
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

            /* Optimisations pour le menu mobile */
            .slide-menu {
                width: 85%; /* Laisse 15% pour l'overlay cliquable */
                max-width: 350px;
                right: -85%;
                height: 100%;
                max-height: -webkit-fill-available; /* Fix pour Safari iOS */
                max-height: 100dvh; /* Dynamic viewport height pour mobiles modernes */
            }

            .slide-menu.active {
                right: 0;
            }

            .menu-items {
                padding: 15px 0 100px 0; /* Plus de padding en bas sur mobile */
            }

            .menu-item {
                padding: 18px 20px; /* Items plus grands pour meilleure accessibilité tactile */
            }
        }
    </style>

    <!-- Firebase Scripts - Using cdnjs as fallback for better reliability -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.22.0/firebase-app-compat.min.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/firebase/9.22.0/firebase-messaging-compat.min.js" crossorigin="anonymous"></script>

    <!-- Fallback to unpkg if cdnjs fails -->
    <script>
        if (typeof firebase === 'undefined') {
            console.warn('Primary Firebase CDN failed, loading from fallback...');
            document.write('<script src="https://unpkg.com/firebase@9.22.0/firebase-app-compat.js"><\/script>');
            document.write('<script src="https://unpkg.com/firebase@9.22.0/firebase-messaging-compat.js"><\/script>');
        }
    </script>
</head>
<body>
    <!-- HEADER MAGNIFIQUE -->
    <header class="app-header">
        <a href="<?php echo site_url('dietetic/portal/profile'); ?>" class="header-profile-btn" title="Mon Profil">
            <div class="header-profile-btn-inner">
                <?php
                // Get contact for profile image
                $header_contact = null;
                $header_contact_id = null;

                if (isset($client) && !empty($client->default_contact)) {
                    $header_contact_id = $client->default_contact;
                } elseif (isset($client) && isset($client->userid)) {
                    $CI = &get_instance();
                    $CI->load->model('clients_model');
                    $contacts = $CI->clients_model->get_contacts($client->userid);
                    if (!empty($contacts)) {
                        $header_contact_id = $contacts[0]['id'];
                    }
                }

                if ($header_contact_id) {
                    $CI = &get_instance();
                    if (!isset($CI->clients_model)) {
                        $CI->load->model('clients_model');
                    }
                    $header_contact = $CI->clients_model->get_contact($header_contact_id);
                }

                if ($header_contact && !empty($header_contact->profile_image)):
                    $header_profile_image_url = base_url('uploads/client_profile_images/' . $header_contact->id . '/thumb_' . $header_contact->profile_image);
                ?>
                    <img src="<?php echo $header_profile_image_url; ?>" alt="Profil">
                <?php else:
                    // Afficher les initiales
                    $header_name = isset($client->company) ? $client->company : (isset($patient->client->company) ? $patient->client->company : 'U');
                    $header_names = explode(' ', trim($header_name));
                    $header_initials = '';
                    if (count($header_names) >= 2) {
                        $header_initials = strtoupper(substr($header_names[0], 0, 1) . substr($header_names[1], 0, 1));
                    } else {
                        $header_initials = strtoupper(substr($header_name, 0, 2));
                    }
                ?>
                    <div class="header-profile-initials"><?php echo $header_initials; ?></div>
                <?php endif; ?>
            </div>
        </a>

        <!-- Logo -->
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
                    <span><?php echo get_option('companyname'); ?></span>
                </div>
            <?php } ?>
        </a>

        <button class="notification-btn" id="notificationBtn">
            <i class="fa fa-bell"></i>
            <span class="notification-badge" style="display: none;">0</span>
        </button>

        <button class="hamburger-btn" id="menuToggle">
            <i class="fa fa-bars"></i>
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

            <?php
            // Check if recipes library is enabled
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_recipes')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/recipes'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'recipes') ? 'active' : ''; ?>">
                <i class="fa fa-book"></i>
                <span>Recettes</span>
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

            <?php
            // Check if activities tracking is enabled
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_activities')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/activities'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'activities') ? 'active' : ''; ?>">
                <i class="fa fa-heartbeat"></i>
                <span>Mes Activités</span>
            </a>
            <?php } ?>

            <a href="<?php echo site_url('clients/invoices'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'invoices') ? 'active' : ''; ?>">
                <i class="fa fa-file-text"></i>
                <span>Mon plan</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/subscriptions'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'subscriptions') ? 'active' : ''; ?>">
                <i class="fa fa-refresh"></i>
                <span>Mes Abonnements</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/statistics'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'statistics') ? 'active' : ''; ?>">
                <i class="fa fa-area-chart"></i>
                <span>Mes Statistiques</span>
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

            <?php
            // Check if notification preferences feature is enabled
            if ($CI_menu->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
            ?>
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'notification_preferences') ? 'active' : ''; ?>">
                <i class="fa fa-bell"></i>
                <span>Préférences de Notifications</span>
            </a>
            <?php } ?>

            <div class="menu-divider"></div>

            <!-- Legal Pages -->
            <a href="<?php echo site_url('dietetic/portal/privacy'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'privacy') ? 'active' : ''; ?>">
                <i class="fa fa-shield"></i>
                <span>Politique de Confidentialité</span>
            </a>

            <a href="<?php echo site_url('dietetic/portal/terms'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'terms') ? 'active' : ''; ?>">
                <i class="fa fa-file-text"></i>
                <span>Conditions d'Utilisation</span>
            </a>

            <div class="menu-divider"></div>

            <a href="<?php echo site_url('dietetic/portal/profile'); ?>" class="menu-item <?php echo (isset($active_page) && $active_page == 'profile') ? 'active' : ''; ?>">
                <i class="fa fa-user"></i>
                <span>Mon Profil</span>
            </a>

            <a href="<?php echo site_url('authentication/logout'); ?>" class="menu-item">
                <i class="fa fa-sign-out"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </nav>

    <!-- NOTIFICATION PANEL -->
    <div class="notification-panel" id="notificationPanel">
        <div class="notification-panel-header">
            <span>Notifications</span>
            <div class="notification-panel-actions">
                <button class="mark-all-read-btn hidden" id="markAllReadBtn">
                    <i class="fa fa-check-double"></i>
                    <span>Tout lire</span>
                </button>
                <button class="notification-panel-close" id="notificationClose">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        <div class="notification-filters">
            <button class="filter-btn active" data-filter="all">Toutes</button>
            <button class="filter-btn" data-filter="unread">Non lues</button>
            <button class="filter-btn" data-filter="read">Lues</button>
        </div>
        <div class="notification-panel-content">
            <!-- Les notifications seront chargées dynamiquement via JavaScript -->
            <div class="notification-empty">
                <i class="fa fa-spinner fa-spin"></i>
                <p>Chargement...</p>
            </div>
        </div>
        <?php
        // Add settings link if notification preferences exist
        $CI_notif_panel = &get_instance();
        if ($CI_notif_panel->db->table_exists(db_prefix() . 'dietic_notification_preferences')) {
        ?>
        <div class="notification-panel-footer">
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="notification-settings-link">
                <i class="fa fa-cog"></i>
                <span>Gérer les notifications</span>
            </a>
        </div>
        <?php } ?>
    </div>

    <div class="content-container">
