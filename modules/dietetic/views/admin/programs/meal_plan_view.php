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
                                <p><?php echo $program->program_name; ?> - Week <?php echo $meal_plan->week_number; ?> | <?php echo $patient->client_name; ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php if (dietetic_has_permission('create')) { ?>
                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addMealModal">
                                        <i class="fa fa-plus"></i> Add Meal
                                    </button>
                                <?php } ?>
                            </div>
                        </div>
                        <hr />

                        <?php if ($program->daily_calories || $program->daily_protein) { ?>
                            <div class="alert alert-info">
                                <strong>Daily Targets:</strong>
                                <?php if ($program->daily_calories) echo $program->daily_calories . ' kcal | '; ?>
                                <?php if ($program->daily_protein) echo 'P: ' . $program->daily_protein . 'g | '; ?>
                                <?php if ($program->daily_carbs) echo 'C: ' . $program->daily_carbs . 'g | '; ?>
                                <?php if ($program->daily_fats) echo 'F: ' . $program->daily_fats . 'g'; ?>
                            </div>
                        <?php } ?>

                        <?php
                        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
                        foreach ($days as $day_num => $day_name) {
                            $day_meals = isset($meals_by_day[$day_num]) ? $meals_by_day[$day_num] : [];
                        ?>
                            <div class="meal-plan-day" style="margin-bottom: 30px; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                                <div class="meal-plan-day-header" style="font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #3498db;">
                                    <?php echo $day_name; ?>
                                </div>

                                <?php if (!empty($day_meals)) { ?>
                                    <?php foreach ($day_meals as $meal) { ?>
                                        <div class="meal-card" style="background: white; padding: 15px; margin-bottom: 15px; border-left: 4px solid #3498db; border-radius: 3px;">
                                            <div class="meal-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                                <div>
                                                    <strong style="font-size: 16px;"><?php echo ucfirst(str_replace('_', ' ', $meal->meal_type)); ?></strong>
                                                    <?php if ($meal->meal_time) { ?>
                                                        <span class="text-muted"> - <?php echo substr($meal->meal_time, 0, 5); ?></span>
                                                    <?php } ?>
                                                    <?php if ($meal->meal_name) { ?>
                                                        <br><em><?php echo $meal->meal_name; ?></em>
                                                    <?php } ?>
                                                </div>
                                                <?php if (dietetic_has_permission('create')) { ?>
                                                    <button type="button" class="btn btn-sm btn-info add-food-btn" data-meal-id="<?php echo $meal->id; ?>">
                                                        <i class="fa fa-plus"></i> Add Food
                                                    </button>
                                                <?php } ?>
                                            </div>

                                            <?php if (!empty($meal->foods)) { ?>
                                                <table class="table table-condensed" style="margin: 10px 0;">
                                                    <thead>
                                                        <tr>
                                                            <th>Food</th>
                                                            <th>Quantity</th>
                                                            <th>Calories</th>
                                                            <th>Protein</th>
                                                            <th>Carbs</th>
                                                            <th>Fats</th>
                                                            <?php if (dietetic_has_permission('delete')) { ?><th></th><?php } ?>
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
                                                                <td><?php echo round($protein); ?>g</td>
                                                                <td><?php echo round($carbs); ?>g</td>
                                                                <td><?php echo round($fats); ?>g</td>
                                                                <?php if (dietetic_has_permission('delete')) { ?>
                                                                    <td>
                                                                        <a href="#" class="text-danger remove-food-btn" data-id="<?php echo $food->meal_food_id; ?>">
                                                                            <i class="fa fa-trash"></i>
                                                                        </a>
                                                                    </td>
                                                                <?php } ?>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr style="font-weight: bold; background: #f5f5f5;">
                                                            <td colspan="2">TOTAL</td>
                                                            <td><?php echo round($meal_calories); ?> kcal</td>
                                                            <td><?php echo round($meal_protein); ?>g</td>
                                                            <td><?php echo round($meal_carbs); ?>g</td>
                                                            <td><?php echo round($meal_fats); ?>g</td>
                                                            <?php if (dietetic_has_permission('delete')) { ?><td></td><?php } ?>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            <?php } else { ?>
                                                <p class="text-muted"><em>No foods added yet</em></p>
                                            <?php } ?>

                                            <?php if ($meal->instructions) { ?>
                                                <div class="alert alert-warning" style="margin-top: 10px; margin-bottom: 0;">
                                                    <i class="fa fa-info-circle"></i> <?php echo nl2br($meal->instructions); ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <p class="text-muted"><em>No meals planned for this day</em></p>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <?php if ($meal_plan->notes) { ?>
                            <div class="alert alert-warning mtop30">
                                <strong>Notes:</strong><br />
                                <?php echo nl2br($meal_plan->notes); ?>
                            </div>
                        <?php } ?>

                        <div class="alert alert-success mtop30">
                            <strong>Week Total:</strong>
                            <?php echo round($nutrition_totals->calories); ?> kcal |
                            P: <?php echo number_format($nutrition_totals->protein, 1); ?>g |
                            C: <?php echo number_format($nutrition_totals->carbs, 1); ?>g |
                            F: <?php echo number_format($nutrition_totals->fats, 1); ?>g
                        </div>

                        <hr />
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-default">Back</a>
                            <?php if (dietetic_has_permission('edit')) { ?>
                                <a href="<?php echo admin_url('dietetic/programs/edit_meal_plan/' . $meal_plan->id); ?>" class="btn btn-info">
                                    <i class="fa fa-pencil"></i> Edit Plan Details
                                </a>
                            <?php } ?>
                            <a href="<?php echo admin_url('dietetic/programs/generate_pdf/' . $meal_plan->id); ?>" class="btn btn-success">
                                <i class="fa fa-file-pdf-o"></i> Generate PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Meal Modal -->
<div class="modal fade" id="addMealModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Meal</h4>
            </div>
            <div class="modal-body">
                <form id="addMealForm">
                    <input type="hidden" name="meal_plan_id" value="<?php echo $meal_plan->id; ?>">

                    <div class="form-group">
                        <label>Day *</label>
                        <select name="day_number" class="form-control" required>
                            <option value="">Select day...</option>
                            <?php for ($i = 1; $i <= 7; $i++) { ?>
                                <option value="<?php echo $i; ?>"><?php echo $days[$i]; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Meal Type *</label>
                        <select name="meal_type" class="form-control" required>
                            <option value="">Select type...</option>
                            <option value="breakfast">Breakfast</option>
                            <option value="snack_am">Morning Snack</option>
                            <option value="lunch">Lunch</option>
                            <option value="snack_pm">Afternoon Snack</option>
                            <option value="dinner">Dinner</option>
                            <option value="snack_evening">Evening Snack</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Meal Name</label>
                        <input type="text" name="meal_name" class="form-control" placeholder="e.g., Chicken Salad">
                    </div>

                    <div class="form-group">
                        <label>Time</label>
                        <input type="time" name="meal_time" class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Instructions</label>
                        <textarea name="instructions" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveMealBtn">Add Meal</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Food Modal -->
<div class="modal fade" id="addFoodModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Food to Meal</h4>
            </div>
            <div class="modal-body">
                <form id="addFoodForm">
                    <input type="hidden" name="meal_id" id="foodMealId">

                    <div class="form-group">
                        <label>Food *</label>
                        <select name="food_id" class="form-control select2" required style="width: 100%">
                            <option value="">Select food...</option>
                            <?php
                            $this->load->model('dietetic/dietetic_foods_model');
                            $foods = $this->dietetic_foods_model->get_all();
                            foreach ($foods as $food) {
                            ?>
                                <option value="<?php echo $food->id; ?>">
                                    <?php echo $food->food_name; ?> (<?php echo $food->calories; ?> kcal/<?php echo $food->serving_size . $food->serving_unit; ?>)
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Quantity *</label>
                        <input type="number" name="quantity" class="form-control" step="0.1" min="0" required>
                    </div>

                    <div class="form-group">
                        <label>Unit *</label>
                        <select name="unit" class="form-control" required>
                            <option value="g">Grams (g)</option>
                            <option value="ml">Milliliters (ml)</option>
                            <option value="unit">Unit</option>
                            <option value="cup">Cup</option>
                            <option value="tbsp">Tablespoon</option>
                            <option value="tsp">Teaspoon</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="saveFoodBtn">Add Food</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize select2
    $('.select2').select2();

    // Add Meal
    $('#saveMealBtn').click(function() {
        var formData = $('#addMealForm').serialize();

        $.post(admin_url + 'dietetic/programs/add_meal', formData, function(response) {
            if (response.success) {
                alert_float('success', 'Meal added successfully');
                $('#addMealModal').modal('hide');
                location.reload();
            } else {
                alert_float('danger', response.message || 'Failed to add meal');
            }
        }, 'json');
    });

    // Open Add Food Modal
    $('.add-food-btn').click(function() {
        var mealId = $(this).data('meal-id');
        $('#foodMealId').val(mealId);
        $('#addFoodModal').modal('show');
    });

    // Add Food
    $('#saveFoodBtn').click(function() {
        var formData = $('#addFoodForm').serialize();

        $.post(admin_url + 'dietetic/programs/add_food_to_meal', formData, function(response) {
            if (response.success) {
                alert_float('success', 'Food added successfully');
                $('#addFoodModal').modal('hide');
                location.reload();
            } else {
                alert_float('danger', response.message || 'Failed to add food');
            }
        }, 'json');
    });

    // Remove Food
    $('.remove-food-btn').click(function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        if (confirm('Remove this food from the meal?')) {
            $.post(admin_url + 'dietetic/programs/remove_food_from_meal/' + id, function(response) {
                if (response.success) {
                    alert_float('success', 'Food removed');
                    location.reload();
                } else {
                    alert_float('danger', response.message || 'Failed to remove food');
                }
            }, 'json');
        }
    });
});
</script>

<?php init_tail(); ?>
