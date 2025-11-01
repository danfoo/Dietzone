<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3><?php echo $meal_plan->plan_name; ?></h3>
                        <p><?php echo $program->program_name; ?> - Week <?php echo $meal_plan->week_number; ?></p>
                        <hr />

                        <?php if ($program->daily_calories || $program->daily_protein) { ?>
                            <div class="alert alert-info">
                                <strong><?php echo _l('dietetic_daily_targets'); ?>:</strong>
                                <?php if ($program->daily_calories) echo $program->daily_calories . ' kcal | '; ?>
                                <?php if ($program->daily_protein) echo 'P: ' . $program->daily_protein . 'g | '; ?>
                                <?php if ($program->daily_carbs) echo 'C: ' . $program->daily_carbs . 'g | '; ?>
                                <?php if ($program->daily_fats) echo 'F: ' . $program->daily_fats . 'g'; ?>
                            </div>
                        <?php } ?>

                        <?php foreach ($meals_by_day as $day => $meals) { ?>
                            <?php if (!empty($meals)) { ?>
                                <div class="meal-plan-day">
                                    <div class="meal-plan-day-header">
                                        <?php echo dietetic_get_day_name($day); ?>
                                    </div>

                                    <?php foreach ($meals as $meal) { ?>
                                        <div class="meal-card">
                                            <div class="meal-card-header">
                                                <div class="meal-type">
                                                    <?php echo ucfirst(str_replace('_', ' ', $meal->meal_type)); ?>
                                                    <?php if ($meal->meal_time) { ?>
                                                        <span class="meal-time">(<?php echo substr($meal->meal_time, 0, 5); ?>)</span>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                            <?php if ($meal->meal_name) { ?>
                                                <h6><?php echo $meal->meal_name; ?></h6>
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
                                                            <span class="food-name"><?php echo $food->food_name; ?></span>
                                                            <span class="food-quantity"><?php echo $food->quantity . ' ' . $food->unit; ?></span>
                                                            <span class="food-calories"><?php echo round($calories); ?> kcal</span>
                                                        </li>
                                                    <?php } ?>
                                                </ul>

                                                <div class="meal-nutrition-summary">
                                                    <strong>Total:</strong>
                                                    <?php echo round($meal_calories); ?> kcal |
                                                    P: <?php echo number_format($meal_protein, 1); ?>g |
                                                    C: <?php echo number_format($meal_carbs, 1); ?>g |
                                                    F: <?php echo number_format($meal_fats, 1); ?>g
                                                </div>
                                            <?php } ?>

                                            <?php if ($meal->instructions) { ?>
                                                <div class="meal-instructions">
                                                    <i class="fa fa-info-circle"></i> <?php echo nl2br($meal->instructions); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($meal_plan->notes) { ?>
                            <div class="alert alert-warning mtop30">
                                <strong><?php echo _l('dietetic_notes'); ?>:</strong><br />
                                <?php echo nl2br($meal_plan->notes); ?>
                            </div>
                        <?php } ?>

                        <div class="mtop30">
                            <strong><?php echo _l('dietetic_total'); ?>:</strong>
                            <?php echo round($nutrition_totals->calories); ?> kcal |
                            P: <?php echo number_format($nutrition_totals->protein, 1); ?>g |
                            C: <?php echo number_format($nutrition_totals->carbs, 1); ?>g |
                            F: <?php echo number_format($nutrition_totals->fats, 1); ?>g
                        </div>

                        <hr />
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
                            <?php if (dietetic_has_permission('edit')) { ?>
                                <a href="<?php echo admin_url('dietetic/programs/edit_meal_plan/' . $meal_plan->id); ?>" class="btn btn-info">
                                    <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                </a>
                            <?php } ?>
                            <a href="<?php echo admin_url('dietetic/programs/generate_pdf/' . $meal_plan->id); ?>" class="btn btn-success">
                                <i class="fa fa-file-pdf-o"></i> <?php echo _l('dietetic_generate_pdf'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
