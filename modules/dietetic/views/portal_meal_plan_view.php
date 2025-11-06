<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title><?php echo isset($title) ? $title : 'Plan Alimentaire'; ?></title>
    <?php if (file_exists(FCPATH . 'assets/images/favicon.ico')) { ?>
        <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico'); ?>">
    <?php } ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 80px;
        }

        /* Sticky Header */
        .header {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            padding: 15px 20px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .back-btn {
            background: #f0f0f0;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: #333;
            cursor: pointer;
            transition: all 0.3s;
        }

        .back-btn:active {
            transform: scale(0.95);
            background: #e0e0e0;
        }

        .header-title {
            flex: 1;
            text-align: center;
            padding: 0 10px;
        }

        .header-title h1 {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-title p {
            font-size: 12px;
            color: #7f8c8d;
            margin: 2px 0 0;
        }

        .menu-btn {
            background: none;
            border: none;
            font-size: 20px;
            color: #333;
            cursor: pointer;
        }

        /* Container */
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px 15px;
        }

        /* Objectives Card */
        .objectives-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .objectives-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .objectives-title i {
            color: #667eea;
        }

        .objectives-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .objective-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            color: white;
        }

        .objective-value {
            font-size: 22px;
            font-weight: 700;
            display: block;
            margin-bottom: 4px;
        }

        .objective-label {
            font-size: 11px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Day Tabs */
        .day-tabs {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding: 0 15px 15px;
            margin: 0 -15px 20px;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        .day-tabs::-webkit-scrollbar {
            display: none;
        }

        .day-tab {
            background: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            color: #7f8c8d;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .day-tab.active {
            background: white;
            color: #667eea;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .day-tab:active {
            transform: scale(0.95);
        }

        /* Day Content */
        .day-content {
            display: none;
        }

        .day-content.active {
            display: block;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Meal Card */
        .meal-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 15px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .meal-header {
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        }

        .meal-header:active {
            background: #f5f5f5;
        }

        .meal-info {
            flex: 1;
        }

        .meal-type {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
        }

        .meal-time {
            font-size: 13px;
            color: #7f8c8d;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .meal-toggle {
            width: 32px;
            height: 32px;
            background: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            transition: transform 0.3s;
        }

        .meal-card.expanded .meal-toggle {
            transform: rotate(180deg);
        }

        .meal-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .meal-card.expanded .meal-body {
            max-height: 2000px;
            transition: max-height 0.5s ease-in;
        }

        .meal-content {
            padding: 0 20px 20px;
        }

        .meal-name {
            background: #fff3cd;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-style: italic;
            color: #856404;
            font-size: 14px;
        }

        /* Food Items */
        .food-items {
            margin-bottom: 15px;
        }

        .food-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 10px;
        }

        .food-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .food-quantity {
            color: #667eea;
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .food-nutrition {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .nutrition-item {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
        }

        .nutrition-label {
            color: #7f8c8d;
        }

        .nutrition-value {
            font-weight: 600;
            color: #2c3e50;
        }

        /* Meal Total */
        .meal-total {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .meal-total-title {
            font-weight: 700;
            color: #155724;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .meal-total-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #155724;
        }

        .total-value {
            font-weight: 700;
        }

        /* Instructions */
        .instructions {
            background: #e7f3ff;
            border-left: 4px solid #2196f3;
            padding: 12px;
            border-radius: 0 8px 8px 0;
            font-size: 13px;
            line-height: 1.6;
            color: #0c5460;
        }

        .instructions-title {
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: white;
        }

        .empty-state i {
            font-size: 48px;
            opacity: 0.5;
            margin-bottom: 15px;
        }

        .empty-state p {
            font-size: 16px;
            opacity: 0.8;
        }

        /* Notes Card */
        .notes-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .notes-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .notes-content {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
        }

        /* Weekly Summary */
        .weekly-summary {
            background: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .summary-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 15px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .summary-item {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 700;
            color: #667eea;
            display: block;
            margin-bottom: 4px;
        }

        .summary-label {
            font-size: 11px;
            color: #7f8c8d;
            text-transform: uppercase;
        }

        /* Bottom Nav */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            padding: 10px 0 max(10px, env(safe-area-inset-bottom));
            z-index: 99;
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px;
            color: #7f8c8d;
            text-decoration: none;
            font-size: 11px;
            transition: all 0.3s;
        }

        .nav-item i {
            font-size: 20px;
        }

        .nav-item.active {
            color: #667eea;
        }

        .nav-item:active {
            transform: scale(0.95);
        }

        /* Responsive */
        @media (min-width: 768px) {
            body {
                padding-bottom: 0;
            }

            .header {
                position: relative;
            }

            .header-title h1 {
                font-size: 22px;
            }

            .bottom-nav {
                display: none;
            }

            .objectives-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .food-nutrition {
                grid-template-columns: repeat(4, 1fr);
            }

            .summary-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        /* Smooth scrolling */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <button class="back-btn" onclick="window.location.href='<?php echo site_url('dietetic/portal/meal_plans'); ?>'">
                <i class="fa fa-arrow-left"></i>
            </button>
            <div class="header-title">
                <h1><?php echo htmlspecialchars($meal_plan->plan_name); ?></h1>
                <p>Semaine <?php echo $meal_plan->week_number; ?></p>
            </div>
            <button class="menu-btn" onclick="toggleMenu()">
                <i class="fa fa-ellipsis-v"></i>
            </button>
        </div>
    </div>

    <div class="container">
        <!-- Objectives Card -->
        <?php if ($program->daily_calories || $program->daily_protein) { ?>
            <div class="objectives-card">
                <div class="objectives-title">
                    <i class="fa fa-bullseye"></i>
                    Objectifs Journaliers
                </div>
                <div class="objectives-grid">
                    <?php if ($program->daily_calories) { ?>
                        <div class="objective-item">
                            <span class="objective-value"><?php echo $program->daily_calories; ?></span>
                            <span class="objective-label">Calories</span>
                        </div>
                    <?php } ?>
                    <?php if ($program->daily_protein) { ?>
                        <div class="objective-item">
                            <span class="objective-value"><?php echo round($program->daily_protein); ?>g</span>
                            <span class="objective-label">Protéines</span>
                        </div>
                    <?php } ?>
                    <?php if ($program->daily_carbs) { ?>
                        <div class="objective-item">
                            <span class="objective-value"><?php echo round($program->daily_carbs); ?>g</span>
                            <span class="objective-label">Glucides</span>
                        </div>
                    <?php } ?>
                    <?php if ($program->daily_fats) { ?>
                        <div class="objective-item">
                            <span class="objective-value"><?php echo round($program->daily_fats); ?>g</span>
                            <span class="objective-label">Lipides</span>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- Day Tabs -->
        <div class="day-tabs">
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

            foreach ($days_fr as $day_num => $day_name) {
                $active = ($day_num == 1) ? 'active' : '';
            ?>
                <button class="day-tab <?php echo $active; ?>" onclick="showDay(<?php echo $day_num; ?>)">
                    <?php echo $day_name; ?>
                </button>
            <?php } ?>
        </div>

        <?php
        $meal_types_fr = [
            'breakfast' => 'Petit-déjeuner',
            'snack_am' => 'Collation Matinale',
            'lunch' => 'Déjeuner',
            'snack_pm' => 'Collation Après-midi',
            'dinner' => 'Dîner',
            'snack_evening' => 'Collation Soirée'
        ];

        $meal_icons = [
            'breakfast' => 'fa-coffee',
            'snack_am' => 'fa-apple',
            'lunch' => 'fa-cutlery',
            'snack_pm' => 'fa-lemon-o',
            'dinner' => 'fa-moon-o',
            'snack_evening' => 'fa-star'
        ];

        foreach ($days_fr as $day_num => $day_name) {
            $day_meals = isset($meals_by_day[$day_num]) ? $meals_by_day[$day_num] : [];
            $active_class = ($day_num == 1) ? 'active' : '';
        ?>
            <div class="day-content <?php echo $active_class; ?>" id="day-<?php echo $day_num; ?>">
                <?php if (!empty($day_meals)) { ?>
                    <?php foreach ($day_meals as $meal) { ?>
                        <div class="meal-card" onclick="toggleMeal(this)">
                            <div class="meal-header">
                                <div class="meal-info">
                                    <div class="meal-type">
                                        <i class="fa <?php echo isset($meal_icons[$meal->meal_type]) ? $meal_icons[$meal->meal_type] : 'fa-cutlery'; ?>"></i>
                                        <?php echo isset($meal_types_fr[$meal->meal_type]) ? $meal_types_fr[$meal->meal_type] : ucfirst(str_replace('_', ' ', $meal->meal_type)); ?>
                                    </div>
                                    <?php if ($meal->meal_time) { ?>
                                        <div class="meal-time">
                                            <i class="fa fa-clock-o"></i>
                                            <?php echo substr($meal->meal_time, 0, 5); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="meal-toggle">
                                    <i class="fa fa-chevron-down"></i>
                                </div>
                            </div>
                            <div class="meal-body">
                                <div class="meal-content">
                                    <?php if ($meal->meal_name) { ?>
                                        <div class="meal-name">
                                            <i class="fa fa-bookmark"></i> <?php echo htmlspecialchars($meal->meal_name); ?>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($meal->foods)) { ?>
                                        <div class="food-items">
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
                                                <div class="food-item">
                                                    <div class="food-name"><?php echo htmlspecialchars($food->food_name); ?></div>
                                                    <div class="food-quantity">
                                                        <i class="fa fa-balance-scale"></i> <?php echo $food->quantity . ' ' . $food->unit; ?>
                                                    </div>
                                                    <div class="food-nutrition">
                                                        <div class="nutrition-item">
                                                            <span class="nutrition-label">Calories</span>
                                                            <span class="nutrition-value"><?php echo round($calories); ?> kcal</span>
                                                        </div>
                                                        <div class="nutrition-item">
                                                            <span class="nutrition-label">Protéines</span>
                                                            <span class="nutrition-value"><?php echo round($protein, 1); ?>g</span>
                                                        </div>
                                                        <div class="nutrition-item">
                                                            <span class="nutrition-label">Glucides</span>
                                                            <span class="nutrition-value"><?php echo round($carbs, 1); ?>g</span>
                                                        </div>
                                                        <div class="nutrition-item">
                                                            <span class="nutrition-label">Lipides</span>
                                                            <span class="nutrition-value"><?php echo round($fats, 1); ?>g</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>

                                        <div class="meal-total">
                                            <div class="meal-total-title">
                                                <i class="fa fa-calculator"></i> Total du Repas
                                            </div>
                                            <div class="meal-total-grid">
                                                <div class="total-item">
                                                    <span>Calories</span>
                                                    <span class="total-value"><?php echo round($meal_calories); ?> kcal</span>
                                                </div>
                                                <div class="total-item">
                                                    <span>Protéines</span>
                                                    <span class="total-value"><?php echo round($meal_protein, 1); ?>g</span>
                                                </div>
                                                <div class="total-item">
                                                    <span>Glucides</span>
                                                    <span class="total-value"><?php echo round($meal_carbs, 1); ?>g</span>
                                                </div>
                                                <div class="total-item">
                                                    <span>Lipides</span>
                                                    <span class="total-value"><?php echo round($meal_fats, 1); ?>g</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <p style="color: #7f8c8d; font-style: italic;">Aucun aliment défini pour ce repas.</p>
                                    <?php } ?>

                                    <?php if ($meal->instructions) { ?>
                                        <div class="instructions">
                                            <div class="instructions-title">
                                                <i class="fa fa-info-circle"></i> Instructions
                                            </div>
                                            <?php echo nl2br(htmlspecialchars($meal->instructions)); ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="empty-state">
                        <i class="fa fa-calendar-times-o"></i>
                        <p>Aucun repas planifié pour ce jour</p>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <!-- Notes -->
        <?php if ($meal_plan->notes) { ?>
            <div class="notes-card">
                <div class="notes-title">
                    <i class="fa fa-sticky-note"></i>
                    Notes du Plan
                </div>
                <div class="notes-content">
                    <?php echo nl2br(htmlspecialchars($meal_plan->notes)); ?>
                </div>
            </div>
        <?php } ?>

        <!-- Weekly Summary -->
        <div class="weekly-summary">
            <div class="summary-title">
                <i class="fa fa-line-chart"></i>
                Total Nutritionnel de la Semaine
            </div>
            <div class="summary-grid">
                <div class="summary-item">
                    <span class="summary-value"><?php echo round($nutrition_totals->calories); ?></span>
                    <span class="summary-label">Calories</span>
                </div>
                <div class="summary-item">
                    <span class="summary-value"><?php echo round($nutrition_totals->protein, 1); ?>g</span>
                    <span class="summary-label">Protéines</span>
                </div>
                <div class="summary-item">
                    <span class="summary-value"><?php echo round($nutrition_totals->carbs, 1); ?>g</span>
                    <span class="summary-label">Glucides</span>
                </div>
                <div class="summary-item">
                    <span class="summary-value"><?php echo round($nutrition_totals->fats, 1); ?>g</span>
                    <span class="summary-label">Lipides</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation -->
    <div class="bottom-nav">
        <a href="<?php echo site_url('dietetic/portal'); ?>" class="nav-item">
            <i class="fa fa-home"></i>
            <span>Accueil</span>
        </a>
        <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="nav-item active">
            <i class="fa fa-cutlery"></i>
            <span>Repas</span>
        </a>
        <a href="<?php echo site_url('clients/profile'); ?>" class="nav-item">
            <i class="fa fa-user"></i>
            <span>Profil</span>
        </a>
    </div>

    <script>
        // Toggle meal expansion
        function toggleMeal(element) {
            element.classList.toggle('expanded');
        }

        // Show specific day
        function showDay(dayNum) {
            // Hide all days
            document.querySelectorAll('.day-content').forEach(el => {
                el.classList.remove('active');
            });

            // Remove active class from all tabs
            document.querySelectorAll('.day-tab').forEach(el => {
                el.classList.remove('active');
            });

            // Show selected day
            document.getElementById('day-' + dayNum).classList.add('active');

            // Add active class to clicked tab
            event.target.classList.add('active');

            // Scroll to top of container smoothly
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Toggle menu (placeholder for future dropdown menu)
        function toggleMenu() {
            alert('Menu options à venir');
        }

        // Expand first meal of first day by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstMeal = document.querySelector('.day-content.active .meal-card');
            if (firstMeal) {
                firstMeal.classList.add('expanded');
            }
        });
    </script>
</body>
</html>
