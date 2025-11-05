<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Mes Plans Alimentaires'; ?></title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body { padding-top: 20px; background: #f5f5f5; }
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
        .navbar-custom .navbar-nav>li>a { color: white; }
        .navbar-custom .navbar-nav>li>a:hover { background: rgba(255,255,255,0.1); }

        .meal-plan-card {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            border-left: 4px solid #e74c3c;
            transition: all 0.3s ease;
        }
        .meal-plan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .meal-plan-card h3 {
            color: #2c3e50;
            margin-top: 0;
        }
        .week-badge {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .navbar-header { float: none; }
            .navbar-toggle {
                display: block;
                float: left;
                margin-left: 15px;
            }
            .navbar-brand { display: block; text-align: center; }
            .navbar-collapse {
                border-top: 1px solid rgba(255,255,255,0.2);
                box-shadow: none;
            }
            .navbar-collapse.collapse { display: none!important; }
            .navbar-collapse.collapse.in { display: block!important; }
            .navbar-nav { float: none!important; margin: 0; }
            .navbar-nav>li { float: none; }
            .navbar-nav>li>a { padding-top: 15px; padding-bottom: 15px; }
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
                    <li><a href="<?php echo site_url('dietetic/portal'); ?>"><i class="fa fa-home"></i> Tableau de bord</a></li>
                    <li class="active"><a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>"><i class="fa fa-cutlery"></i> Mes Repas</a></li>
                    <li><a href="<?php echo site_url('clients/profile'); ?>"><i class="fa fa-user"></i> Profil</a></li>
                    <li><a href="<?php echo site_url('authentication/logout'); ?>"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h2><i class="fa fa-cutlery"></i> Mes Plans Alimentaires</h2>
        <hr>

        <?php if (isset($active_program) && $active_program) { ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i> Programme: <strong><?php echo htmlspecialchars($active_program->program_name); ?></strong>
            </div>

            <?php if (!empty($meal_plans)) { ?>
                <div class="row">
                    <?php foreach ($meal_plans as $plan) { ?>
                        <div class="col-md-6">
                            <div class="meal-plan-card">
                                <span class="week-badge"><i class="fa fa-calendar"></i> Semaine <?php echo $plan->week_number; ?></span>
                                <h3><?php echo htmlspecialchars($plan->plan_name); ?></h3>

                                <?php if ($plan->notes) { ?>
                                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($plan->notes)); ?></p>
                                <?php } ?>

                                <hr>
                                <a href="<?php echo site_url('dietetic/portal/view_meal_plan/' . $plan->id); ?>" class="btn btn-primary btn-block">
                                    <i class="fa fa-eye"></i> Voir les Repas
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle"></i> Aucun plan alimentaire créé pour ce programme.
                </div>
            <?php } ?>

        <?php } else { ?>
            <div class="alert alert-warning">
                <i class="fa fa-exclamation-triangle"></i> Aucun programme actif pour le moment.
            </div>
        <?php } ?>

        <hr>
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Retour au Tableau de bord
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
