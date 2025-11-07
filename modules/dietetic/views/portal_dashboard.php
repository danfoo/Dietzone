<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title><?php echo isset($title) ? $title : 'Mon Programme'; ?></title>
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
            padding-bottom: 80px; /* Space for bottom nav on mobile */
        }

        /* Header Simplifié Mobile-First */
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

        /* Desktop Navigation (Hidden on mobile) */
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

        /* Bottom Navigation Mobile */
        .bottom-nav {
            display: none; /* Hidden by default, shown on mobile */
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
            transition: transform 0.2s ease;
        }

        .bottom-nav-item.active i {
            transform: scale(1.1);
        }

        .bottom-nav-item span {
            font-size: 11px;
            font-weight: 600;
        }

        /* Active indicator */
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

        /* Page Header Mobile-Friendly */
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

        /* Stats Grid - Swipeable on mobile */
        .stats-scroll-container {
            overflow-x: auto;
            margin: 0 -15px 24px -15px;
            padding: 0 15px;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
        }

        .stats-scroll-container::-webkit-scrollbar {
            display: none;
        }

        .stats-grid {
            display: flex;
            gap: 16px;
            min-width: min-content;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 24px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            flex-shrink: 0;
            width: 160px;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stat-card.weight { border-left-color: #01807B; }
        .stat-card.target { border-left-color: #F3911D; }
        .stat-card.bmi { border-left-color: #01807B; }
        .stat-card.fat { border-left-color: #F3911D; }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 16px;
        }

        .stat-card.weight .stat-icon { background: linear-gradient(135deg, #01807B 0%, #026661 100%); }
        .stat-card.target .stat-icon { background: linear-gradient(135deg, #F3911D 0%, #D67A0F 100%); }
        .stat-card.bmi .stat-icon { background: linear-gradient(135deg, #01807B 0%, #026661 100%); }
        .stat-card.fat .stat-icon { background: linear-gradient(135deg, #F3911D 0%, #D67A0F 100%); }

        .stat-label {
            color: #6c757d;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        .stat-value span {
            font-size: 16px !important;
            color: #6c757d;
        }

        /* Progress Card - Mobile Optimized */
        .progress-card {
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            border-radius: 20px;
            padding: 28px 24px;
            color: white;
            box-shadow: 0 8px 16px rgba(1, 128, 123, 0.3);
            margin-bottom: 24px;
            text-align: center;
        }

        .progress-card .icon {
            font-size: 40px;
            margin-bottom: 12px;
            opacity: 0.9;
        }

        .progress-card .value {
            font-size: 36px;
            font-weight: 700;
            margin: 8px 0;
        }

        .progress-card .label {
            font-size: 14px;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
        }

        /* Action Cards - Touch-Friendly */
        .action-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
            margin-bottom: 24px;
        }

        .action-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 16px;
            border-left: 4px solid transparent;
            min-height: 80px; /* Touch-friendly height */
            cursor: pointer;
        }

        .action-card:active {
            transform: scale(0.98);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .action-card.add-measure { border-left-color: #01807B; }
        .action-card.history { border-left-color: #F3911D; }
        .action-card.meals { border-left-color: #01807B; }
        .action-card.consultations { border-left-color: #F3911D; }
        .action-card.dietitians { border-left-color: #01807B; }

        .action-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            flex-shrink: 0;
        }

        .action-card.add-measure .action-icon { background: linear-gradient(135deg, #01807B 0%, #026661 100%); }
        .action-card.history .action-icon { background: linear-gradient(135deg, #F3911D 0%, #D67A0F 100%); }
        .action-card.meals .action-icon { background: linear-gradient(135deg, #01807B 0%, #026661 100%); }
        .action-card.consultations .action-icon { background: linear-gradient(135deg, #F3911D 0%, #D67A0F 100%); }
        .action-card.dietitians .action-icon { background: linear-gradient(135deg, #01807B 0%, #026661 100%); }

        .action-content {
            flex: 1;
        }

        .action-card h4 {
            color: #2c3e50;
            font-size: 17px;
            font-weight: 700;
            margin: 0 0 6px 0;
        }

        .action-card p {
            color: #6c757d;
            margin: 0;
            font-size: 14px;
        }

        .action-chevron {
            color: #dee2e6;
            font-size: 20px;
        }

        /* Info Box */
        .info-box {
            background: white;
            border-radius: 16px;
            padding: 24px 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .info-box h3 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 16px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-box h3 i {
            color: #01807B;
        }

        .info-box hr {
            border-color: #e9ecef;
            margin: 16px 0;
        }

        .info-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 12px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 12px;
        }

        .info-item i {
            color: #01807B;
            font-size: 20px;
            width: 24px;
            text-align: center;
        }

        .info-item strong {
            color: #2c3e50;
            margin-right: 6px;
            font-size: 14px;
        }

        .info-item span {
            color: #6c757d;
            font-size: 14px;
        }

        /* Consultation List */
        .consultation-item {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-left: 4px solid #01807B;
        }

        .consultation-item i {
            font-size: 24px;
            color: #01807B;
        }

        .consultation-info {
            flex: 1;
        }

        .consultation-date {
            font-size: 15px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .consultation-type {
            font-size: 13px;
            color: #6c757d;
        }

        /* Alert */
        .alert-modern {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 16px;
            border-left: 4px solid #01807B;
        }

        .alert-modern i {
            font-size: 28px;
            color: #01807B;
            flex-shrink: 0;
        }

        .alert-modern p {
            margin: 0;
            color: #495057;
            font-size: 15px;
            line-height: 1.5;
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
        .delay-3 { animation-delay: 0.3s; opacity: 0; }

        /* Desktop Breakpoint */
        @media (min-width: 769px) {
            body {
                padding-bottom: 0; /* Remove bottom padding on desktop */
            }

            .bottom-nav {
                display: none !important; /* Always hide on desktop */
            }

            .portal-nav-desktop {
                display: flex !important;
            }

            .stats-scroll-container {
                margin: 0 0 30px 0;
                padding: 0;
                overflow-x: visible;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 20px;
            }

            .stat-card {
                width: auto;
            }

            .action-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 20px;
            }

            .info-row {
                flex-direction: row;
                flex-wrap: wrap;
            }

            .info-item {
                flex: 1;
                min-width: 200px;
            }
        }

        /* Mobile Optimization */
        @media (max-width: 768px) {
            .portal-nav-desktop {
                display: none !important;
            }

            .bottom-nav {
                display: block;
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

            .page-header-mobile p {
                font-size: 14px;
            }

            .stats-grid {
                padding-left: 3px; /* Small padding for swipe indicator */
            }

            .action-card {
                padding: 16px;
                border-radius: 12px;
            }

            .action-icon {
                width: 48px;
                height: 48px;
                font-size: 24px;
            }

            .action-card h4 {
                font-size: 16px;
            }

            .action-card p {
                font-size: 13px;
            }

            .info-box {
                padding: 20px 16px;
                border-radius: 12px;
            }

            .info-box h3 {
                font-size: 17px;
            }
        }

        /* Small Mobile */
        @media (max-width: 375px) {
            .portal-logo img {
                max-height: 32px;
                max-width: 120px;
            }

            .portal-logo-text {
                font-size: 16px;
            }

            .stat-card {
                width: 140px;
                padding: 20px 16px;
            }

            .stat-value {
                font-size: 24px;
            }

            .page-header-mobile h1 {
                font-size: 18px;
            }
        }

        /* Pull to Refresh Indicator (Optional Enhancement) */
        .pull-to-refresh {
            display: none;
            text-align: center;
            padding: 10px;
            color: #01807B;
        }
    </style>
</head>
<body>
    <!-- Header Simplifié -->
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
                    <a href="<?php echo site_url('dietetic/portal'); ?>" class="active">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
                        <i class="fa fa-cutlery"></i> Repas
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>">
                        <i class="fa fa-user-md"></i> Diététicien
                    </a>
                    <a href="<?php echo site_url('clients/profile'); ?>">
                        <i class="fa fa-user"></i> Profil
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div class="content-container">
        <!-- Page Header Mobile-Friendly -->
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-heartbeat"></i> Mon Programme</h1>
            <p>Bonjour <?php echo isset($client->company) && $client->company ? htmlspecialchars($client->company) : 'Patient'; ?> 👋</p>
        </div>

        <!-- Stats - Swipeable -->
        <div class="stats-scroll-container animate-in delay-1">
            <div class="stats-grid">
                <div class="stat-card weight">
                    <div class="stat-icon"><i class="fa fa-balance-scale"></i></div>
                    <div class="stat-label">Poids Actuel</div>
                    <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement ? number_format($latest_measurement->weight, 1) : '-'; ?> <span>kg</span></div>
                </div>
                <div class="stat-card target">
                    <div class="stat-icon"><i class="fa fa-bullseye"></i></div>
                    <div class="stat-label">Objectif</div>
                    <div class="stat-value"><?php echo isset($patient->target_weight) && $patient->target_weight ? number_format($patient->target_weight, 1) : '-'; ?> <span>kg</span></div>
                </div>
                <div class="stat-card bmi">
                    <div class="stat-icon"><i class="fa fa-tachometer"></i></div>
                    <div class="stat-label">IMC</div>
                    <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->bmi) ? number_format($latest_measurement->bmi, 1) : '-'; ?></div>
                </div>
                <div class="stat-card fat">
                    <div class="stat-icon"><i class="fa fa-pie-chart"></i></div>
                    <div class="stat-label">Masse Grasse</div>
                    <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->body_fat) ? number_format($latest_measurement->body_fat, 1) . '%' : '-'; ?></div>
                </div>
            </div>
        </div>

        <!-- Progress Card -->
        <div class="progress-card animate-in delay-2">
            <div class="icon"><i class="fa fa-line-chart"></i></div>
            <div class="value">
                <?php
                if (isset($weight_progress->weight_change) && $weight_progress->weight_change !== null) {
                    $change = $weight_progress->weight_change;
                    echo ($change > 0 ? '+' : '') . number_format($change, 1) . ' kg';
                } else {
                    echo '-';
                }
                ?>
            </div>
            <div class="label">Progression</div>
        </div>

        <!-- Action Cards - Touch-Friendly -->
        <div class="action-grid animate-in delay-3">
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="action-card add-measure">
                <div class="action-icon"><i class="fa fa-plus-circle"></i></div>
                <div class="action-content">
                    <h4>Ajouter une Mesure</h4>
                    <p>Suivez votre évolution</p>
                </div>
                <i class="fa fa-chevron-right action-chevron"></i>
            </a>
            <a href="<?php echo site_url('dietetic/portal/measurements'); ?>" class="action-card history">
                <div class="action-icon"><i class="fa fa-history"></i></div>
                <div class="action-content">
                    <h4>Mon Historique</h4>
                    <p>Toutes mes mesures</p>
                </div>
                <i class="fa fa-chevron-right action-chevron"></i>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="action-card meals">
                <div class="action-icon"><i class="fa fa-cutlery"></i></div>
                <div class="action-content">
                    <h4>Mes Repas</h4>
                    <p>Plans alimentaires</p>
                </div>
                <i class="fa fa-chevron-right action-chevron"></i>
            </a>
            <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="action-card consultations">
                <div class="action-icon"><i class="fa fa-calendar-check-o"></i></div>
                <div class="action-content">
                    <h4>Mes Rendez-vous</h4>
                    <p>Consultations</p>
                </div>
                <i class="fa fa-chevron-right action-chevron"></i>
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="action-card dietitians">
                <div class="action-icon"><i class="fa fa-user-md"></i></div>
                <div class="action-content">
                    <h4>Mon Diététicien</h4>
                    <p>Noter et contacter</p>
                </div>
                <i class="fa fa-chevron-right action-chevron"></i>
            </a>
        </div>

        <!-- Active Program -->
        <?php if (isset($active_program) && $active_program) { ?>
            <div class="info-box animate-in">
                <h3><i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($active_program->program_name); ?></h3>
                <hr>
                <div class="info-row">
                    <div class="info-item">
                        <i class="fa fa-calendar"></i>
                        <div>
                            <strong>Début:</strong>
                            <span><?php echo date('d/m/Y', strtotime($active_program->start_date)); ?></span>
                        </div>
                    </div>
                    <?php if (isset($active_program->end_date) && $active_program->end_date) { ?>
                        <div class="info-item">
                            <i class="fa fa-calendar-check-o"></i>
                            <div>
                                <strong>Fin:</strong>
                                <span><?php echo date('d/m/Y', strtotime($active_program->end_date)); ?></span>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (isset($active_program->daily_calories) && $active_program->daily_calories) { ?>
                        <div class="info-item">
                            <i class="fa fa-fire"></i>
                            <div>
                                <strong>Calories:</strong>
                                <span><?php echo $active_program->daily_calories; ?> kcal/j</span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php if (isset($active_program->objective) && $active_program->objective) { ?>
                    <hr>
                    <div style="padding: 16px; background: #f8f9fa; border-radius: 12px; border-left: 4px solid #01807B;">
                        <strong style="color: #2c3e50; display: flex; align-items: center; gap: 8px; margin-bottom: 10px; font-size: 14px;">
                            <i class="fa fa-target"></i> Objectif:
                        </strong>
                        <p style="margin: 0; color: #495057; font-size: 14px; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert-modern animate-in">
                <i class="fa fa-info-circle"></i>
                <p>Aucun programme actif. Contactez votre diététicien.</p>
            </div>
        <?php } ?>

        <!-- Upcoming Consultations -->
        <?php if (!empty($upcoming_consultations)) { ?>
            <div class="info-box animate-in">
                <h3><i class="fa fa-calendar"></i> Prochains Rendez-vous</h3>
                <hr>
                <?php foreach ($upcoming_consultations as $consultation) { ?>
                    <div class="consultation-item">
                        <i class="fa fa-calendar-o"></i>
                        <div class="consultation-info">
                            <div class="consultation-date">
                                <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
                            </div>
                            <div class="consultation-type">
                                <?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

    <!-- Bottom Navigation (Mobile Only) -->
    <nav class="bottom-nav">
        <div class="bottom-nav-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="bottom-nav-item active">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="bottom-nav-item">
                <i class="fa fa-cutlery"></i>
                <span>Repas</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="bottom-nav-item">
                <i class="fa fa-plus-circle"></i>
                <span>Mesure</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="bottom-nav-item">
                <i class="fa fa-user-md"></i>
                <span>Contact</span>
            </a>
            <a href="<?php echo site_url('clients/profile'); ?>" class="bottom-nav-item">
                <i class="fa fa-user"></i>
                <span>Profil</span>
            </a>
        </div>
    </nav>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        // Touch feedback for action cards
        document.querySelectorAll('.action-card, .bottom-nav-item').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.97)';
            });
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        // Smooth scroll for stats
        const statsContainer = document.querySelector('.stats-scroll-container');
        if (statsContainer) {
            let isDown = false;
            let startX;
            let scrollLeft;

            statsContainer.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - statsContainer.offsetLeft;
                scrollLeft = statsContainer.scrollLeft;
            });

            statsContainer.addEventListener('mouseleave', () => {
                isDown = false;
            });

            statsContainer.addEventListener('mouseup', () => {
                isDown = false;
            });

            statsContainer.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - statsContainer.offsetLeft;
                const walk = (x - startX) * 2;
                statsContainer.scrollLeft = scrollLeft - walk;
            });
        }

        // Haptic feedback simulation (for devices that support it)
        if ('vibrate' in navigator) {
            document.querySelectorAll('.action-card').forEach(function(card) {
                card.addEventListener('click', function() {
                    navigator.vibrate(10);
                });
            });
        }
    </script>
</body>
</html>
