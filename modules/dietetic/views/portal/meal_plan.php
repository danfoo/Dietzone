<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#01807B">
    <title><?php echo $meal_plan->plan_name; ?></title>
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

        .content-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        .page-header-mobile {
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            border-radius: 16px;
            padding: 24px 20px;
            margin-bottom: 20px;
            color: white;
            box-shadow: 0 8px 16px rgba(1, 128, 123, 0.3);
        }

        .page-header-mobile h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 4px 0;
        }

        .page-header-mobile p {
            margin: 0;
            font-size: 14px;
            opacity: 0.95;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            min-width: 120px;
            padding: 12px 16px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
        }

        .btn-print {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
        }

        .btn-download {
            background: linear-gradient(135deg, #F3911D 0%, #D67A0F 100%);
            color: white;
        }

        .btn-back-simple {
            background: white;
            color: #495057;
            border: 2px solid #dee2e6;
        }

        .btn-action:hover {
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .btn-action:active {
            transform: scale(0.97);
        }

        .targets-card {
            background: white;
            border-left: 4px solid #F3911D;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .targets-card h5 {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 12px 0;
        }

        .targets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
        }

        .target-item {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            text-align: center;
        }

        .target-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .target-value {
            font-size: 16px;
            font-weight: 700;
            color: #01807B;
        }

        .day-section {
            margin-bottom: 24px;
        }

        .day-header {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
            padding: 14px 20px;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .meal-card {
            background: white;
            border-left: 4px solid #01807B;
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .meal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .meal-type {
            font-size: 16px;
            font-weight: 700;
            color: #01807B;
        }

        .meal-time {
            font-size: 13px;
            color: #6c757d;
            font-weight: 400;
        }

        .meal-name {
            font-size: 15px;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .meal-foods {
            list-style: none;
            margin: 0 0 12px 0;
            padding: 0;
        }

        .meal-foods li {
            display: flex;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .meal-foods li:last-child {
            border-bottom: none;
        }

        .food-name {
            flex: 1;
            font-size: 14px;
            color: #2c3e50;
        }

        .food-quantity {
            font-size: 13px;
            color: #6c757d;
            margin: 0 12px;
        }

        .food-calories {
            font-size: 13px;
            font-weight: 600;
            color: #F3911D;
        }

        .nutrition-summary {
            background: #f8f9fa;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 13px;
            color: #495057;
            margin-top: 10px;
        }

        .nutrition-summary strong {
            color: #01807B;
        }

        .meal-instructions {
            background: #fff8f0;
            border-left: 3px solid #F3911D;
            padding: 12px;
            border-radius: 8px;
            margin-top: 12px;
            font-size: 14px;
            color: #495057;
            line-height: 1.6;
        }

        .meal-instructions i {
            color: #F3911D;
            margin-right: 8px;
        }

        .notes-card {
            background: white;
            border-left: 4px solid #F3911D;
            border-radius: 12px;
            padding: 20px;
            margin-top: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .notes-card h5 {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .notes-card h5 i {
            color: #F3911D;
        }

        .notes-card p {
            color: #495057;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

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

        @media print {
            body {
                padding-bottom: 0;
            }
            .portal-header,
            .bottom-nav,
            .action-buttons {
                display: none !important;
            }
        }

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

            .targets-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

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
            }

            .page-header-mobile h1 {
                font-size: 20px;
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

            .targets-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
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

                <nav class="portal-nav-desktop">
                    <a href="<?php echo site_url('dietetic/portal'); ?>">
                        <i class="fa fa-home"></i> Accueil
                    </a>
                    <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="active">
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

    <div class="content-container">
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-cutlery"></i> <?php echo htmlspecialchars($meal_plan->plan_name); ?></h1>
            <p><?php echo htmlspecialchars($program->program_name); ?> - Semaine <?php echo $meal_plan->week_number; ?></p>
        </div>

        <div class="action-buttons animate-in delay-1">
            <button onclick="window.print()" class="btn-action btn-print">
                <i class="fa fa-print"></i> Imprimer
            </button>
            <a href="<?php echo site_url('dietetic/portal/download_meal_plan/' . $meal_plan->id); ?>" class="btn-action btn-download">
                <i class="fa fa-download"></i> PDF
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="btn-action btn-back-simple">
                <i class="fa fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if ($program->daily_calories || $program->daily_protein || $program->daily_carbs || $program->daily_fats) { ?>
        <div class="targets-card animate-in delay-2">
            <h5><i class="fa fa-bullseye"></i> Objectifs Journaliers</h5>
            <div class="targets-grid">
                <?php if ($program->daily_calories) { ?>
                <div class="target-item">
                    <div class="target-label">Calories</div>
                    <div class="target-value"><?php echo $program->daily_calories; ?> kcal</div>
                </div>
                <?php } ?>
                <?php if ($program->daily_protein) { ?>
                <div class="target-item">
                    <div class="target-label">Protéines</div>
                    <div class="target-value"><?php echo $program->daily_protein; ?>g</div>
                </div>
                <?php } ?>
                <?php if ($program->daily_carbs) { ?>
                <div class="target-item">
                    <div class="target-label">Glucides</div>
                    <div class="target-value"><?php echo $program->daily_carbs; ?>g</div>
                </div>
                <?php } ?>
                <?php if ($program->daily_fats) { ?>
                <div class="target-item">
                    <div class="target-label">Lipides</div>
                    <div class="target-value"><?php echo $program->daily_fats; ?>g</div>
                </div>
                <?php } ?>
            </div>
        </div>
        <?php } ?>

        <?php foreach ($meals_by_day as $day => $meals) { ?>
            <?php if (!empty($meals)) { ?>
                <div class="day-section animate-in delay-3">
                    <div class="day-header">
                        <i class="fa fa-calendar"></i>
                        <?php
                        $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
                        echo isset($days[$day - 1]) ? $days[$day - 1] : 'Jour ' . $day;
                        ?>
                    </div>

                    <?php foreach ($meals as $meal) { ?>
                        <div class="meal-card">
                            <div class="meal-header">
                                <div class="meal-type">
                                    <?php
                                    $meal_types = [
                                        'breakfast' => 'Petit-déjeuner',
                                        'morning_snack' => 'Collation matin',
                                        'lunch' => 'Déjeuner',
                                        'afternoon_snack' => 'Collation après-midi',
                                        'dinner' => 'Dîner',
                                        'evening_snack' => 'Collation soir'
                                    ];
                                    echo isset($meal_types[$meal->meal_type]) ? $meal_types[$meal->meal_type] : ucfirst(str_replace('_', ' ', $meal->meal_type));
                                    ?>
                                    <?php if ($meal->meal_time) { ?>
                                        <span class="meal-time">(<?php echo substr($meal->meal_time, 0, 5); ?>)</span>
                                    <?php } ?>
                                </div>
                            </div>

                            <?php if ($meal->meal_name) { ?>
                                <div class="meal-name"><?php echo htmlspecialchars($meal->meal_name); ?></div>
                            <?php } ?>

                            <?php if (!empty($meal->foods)) { ?>
                                <ul class="meal-foods">
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
                                        <li>
                                            <span class="food-name"><?php echo htmlspecialchars($food->food_name); ?></span>
                                            <span class="food-quantity"><?php echo $food->quantity . ' ' . $food->unit; ?></span>
                                            <span class="food-calories"><?php echo round($calories); ?> kcal</span>
                                        </li>
                                    <?php } ?>
                                </ul>

                                <div class="nutrition-summary">
                                    <strong>Total:</strong>
                                    <?php echo round($meal_calories); ?> kcal |
                                    P: <?php echo number_format($meal_protein, 1); ?>g |
                                    C: <?php echo number_format($meal_carbs, 1); ?>g |
                                    L: <?php echo number_format($meal_fats, 1); ?>g
                                </div>
                            <?php } ?>

                            <?php if ($meal->instructions) { ?>
                                <div class="meal-instructions">
                                    <i class="fa fa-info-circle"></i> <?php echo nl2br(htmlspecialchars($meal->instructions)); ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        <?php } ?>

        <?php if ($meal_plan->notes) { ?>
            <div class="notes-card animate-in">
                <h5><i class="fa fa-sticky-note"></i> Notes Importantes</h5>
                <p><?php echo nl2br(htmlspecialchars($meal_plan->notes)); ?></p>
            </div>
        <?php } ?>
    </div>

    <nav class="bottom-nav">
        <div class="bottom-nav-items">
            <a href="<?php echo site_url('dietetic/portal'); ?>" class="bottom-nav-item">
                <i class="fa fa-home"></i>
                <span>Accueil</span>
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="bottom-nav-item active">
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
        document.querySelectorAll('.meal-card, .bottom-nav-item').forEach(function(element) {
            element.addEventListener('touchstart', function() {
                this.style.transform = 'scale(0.98)';
            });
            element.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });

        if ('vibrate' in navigator) {
            document.querySelectorAll('.btn-action').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    navigator.vibrate(10);
                });
            });
        }
    </script>
</body>
</html>
