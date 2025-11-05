<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Mon Programme'; ?></title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { padding-top: 20px; background: #f5f5f5; }

        /* Navbar with logo */
        .navbar-custom {
            background: #3498db;
            border: none;
            border-radius: 0;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .navbar-custom .navbar-brand {
            color: white;
            display: flex;
            align-items: center;
        }
        .navbar-custom .navbar-brand img {
            max-height: 30px;
            margin-right: 10px;
        }
        .navbar-custom .navbar-nav>li>a {
            color: white;
        }
        .navbar-custom .navbar-nav>li>a:hover {
            background: rgba(255,255,255,0.1);
        }

        /* Stat boxes with colors */
        .stat-box {
            background: white;
            padding: 25px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .stat-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }
        .stat-box.weight::before { background: #3498db; }
        .stat-box.target::before { background: #2ecc71; }
        .stat-box.bmi::before { background: #f39c12; }
        .stat-box.weight-progress::before { background: #9b59b6; }

        .stat-box .icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        .stat-box.weight .icon { color: #3498db; }
        .stat-box.target .icon { color: #2ecc71; }
        .stat-box.bmi .icon { color: #f39c12; }
        .stat-box.weight-progress .icon { color: #9b59b6; }

        .stat-box h2 {
            margin: 10px 0;
            color: #333;
            font-size: 32px;
            font-weight: bold;
        }
        .stat-box p {
            color: #666;
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Program box */
        .program-box {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .program-box h3 {
            color: #2c3e50;
            margin-top: 0;
        }

        /* Action buttons */
        .action-btn {
            background: white;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s ease;
            border-left: 4px solid;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .action-btn.measurement { border-left-color: #3498db; }
        .action-btn.meals { border-left-color: #e74c3c; }
        .action-btn.consultations { border-left-color: #2ecc71; }

        .action-btn .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .action-btn.measurement .icon { color: #3498db; }
        .action-btn.meals .icon { color: #e74c3c; }
        .action-btn.consultations .icon { color: #2ecc71; }

        .action-btn h4 {
            margin: 10px 0 5px 0;
            color: #2c3e50;
        }
        .action-btn p {
            margin: 0;
            color: #7f8c8d;
            font-size: 13px;
        }

        /* Mobile menu from left */
        @media (max-width: 768px) {
            .navbar-header { float: none; }
            .navbar-toggle {
                display: block;
                float: left;
                margin-left: 15px;
            }
            .navbar-brand {
                display: block;
                text-align: center;
            }
            .navbar-collapse {
                border-top: 1px solid rgba(255,255,255,0.2);
                box-shadow: none;
            }
            .navbar-collapse.collapse {
                display: none!important;
            }
            .navbar-collapse.collapse.in {
                display: block!important;
            }
            .navbar-nav {
                float: none!important;
                margin: 0;
            }
            .navbar-nav>li { float: none; }
            .navbar-nav>li>a {
                padding-top: 15px;
                padding-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
        <div class="container">
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
                    <li class="active"><a href="<?php echo site_url('dietetic/portal'); ?>"><i class="fa fa-home"></i> Tableau de bord</a></li>
                    <li><a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>"><i class="fa fa-cutlery"></i> Mes Repas</a></li>
                    <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Profil</a></li>
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2><i class="fa fa-heartbeat"></i> Mon Programme Diététique</h2>
        <hr>

        <!-- Stats -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="stat-box weight">
                    <div class="icon"><i class="fa fa-balance-scale"></i></div>
                    <h2><?php echo isset($latest_measurement) && $latest_measurement ? $latest_measurement->weight : '-'; ?></h2>
                    <p>Poids Actuel (kg)</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-box target">
                    <div class="icon"><i class="fa fa-bullseye"></i></div>
                    <h2><?php echo isset($patient->target_weight) && $patient->target_weight ? $patient->target_weight : '-'; ?></h2>
                    <p>Poids Cible (kg)</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-box bmi">
                    <div class="icon"><i class="fa fa-tachometer"></i></div>
                    <h2><?php echo isset($latest_measurement) && $latest_measurement && isset($latest_measurement->bmi) ? number_format($latest_measurement->bmi, 1) : '-'; ?></h2>
                    <p>IMC</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stat-box weight-progress">
                    <div class="icon"><i class="fa fa-line-chart"></i></div>
                    <h2 class="<?php echo isset($weight_progress->weight_change) && $weight_progress->weight_change < 0 ? 'text-success' : ''; ?>">
                        <?php echo isset($weight_progress->weight_change) && $weight_progress->weight_change !== null ? ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1) : '-'; ?>
                    </h2>
                    <p>Progression (kg)</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-4">
                <a href="<?php echo site_url('dietetic/portal/add_measurement'); ?>" class="action-btn measurement" style="display: block; text-decoration: none;">
                    <div class="icon"><i class="fa fa-plus-circle"></i></div>
                    <h4>Ajouter une Mesure</h4>
                    <p>Suivez votre évolution</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="action-btn meals" style="display: block; text-decoration: none;">
                    <div class="icon"><i class="fa fa-cutlery"></i></div>
                    <h4>Mes Plans Alimentaires</h4>
                    <p>Consultez vos repas</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="action-btn consultations" style="display: block; text-decoration: none;">
                    <div class="icon"><i class="fa fa-calendar-check-o"></i></div>
                    <h4>Mes Consultations</h4>
                    <p>Gérez vos rendez-vous</p>
                </a>
            </div>
        </div>

        <!-- Active Program -->
        <?php if (isset($active_program) && $active_program) { ?>
            <div class="program-box">
                <h3><i class="fa fa-check-circle" style="color: #2ecc71;"></i> <?php echo htmlspecialchars($active_program->program_name); ?></h3>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong><i class="fa fa-calendar"></i> Date de Début:</strong> <?php echo date('d/m/Y', strtotime($active_program->start_date)); ?></p>
                        <?php if (isset($active_program->end_date) && $active_program->end_date) { ?>
                            <p><strong><i class="fa fa-calendar"></i> Date de Fin:</strong> <?php echo date('d/m/Y', strtotime($active_program->end_date)); ?></p>
                        <?php } ?>
                    </div>
                    <div class="col-md-6">
                        <?php if (isset($active_program->daily_calories) && $active_program->daily_calories) { ?>
                            <p><strong><i class="fa fa-fire"></i> Calories Journalières:</strong> <?php echo $active_program->daily_calories; ?> kcal</p>
                        <?php } ?>
                        <?php if (isset($active_program->daily_protein) && $active_program->daily_protein) { ?>
                            <p><strong><i class="fa fa-pie-chart"></i> Protéines:</strong> <?php echo $active_program->daily_protein; ?>g</p>
                        <?php } ?>
                    </div>
                </div>
                <?php if (isset($active_program->objective) && $active_program->objective) { ?>
                    <hr>
                    <p><strong><i class="fa fa-target"></i> Objectif:</strong></p>
                    <p><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Aucun programme actif pour le moment.
            </div>
        <?php } ?>

        <!-- Upcoming Consultations -->
        <?php if (!empty($upcoming_consultations)) { ?>
            <div class="program-box">
                <h3><i class="fa fa-calendar" style="color: #3498db;"></i> Consultations à Venir</h3>
                <hr>
                <ul class="list-unstyled">
                    <?php foreach ($upcoming_consultations as $consultation) { ?>
                        <li style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                            <i class="fa fa-calendar-o" style="color: #3498db;"></i>
                            <?php echo date('d/m/Y H:i', strtotime($consultation->consultation_date)); ?>
                            - <strong><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></strong>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
