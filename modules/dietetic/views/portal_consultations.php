<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title>Mes Consultations</title>
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

        /* Section Title */
        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin: 30px 0 16px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section-title i {
            color: #01807B;
        }

        /* Consultation Card */
        .consultation-card {
            background: white;
            border-left: 4px solid #01807B;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .consultation-card.past {
            border-left-color: #6c757d;
            opacity: 0.85;
        }

        .consultation-card:active {
            transform: scale(0.98);
        }

        .consultation-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .consultation-date {
            font-size: 18px;
            font-weight: 700;
            color: #01807B;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge.upcoming {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
        }

        .status-badge.completed {
            background: #e9ecef;
            color: #6c757d;
        }

        .status-badge.cancelled {
            background: #fee;
            color: #dc3545;
        }

        .consultation-info {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 12px;
            margin-bottom: 16px;
        }

        .info-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        .info-item i {
            color: #01807B;
            font-size: 20px;
            margin-bottom: 6px;
        }

        .info-item label {
            display: block;
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .info-item .value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }

        .consultation-notes {
            padding: 14px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 3px solid #F3911D;
        }

        .consultation-notes strong {
            color: #2c3e50;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .consultation-notes p {
            margin: 8px 0 0 0;
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
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

        /* Buttons */
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

            .consultation-info {
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

            .consultation-card {
                padding: 16px;
                border-radius: 12px;
            }

            .consultation-info {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }

            .consultation-header {
                flex-direction: column;
                align-items: flex-start;
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
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-calendar-check-o"></i> Mes Consultations</h1>
            <p>Historique et rendez-vous à venir</p>
        </div>

        <?php if (!empty($consultations)) { ?>
            <?php
            $now = new DateTime();
            $upcoming = [];
            $past = [];

            foreach ($consultations as $consultation) {
                $consultation_date = new DateTime($consultation->consultation_date);
                if ($consultation_date > $now) {
                    $upcoming[] = $consultation;
                } else {
                    $past[] = $consultation;
                }
            }
            ?>

            <?php if (!empty($upcoming)) { ?>
                <div class="section-title animate-in delay-1">
                    <i class="fa fa-clock-o"></i>
                    <span>Rendez-vous à venir</span>
                </div>
                <div class="animate-in delay-1">
                    <?php foreach ($upcoming as $consultation) { ?>
                        <div class="consultation-card">
                            <div class="consultation-header">
                                <div class="consultation-date">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
                                </div>
                                <span class="status-badge upcoming">À venir</span>
                            </div>

                            <div class="consultation-info">
                                <div class="info-item">
                                    <i class="fa fa-stethoscope"></i>
                                    <label>Type</label>
                                    <div class="value"><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></div>
                                </div>

                                <?php if (isset($consultation->duration)) { ?>
                                <div class="info-item">
                                    <i class="fa fa-clock-o"></i>
                                    <label>Durée</label>
                                    <div class="value"><?php echo $consultation->duration; ?> min</div>
                                </div>
                                <?php } ?>

                                <?php if (isset($consultation->dietitian_name)) { ?>
                                <div class="info-item">
                                    <i class="fa fa-user-md"></i>
                                    <label>Diététicien</label>
                                    <div class="value"><?php echo htmlspecialchars($consultation->dietitian_name); ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php if (!empty($past)) { ?>
                <div class="section-title animate-in delay-2" style="margin-top: 30px;">
                    <i class="fa fa-history"></i>
                    <span>Consultations passées</span>
                </div>
                <div class="animate-in delay-2">
                    <?php foreach ($past as $consultation) { ?>
                        <div class="consultation-card past">
                            <div class="consultation-header">
                                <div class="consultation-date">
                                    <i class="fa fa-calendar"></i>
                                    <?php echo date('d/m/Y à H:i', strtotime($consultation->consultation_date)); ?>
                                </div>
                                <span class="status-badge completed">Terminée</span>
                            </div>

                            <div class="consultation-info">
                                <div class="info-item">
                                    <i class="fa fa-stethoscope"></i>
                                    <label>Type</label>
                                    <div class="value"><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></div>
                                </div>

                                <?php if (isset($consultation->duration)) { ?>
                                <div class="info-item">
                                    <i class="fa fa-clock-o"></i>
                                    <label>Durée</label>
                                    <div class="value"><?php echo $consultation->duration; ?> min</div>
                                </div>
                                <?php } ?>

                                <?php if (isset($consultation->dietitian_name)) { ?>
                                <div class="info-item">
                                    <i class="fa fa-user-md"></i>
                                    <label>Diététicien</label>
                                    <div class="value"><?php echo htmlspecialchars($consultation->dietitian_name); ?></div>
                                </div>
                                <?php } ?>
                            </div>

                            <?php if (isset($consultation->observations) && $consultation->observations) { ?>
                            <div class="consultation-notes">
                                <strong><i class="fa fa-sticky-note"></i> Observations</strong>
                                <p><?php echo nl2br(htmlspecialchars($consultation->observations)); ?></p>
                            </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

        <?php } else { ?>
            <!-- Empty State -->
            <div class="empty-state animate-in delay-1">
                <div class="empty-state-icon">
                    <i class="fa fa-calendar-o"></i>
                </div>
                <div class="empty-state-title">Aucune consultation</div>
                <div class="empty-state-text">
                    Vous n'avez pas encore de consultations enregistrées. Contactez votre diététicien pour prendre rendez-vous.
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
        // Touch feedback
        document.querySelectorAll('.consultation-card, .bottom-nav-item').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.97)';
            });
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        // Haptic feedback
        if ('vibrate' in navigator) {
            document.querySelectorAll('.consultation-card').forEach(function(card) {
                card.addEventListener('click', function() {
                    navigator.vibrate(10);
                });
            });
        }
    </script>
</body>
</html>
