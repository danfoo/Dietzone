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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-bottom: 30px;
        }

        /* Modern Navbar */
        .navbar-modern {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: none;
            border-radius: 0;
            margin-bottom: 0;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-modern .navbar-brand {
            color: #2c3e50;
            font-weight: 700;
            font-size: 20px;
            padding: 15px;
            display: flex;
            align-items: center;
        }

        .navbar-modern .navbar-brand img {
            max-height: 35px;
            margin-right: 10px;
        }

        .navbar-modern .navbar-brand i {
            margin-right: 8px;
            color: #667eea;
        }

        .navbar-modern .navbar-nav > li > a {
            color: #2c3e50;
            font-weight: 600;
            padding: 15px;
            transition: all 0.3s ease;
        }

        .navbar-modern .navbar-nav > li > a:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }

        .navbar-modern .navbar-nav > li.active > a {
            background: rgba(102, 126, 234, 0.15);
            color: #667eea;
        }

        .navbar-modern .navbar-toggle {
            border-color: #667eea;
            margin-top: 12px;
        }

        .navbar-modern .navbar-toggle .icon-bar {
            background-color: #667eea;
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
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        .page-header-modern h1 {
            color: #2c3e50;
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .page-header-modern p {
            color: #7f8c8d;
            margin: 0;
            font-size: 16px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
        }

        .stat-card.weight::before { background: linear-gradient(90deg, #667eea, #764ba2); }
        .stat-card.target::before { background: linear-gradient(90deg, #11998e, #38ef7d); }
        .stat-card.bmi::before { background: linear-gradient(90deg, #f093fb, #f5576c); }
        .stat-card.fat::before { background: linear-gradient(90deg, #fa709a, #fee140); }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            font-size: 28px;
            color: white;
        }

        .stat-card.weight .stat-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
        .stat-card.target .stat-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .stat-card.bmi .stat-icon { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .stat-card.fat .stat-icon { background: linear-gradient(135deg, #fa709a, #fee140); }

        .stat-value {
            font-size: 36px;
            font-weight: 700;
            color: #2c3e50;
            margin: 10px 0 5px 0;
        }

        .stat-label {
            color: #7f8c8d;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
        }

        /* Progress Card */
        .progress-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 30px;
            color: white;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
            margin-bottom: 30px;
            text-align: center;
        }

        .progress-card .icon {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .progress-card .value {
            font-size: 42px;
            font-weight: 700;
            margin: 10px 0;
        }

        .progress-card .label {
            font-size: 16px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Action Grid */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            border-radius: 16px;
            padding: 30px;
            text-align: center;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: block;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .action-card:hover::before {
            transform: scaleX(1);
        }

        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            text-decoration: none;
        }

        .action-card.add-measure::before { background: linear-gradient(90deg, #667eea, #764ba2); }
        .action-card.history::before { background: linear-gradient(90deg, #11998e, #38ef7d); }
        .action-card.meals::before { background: linear-gradient(90deg, #f093fb, #f5576c); }
        .action-card.consultations::before { background: linear-gradient(90deg, #fa709a, #fee140); }
        .action-card.dietitians::before { background: linear-gradient(90deg, #4facfe, #00f2fe); }

        .action-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            color: white;
        }

        .action-card.add-measure .action-icon { background: linear-gradient(135deg, #667eea, #764ba2); }
        .action-card.history .action-icon { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .action-card.meals .action-icon { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .action-card.consultations .action-icon { background: linear-gradient(135deg, #fa709a, #fee140); }
        .action-card.dietitians .action-icon { background: linear-gradient(135deg, #4facfe, #00f2fe); }

        .action-card h4 {
            color: #2c3e50;
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .action-card p {
            color: #7f8c8d;
            margin: 0;
            font-size: 14px;
        }

        /* Info Box */
        .info-box {
            background: white;
            border-radius: 16px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .info-box h3 {
            color: #2c3e50;
            font-size: 22px;
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
            border-color: #ecf0f1;
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
            font-size: 18px;
        }

        .info-item strong {
            color: #2c3e50;
            margin-right: 5px;
        }

        .info-item span {
            color: #7f8c8d;
        }

        /* Consultation List */
        .consultation-item {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .consultation-item i {
            font-size: 32px;
            color: #667eea;
        }

        .consultation-info {
            flex: 1;
        }

        .consultation-date {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 5px;
        }

        .consultation-type {
            font-size: 14px;
            color: #7f8c8d;
        }

        /* Alert */
        .alert-modern {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 15px;
            border-left: 5px solid #667eea;
        }

        .alert-modern i {
            font-size: 32px;
            color: #667eea;
        }

        .alert-modern p {
            margin: 0;
            color: #2c3e50;
            font-size: 16px;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
        .delay-4 { animation-delay: 0.4s; opacity: 0; }

        /* Responsive */
        @media (max-width: 768px) {
            .content-container {
                padding: 20px 10px;
            }

            .page-header-modern {
                padding: 20px 15px;
                margin-bottom: 20px;
            }

            .page-header-modern h1 {
                font-size: 24px;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .stat-card {
                padding: 20px 15px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
                margin-bottom: 10px;
            }

            .stat-value {
                font-size: 28px;
            }

            .stat-label {
                font-size: 11px;
            }

            .progress-card {
                padding: 25px 20px;
            }

            .progress-card .icon {
                font-size: 36px;
            }

            .progress-card .value {
                font-size: 32px;
            }

            .action-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .action-card {
                padding: 25px 20px;
            }

            .action-icon {
                width: 70px;
                height: 70px;
                font-size: 32px;
                margin-bottom: 15px;
            }

            .info-box {
                padding: 20px 15px;
            }

            .info-box h3 {
                font-size: 18px;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .consultation-item {
                flex-direction: column;
                text-align: center;
                padding: 20px 15px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-modern">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Menu</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo site_url('dietetic/portal'); ?>">
                    <?php
                    $logo_path = get_option('company_logo');
                    if ($logo_path && file_exists(FCPATH . 'uploads/company/' . $logo_path)) {
                    ?>
                        <img src="<?php echo base_url('uploads/company/' . $logo_path); ?>" alt="Logo">
                    <?php } else { ?>
                        <i class="fa fa-heartbeat"></i> Programme Diététique
                    <?php } ?>
                </a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="<?php echo site_url('dietetic/portal'); ?>"><i class="fa fa-home"></i> Accueil</a></li>
                    <li><a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>"><i class="fa fa-cutlery"></i> Repas</a></li>
                    <li><a href="<?php echo site_url('dietetic/portal/my_dietitians'); ?>"><i class="fa fa-user-md"></i> Diététicien</a></li>
                    <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Profil</a></li>
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Sortir</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="content-container">
        <!-- Page Header -->
        <div class="page-header-modern animate-in">
            <h1><i class="fa fa-heartbeat"></i> Mon Programme Diététique</h1>
            <p>Bienvenue, <?php echo isset($patient->client->company) ? htmlspecialchars($patient->client->company) : 'Patient'; ?></p>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid animate-in delay-1">
            <div class="stat-card weight">
                <div class="stat-icon"><i class="fa fa-balance-scale"></i></div>
                <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement ? number_format($latest_measurement->weight, 1) : '-'; ?></div>
                <div class="stat-label">Poids Actuel (kg)</div>
            </div>
            <div class="stat-card target">
                <div class="stat-icon"><i class="fa fa-bullseye"></i></div>
                <div class="stat-value"><?php echo isset($patient->target_weight) && $patient->target_weight ? number_format($patient->target_weight, 1) : '-'; ?></div>
                <div class="stat-label">Poids Cible (kg)</div>
            </div>
            <div class="stat-card bmi">
                <div class="stat-icon"><i class="fa fa-tachometer"></i></div>
                <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->bmi) ? number_format($latest_measurement->bmi, 1) : '-'; ?></div>
                <div class="stat-label">IMC</div>
            </div>
            <div class="stat-card fat">
                <div class="stat-icon"><i class="fa fa-pie-chart"></i></div>
                <div class="stat-value"><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->body_fat) ? number_format($latest_measurement->body_fat, 1) . '%' : '-'; ?></div>
                <div class="stat-label">Masse Grasse</div>
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
                    <div style="padding: 15px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px;">
                        <strong style="color: #2c3e50; display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                            <i class="fa fa-target"></i> Objectif:
                        </strong>
                        <p style="margin: 0; color: #555;"><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
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
</body>
</html>
