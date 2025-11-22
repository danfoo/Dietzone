<?php
$active_page = 'meal_plans';
$page_title = 'Plan de Repas';
?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php $this->load->view('portal/includes/portal_header'); ?>

<style>
        /* Modern Page Header */
        .page-header-mobile {
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            border-radius: 24px;
            padding: 28px 24px;
            margin-bottom: 24px;
            color: white;
            box-shadow: 0 12px 24px rgba(1, 128, 123, 0.25);
        }

        .page-header-mobile h1 {
            font-size: 22px;
            font-weight: 800;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .page-header-mobile h1 i {
            font-size: 28px;
        }

        .page-header-mobile p {
            margin: 0;
            font-size: 15px;
            opacity: 0.95;
            font-weight: 500;
            color: white;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .btn-action {
            flex: 1;
            min-width: 140px;
            background: white;
            color: #495057;
            padding: 12px 20px;
            border-radius: 10px;
            font-weight: 600;
            border: 2px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 48px;
        }

        .btn-action:hover {
            background: #f8f9fa;
            border-color: #01807B;
            color: #01807B;
            text-decoration: none;
        }

        .btn-action:active {
            transform: scale(0.97);
        }

        .btn-action.primary {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            color: white;
            border-color: transparent;
        }

        .btn-action.primary:hover {
            box-shadow: 0 6px 16px rgba(1, 128, 123, 0.4);
            color: white;
        }

        .objectives-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .objectives-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 16px;
        }

        .objectives-title i {
            color: #01807B;
            font-size: 22px;
        }

        .objectives-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 12px;
        }

        .objective-item {
            background: linear-gradient(135deg, #01807B 0%, #F3911D 100%);
            padding: 16px;
            border-radius: 10px;
            text-align: center;
            color: white;
        }

        .objective-value {
            font-size: 24px;
            font-weight: 700;
            display: block;
            margin-bottom: 6px;
        }

        .objective-label {
            font-size: 11px;
            opacity: 0.95;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .day-tabs {
            display: flex;
            overflow-x: auto;
            gap: 10px;
            padding-bottom: 15px;
            margin-bottom: 20px;
            scrollbar-width: none;
            -webkit-overflow-scrolling: touch;
        }

        .day-tabs::-webkit-scrollbar {
            display: none;
        }

        .day-tab {
            background: white;
            border: 2px solid #e9ecef;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            color: #6c757d;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            min-height: 48px;
            display: flex;
            align-items: center;
        }

        .day-tab.active {
            background: #01807B;
            color: white;
            border-color: #01807B;
            box-shadow: 0 4px 12px rgba(1, 128, 123, 0.3);
        }

        .day-tab:active {
            transform: scale(0.95);
        }

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

        .meal-card {
            background: white;
            border-radius: 20px;
            margin-bottom: 16px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
        }

        .meal-card:active {
            transform: scale(0.98);
        }

        .meal-header {
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            user-select: none;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            min-height: 56px;
        }

        .meal-header:active {
            background: #f5f5f5;
        }

        .meal-info {
            flex: 1;
        }

        .meal-type {
            font-size: 17px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meal-type i {
            color: #F3911D;
        }

        .meal-time {
            font-size: 13px;
            color: #6c757d;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .meal-toggle {
            width: 36px;
            height: 36px;
            background: #F3911D;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
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
            max-height: 3000px;
            transition: max-height 0.5s ease-in;
        }

        .meal-content {
            padding: 0 20px 20px;
        }

        .meal-name {
            background: linear-gradient(135deg, rgba(1, 128, 123, 0.1) 0%, rgba(243, 145, 29, 0.1) 100%);
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 16px;
            font-style: italic;
            color: #2c3e50;
            font-size: 14px;
            font-weight: 600;
        }

        .food-items {
            margin-bottom: 16px;
        }

        .food-item {
            background: #f8f9fa;
            padding: 14px;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .food-name {
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 15px;
        }

        .food-quantity {
            color: #01807B;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .food-nutrition {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .nutrition-item {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 6px 10px;
            background: white;
            border-radius: 6px;
        }

        .nutrition-label {
            color: #6c757d;
            font-weight: 600;
        }

        .nutrition-value {
            font-weight: 700;
            color: #2c3e50;
        }

        .meal-total {
            background: linear-gradient(135deg, #01807B 0%, #026661 100%);
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 16px;
            color: white;
        }

        .meal-total-title {
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meal-total-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .total-item {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
        }

        .total-value {
            font-weight: 700;
        }

        .instructions {
            background: linear-gradient(135deg, rgba(243, 145, 29, 0.1) 0%, rgba(243, 145, 29, 0.05) 100%);
            padding: 14px;
            border-radius: 12px;
            font-size: 14px;
            line-height: 1.6;
            color: #495057;
        }

        .instructions-title {
            font-weight: 700;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: #2c3e50;
        }

        .instructions-title i {
            color: #F3911D;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
            background: white;
            border-radius: 12px;
            margin-bottom: 20px;
        }

        .empty-state i {
            font-size: 56px;
            color: #dee2e6;
            margin-bottom: 16px;
        }

        .empty-state p {
            font-size: 16px;
            color: #6c757d;
        }

        .notes-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .notes-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 14px;
            font-size: 16px;
        }

        .notes-title i {
            color: #F3911D;
        }

        .notes-content {
            font-size: 14px;
            line-height: 1.6;
            color: #495057;
        }

        .weekly-summary {
            background: white;
            border-radius: 20px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
        }

        .summary-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 16px;
            font-size: 16px;
        }

        .summary-title i {
            color: #01807B;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .summary-item {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 10px;
            text-align: center;
        }

        .summary-value {
            font-size: 20px;
            font-weight: 700;
            color: #01807B;
            display: block;
            margin-bottom: 6px;
        }

        .summary-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
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

            .objectives-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .food-nutrition {
                grid-template-columns: repeat(4, 1fr);
            }

            .summary-grid {
                grid-template-columns: repeat(4, 1fr);
            }

            .meal-total-grid {
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
                margin-bottom: 20px;
            }

            .page-header-mobile h1 {
                font-size: 20px;
            }

            .action-buttons {
                flex-direction: column;
            }

            .btn-action {
                width: 100%;
            }
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

        @media print {
            .action-buttons,
            .day-tabs {
                display: none !important;
            }

            body {
                background: white;
                padding: 0;
            }

            .meal-card {
                page-break-inside: avoid;
            }

            .meal-body {
                max-height: none !important;
            }

            .meal-card.expanded .meal-body,
            .meal-body {
                max-height: none !important;
            }
        }
</style>

<div class="content-container">
        <div class="page-header-mobile animate-in">
            <h1><i class="fa fa-cutlery"></i> <?php echo htmlspecialchars($meal_plan->plan_name); ?></h1>
            <p><?php echo htmlspecialchars($program->program_name); ?> - Semaine <?php echo $meal_plan->week_number; ?></p>
        </div>

        <div class="action-buttons animate-in delay-1">
            <button class="btn-action" onclick="window.print()">
                <i class="fa fa-print"></i> Imprimer
            </button>
            <a href="<?php echo site_url('dietetic/portal/download_meal_plan_pdf/' . $meal_plan->id); ?>" class="btn-action">
                <i class="fa fa-file-pdf-o"></i> Télécharger PDF
            </a>
            <a href="<?php echo site_url('dietetic/portal/meal_plans'); ?>" class="btn-action primary">
                <i class="fa fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if ($program->daily_calories || $program->daily_protein) { ?>
            <div class="objectives-card animate-in delay-2">
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
                                        <p style="color: #6c757d; font-style: italic; padding: 16px;">Aucun aliment défini pour ce repas.</p>
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

<script>
    function toggleMeal(element) {
        element.classList.toggle('expanded');

        if ('vibrate' in navigator) {
            navigator.vibrate(10);
        }
    }

    function showDay(dayNum) {
        document.querySelectorAll('.day-content').forEach(el => {
            el.classList.remove('active');
        });

        document.querySelectorAll('.day-tab').forEach(el => {
            el.classList.remove('active');
        });

        document.getElementById('day-' + dayNum).classList.add('active');
        event.target.classList.add('active');

        window.scrollTo({ top: 300, behavior: 'smooth' });

        if ('vibrate' in navigator) {
            navigator.vibrate(10);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const firstMeal = document.querySelector('.day-content.active .meal-card');
        if (firstMeal) {
            firstMeal.classList.add('expanded');
        }
    });
</script>

<?php $this->load->view('portal/includes/portal_footer'); ?>
