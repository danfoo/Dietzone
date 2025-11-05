<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>
                            <?php echo isset($meal) ? 'Edit Meal' : 'Add Meal to ' . $meal_plan->plan_name; ?>
                        </h4>
                        <p>Week <?php echo $meal_plan->week_number; ?> - <?php echo $program->program_name; ?></p>
                        <hr>

                        <?php echo form_open(admin_url('dietetic/programs/meal/' . (isset($meal) ? 'edit/' . $meal->id : 'create'))); ?>

                        <input type="hidden" name="meal_plan_id" value="<?php echo $meal_plan->id; ?>">

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Day *</label>
                                    <select name="day_number" class="form-control" required>
                                        <option value="">Select day...</option>
                                        <?php
                                        $days = ['1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday'];
                                        foreach ($days as $num => $day) {
                                            $selected = (isset($meal) && $meal->day_number == $num) ? 'selected' : '';
                                            echo "<option value='$num' $selected>$day</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Meal Type *</label>
                                    <select name="meal_type" class="form-control" required>
                                        <option value="">Select type...</option>
                                        <?php
                                        $types = ['breakfast' => 'Breakfast', 'snack_am' => 'Morning Snack', 'lunch' => 'Lunch', 'snack_pm' => 'Afternoon Snack', 'dinner' => 'Dinner', 'snack_evening' => 'Evening Snack'];
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
                                    <label>Time</label>
                                    <input type="time" name="meal_time" class="form-control" value="<?php echo isset($meal) ? $meal->meal_time : ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Meal Name</label>
                            <input type="text" name="meal_name" class="form-control" value="<?php echo isset($meal) ? $meal->meal_name : ''; ?>" placeholder="e.g., Grilled Chicken with Rice">
                        </div>

                        <div class="form-group">
                            <label>Instructions / Preparation</label>
                            <textarea name="instructions" class="form-control" rows="4"><?php echo isset($meal) ? $meal->instructions : ''; ?></textarea>
                        </div>

                        <?php if (isset($meal)) { ?>
                            <hr>
                            <h4>Foods in this Meal</h4>

                            <a href="<?php echo admin_url('dietetic/programs/meal_food/create?meal_id=' . $meal->id); ?>" class="btn btn-success btn-sm mbottom15">
                                <i class="fa fa-plus"></i> Add Food to Meal
                            </a>

                            <?php if (!empty($meal_foods)) { ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Food</th>
                                            <th>Quantity</th>
                                            <th>Calories</th>
                                            <th>Protein</th>
                                            <th>Carbs</th>
                                            <th>Fats</th>
                                            <th>Actions</th>
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
                                                    <a href="<?php echo admin_url('dietetic/programs/meal_food/delete/' . $mf->id); ?>" class="btn btn-danger btn-xs" onclick="return confirm('Remove this food?');">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        <tr style="font-weight: bold; background: #f5f5f5;">
                                            <td>TOTAL</td>
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
                                <p class="text-muted">No foods added to this meal yet.</p>
                            <?php } ?>
                        <?php } ?>

                        <hr>
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/programs/meal_plan/' . $meal_plan->id); ?>" class="btn btn-default">
                                Back to Meal Plan
                            </a>
                            <button type="submit" class="btn btn-info">
                                <?php echo isset($meal) ? 'Update Meal' : 'Save Meal'; ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>

                        <?php if (!isset($meal)) { ?>
                            <div class="alert alert-info mtop15">
                                <i class="fa fa-info-circle"></i> After creating the meal, you'll be able to add foods to it.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
