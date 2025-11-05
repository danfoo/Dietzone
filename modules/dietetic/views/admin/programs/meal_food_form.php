<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>
                            <?php echo isset($meal_food) ? 'Edit Food' : 'Add Food to Meal'; ?>
                        </h4>
                        <p><?php echo $meal->meal_name ? $meal->meal_name : ucfirst($meal->meal_type); ?> - Day <?php echo $days[$meal->day_number]; ?></p>
                        <hr>

                        <?php echo form_open(admin_url('dietetic/programs/meal_food/' . (isset($meal_food) ? 'edit/' . $meal_food->id : 'create'))); ?>

                        <input type="hidden" name="meal_id" value="<?php echo $meal->id; ?>">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Food *</label>
                                    <select name="food_id" class="form-control selectpicker" data-live-search="true" required <?php echo isset($meal_food) ? 'disabled' : ''; ?>>
                                        <option value="">Choose a food...</option>
                                        <?php foreach ($foods as $food) { ?>
                                            <option value="<?php echo $food->id; ?>"
                                                    data-calories="<?php echo $food->calories; ?>"
                                                    data-protein="<?php echo $food->protein; ?>"
                                                    data-carbs="<?php echo $food->carbs; ?>"
                                                    data-fats="<?php echo $food->fats; ?>"
                                                    data-serving="<?php echo $food->serving_size . $food->serving_unit; ?>"
                                                    <?php echo (isset($meal_food) && $meal_food->food_id == $food->id) ? 'selected' : ''; ?>>
                                                <?php echo $food->food_name; ?>
                                                (<?php echo $food->calories; ?> kcal / <?php echo $food->serving_size . $food->serving_unit; ?>)
                                            </option>
                                        <?php } ?>
                                    </select>
                                    <?php if (isset($meal_food)) { ?>
                                        <input type="hidden" name="food_id" value="<?php echo $meal_food->food_id; ?>">
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <div id="foodInfo" style="display: none;" class="alert alert-info">
                            <strong>Nutritional Info (per serving):</strong><br>
                            <span id="foodDetails"></span>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Quantity *</label>
                                    <input type="number" name="quantity" class="form-control" step="0.1" min="0"
                                           value="<?php echo isset($meal_food) ? $meal_food->quantity : ''; ?>"
                                           id="quantityInput" required>
                                    <small class="text-muted">Amount to use in this meal</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Unit *</label>
                                    <select name="unit" class="form-control" required>
                                        <?php
                                        $units = ['g' => 'Grams (g)', 'ml' => 'Milliliters (ml)', 'unit' => 'Unit', 'cup' => 'Cup', 'tbsp' => 'Tablespoon', 'tsp' => 'Teaspoon'];
                                        foreach ($units as $value => $label) {
                                            $selected = (isset($meal_food) && $meal_food->unit == $value) ? 'selected' : '';
                                            echo "<option value='$value' $selected>$label</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div id="calculatedNutrition" style="display: none;" class="alert alert-success">
                            <strong>Calculated Nutrition for this meal:</strong><br>
                            <div id="calcDetails"></div>
                        </div>

                        <hr>
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/programs/meal/edit/' . $meal->id); ?>" class="btn btn-default">
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <?php echo isset($meal_food) ? 'Update' : 'Add to Meal'; ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.selectpicker').selectpicker();

    $('select[name="food_id"]').change(function() {
        var selected = $(this).find(':selected');
        if (selected.val()) {
            var calories = selected.data('calories');
            var protein = selected.data('protein');
            var carbs = selected.data('carbs');
            var fats = selected.data('fats');
            var serving = selected.data('serving');

            $('#foodDetails').html(
                'Serving: ' + serving + '<br>' +
                'Calories: ' + calories + ' kcal | ' +
                'Protein: ' + protein + 'g | ' +
                'Carbs: ' + carbs + 'g | ' +
                'Fats: ' + fats + 'g'
            );
            $('#foodInfo').show();

            calculateNutrition();
        } else {
            $('#foodInfo').hide();
            $('#calculatedNutrition').hide();
        }
    });

    $('#quantityInput, select[name="food_id"]').on('change keyup', function() {
        calculateNutrition();
    });

    function calculateNutrition() {
        var selected = $('select[name="food_id"]').find(':selected');
        var quantity = parseFloat($('#quantityInput').val());

        if (selected.val() && quantity > 0) {
            var calories = parseFloat(selected.data('calories'));
            var protein = parseFloat(selected.data('protein'));
            var carbs = parseFloat(selected.data('carbs'));
            var fats = parseFloat(selected.data('fats'));
            var servingSize = parseFloat(selected.data('serving'));

            var ratio = quantity / servingSize;
            var calcCal = (calories * ratio).toFixed(0);
            var calcProt = (protein * ratio).toFixed(1);
            var calcCarbs = (carbs * ratio).toFixed(1);
            var calcFats = (fats * ratio).toFixed(1);

            $('#calcDetails').html(
                quantity + ' ' + $('select[name="unit"]').val() + ' = ' +
                calcCal + ' kcal | ' +
                'P: ' + calcProt + 'g | ' +
                'C: ' + calcCarbs + 'g | ' +
                'F: ' + calcFats + 'g'
            );
            $('#calculatedNutrition').show();
        }
    }

    // Trigger on load if editing
    <?php if (isset($meal_food)) { ?>
        $('select[name="food_id"]').trigger('change');
    <?php } ?>
});
</script>

<?php init_tail(); ?>
