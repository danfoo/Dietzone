<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
        }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        /* Header Uniforme Perfex */
        .portal-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
            max-height: 50px;
            max-width: 200px;
        }

        .portal-logo-text {
            font-size: 24px;
            font-weight: 700;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .portal-logo-text i {
            color: #667eea;
        }

        .portal-nav {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .portal-nav a {
            padding: 10px 20px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .portal-nav a:hover {
            background: #f8f9fa;
            color: #667eea;
        }

        .portal-nav a.active {
            background: #667eea;
            color: white;
        }

        .portal-menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: #495057;
            cursor: pointer;
            padding: 5px 10px;
        }

        /* Container */
        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 15px;
        }

        /* Page Header */
        .page-header-modern {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
        }

        .page-header-modern h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-header-modern h1 i {
            color: #667eea;
        }

        .page-header-modern p {
            color: #6c757d;
            margin: 0;
            font-size: 15px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .stat-card.weight { border-left-color: #667eea; }
        .stat-card.target { border-left-color: #11998e; }
        .stat-card.bmi { border-left-color: #f093fb; }
        .stat-card.fat { border-left-color: #fa709a; }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            flex-shrink: 0;
        }

        .stat-card.weight .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card.target .stat-icon { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
        .stat-card.bmi .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-card.fat .stat-icon { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }

        .stat-content {
            flex: 1;
        }

        .stat-label {
            color: #6c757d;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #2c3e50;
            line-height: 1;
        }

        /* Progress Card */
        .progress-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 30px;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            margin-bottom: 30px;
            text-align: center;
        }

        .progress-card .icon {
            font-size: 42px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .progress-card .value {
            font-size: 38px;
            font-weight: 700;
            margin: 10px 0;
        }

        .progress-card .label {
            font-size: 15px;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Action Grid */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            display: block;
            position: relative;
            overflow: hidden;
            border-top: 3px solid transparent;
        }

        .action-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .action-card:hover::after {
            transform: scaleX(1);
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            text-decoration: none;
        }

        .action-card.add-measure { border-top-color: #667eea; }
        .action-card.add-measure::after { background: #667eea; }
        .action-card.history { border-top-color: #11998e; }
        .action-card.history::after { background: #11998e; }
        .action-card.meals { border-top-color: #f093fb; }
        .action-card.meals::after { background: #f093fb; }
        .action-card.consultations { border-top-color: #fa709a; }
        .action-card.consultations::after { background: #fa709a; }
        .action-card.dietitians { border-top-color: #4facfe; }
        .action-card.dietitians::after { background: #4facfe; }

        .action-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }

        .action-card.add-measure .action-icon { background: #667eea; }
        .action-card.history .action-icon { background: #11998e; }
        .action-card.meals .action-icon { background: #f093fb; }
        .action-card.consultations .action-icon { background: #fa709a; }
        .action-card.dietitians .action-icon { background: #4facfe; }

        .action-card h4 {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .action-card p {
            color: #6c757d;
            margin: 0;
            font-size: 13px;
        }

        /* Info Box */
        .info-box {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .info-box h3 {
            color: #2c3e50;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .info-box h3 i {
            color: #667eea;
        }

        .info-box hr {
            border-color: #e9ecef;
            margin: 20px 0;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 15px;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-item i {
            color: #667eea;
            font-size: 16px;
        }

        .info-item strong {
            color: #2c3e50;
            margin-right: 5px;
        }

        .info-item span {
            color: #6c757d;
        }

        /* Consultation List */
        .consultation-item {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid #667eea;
        }

        .consultation-item i {
            font-size: 28px;
            color: #667eea;
        }

        .consultation-info {
            flex: 1;
        }

        .consultation-date {
            font-size: 15px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
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
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 4px solid #667eea;
        }

        .alert-modern i {
            font-size: 28px;
            color: #667eea;
        }

        .alert-modern p {
            margin: 0;
            color: #495057;
            font-size: 15px;
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
        .delay-4 { animation-delay: 0.4s; opacity: 0; }

        /* Responsive */
        @media (max-width: 768px) {
            .portal-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 15px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                gap: 5px;
                z-index: 1000;
            }

            .portal-nav.show {
                display: flex;
            }

            .portal-nav a {
                width: 100%;
                justify-content: flex-start;
            }

            .portal-menu-toggle {
                display: block;
            }

            .portal-header-content {
                position: relative;
            }

            .content-container {
                padding: 20px 10px;
            }

            .page-header-modern {
                padding: 20px 15px;
            }

            .page-header-modern h1 {
                font-size: 22px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .stat-card {
                padding: 20px 15px;
            }

            .stat-icon {
                width: 45px;
                height: 45px;
                font-size: 20px;
            }

            .stat-value {
                font-size: 26px;
            }

            .progress-card {
                padding: 25px 20px;
            }

            .progress-card .icon {
                font-size: 32px;
            }

            .progress-card .value {
                font-size: 30px;
            }

            .action-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .info-row {
                grid-template-columns: 1fr;
            }

            .consultation-item {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            .portal-logo img {
                max-height: 40px;
            }

            .portal-logo-text {
                font-size: 18px;
            }
        }
    </style>
</head>
<body>
    <!-- Header Uniforme -->
    <div class="portal-header">
        <div class="container-fluid">
            <div class="portal-header-content">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="portal-logo">
                    <?php
                    // Essayer d'abord le logo sombre, puis le logo normal
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
                            <?php echo get_option('companyname') ? get_option('companyname') : 'Programme Diététique'; ?>
                        </div>
                    <?php } ?>
                </a>

                <button class="portal-menu-toggle" onclick="toggleMenu()">
                    <i class="fa fa-bars"></i>
                </button>

                <nav class="portal-nav" id="portalNav">
                    <a href="<?php echo site_url('dietetic/portal'); ?>" class="active">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>">
                        <i class="fa fa-cutlery"></i> Repas
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>">
                        <i class="fa fa-user-md"></i> Mon Diététicien
                    </a>
                    <a href="<?php echo site_url('clients/profile'); ?>">
                        <i class="fa fa-user"></i> Profil
                    </a>
                    <a href="<?php echo site_url('authentication/logout'); ?>">
                        <i class="fa fa-sign-out"></i> Déconnexion
                    </a>
                </nav>
            </div>
        </div>
    </div>

    <div class="content-container">
        <!-- Page Header -->
        <div class="page-header-modern animate-in">
            <h1><i class="fa fa-heartbeat"></i> Mon Programme Diététique</h1>
            <p>Bienvenue, <?php echo isset($patient->client->company) ? htmlspecialchars($patient->client->company) : 'Patient'; ?></p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid animate-in delay-1">
            <div class="stat-card weight">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa fa-balance-scale"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Poids Actuel</div>
                        <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement ? number_format($latest_measurement->weight, 1) : '-'; ?> <span style="font-size: 20px; color: #6c757d;">kg</span></div>
                    </div>
                </div>
            </div>
            <div class="stat-card target">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa fa-bullseye"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Poids Cible</div>
                        <div class="stat-value"><?php echo isset($patient->target_weight) && $patient->target_weight ? number_format($patient->target_weight, 1) : '-'; ?> <span style="font-size: 20px; color: #6c757d;">kg</span></div>
                    </div>
                </div>
            </div>
            <div class="stat-card bmi">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa fa-tachometer"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Indice de Masse Corporelle</div>
                        <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->bmi) ? number_format($latest_measurement->bmi, 1) : '-'; ?></div>
                    </div>
                </div>
            </div>
            <div class="stat-card fat">
                <div class="stat-header">
                    <div class="stat-icon"><i class="fa fa-pie-chart"></i></div>
                    <div class="stat-content">
                        <div class="stat-label">Masse Grasse</div>
                        <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->body_fat) ? number_format($latest_measurement->body_fat, 1) . '%' : '-'; ?></div>
                    </div>
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
            <div class="label">Progression du Poids</div>
        </div>

        <!-- Action Grid -->
        <div class="action-grid animate-in delay-3">
            <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="action-card add-measure">
                <div class="action-icon"><i class="fa fa-plus-circle"></i></div>
                <h4>Ajouter une Mesure</h4>
                <p>Suivez votre évolution</p>
            </a>
            <a href="<?php echo site_url('dietetic/portal/measurements'); ?>" class="action-card history">
                <div class="action-icon"><i class="fa fa-history"></i></div>
                <h4>Historique</h4>
                <p>Consultez vos mesures</p>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="action-card meals">
                <div class="action-icon"><i class="fa fa-cutlery"></i></div>
                <h4>Plans Alimentaires</h4>
                <p>Consultez vos repas</p>
            </a>
            <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="action-card consultations">
                <div class="action-icon"><i class="fa fa-calendar-check-o"></i></div>
                <h4>Consultations</h4>
                <p>Vos rendez-vous</p>
            </a>
            <a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>" class="action-card dietitians">
                <div class="action-icon"><i class="fa fa-user-md"></i></div>
                <h4>Mon Diététicien</h4>
                <p>Noter et contacter</p>
            </a>
        </div>

        <!-- Active Program -->
        <?php if (isset($active_program) && $active_program) { ?>
            <div class="info-box animate-in delay-4">
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
                                <span><?php echo $active_program->daily_calories; ?> kcal/jour</span>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (isset($active_program->daily_protein) && $active_program->daily_protein) { ?>
                        <div class="info-item">
                            <i class="fa fa-pie-chart"></i>
                            <div>
                                <strong>Protéines:</strong>
                                <span><?php echo $active_program->daily_protein; ?>g/jour</span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php if (isset($active_program->objective) && $active_program->objective) { ?>
                    <hr>
                    <div style="padding: 15px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #667eea;">
                        <strong style="color: #2c3e50; display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                            <i class="fa fa-target"></i> Objectif:
                        </strong>
                        <p style="margin: 0; color: #495057;"><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert-modern animate-in delay-4">
                <i class="fa fa-info-circle"></i>
                <p>Aucun programme actif pour le moment. Contactez votre diététicien pour commencer votre suivi.</p>
            </div>
        <?php } ?>

        <!-- Upcoming Consultations -->
        <?php if (!empty($upcoming_consultations)) { ?>
            <div class="info-box animate-in delay-4">
                <h3><i class="fa fa-calendar"></i> Consultations à Venir</h3>
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

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        function toggleMenu() {
            var nav = document.getElementById('portalNav');
            nav.classList.toggle('show');
        }

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            var nav = document.getElementById('portalNav');
            var toggle = document.querySelector('.portal-menu-toggle');
            if (!nav.contains(event.target) && !toggle.contains(event.target)) {
                nav.classList.remove('show');
            }
        });
    </script>
</body>
</html>
