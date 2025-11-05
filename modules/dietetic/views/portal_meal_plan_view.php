<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Plan Alimentaire'; ?></title>
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

        .day-panel {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .day-panel .panel-heading {
            background: #3498db;
            color: white;
            padding: 15px;
            margin: -20px -20px 20px;
            border-radius: 8px 8px 0 0;
            font-size: 18px;
            font-weight: bold;
        }
        .meal-box {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #e74c3c;
            border-radius: 3px;
        }
        .meal-type {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        .meal-time {
            color: #7f8c8d;
            font-size: 13px;
        }
        .food-table {
            margin: 10px 0;
            background: white;
            font-size: 13px;
        }
        .food-table th {
            background: #ecf0f1;
            font-weight: bold;
            font-size: 12px;
        }
        .meal-total {
            font-weight: bold;
            background: #e8f5e9;
        }
        .instructions-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 4px;
            margin-top: 10px;
        }
        .nutrition-summary {
            background: #d4edda;
            border: 1px solid #28a745;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
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
        <h3><i class="fa fa-cutlery"></i> <?php echo htmlspecialchars($meal_plan->plan_name); ?></h3>
        <p class="text-muted"><?php echo htmlspecialchars($program->program_name); ?> - Semaine <?php echo $meal_plan->week_number; ?></p>
        <hr>

        <?php if ($program->daily_calories || $program->daily_protein) { ?>
            <div class="nutrition-summary">
                <strong><i class="fa fa-bullseye"></i> Objectifs Journaliers:</strong>
                <?php if ($program->daily_calories) echo '<span class="label label-info">' . $program->daily_calories . ' kcal</span> '; ?>
                <?php if ($program->daily_protein) echo '<span class="label label-success">P: ' . $program->daily_protein . 'g</span> '; ?>
                <?php if ($program->daily_carbs) echo '<span class="label label-warning">G: ' . $program->daily_carbs . 'g</span> '; ?>
                <?php if ($program->daily_fats) echo '<span class="label label-danger">L: ' . $program->daily_fats . 'g</span>'; ?>
            </div>
        <?php } ?>

        <?php
        $days_fr = [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche'
        ];

        $meal_types_fr = [
            'breakfast' => 'Petit-déjeuner',
            'snack_am' => 'Collation Matinale',
            'lunch' => 'Déjeuner',
            'snack_pm' => 'Collation Après-midi',
            'dinner' => 'Dîner',
            'snack_evening' => 'Collation Soirée'
        ];

        foreach ($days_fr as $day_num => $day_name) {
            $day_meals = isset($meals_by_day[$day_num]) ? $meals_by_day[$day_num] : [];
        ?>
            <div class="day-panel">
                <div class="panel-heading">
                    <i class="fa fa-calendar"></i> <?php echo $day_name; ?>
                </div>

                <?php if (!empty($day_meals)) { ?>
                    <?php foreach ($day_meals as $meal) { ?>
                        <div class="meal-box">
                            <div class="meal-type">
                                <?php echo isset($meal_types_fr[$meal->meal_type]) ? $meal_types_fr[$meal->meal_type] : ucfirst(str_replace('_', ' ', $meal->meal_type)); ?>
                                <?php if ($meal->meal_time) { ?>
                                    <span class="meal-time">- <?php echo substr($meal->meal_time, 0, 5); ?></span>
                                <?php } ?>
                            </div>

                            <?php if ($meal->meal_name) { ?>
                                <div style="margin-bottom: 10px;">
                                    <em><?php echo htmlspecialchars($meal->meal_name); ?></em>
                                </div>
                            <?php } ?>

                            <?php if (!empty($meal->foods)) { ?>
                                <table class="table table-condensed table-bordered food-table">
                                    <thead>
                                        <tr>
                                            <th>Aliment</th>
                                            <th>Quantité</th>
                                            <th>Calories</th>
                                            <th>Protéines</th>
                                            <th>Glucides</th>
                                            <th>Lipides</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $meal_calories = 0;
                                        $meal_protein = 0;
                                        $meal_carbs = 0;
                                        $meal_fats = 0;

                                        foreach ($meal->foods as $food) {
                                            $ratio = $food->quantity / $food->serving_size;
                                            $calories = $food->calories * $ratio;
                                            $protein = $food->protein * $ratio;
                                            $carbs = $food->carbs * $ratio;
                                            $fats = $food->fats * $ratio;

                                            $meal_calories += $calories;
                                            $meal_protein += $protein;
                                            $meal_carbs += $carbs;
                                            $meal_fats += $fats;
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($food->food_name); ?></td>
                                                <td><?php echo $food->quantity . ' ' . $food->unit; ?></td>
                                                <td><?php echo round($calories); ?> kcal</td>
                                                <td><?php echo round($protein, 1); ?>g</td>
                                                <td><?php echo round($carbs, 1); ?>g</td>
                                                <td><?php echo round($fats, 1); ?>g</td>
                                            </tr>
                                        <?php } ?>
                                        <tr class="meal-total">
                                            <td>TOTAL REPAS</td>
                                            <td>-</td>
                                            <td><?php echo round($meal_calories); ?> kcal</td>
                                            <td><?php echo round($meal_protein, 1); ?>g</td>
                                            <td><?php echo round($meal_carbs, 1); ?>g</td>
                                            <td><?php echo round($meal_fats, 1); ?>g</td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p class="text-muted"><em>Aucun aliment défini pour ce repas.</em></p>
                            <?php } ?>

                            <?php if ($meal->instructions) { ?>
                                <div class="instructions-box">
                                    <i class="fa fa-info-circle"></i> <strong>Instructions:</strong> <?php echo nl2br(htmlspecialchars($meal->instructions)); ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p class="text-muted"><em>Aucun repas planifié pour ce jour</em></p>
                <?php } ?>
            </div>
        <?php } ?>

        <?php if ($meal_plan->notes) { ?>
            <div class="alert alert-warning">
                <strong><i class="fa fa-sticky-note"></i> Notes du Plan:</strong><br>
                <?php echo nl2br(htmlspecialchars($meal_plan->notes)); ?>
            </div>
        <?php } ?>

        <div class="alert alert-success">
            <strong><i class="fa fa-calculator"></i> Total Nutritionnel de la Semaine:</strong><br>
            <?php echo round($nutrition_totals->calories); ?> kcal |
            P: <?php echo number_format($nutrition_totals->protein, 1); ?>g |
            G: <?php echo number_format($nutrition_totals->carbs, 1); ?>g |
            L: <?php echo number_format($nutrition_totals->fats, 1); ?>g
        </div>

        <hr>
        <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Retour aux Plans Alimentaires
        </a>
    </div>

    <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
