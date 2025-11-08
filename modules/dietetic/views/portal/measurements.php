<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title>Historique des Mesures</title>
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
            padding-bottom: 80px;
        }

        /* Header */
        .portal-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 12px 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .portal-header .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .portal-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .portal-logo {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .portal-logo img {
            max-height: 40px;
            max-width: 150px;
        }

        .portal-logo-text {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .portal-logo-text i {
            color: #01807B;
        }

        .portal-nav-desktop {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .portal-nav-desktop a {
            padding: 10px 20px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
        }

        .portal-nav-desktop a:hover {
            background: #f8f9fa;
            color: #01807B;
        }

        .portal-nav-desktop a.active {
            background: #01807B;
            color: white;
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

        /* Bottom Nav */
        .bottom-nav {
            display: none;
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
            font-size: 24px;
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

        /* Container */
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        /* Page Header */
        .page-header-mobile {
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            border-radius: 16px;
            padding: 24px 20px;
            margin-bottom: 24px;
            color: white;
            box-shadow: 0 8px 16px rgba(1, 128, 123, 0.3);
        }

        .page-header-mobile h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-header-mobile p {
            margin: 0;
            font-size: 15px;
            opacity: 0.95;
        }

        /* Chart Box */
        .chart-box {
            background: white;
            border-radius: 16px;
            padding: 24px 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .chart-box h4 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-box h4 i {
            color: #01807B;
        }

        /* Measurement Cards */
        .measurement-card {
            background: white;
            border-left: 4px solid #01807B;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .measurement-card:active {
            transform: scale(0.98);
        }

        .measurement-date {
            font-size: 18px;
            font-weight: 700;
            color: #01807B;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .measurement-stats {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
        }

        .stat-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-item label {
            display: block;
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .stat-item .value {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
        }

        .measurement-notes {
            margin-top: 16px;
            padding: 14px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 3px solid #01807B;
        }

        .measurement-notes strong {
            color: #2c3e50;
            font-size: 14px;
        }

        .measurement-notes p {
            margin: 8px 0 0 0;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Buttons */
        .btn-add-measure {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
            padding: 14px 24px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 48px;
        }

        .btn-add-measure:hover {
            box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-add-measure:active {
            transform: scale(0.97);
        }

        .btn-back {
            background: white;
            color: #495057;
            border: 2px solid #dee2e6;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 48px;
        }

        .btn-back:hover {
            background: #f8f9fa;
            border-color: #01807B;
            color: #01807B;
            text-decoration: none;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 50px 30px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .empty-state-icon {
            font-size: 56px;
            color: #dee2e6;
            margin-bottom: 16px;
        }

        .empty-state-title {
            font-size: 18px;
            font-weight: 700;
            color: #495057;
            margin-bottom: 8px;
        }

        .empty-state-text {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }

        /* Pagination */
        .pagination-container {
            text-align: center;
            margin: 24px 0;
        }

        .pagination {
            display: inline-flex;
            gap: 6px;
            margin: 0;
        }

        .pagination li {
            list-style: none;
        }

        .pagination a,
        .pagination span {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            color: #495057;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: #01807B;
            border-color: #01807B;
            color: white;
        }

        .pagination li.active span {
            background: #01807B;
            border-color: #01807B;
            color: white;
        }

        .pagination li.disabled span {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.5s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }

        /* Desktop */
        @media (min-width: 769px) {
            body {
                padding-bottom: 0;
            }

            .bottom-nav {
                display: none !important;
            }

            .portal-nav-desktop {
                display: flex !important;
            }

            .hamburger-menu {
                display: none !important;
            }

            .mobile-menu-overlay,
            .mobile-menu-panel {
                display: none !important;
            }

            .measurement-stats {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }
        }

        /* Mobile */
        @media (max-width: 768px) {
            .portal-nav-desktop {
                display: none !important;
            }

            .bottom-nav {
                display: block;
            }

            .hamburger-menu {
                display: flex !important;
            }

            .content-container {
                padding: 16px 12px 20px;
            }

            .page-header-mobile {
                padding: 20px 16px;
                border-radius: 12px;
                margin-bottom: 20px;
            }

            .page-header-mobile h1 {
                font-size: 20px;
            }

            .chart-box {
                padding: 20px 16px;
                border-radius: 12px;
            }

            .measurement-card {
                padding: 16px;
                border-radius: 12px;
            }

            .measurement-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
        }

        @media (max-width: 375px) {
            .portal-logo img {
                max-height: 32px;
                max-width: 120px;
            }

            .portal-logo-text {
                font-size: 16px;
            }

            .page-header-mobile h1 {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="portal-header">
        <div class="container-fluid">
            <div class="portal-header-content">
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
                    <a href="<?php echo site_url('dietetic/portal'); ?>">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
                        <i class="fa fa-cutlery"></i> Repas
                    </a>
                    <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
                    <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>">
                        <i class="fa fa-clipboard-list"></i> Enquêtes
                    </a>
                    <?php } ?>
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>">
                        <i class="fa fa-user-md"></i> Diététicien
                    </a>
                    <a href="<?php echo site_url('clients/profile'); ?>">
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
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu Panel -->
    <div class="mobile-menu-panel" id="mobileMenuPanel">
        <div class="mobile-menu-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="mobile-menu-item">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="mobile-menu-item">
                <i class="fa fa-cutlery"></i>
                <span>Plans de Repas</span>
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="mobile-menu-item">
                <i class="fa fa-clipboard-list"></i>
                <span>Enquêtes Alimentaires</span>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="mobile-menu-item active">
                <i class="fa fa-heartbeat"></i>
                <span>Mes Mesures</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="mobile-menu-item">
                <i class="fa fa-user-md"></i>
                <span>Mon Diététicien</span>
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_notification_preferences')) { ?>
            <a href="<?php echo site_url('dietetic/portal/notification_preferences'); ?>" class="mobile-menu-item">
                <i class="fa fa-bell"></i>
                <span>Notifications</span>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('clients/profile'); ?>" class="mobile-menu-item">
                <i class="fa fa-user"></i>
                <span>Mon Profil</span>
            </a>
            <a href="<?php echo site_url('authentication/logout'); ?>" class="mobile-menu-item">
                <i class="fa fa-sign-out"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>

    <div class="content-container">
        <!-- Page Header -->
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-history"></i> Mes Mesures</h1>
            <p>Suivez votre évolution</p>
        </div>

        <!-- Add Button -->
        <div style="text-align: right; margin-bottom: 24px;" class="animate-in delay-1">
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="btn-add-measure">
                <i class="fa fa-plus-circle"></i> Ajouter une Mesure
            </a>
        </div>

        <?php if (!empty($measurements)) { ?>
            <!-- Weight Evolution Chart -->
            <?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
            <div class="chart-box animate-in delay-1">
                <h4><i class="fa fa-line-chart"></i> Évolution du Poids</h4>
                <canvas id="weightChart" height="100"></canvas>
            </div>
            <?php } ?>

            <!-- Measurements List -->
            <div id="measurements-list" class="animate-in delay-2">
                <?php foreach ($measurements as $measurement) { ?>
                    <div class="measurement-card">
                        <div class="measurement-date">
                            <i class="fa fa-calendar"></i>
                            <?php echo date('d/m/Y', strtotime($measurement->measurement_date)); ?>
                        </div>

                        <div class="measurement-stats">
                            <div class="stat-item">
                                <label>Poids</label>
                                <div class="value"><?php echo number_format($measurement->weight, 1); ?> <span style="font-size: 14px; color: #6c757d;">kg</span></div>
                            </div>

                            <?php if ($measurement->bmi) { ?>
                            <div class="stat-item">
                                <label>IMC</label>
                                <div class="value"><?php echo number_format($measurement->bmi, 1); ?></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->body_fat) { ?>
                            <div class="stat-item">
                                <label>Masse Grasse</label>
                                <div class="value"><?php echo number_format($measurement->body_fat, 1); ?><span style="font-size: 14px; color: #6c757d;">%</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->muscle_mass) { ?>
                            <div class="stat-item">
                                <label>Masse Musculaire</label>
                                <div class="value"><?php echo number_format($measurement->muscle_mass, 1); ?><span style="font-size: 14px; color: #6c757d;">%</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->waist) { ?>
                            <div class="stat-item">
                                <label>Tour de Taille</label>
                                <div class="value"><?php echo number_format($measurement->waist, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->hips) { ?>
                            <div class="stat-item">
                                <label>Tour de Hanches</label>
                                <div class="value"><?php echo number_format($measurement->hips, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->chest) { ?>
                            <div class="stat-item">
                                <label>Tour de Poitrine</label>
                                <div class="value"><?php echo number_format($measurement->chest, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->arms) { ?>
                            <div class="stat-item">
                                <label>Tour de Bras</label>
                                <div class="value"><?php echo number_format($measurement->arms, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>

                            <?php if ($measurement->thighs) { ?>
                            <div class="stat-item">
                                <label>Tour de Cuisses</label>
                                <div class="value"><?php echo number_format($measurement->thighs, 1); ?><span style="font-size: 14px; color: #6c757d;">cm</span></div>
                            </div>
                            <?php } ?>
                        </div>

                        <?php if ($measurement->notes) { ?>
                        <div class="measurement-notes">
                            <strong><i class="fa fa-sticky-note"></i> Notes</strong>
                            <p><?php echo nl2br(htmlspecialchars($measurement->notes)); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>

            <!-- Pagination -->
            <div id="measurements-pagination" class="pagination-container"></div>

        <?php } else { ?>
            <!-- Empty State -->
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-history"></i>
                </div>
                <div class="empty-state-title">Aucune mesure enregistrée</div>
                <div class="empty-state-text">
                    Ajoutez votre première mesure pour commencer à suivre votre évolution.
                </div>
                <div style="margin-top: 24px;">
                    <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="btn-add-measure">
                        <i class="fa fa-plus-circle"></i> Ajouter une Mesure
                    </a>
                </div>
            </div>
        <?php } ?>

        <!-- Back Button -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn-back">
                <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
            </a>
        </div>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="bottom-nav-item">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="bottom-nav-item">
                <i class="fa fa-cutlery"></i>
                <span>Repas</span>
            </a>
            <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="bottom-nav-item">
                <i class="fa fa-clipboard-list"></i>
                <span>Enquêtes</span>
            </a>
            <?php } ?>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="bottom-nav-item active">
                <i class="fa fa-plus-circle"></i>
                <span>Mesure</span>
            </a>
            <a href="<?php echo site_url('clients/profile'); ?>" class="bottom-nav-item">
                <i class="fa fa-user"></i>
                <span>Profil</span>
            </a>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>

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
    });

    // Touch feedback
    document.querySelectorAll('.measurement-card, .btn-add-measure, .bottom-nav-item, .mobile-menu-item').forEach(function(element) {
        element.addEventListener('touchstart', function() {
            this.style.transform = 'scale(0.97)';
        });
        element.addEventListener('touchend', function() {
            this.style.transform = '';
        });
    });

    // Pagination
    function paginateItems(containerId, paginationId, itemsPerPage) {
        var $container = $('#' + containerId);
        var $items = $container.find('.measurement-card');
        var $pagination = $('#' + paginationId);
        var totalItems = $items.length;
        var totalPages = Math.ceil(totalItems / itemsPerPage);

        if (totalItems === 0 || totalPages <= 1) {
            return;
        }

        function showPage(page) {
            $items.hide();
            var start = (page - 1) * itemsPerPage;
            var end = start + itemsPerPage;
            $items.slice(start, end).show();

            $pagination.empty();
            var paginationHtml = '<ul class="pagination">';

            if (page > 1) {
                paginationHtml += '<li><a href="#" data-page="' + (page - 1) + '"><i class="fa fa-chevron-left"></i></a></li>';
            } else {
                paginationHtml += '<li class="disabled"><span><i class="fa fa-chevron-left"></i></span></li>';
            }

            for (var i = 1; i <= totalPages; i++) {
                if (i === page) {
                    paginationHtml += '<li class="active"><span>' + i + '</span></li>';
                } else {
                    paginationHtml += '<li><a href="#" data-page="' + i + '">' + i + '</a></li>';
                }
            }

            if (page < totalPages) {
                paginationHtml += '<li><a href="#" data-page="' + (page + 1) + '"><i class="fa fa-chevron-right"></i></a></li>';
            } else {
                paginationHtml += '<li class="disabled"><span><i class="fa fa-chevron-right"></i></span></li>';
            }

            paginationHtml += '</ul>';
            $pagination.html(paginationHtml);

            $pagination.find('a').on('click', function(e) {
                e.preventDefault();
                var newPage = parseInt($(this).data('page'));
                showPage(newPage);
                $('html, body').animate({
                    scrollTop: $container.offset().top - 100
                }, 300);
            });
        }

        showPage(1);
    }

    $(document).ready(function() {
        paginateItems('measurements-list', 'measurements-pagination', 10);

        <?php if (!empty($weight_evolution) && count($weight_evolution) > 1) { ?>
        // Weight Chart
        var ctx = document.getElementById('weightChart').getContext('2d');
        var weightChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [
                    <?php foreach ($weight_evolution as $point) {
                        echo '"' . date('d/m', strtotime($point->measurement_date)) . '",';
                    } ?>
                ],
                datasets: [{
                    label: 'Poids (kg)',
                    data: [
                        <?php foreach ($weight_evolution as $point) {
                            echo $point->weight . ',';
                        } ?>
                    ],
                    borderColor: '#01807B',
                    backgroundColor: 'rgba(1, 128, 123, 0.1)',
                    borderWidth: 3,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: false
                        }
                    }]
                },
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        });
        <?php } ?>
    });

    // Haptic feedback
    if ('vibrate' in navigator) {
        document.querySelectorAll('.btn-add-measure').forEach(function(btn) {
            btn.addEventListener('click', function() {
                navigator.vibrate(10);
            });
        });
    }
    </script>
</body>
</html>
