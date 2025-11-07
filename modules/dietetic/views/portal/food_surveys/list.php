<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title>Mes Enquêtes Alimentaires - <?php echo get_option('companyname'); ?></title>
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
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .portal-logo img {
            max-height: 40px;
            width: auto;
        }

        .portal-logo-text {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #01807B;
            font-size: 20px;
            font-weight: 700;
        }

        .portal-logo-text i {
            font-size: 24px;
        }

        .portal-nav-desktop {
            display: none;
            gap: 5px;
        }

        @media (min-width: 769px) {
            .portal-nav-desktop {
                display: flex;
            }
        }

        .portal-nav-desktop a {
            padding: 10px 16px;
            color: #6c757d;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .portal-nav-desktop a:hover {
            background: #f8f9fa;
            color: #01807B;
        }

        .portal-nav-desktop a.active {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            color: white;
        }

        /* Content Container */
        .content-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        /* Page Header */
        .page-header-mobile {
            margin-bottom: 25px;
        }

        .page-header-mobile h1 {
            font-size: 26px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header-mobile h1 i {
            color: #01807B;
            font-size: 28px;
        }

        .page-header-mobile p {
            color: #6c757d;
            font-size: 15px;
            margin: 0;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 60px 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .empty-state i {
            font-size: 80px;
            color: #01807B;
            opacity: 0.2;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 22px;
            font-weight: 700;
            color: #212529;
            margin: 0 0 12px 0;
        }

        .empty-state p {
            color: #6c757d;
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 8px 0;
        }

        /* Surveys Grid */
        .surveys-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 769px) {
            .surveys-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1200px) {
            .surveys-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* Survey Card */
        .survey-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .survey-card:active {
            transform: scale(0.98);
        }

        @media (min-width: 769px) {
            .survey-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 8px 24px rgba(1, 128, 123, 0.15);
            }
        }

        .survey-card-header {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            padding: 20px;
            color: white;
        }

        .survey-card-header h3 {
            margin: 0 0 8px 0;
            font-size: 19px;
            font-weight: 700;
        }

        .survey-objective {
            font-size: 14px;
            opacity: 0.95;
            line-height: 1.5;
        }

        .survey-card-body {
            padding: 20px;
        }

        .survey-info-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 14px;
            font-size: 14px;
            color: #495057;
        }

        .survey-info-item i {
            color: #01807B;
            margin-top: 2px;
            flex-shrink: 0;
        }

        .survey-info-item strong {
            font-weight: 600;
            color: #212529;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge.active {
            background: rgba(72, 187, 120, 0.1);
            color: #48bb78;
        }

        .status-badge.completed {
            background: rgba(66, 153, 225, 0.1);
            color: #4299e1;
        }

        .status-badge.cancelled {
            background: rgba(245, 101, 101, 0.1);
            color: #f56565;
        }

        /* Progress Section */
        .progress-section {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid #e9ecef;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .progress-label span {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
        }

        .progress-percentage {
            color: #01807B !important;
            font-size: 16px !important;
        }

        .progress-bar-container {
            height: 10px;
            background: #e9ecef;
            border-radius: 20px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #01807B 0%, #F3911D 100%);
            border-radius: 20px;
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
        }

        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Action Buttons */
        .survey-card-footer {
            padding: 20px;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 10px;
        }

        .btn-action {
            flex: 1;
            padding: 12px 16px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            min-height: 44px;
        }

        .btn-primary-action {
            background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
            color: white;
        }

        .btn-primary-action:active {
            transform: scale(0.96);
        }

        @media (min-width: 769px) {
            .btn-primary-action:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
                color: white;
                text-decoration: none;
            }
        }

        .btn-secondary-action {
            background: white;
            color: #01807B;
            border: 2px solid #01807B;
        }

        .btn-secondary-action:active {
            background: #01807B;
            color: white;
        }

        @media (min-width: 769px) {
            .btn-secondary-action:hover {
                background: #01807B;
                color: white;
                text-decoration: none;
            }
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            border-top: 1px solid #e9ecef;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.08);
            z-index: 99;
            display: block;
        }

        @media (min-width: 769px) {
            .bottom-nav {
                display: none;
            }
        }

        .bottom-nav-items {
            display: flex;
            justify-content: space-around;
            padding: 8px 0;
            max-width: 600px;
            margin: 0 auto;
        }

        .bottom-nav-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 6px 0;
            color: #6c757d;
            text-decoration: none;
            font-size: 11px;
            font-weight: 500;
            transition: all 0.2s;
            min-height: 44px;
            justify-content: center;
        }

        .bottom-nav-item.active {
            color: #01807B;
        }

        .bottom-nav-item i {
            font-size: 22px;
        }

        .bottom-nav-item:active {
            transform: scale(0.95);
        }

        @media (max-width: 480px) {
            .survey-card-footer {
                flex-direction: column;
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
                    <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="active">
                        <i class="fa fa-clipboard-list"></i> Enquêtes
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
        <!-- Page Header -->
        <div class="page-header-mobile">
            <h1>
                <i class="fa fa-clipboard-list"></i>
                Mes Enquêtes Alimentaires
            </h1>
            <p>Suivez vos enquêtes et soumettez vos repas quotidiens</p>
        </div>

        <?php if (empty($surveys)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fa fa-clipboard-list"></i>
                <h3>Aucune enquête alimentaire</h3>
                <p>Votre diététicien ne vous a pas encore assigné d'enquête alimentaire.</p>
                <p>Les enquêtes alimentaires vous permettent de partager vos repas<br class="d-none d-md-block"> et de recevoir des recommandations personnalisées.</p>
            </div>
        <?php else: ?>
            <!-- Surveys Grid -->
            <div class="surveys-grid">
                <?php foreach ($surveys as $survey): ?>
                    <div class="survey-card">
                        <div class="survey-card-header">
                            <h3><?php echo htmlspecialchars($survey->survey_name); ?></h3>
                            <?php if ($survey->objective): ?>
                                <div class="survey-objective">
                                    <?php echo htmlspecialchars($survey->objective); ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="survey-card-body">
                            <!-- Status -->
                            <div class="survey-info-item">
                                <i class="fa fa-info-circle"></i>
                                <span>
                                    Statut:
                                    <?php
                                    $status_class = 'active';
                                    $status_text = 'Active';
                                    $status_icon = 'check-circle';

                                    if ($survey->status == 'completed') {
                                        $status_class = 'completed';
                                        $status_text = 'Terminée';
                                        $status_icon = 'flag-checkered';
                                    } elseif ($survey->status == 'cancelled') {
                                        $status_class = 'cancelled';
                                        $status_text = 'Annulée';
                                        $status_icon = 'times-circle';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <i class="fa fa-<?php echo $status_icon; ?>"></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </span>
                            </div>

                            <!-- Duration -->
                            <div class="survey-info-item">
                                <i class="fa fa-calendar"></i>
                                <span>
                                    <strong><?php echo $survey->duration_days; ?> jours</strong>
                                    (<?php echo date('d/m/Y', strtotime($survey->start_date)); ?> - <?php echo date('d/m/Y', strtotime($survey->end_date)); ?>)
                                </span>
                            </div>

                            <!-- Dietitian -->
                            <?php if (isset($survey->dietitian_name) && $survey->dietitian_name): ?>
                                <div class="survey-info-item">
                                    <i class="fa fa-user-md"></i>
                                    <span>Diététicien: <strong><?php echo htmlspecialchars($survey->dietitian_name); ?></strong></span>
                                </div>
                            <?php endif; ?>

                            <!-- Progress -->
                            <div class="progress-section">
                                <div class="progress-label">
                                    <span>Progression</span>
                                    <span class="progress-percentage"><?php echo round($survey->completion_percentage); ?>%</span>
                                </div>
                                <div class="progress-bar-container">
                                    <div class="progress-bar-fill" style="width: <?php echo $survey->completion_percentage; ?>%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="survey-card-footer">
                            <?php if ($survey->status == 'active'): ?>
                                <a href="<?php echo site_url('dietetic/portal/food_survey_submit/' . $survey->id); ?>" class="btn-action btn-primary-action">
                                    <i class="fa fa-camera"></i>
                                    Soumettre
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo site_url('dietetic/portal/view_recommendations/' . $survey->id); ?>" class="btn-action btn-secondary-action">
                                <i class="fa fa-comments"></i>
                                Recommandations
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
            <a href="<?php echo site_url('dietetic/portal/food_surveys'); ?>" class="bottom-nav-item active">
                <i class="fa fa-clipboard-list"></i>
                <span>Enquêtes</span>
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
        // Touch feedback for cards
        document.querySelectorAll('.survey-card, .btn-action, .bottom-nav-item').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.opacity = '0.8';
            });
            element.addEventListener('touchend', function() {
                this.style.opacity = '1';
            });
        });
    </script>
</body>
</html>
