<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h3><?php echo $meal_plan->plan_name; ?></h3>
                                <p><?php echo $program->program_name; ?> - <?php echo _l('dietetic_week'); ?> <?php echo $meal_plan->week_number; ?> | <?php echo $patient->client_name; ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if (dietetic_has_permission('create')) { ?>
                                    <a href="<?php echo admin_url('dietetic/programs/meal/create?meal_plan_id=' . $meal_plan->id); ?>" class="btn btn-success">
                                        <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_meal'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
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
                        foreach ($days as $day_num => $day_name) {
                            $day_meals = isset($meals_by_day[$day_num]) ? $meals_by_day[$day_num] : [];
                        ?>
                            <div class="panel panel-default mtop15">
                                <div class="panel-heading">
                                    <h4 class="panel-title" style="color: #3498db;">
                                        <i class="fa fa-calendar"></i> <?php echo $day_name; ?>
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <?php if (!empty($day_meals)) { ?>
                                        <?php foreach ($day_meals as $meal) { ?>
                                            <div style="background: #f9f9f9; padding: 15px; margin-bottom: 15px; border-left: 4px solid #3498db; border-radius: 3px;">
                                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                                    <div>
                                                        <strong style="font-size: 16px;"><?php echo ucfirst(str_replace('_', ' ', $meal->meal_type)); ?></strong>
                                                        <?php if ($meal->meal_time) { ?>
                                                            <span class="text-muted"> - <?php echo substr($meal->meal_time, 0, 5); ?></span>
                                                        <?php } ?>
                                                        <?php if ($meal->meal_name) { ?>
                                                            <br><em><?php echo $meal->meal_name; ?></em>
                                                        <?php } ?>
                                                    </div>
                                                    <div>
                                                        <?php if (dietetic_has_permission('edit')) { ?>
                                                            <a href="<?php echo admin_url('dietetic/programs/meal/edit/' . $meal->id); ?>" class="btn btn-default btn-sm">
                                                                <i class="fa fa-pencil"></i> <?php echo _l('dietetic_edit_meal'); ?>
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                </div>

                                                <?php if (!empty($meal->foods)) { ?>
                                                    <table class="table table-condensed" style="margin: 10px 0; background: white;">
                                                        <thead>
                                                            <tr>
                                                                <th><?php echo _l('dietetic_food'); ?></th>
                                                                <th><?php echo _l('dietetic_quantity'); ?></th>
                                                                <th><?php echo _l('dietetic_calories'); ?></th>
                                                                <th><?php echo _l('dietetic_protein'); ?></th>
                                                                <th><?php echo _l('dietetic_carbs'); ?></th>
                                                                <th><?php echo _l('dietetic_fats'); ?></th>
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
                                                                    <td><?php echo $food->food_name; ?></td>
                                                                    <td><?php echo $food->quantity . ' ' . $food->unit; ?></td>
                                                                    <td><?php echo round($calories); ?> kcal</td>
                                                                    <td><?php echo round($protein, 1); ?>g</td>
                                                                    <td><?php echo round($carbs, 1); ?>g</td>
                                                                    <td><?php echo round($fats, 1); ?>g</td>
                                                                </tr>
                                                            <?php } ?>
                                                            <tr style="font-weight: bold; background: #e8f5e9;">
                                                                <td><?php echo _l('dietetic_meal_total'); ?></td>
                                                                <td>-</td>
                                                                <td><?php echo round($meal_calories); ?> kcal</td>
                                                                <td><?php echo round($meal_protein, 1); ?>g</td>
                                                                <td><?php echo round($meal_carbs, 1); ?>g</td>
                                                                <td><?php echo round($meal_fats, 1); ?>g</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                <?php } else { ?>
                                                    <p class="text-muted"><em><?php echo _l('dietetic_no_foods_in_meal'); ?></em></p>
                                                <?php } ?>

                                                <?php if ($meal->instructions) { ?>
                                                    <div class="alert alert-warning" style="margin-top: 10px; margin-bottom: 0;">
                                                        <i class="fa fa-info-circle"></i> <strong><?php echo _l('dietetic_instructions'); ?>:</strong> <?php echo nl2br($meal->instructions); ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <p class="text-muted"><em><?php echo _l('dietetic_no_meals_planned'); ?></em></p>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($meal_plan->notes) { ?>
                            <div class="alert alert-warning mtop30">
                                <strong><?php echo _l('dietetic_plan_notes'); ?>:</strong><br />
                                <?php echo nl2br($meal_plan->notes); ?>
                            </div>
                        <?php } ?>

                        <div class="alert alert-success mtop30">
                            <strong><?php echo _l('dietetic_week_total_nutrition'); ?>:</strong>
                            <?php echo round($nutrition_totals->calories); ?> kcal |
                            P: <?php echo number_format($nutrition_totals->protein, 1); ?>g |
                            C: <?php echo number_format($nutrition_totals->carbs, 1); ?>g |
                            F: <?php echo number_format($nutrition_totals->fats, 1); ?>g
                        </div>

                        <hr />
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> <?php echo _l('dietetic_back_to_program'); ?>
                            </a>
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
        </div>
    </div>
</div>

<?php init_tail(); ?>
