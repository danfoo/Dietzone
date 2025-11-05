<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>
                            <?php echo isset($meal) ? _l('dietetic_edit_meal') : _l('dietetic_add_meal_to') . ' ' . $meal_plan->plan_name; ?>
                        </h4>
                        <p><?php echo _l('dietetic_week'); ?> <?php echo $meal_plan->week_number; ?> - <?php echo $program->program_name; ?></p>
                        <hr>

                        <?php echo form_open(admin_url('dietetic/programs/meal/' . (isset($meal) ? 'edit/' . $meal->id : 'create'))); ?>

                        <input type="hidden" name="meal_plan_id" value="<?php echo $meal_plan->id; ?>">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('dietetic_day'); ?> *</label>
                                    <select name="day_number" class="form-control" required>
                                        <option value=""><?php echo _l('dietetic_select_day'); ?></option>
                                        <?php
                                        $days = [
                                            '1' => _l('dietetic_monday'),
                                            '2' => _l('dietetic_tuesday'),
                                            '3' => _l('dietetic_wednesday'),
                                            '4' => _l('dietetic_thursday'),
                                            '5' => _l('dietetic_friday'),
                                            '6' => _l('dietetic_saturday'),
                                            '7' => _l('dietetic_sunday')
                                        ];
                                        foreach ($days as $num => $day) {
                                            $selected = (isset($meal) && $meal->day_of_week == $num) ? 'selected' : '';
                                            echo "<option value='$num' $selected>$day</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('dietetic_meal_type'); ?> *</label>
                                    <select name="meal_type" class="form-control" required>
                                        <option value=""><?php echo _l('dietetic_select_type'); ?></option>
                                        <?php
                                        $types = [
                                            'breakfast' => _l('dietetic_meal_breakfast'),
                                            'snack_am' => _l('dietetic_meal_snack_am'),
                                            'lunch' => _l('dietetic_meal_lunch'),
                                            'snack_pm' => _l('dietetic_meal_snack_pm'),
                                            'dinner' => _l('dietetic_meal_dinner'),
                                            'snack_evening' => _l('dietetic_meal_snack_evening')
                                        ];
                                        foreach ($types as $value => $label) {
                                            $selected = (isset($meal) && $meal->meal_type == $value) ? 'selected' : '';
                                            echo "<option value='$value' $selected>$label</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label><?php echo _l('dietetic_time'); ?></label>
                                    <input type="time" name="meal_time" class="form-control" value="<?php echo isset($meal) ? $meal->meal_time : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('dietetic_meal_name'); ?></label>
                            <input type="text" name="meal_name" class="form-control" value="<?php echo isset($meal) ? $meal->meal_name : ''; ?>" placeholder="<?php echo _l('dietetic_meal_placeholder'); ?>">
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('dietetic_instructions_preparation'); ?></label>
                            <textarea name="instructions" class="form-control" rows="4"><?php echo isset($meal) ? $meal->instructions : ''; ?></textarea>
                        </div>

                        <?php if (isset($meal)) { ?>
                            <hr>
                            <h4><?php echo _l('dietetic_foods_in_meal'); ?></h4>

                            <a href="<?php echo admin_url('dietetic/programs/meal_food/create?meal_id=' . $meal->id); ?>" class="btn btn-success btn-sm mbottom15">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_food_to_meal'); ?>
                            </a>

                            <?php if (!empty($meal_foods)) { ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('dietetic_food'); ?></th>
                                            <th><?php echo _l('dietetic_quantity'); ?></th>
                                            <th><?php echo _l('dietetic_calories'); ?></th>
                                            <th><?php echo _l('dietetic_protein'); ?></th>
                                            <th><?php echo _l('dietetic_carbs'); ?></th>
                                            <th><?php echo _l('dietetic_fats'); ?></th>
                                            <th><?php echo _l('dietetic_actions'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $total_cal = 0;
                                        $total_prot = 0;
                                        $total_carbs = 0;
                                        $total_fats = 0;

                                        foreach ($meal_foods as $mf) {
                                            $ratio = $mf->quantity / $mf->serving_size;
                                            $cal = $mf->calories * $ratio;
                                            $prot = $mf->protein * $ratio;
                                            $carbs = $mf->carbs * $ratio;
                                            $fats = $mf->fats * $ratio;

                                            $total_cal += $cal;
                                            $total_prot += $prot;
                                            $total_carbs += $carbs;
                                            $total_fats += $fats;
                                        ?>
                                            <tr>
                                                <td><?php echo $mf->food_name; ?></td>
                                                <td><?php echo $mf->quantity . ' ' . $mf->unit; ?></td>
                                                <td><?php echo round($cal); ?> kcal</td>
                                                <td><?php echo round($prot, 1); ?>g</td>
                                                <td><?php echo round($carbs, 1); ?>g</td>
                                                <td><?php echo round($fats, 1); ?>g</td>
                                                <td>
                                                    <a href="<?php echo admin_url('dietetic/programs/meal_food/edit/' . $mf->id . '?meal_id=' . $meal->id); ?>" class="btn btn-default btn-xs">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('dietetic/programs/meal_food/delete/' . $mf->id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('<?php echo _l('dietetic_remove_food_confirm'); ?>');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr style="font-weight: bold; background: #f5f5f5;">
                                            <td><?php echo strtoupper(_l('dietetic_total')); ?></td>
                                            <td>-</td>
                                            <td><?php echo round($total_cal); ?> kcal</td>
                                            <td><?php echo round($total_prot, 1); ?>g</td>
                                            <td><?php echo round($total_carbs, 1); ?>g</td>
                                            <td><?php echo round($total_fats, 1); ?>g</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <p class="text-muted"><?php echo _l('dietetic_no_foods_yet'); ?></p>
                            <?php } ?>
                        <?php } ?>

                        <hr>
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/programs/meal_plan/' . $meal_plan->id); ?>" class="btn btn-default">
                                <?php echo _l('dietetic_back_to_meal_plan'); ?>
                            </a>
                            <button type="submit" class="btn btn-info">
                                <?php echo isset($meal) ? _l('dietetic_update_meal') : _l('dietetic_save_meal'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>

                        <?php if (!isset($meal)) { ?>
                            <div class="alert alert-info mtop15">
                                <i class="fa fa-info-circle"></i> <?php echo _l('dietetic_after_creating_meal'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
