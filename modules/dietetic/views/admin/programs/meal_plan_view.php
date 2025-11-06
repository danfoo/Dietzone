<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.meal-plan-header {
    background: linear-gradient(135deg, #8e44ad 0%, #9b59b6 100%);
    color: white;
    padding: 30px;
    border-radius: 8px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(142, 68, 173, 0.3);
}

.meal-plan-header h1 {
    margin: 0 0 10px 0;
    font-size: 32px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 15px;
}

.meal-plan-header .plan-meta {
    font-size: 16px;
    opacity: 0.95;
    margin-bottom: 15px;
}

.meal-plan-header .header-actions {
    margin-top: 15px;
}

.meal-plan-header .btn {
    background: white;
    color: #8e44ad;
    border: none;
    padding: 10px 25px;
    font-weight: 600;
    margin-right: 10px;
    transition: all 0.3s ease;
}

.meal-plan-header .btn:hover {
    background: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
}

.meal-plan-header .btn.whatsapp-btn {
    background: #25D366 !important;
    color: white !important;
}

.meal-plan-header .btn.whatsapp-btn:hover {
    background: #128C7E !important;
    color: white !important;
}

.nutrition-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
}

.nutrition-card {
    flex: 1;
    min-width: 200px;
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.nutrition-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.nutrition-card.calories {
    border-left-color: #e74c3c;
}

.nutrition-card.protein {
    border-left-color: #3498db;
}

.nutrition-card.carbs {
    border-left-color: #f39c12;
}

.nutrition-card.fats {
    border-left-color: #27ae60;
}

.nutrition-card .icon {
    font-size: 32px;
    margin-bottom: 10px;
}

.nutrition-card.calories .icon { color: #e74c3c; }
.nutrition-card.protein .icon { color: #3498db; }
.nutrition-card.carbs .icon { color: #f39c12; }
.nutrition-card.fats .icon { color: #27ae60; }

.nutrition-card h3 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: #2c3e50;
}

.nutrition-card p {
    margin: 5px 0 0 0;
    color: #7f8c8d;
    font-size: 13px;
    text-transform: uppercase;
}

.nutrition-card .target {
    font-size: 12px;
    color: #95a5a6;
    margin-top: 5px;
}

.day-quick-nav {
    background: white;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 60px;
    z-index: 100;
}

.day-quick-nav .nav-title {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.day-quick-nav .day-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.day-quick-nav .day-link {
    padding: 8px 15px;
    background: #ecf0f1;
    color: #2c3e50;
    border-radius: 20px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.day-quick-nav .day-link:hover {
    background: #8e44ad;
    color: white;
    transform: scale(1.05);
}

.day-panel {
    background: white;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.day-panel-header {
    background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
    color: white;
    padding: 20px 25px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.day-panel-header-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.day-panel-header h4 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}

.day-panel-header i.fa-calendar {
    font-size: 24px;
}

.day-share-btn {
    background: #25D366;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.day-share-btn:hover {
    background: #128C7E;
    transform: scale(1.05);
}

.day-panel-body {
    padding: 25px;
}

.meal-card {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 20px;
    border-left: 5px solid;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.meal-card:hover {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transform: translateX(5px);
}

.meal-card.breakfast { border-left-color: #f39c12; }
.meal-card.morning_snack { border-left-color: #3498db; }
.meal-card.lunch { border-left-color: #e74c3c; }
.meal-card.afternoon_snack { border-left-color: #9b59b6; }
.meal-card.dinner { border-left-color: #27ae60; }
.meal-card.evening_snack { border-left-color: #16a085; }

.meal-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 15px;
}

.meal-title {
    flex: 1;
}

.meal-title h5 {
    margin: 0 0 5px 0;
    font-size: 18px;
    font-weight: 700;
    color: #2c3e50;
    display: flex;
    align-items: center;
    gap: 10px;
}

.meal-type-icon {
    font-size: 20px;
}

.meal-type-icon.breakfast { color: #f39c12; }
.meal-type-icon.morning_snack { color: #3498db; }
.meal-type-icon.lunch { color: #e74c3c; }
.meal-type-icon.afternoon_snack { color: #9b59b6; }
.meal-type-icon.dinner { color: #27ae60; }
.meal-type-icon.evening_snack { color: #16a085; }

.meal-time {
    background: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    color: #7f8c8d;
}

.foods-table {
    background: white;
    border-radius: 5px;
    overflow: hidden;
    margin-top: 15px;
}

.foods-table table {
    margin: 0;
}

.foods-table thead th {
    background: #34495e;
    color: white;
    font-weight: 600;
    border: none;
    padding: 12px;
    font-size: 12px;
    text-transform: uppercase;
}

.foods-table tbody td {
    padding: 12px;
    border-color: #ecf0f1;
}

.foods-table .total-row {
    background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
    font-weight: 700;
    color: #27ae60;
}

.meal-instructions {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 15px;
    margin-top: 15px;
    border-radius: 4px;
}

.meal-instructions i {
    color: #f39c12;
    margin-right: 8px;
}

.no-meals-message {
    text-align: center;
    padding: 40px;
    color: #95a5a6;
}

.no-meals-message i {
    font-size: 48px;
    margin-bottom: 15px;
    display: block;
    opacity: 0.5;
}

.bottom-actions {
    background: white;
    padding: 20px 25px;
    border-radius: 8px;
    margin-top: 30px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
}

.bottom-actions .btn {
    padding: 12px 25px;
    font-weight: 600;
}

.week-summary {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
    color: white;
    padding: 25px;
    border-radius: 8px;
    margin-top: 30px;
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
}

.week-summary h4 {
    margin: 0 0 15px 0;
    font-size: 18px;
    font-weight: 600;
}

.week-summary .summary-stats {
    display: flex;
    gap: 30px;
    flex-wrap: wrap;
}

.week-summary .stat {
    display: flex;
    flex-direction: column;
}

.week-summary .stat-value {
    font-size: 24px;
    font-weight: 700;
}

.week-summary .stat-label {
    font-size: 12px;
    opacity: 0.9;
    text-transform: uppercase;
}

.plan-notes {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 20px;
    border-radius: 5px;
    margin-top: 20px;
}

.plan-notes h5 {
    margin: 0 0 10px 0;
    color: #856404;
    font-weight: 600;
}

@media (max-width: 768px) {
    .nutrition-stats {
        flex-direction: column;
    }

    .day-quick-nav {
        position: relative;
        top: 0;
    }

    .meal-header {
        flex-direction: column;
        gap: 10px;
    }

    .bottom-actions {
        flex-direction: column;
    }

    .day-panel-header {
        flex-wrap: wrap;
    }

    .day-share-btn {
        font-size: 12px;
        padding: 6px 12px;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Header Section -->
        <div class="meal-plan-header">
            <h1>
                <i class="fa fa-cutlery"></i>
                <?php echo htmlspecialchars($meal_plan->plan_name); ?>
            </h1>
            <div class="plan-meta">
                <i class="fa fa-folder-o"></i> <?php echo htmlspecialchars($program->program_name); ?>
                <span style="margin: 0 10px;">•</span>
                <i class="fa fa-calendar"></i> <?php echo _l('dietetic_week'); ?> <?php echo $meal_plan->week_number; ?>
                <span style="margin: 0 10px;">•</span>
                <i class="fa fa-user"></i> <?php echo htmlspecialchars($patient->client_name); ?>
            </div>
            <div class="header-actions">
                <?php if (dietetic_has_permission('create')) { ?>
                    <a href="<?php echo admin_url('dietetic/programs/meal/create?meal_plan_id=' . $meal_plan->id); ?>" class="btn">
                        <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_meal'); ?>
                    </a>
                <?php } ?>
                <?php if (dietetic_has_permission('edit')) { ?>
                    <a href="<?php echo admin_url('dietetic/programs/edit_meal_plan/' . $meal_plan->id); ?>" class="btn">
                        <i class="fa fa-pencil"></i> Modifier le Plan
                    </a>
                <?php } ?>
                <a href="<?php echo admin_url('dietetic/programs/generate_pdf/' . $meal_plan->id); ?>" class="btn">
                    <i class="fa fa-file-pdf-o"></i> Générer PDF
                </a>
                <button type="button" class="btn whatsapp-btn" onclick="shareViaWhatsApp()">
                    <i class="fa fa-whatsapp"></i> Partager via WhatsApp
                </button>
            </div>
        </div>

        <!-- Daily Targets -->
        <?php if ($program->daily_calories || $program->daily_protein) { ?>
            <div class="nutrition-stats">
                <?php if ($program->daily_calories) { ?>
                    <div class="nutrition-card calories">
                        <div class="icon"><i class="fa fa-fire"></i></div>
                        <h3><?php echo number_format($program->daily_calories); ?></h3>
                        <p>Calories / Jour</p>
                        <div class="target">Objectif quotidien</div>
                    </div>
                <?php } ?>
                <?php if ($program->daily_protein) { ?>
                    <div class="nutrition-card protein">
                        <div class="icon"><i class="fa fa-flash"></i></div>
                        <h3><?php echo number_format($program->daily_protein); ?>g</h3>
                        <p>Protéines</p>
                        <div class="target">Objectif quotidien</div>
                    </div>
                <?php } ?>
                <?php if ($program->daily_carbs) { ?>
                    <div class="nutrition-card carbs">
                        <div class="icon"><i class="fa fa-leaf"></i></div>
                        <h3><?php echo number_format($program->daily_carbs); ?>g</h3>
                        <p>Glucides</p>
                        <div class="target">Objectif quotidien</div>
                    </div>
                <?php } ?>
                <?php if ($program->daily_fats) { ?>
                    <div class="nutrition-card fats">
                        <div class="icon"><i class="fa fa-tint"></i></div>
                        <h3><?php echo number_format($program->daily_fats); ?>g</h3>
                        <p>Lipides</p>
                        <div class="target">Objectif quotidien</div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <!-- Quick Navigation -->
        <div class="day-quick-nav">
            <div class="nav-title">
                <i class="fa fa-calendar-o"></i>
                Navigation Rapide
            </div>
            <div class="day-links">
                <a href="#day-1" class="day-link"><i class="fa fa-circle"></i> Lundi</a>
                <a href="#day-2" class="day-link"><i class="fa fa-circle"></i> Mardi</a>
                <a href="#day-3" class="day-link"><i class="fa fa-circle"></i> Mercredi</a>
                <a href="#day-4" class="day-link"><i class="fa fa-circle"></i> Jeudi</a>
                <a href="#day-5" class="day-link"><i class="fa fa-circle"></i> Vendredi</a>
                <a href="#day-6" class="day-link"><i class="fa fa-circle"></i> Samedi</a>
                <a href="#day-7" class="day-link"><i class="fa fa-circle"></i> Dimanche</a>
            </div>
        </div>

        <!-- Days and Meals -->
        <?php
        $days = [
            1 => _l('dietetic_monday'),
            2 => _l('dietetic_tuesday'),
            3 => _l('dietetic_wednesday'),
            4 => _l('dietetic_thursday'),
            5 => _l('dietetic_friday'),
            6 => _l('dietetic_saturday'),
            7 => _l('dietetic_sunday')
        ];

        $meal_type_icons = [
            'breakfast' => 'fa-sun-o',
            'morning_snack' => 'fa-coffee',
            'lunch' => 'fa-cutlery',
            'afternoon_snack' => 'fa-apple',
            'dinner' => 'fa-moon-o',
            'evening_snack' => 'fa-star'
        ];

        foreach ($days as $day_num => $day_name) {
            $day_meals = isset($meals_by_day[$day_num]) ? $meals_by_day[$day_num] : [];
        ?>
            <div class="day-panel" id="day-<?php echo $day_num; ?>">
                <div class="day-panel-header">
                    <div class="day-panel-header-left">
                        <i class="fa fa-calendar"></i>
                        <h4><?php echo $day_name; ?></h4>
                    </div>
                    <button type="button" class="day-share-btn" onclick="shareDayViaWhatsApp(<?php echo $day_num; ?>, '<?php echo addslashes($day_name); ?>')">
                        <i class="fa fa-whatsapp"></i> Partager
                    </button>
                </div>
                <div class="day-panel-body">
                    <?php if (!empty($day_meals)) { ?>
                        <?php foreach ($day_meals as $meal) {
                            $meal_type_class = str_replace(' ', '_', strtolower($meal->meal_type));
                            $meal_icon = isset($meal_type_icons[$meal_type_class]) ? $meal_type_icons[$meal_type_class] : 'fa-cutlery';
                        ?>
                            <div class="meal-card <?php echo $meal_type_class; ?>">
                                <div class="meal-header">
                                    <div class="meal-title">
                                        <h5>
                                            <i class="fa <?php echo $meal_icon; ?> meal-type-icon <?php echo $meal_type_class; ?>"></i>
                                            <?php echo ucfirst(str_replace('_', ' ', $meal->meal_type)); ?>
                                        </h5>
                                        <?php if ($meal->meal_name) { ?>
                                            <em style="color: #7f8c8d;"><?php echo htmlspecialchars($meal->meal_name); ?></em>
                                        <?php } ?>
                                    </div>
                                    <div>
                                        <?php if ($meal->meal_time) { ?>
                                            <span class="meal-time">
                                                <i class="fa fa-clock-o"></i> <?php echo substr($meal->meal_time, 0, 5); ?>
                                            </span>
                                        <?php } ?>
                                        <?php if (dietetic_has_permission('edit')) { ?>
                                            <a href="<?php echo admin_url('dietetic/programs/meal/edit/' . $meal->id); ?>"
                                               class="btn btn-sm btn-default"
                                               style="margin-left: 10px;">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>

                                <?php if (!empty($meal->foods)) { ?>
                                    <div class="foods-table">
                                        <table class="table table-condensed">
                                            <thead>
                                                <tr>
                                                    <th><i class="fa fa-cutlery"></i> <?php echo _l('dietetic_food'); ?></th>
                                                    <th><i class="fa fa-balance-scale"></i> <?php echo _l('dietetic_quantity'); ?></th>
                                                    <th><i class="fa fa-fire"></i> <?php echo _l('dietetic_calories'); ?></th>
                                                    <th><i class="fa fa-flash"></i> <?php echo _l('dietetic_protein'); ?></th>
                                                    <th><i class="fa fa-leaf"></i> <?php echo _l('dietetic_carbs'); ?></th>
                                                    <th><i class="fa fa-tint"></i> <?php echo _l('dietetic_fats'); ?></th>
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
                                                        <td><strong><?php echo $food->quantity . ' ' . $food->unit; ?></strong></td>
                                                        <td><?php echo round($calories); ?> kcal</td>
                                                        <td><?php echo round($protein, 1); ?>g</td>
                                                        <td><?php echo round($carbs, 1); ?>g</td>
                                                        <td><?php echo round($fats, 1); ?>g</td>
                                                    </tr>
                                                <?php } ?>
                                                <tr class="total-row">
                                                    <td><i class="fa fa-calculator"></i> <strong><?php echo _l('dietetic_meal_total'); ?></strong></td>
                                                    <td>-</td>
                                                    <td><strong><?php echo round($meal_calories); ?> kcal</strong></td>
                                                    <td><strong><?php echo round($meal_protein, 1); ?>g</strong></td>
                                                    <td><strong><?php echo round($meal_carbs, 1); ?>g</strong></td>
                                                    <td><strong><?php echo round($meal_fats, 1); ?>g</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } else { ?>
                                    <p class="text-muted" style="margin: 15px 0;">
                                        <i class="fa fa-info-circle"></i>
                                        <em><?php echo _l('dietetic_no_foods_in_meal'); ?></em>
                                    </p>
                                <?php } ?>

                                <?php if ($meal->instructions) { ?>
                                    <div class="meal-instructions">
                                        <i class="fa fa-info-circle"></i>
                                        <strong><?php echo _l('dietetic_instructions'); ?>:</strong>
                                        <?php echo nl2br(htmlspecialchars($meal->instructions)); ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="no-meals-message">
                            <i class="fa fa-calendar-times-o"></i>
                            <p><em><?php echo _l('dietetic_no_meals_planned'); ?></em></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- Plan Notes -->
        <?php if ($meal_plan->notes) { ?>
            <div class="plan-notes">
                <h5>
                    <i class="fa fa-sticky-note-o"></i>
                    <?php echo _l('dietetic_plan_notes'); ?>
                </h5>
                <?php echo nl2br(htmlspecialchars($meal_plan->notes)); ?>
            </div>
        <?php } ?>

        <!-- Week Summary -->
        <div class="week-summary">
            <h4>
                <i class="fa fa-pie-chart"></i>
                <?php echo _l('dietetic_week_total_nutrition'); ?>
            </h4>
            <div class="summary-stats">
                <div class="stat">
                    <span class="stat-value">
                        <i class="fa fa-fire"></i> <?php echo number_format(round($nutrition_totals->calories)); ?>
                    </span>
                    <span class="stat-label">Calories Totales</span>
                </div>
                <div class="stat">
                    <span class="stat-value">
                        <i class="fa fa-flash"></i> <?php echo number_format($nutrition_totals->protein, 1); ?>g
                    </span>
                    <span class="stat-label">Protéines</span>
                </div>
                <div class="stat">
                    <span class="stat-value">
                        <i class="fa fa-leaf"></i> <?php echo number_format($nutrition_totals->carbs, 1); ?>g
                    </span>
                    <span class="stat-label">Glucides</span>
                </div>
                <div class="stat">
                    <span class="stat-value">
                        <i class="fa fa-tint"></i> <?php echo number_format($nutrition_totals->fats, 1); ?>g
                    </span>
                    <span class="stat-label">Lipides</span>
                </div>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="bottom-actions">
            <div>
                <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> <?php echo _l('dietetic_back_to_program'); ?>
                </a>
            </div>
            <div>
                <?php if (dietetic_has_permission('edit')) { ?>
                    <a href="<?php echo admin_url('dietetic/programs/edit_meal_plan/' . $meal_plan->id); ?>" class="btn btn-info">
                        <i class="fa fa-pencil"></i> <?php echo _l('dietetic_edit_plan_details'); ?>
                    </a>
                <?php } ?>
                <a href="<?php echo admin_url('dietetic/programs/generate_pdf/' . $meal_plan->id); ?>" class="btn btn-success">
                    <i class="fa fa-file-pdf-o"></i> <?php echo _l('dietetic_generate_pdf'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
// Smooth scroll for quick navigation
$(document).ready(function() {
    $('.day-link').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');
        $('html, body').animate({
            scrollTop: $(target).offset().top - 80
        }, 500);
    });
});

// WhatsApp Share Function
function shareViaWhatsApp() {
    // Build message text
    let message = "*📋 PLAN DE REPAS DIÉTÉTIQUE*\n\n";
    message += "*Plan:* <?php echo htmlspecialchars($meal_plan->plan_name); ?>\n";
    message += "*Semaine:* <?php echo $meal_plan->week_number; ?>\n";
    message += "*Patient:* <?php echo htmlspecialchars($patient->client_name); ?>\n";
    message += "*Programme:* <?php echo htmlspecialchars($program->program_name); ?>\n";
    message += "\n";

    <?php if ($program->daily_calories || $program->daily_protein) { ?>
    message += "*🎯 OBJECTIFS QUOTIDIENS*\n";
    <?php if ($program->daily_calories) { ?>
    message += "• Calories: <?php echo number_format($program->daily_calories); ?> kcal/jour\n";
    <?php } ?>
    <?php if ($program->daily_protein) { ?>
    message += "• Protéines: <?php echo number_format($program->daily_protein); ?>g/jour\n";
    <?php } ?>
    <?php if ($program->daily_carbs) { ?>
    message += "• Glucides: <?php echo number_format($program->daily_carbs); ?>g/jour\n";
    <?php } ?>
    <?php if ($program->daily_fats) { ?>
    message += "• Lipides: <?php echo number_format($program->daily_fats); ?>g/jour\n";
    <?php } ?>
    message += "\n";
    <?php } ?>

    message += "━━━━━━━━━━━━━━━━━━━━\n\n";

    <?php
    $days_fr = [
        1 => 'LUNDI',
        2 => 'MARDI',
        3 => 'MERCREDI',
        4 => 'JEUDI',
        5 => 'VENDREDI',
        6 => 'SAMEDI',
        7 => 'DIMANCHE'
    ];

    $meal_type_fr = [
        'breakfast' => '☀️ Petit-déjeuner',
        'morning_snack' => '☕ Collation matinale',
        'lunch' => '🍽️ Déjeuner',
        'afternoon_snack' => '🍎 Collation après-midi',
        'dinner' => '🌙 Dîner',
        'evening_snack' => '⭐ Collation soirée'
    ];

    foreach ($days as $day_num => $day_name) {
        $day_meals = isset($meals_by_day[$day_num]) ? $meals_by_day[$day_num] : [];
        if (!empty($day_meals)) {
    ?>
    message += "*📅 <?php echo $days_fr[$day_num]; ?>*\n";
    <?php foreach ($day_meals as $meal) {
        $meal_type_class = str_replace(' ', '_', strtolower($meal->meal_type));
        $meal_type_label = isset($meal_type_fr[$meal_type_class]) ? $meal_type_fr[$meal_type_class] : ucfirst(str_replace('_', ' ', $meal->meal_type));
    ?>
    message += "\n*<?php echo $meal_type_label; ?>*";
    <?php if ($meal->meal_time) { ?>
    message += " (<?php echo substr($meal->meal_time, 0, 5); ?>)";
    <?php } ?>
    <?php if ($meal->meal_name) { ?>
    message += "\n_<?php echo htmlspecialchars($meal->meal_name); ?>_";
    <?php } ?>
    message += "\n";

    <?php if (!empty($meal->foods)) {
        foreach ($meal->foods as $food) {
    ?>
    message += "  • <?php echo htmlspecialchars($food->food_name); ?> - <?php echo $food->quantity . ' ' . $food->unit; ?>\n";
    <?php }

        // Calculate totals
        $meal_calories = 0;
        $meal_protein = 0;
        $meal_carbs = 0;
        $meal_fats = 0;

        foreach ($meal->foods as $food) {
            $ratio = $food->quantity / $food->serving_size;
            $meal_calories += $food->calories * $ratio;
            $meal_protein += $food->protein * $ratio;
            $meal_carbs += $food->carbs * $ratio;
            $meal_fats += $food->fats * $ratio;
        }
    ?>
    message += "  _Total: <?php echo round($meal_calories); ?> kcal | P: <?php echo round($meal_protein, 1); ?>g | G: <?php echo round($meal_carbs, 1); ?>g | L: <?php echo round($meal_fats, 1); ?>g_\n";
    <?php } ?>

    <?php if ($meal->instructions) { ?>
    message += "  ℹ️ Instructions: <?php echo htmlspecialchars(str_replace(["\r\n", "\n", "\r"], ' ', $meal->instructions)); ?>\n";
    <?php } ?>

    <?php } ?>
    message += "\n";
    <?php }
    } ?>

    message += "━━━━━━━━━━━━━━━━━━━━\n\n";
    message += "*📊 BILAN HEBDOMADAIRE*\n";
    message += "• Calories totales: <?php echo number_format(round($nutrition_totals->calories)); ?> kcal\n";
    message += "• Protéines: <?php echo number_format($nutrition_totals->protein, 1); ?>g\n";
    message += "• Glucides: <?php echo number_format($nutrition_totals->carbs, 1); ?>g\n";
    message += "• Lipides: <?php echo number_format($nutrition_totals->fats, 1); ?>g\n";

    <?php if ($meal_plan->notes) { ?>
    message += "\n*📝 NOTES*\n";
    message += "<?php echo htmlspecialchars(str_replace(["\r\n", "\n", "\r"], '\n', $meal_plan->notes)); ?>\n";
    <?php } ?>

    message += "\n_Généré par DietSenegal - Programme Diététique_";

    // Encode for URL
    const encodedMessage = encodeURIComponent(message);

    // Get patient phone if available (would need to be passed from backend)
    <?php if (!empty($patient->phonenumber)) { ?>
    const phoneNumber = "<?php echo preg_replace('/[^0-9+]/', '', $patient->phonenumber); ?>";
    const whatsappURL = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
    <?php } else { ?>
    const whatsappURL = `https://wa.me/?text=${encodedMessage}`;
    <?php } ?>

    // Open WhatsApp
    window.open(whatsappURL, '_blank');
}

// WhatsApp Share Function for Single Day
function shareDayViaWhatsApp(dayNum, dayName) {
    // Build message text for single day
    let message = "*📋 PLAN DE REPAS DIÉTÉTIQUE*\n\n";
    message += "*Plan:* <?php echo htmlspecialchars($meal_plan->plan_name); ?>\n";
    message += "*Semaine:* <?php echo $meal_plan->week_number; ?>\n";
    message += "*Jour:* " + dayName + "\n";
    message += "*Patient:* <?php echo htmlspecialchars($patient->client_name); ?>\n";
    message += "*Programme:* <?php echo htmlspecialchars($program->program_name); ?>\n";
    message += "\n";

    <?php if ($program->daily_calories || $program->daily_protein) { ?>
    message += "*🎯 OBJECTIFS QUOTIDIENS*\n";
    <?php if ($program->daily_calories) { ?>
    message += "• Calories: <?php echo number_format($program->daily_calories); ?> kcal/jour\n";
    <?php } ?>
    <?php if ($program->daily_protein) { ?>
    message += "• Protéines: <?php echo number_format($program->daily_protein); ?>g/jour\n";
    <?php } ?>
    <?php if ($program->daily_carbs) { ?>
    message += "• Glucides: <?php echo number_format($program->daily_carbs); ?>g/jour\n";
    <?php } ?>
    <?php if ($program->daily_fats) { ?>
    message += "• Lipides: <?php echo number_format($program->daily_fats); ?>g/jour\n";
    <?php } ?>
    message += "\n";
    <?php } ?>

    message += "━━━━━━━━━━━━━━━━━━━━\n\n";
    message += "*📅 " + dayName.toUpperCase() + "*\n\n";

    <?php
    $meal_type_fr = [
        'breakfast' => '☀️ Petit-déjeuner',
        'morning_snack' => '☕ Collation matinale',
        'lunch' => '🍽️ Déjeuner',
        'afternoon_snack' => '🍎 Collation après-midi',
        'dinner' => '🌙 Dîner',
        'evening_snack' => '⭐ Collation soirée'
    ];

    foreach ($days as $day_num => $day_name) {
        $day_meals = isset($meals_by_day[$day_num]) ? $meals_by_day[$day_num] : [];
    ?>
    if (dayNum === <?php echo $day_num; ?>) {
        <?php if (!empty($day_meals)) { ?>
            <?php foreach ($day_meals as $meal) {
                $meal_type_class = str_replace(' ', '_', strtolower($meal->meal_type));
                $meal_type_label = isset($meal_type_fr[$meal_type_class]) ? $meal_type_fr[$meal_type_class] : ucfirst(str_replace('_', ' ', $meal->meal_type));
            ?>
            message += "*<?php echo $meal_type_label; ?>*";
            <?php if ($meal->meal_time) { ?>
            message += " (<?php echo substr($meal->meal_time, 0, 5); ?>)";
            <?php } ?>
            <?php if ($meal->meal_name) { ?>
            message += "\n_<?php echo htmlspecialchars($meal->meal_name); ?>_";
            <?php } ?>
            message += "\n";

            <?php if (!empty($meal->foods)) {
                foreach ($meal->foods as $food) {
            ?>
            message += "  • <?php echo htmlspecialchars($food->food_name); ?> - <?php echo $food->quantity . ' ' . $food->unit; ?>\n";
            <?php }

                // Calculate totals
                $meal_calories = 0;
                $meal_protein = 0;
                $meal_carbs = 0;
                $meal_fats = 0;

                foreach ($meal->foods as $food) {
                    $ratio = $food->quantity / $food->serving_size;
                    $meal_calories += $food->calories * $ratio;
                    $meal_protein += $food->protein * $ratio;
                    $meal_carbs += $food->carbs * $ratio;
                    $meal_fats += $food->fats * $ratio;
                }
            ?>
            message += "  _Total: <?php echo round($meal_calories); ?> kcal | P: <?php echo round($meal_protein, 1); ?>g | G: <?php echo round($meal_carbs, 1); ?>g | L: <?php echo round($meal_fats, 1); ?>g_\n";
            <?php } ?>

            <?php if ($meal->instructions) { ?>
            message += "  ℹ️ Instructions: <?php echo htmlspecialchars(str_replace(["\r\n", "\n", "\r"], ' ', $meal->instructions)); ?>\n";
            <?php } ?>
            message += "\n";
            <?php } ?>
        <?php } else { ?>
            message += "_Aucun repas planifié pour ce jour_\n\n";
        <?php } ?>
    }
    <?php } ?>

    <?php if ($meal_plan->notes) { ?>
    message += "━━━━━━━━━━━━━━━━━━━━\n\n";
    message += "*📝 NOTES DU PLAN*\n";
    message += "<?php echo htmlspecialchars(str_replace(["\r\n", "\n", "\r"], '\n', $meal_plan->notes)); ?>\n";
    <?php } ?>

    message += "\n_Généré par DietSenegal - Programme Diététique_";

    // Encode for URL
    const encodedMessage = encodeURIComponent(message);

    // Get patient phone if available
    <?php if (!empty($patient->phonenumber)) { ?>
    const phoneNumber = "<?php echo preg_replace('/[^0-9+]/', '', $patient->phonenumber); ?>";
    const whatsappURL = `https://wa.me/${phoneNumber}?text=${encodedMessage}`;
    <?php } else { ?>
    const whatsappURL = `https://wa.me/?text=${encodedMessage}`;
    <?php } ?>

    // Open WhatsApp
    window.open(whatsappURL, '_blank');
}
</script>

<?php init_tail(); ?>
