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

                        <?php if (!isset($meal)) { ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-book"></i> <?php echo _l('dietetic_select_from_recipe_library'); ?>
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label><?php echo _l('dietetic_recipe'); ?></label>
                                        <select name="recipe_id" id="recipe_select" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('dietetic_no_recipe_selected'); ?>">
                                            <option value=""><?php echo _l('dietetic_create_meal_manually'); ?></option>
                                            <?php if (!empty($recipes)) {
                                                foreach ($recipes as $recipe) { ?>
                                                    <option value="<?php echo $recipe->id; ?>"
                                                        data-name="<?php echo htmlspecialchars($recipe->recipe_name); ?>"
                                                        data-description="<?php echo htmlspecialchars($recipe->description); ?>"
                                                        data-prep-time="<?php echo $recipe->prep_time; ?>"
                                                        data-cook-time="<?php echo $recipe->cook_time; ?>"
                                                        data-servings="<?php echo $recipe->servings; ?>">
                                                        <?php echo $recipe->recipe_name; ?>
                                                        <?php if ($recipe->prep_time || $recipe->cook_time) { ?>
                                                            (<?php if ($recipe->prep_time) echo $recipe->prep_time . 'min prep'; ?>
                                                            <?php if ($recipe->prep_time && $recipe->cook_time) echo ' + '; ?>
                                                            <?php if ($recipe->cook_time) echo $recipe->cook_time . 'min cuisson'; ?>)
                                                        <?php } ?>
                                                    </option>
                                                <?php }
                                            } ?>
                                        </select>
                                        <?php if (!empty($recipes)) { ?>
                                            <p class="help-block">
                                                <i class="fa fa-info-circle"></i> <?php echo _l('dietetic_recipe_select_help'); ?>
                                                <small class="text-muted">(<?php echo count($recipes); ?> recette<?php echo count($recipes) > 1 ? 's' : ''; ?> disponible<?php echo count($recipes) > 1 ? 's' : ''; ?>)</small>
                                            </p>
                                        <?php } else { ?>
                                            <p class="help-block text-warning">
                                                <i class="fa fa-exclamation-triangle"></i> Aucune recette disponible dans la bibliothèque. <a href="<?php echo admin_url('dietetic/recipes/create'); ?>">Créer une recette</a>
                                            </p>
                                        <?php } ?>
                                    </div>

                                    <div id="recipe-preview" class="alert alert-info" style="display: none;">
                                        <h5><strong id="preview-name"></strong></h5>
                                        <p id="preview-description"></p>
                                        <div id="preview-details"></div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

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
                            <input type="text" name="meal_name" id="meal_name" class="form-control" value="<?php echo isset($meal) ? $meal->meal_name : ''; ?>" placeholder="<?php echo _l('dietetic_meal_placeholder'); ?>">
                            <p class="help-block" id="meal-name-help" style="display: none;">
                                <i class="fa fa-magic"></i> <?php echo _l('dietetic_auto_filled_from_recipe'); ?>
                            </p>
                        </div>

                        <div class="form-group">
                            <label><?php echo _l('dietetic_instructions_preparation'); ?></label>
                            <textarea name="instructions" id="instructions" class="form-control" rows="4"><?php echo isset($meal) ? $meal->instructions : ''; ?></textarea>
                            <p class="help-block" id="instructions-help" style="display: none;">
                                <i class="fa fa-magic"></i> <?php echo _l('dietetic_auto_filled_from_recipe'); ?>
                            </p>
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

<?php if (!isset($meal)) { ?>
<script>
$(document).ready(function() {
    // Initialize selectpicker
    $('#recipe_select').selectpicker();

    // Handle recipe selection
    $('#recipe_select').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var recipeId = $(this).val();

        if (recipeId) {
            // Get recipe data from option attributes
            var recipeName = selectedOption.data('name');
            var recipeDescription = selectedOption.data('description');
            var prepTime = selectedOption.data('prep-time');
            var cookTime = selectedOption.data('cook-time');
            var servings = selectedOption.data('servings');

            // Fill form fields
            $('#meal_name').val(recipeName);
            $('#instructions').val(recipeDescription);

            // Show help texts
            $('#meal-name-help').show();
            $('#instructions-help').show();

            // Show preview
            $('#preview-name').text(recipeName);
            $('#preview-description').text(recipeDescription);

            var detailsHtml = '<small>';
            if (prepTime) {
                detailsHtml += '<i class="fa fa-clock-o"></i> <?php echo _l("dietetic_prep_time"); ?>: ' + prepTime + ' min &nbsp;&nbsp;';
            }
            if (cookTime) {
                detailsHtml += '<i class="fa fa-fire"></i> <?php echo _l("dietetic_cook_time"); ?>: ' + cookTime + ' min &nbsp;&nbsp;';
            }
            if (servings) {
                detailsHtml += '<i class="fa fa-users"></i> <?php echo _l("dietetic_servings"); ?>: ' + servings;
            }
            detailsHtml += '</small>';

            $('#preview-details').html(detailsHtml);
            $('#recipe-preview').show();

            // Disable manual editing of name and instructions
            $('#meal_name').prop('readonly', true).css('background-color', '#f9f9f9');
            $('#instructions').prop('readonly', true).css('background-color', '#f9f9f9');

        } else {
            // Clear form fields
            $('#meal_name').val('').prop('readonly', false).css('background-color', '');
            $('#instructions').val('').prop('readonly', false).css('background-color', '');

            // Hide help texts and preview
            $('#meal-name-help').hide();
            $('#instructions-help').hide();
            $('#recipe-preview').hide();
        }
    });
});
</script>
<?php } ?>
