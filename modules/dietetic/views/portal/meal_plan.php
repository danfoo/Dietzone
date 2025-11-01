<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head'); ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="text-right no-print mtop20 mbottom20">
                <a href="<?php echo site_url('dietetic/portal'); ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> <?php echo _l('back'); ?>
                </a>
                <button onclick="dietetic_portal.printMealPlan()" class="btn btn-info">
                    <i class="fa fa-print"></i> <?php echo _l('print'); ?>
                </button>
                <a href="<?php echo site_url('dietetic/portal/download_meal_plan/' . $meal_plan->id); ?>" class="btn btn-success">
                    <i class="fa fa-download"></i> <?php echo _l('dietetic_download_pdf'); ?>
                </a>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading text-center">
                    <h3><?php echo $meal_plan->plan_name; ?></h3>
                    <p><?php echo $program->program_name; ?> - <?php echo _l('dietetic_week'); ?> <?php echo $meal_plan->week_number; ?></p>
                </div>

                <div class="panel-body">
                    <?php if ($program->daily_calories || $program->daily_protein) { ?>
                        <div class="alert alert-info">
                            <h5><?php echo _l('dietetic_daily_targets'); ?></h5>
                            <?php if ($program->daily_calories) { ?>
                                <strong><?php echo _l('dietetic_calories'); ?>:</strong> <?php echo $program->daily_calories; ?> kcal |
                            <?php } ?>
                            <?php if ($program->daily_protein) { ?>
                                <strong><?php echo _l('dietetic_protein'); ?>:</strong> <?php echo $program->daily_protein; ?>g |
                            <?php } ?>
                            <?php if ($program->daily_carbs) { ?>
                                <strong><?php echo _l('dietetic_carbs'); ?>:</strong> <?php echo $program->daily_carbs; ?>g |
                            <?php } ?>
                            <?php if ($program->daily_fats) { ?>
                                <strong><?php echo _l('dietetic_fats'); ?>:</strong> <?php echo $program->daily_fats; ?>g
                            <?php } ?>
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
                                                <strong><?php echo _l('dietetic_total'); ?>:</strong>
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
                            <h5><?php echo _l('dietetic_notes'); ?></h5>
                            <p><?php echo nl2br($meal_plan->notes); ?></p>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('authentication/includes/footer'); ?>
