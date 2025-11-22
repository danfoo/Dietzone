<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo $title; ?></h4>
                        <hr />

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id"><?php echo _l('dietetic_patient'); ?> *</label>
                                    <select name="patient_id" id="patient_id" class="form-control selectpicker" data-live-search="true" required>
                                        <option value="">-- <?php echo _l('select'); ?> --</option>
                                        <?php foreach ($patients as $patient) { ?>
                                            <option value="<?php echo $patient->id; ?>" <?php echo set_select('patient_id', $patient->id, (isset($program) && $program->patient_id == $patient->id) || (isset($_GET['patient_id']) && $_GET['patient_id'] == $patient->id)); ?>>
                                                <?php echo $patient->client_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="dietitian_id"><?php echo _l('dietetic_dietitian'); ?> *</label>
                                    <select name="dietitian_id" id="dietitian_id" class="form-control selectpicker" required>
                                        <?php foreach ($staff as $member) { ?>
                                            <option value="<?php echo $member['staffid']; ?>" <?php echo set_select('dietitian_id', $member['staffid'], (isset($program) && $program->dietitian_id == $member['staffid']) || (!isset($program) && $member['staffid'] == get_staff_user_id())); ?>>
                                                <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="program_name"><?php echo _l('dietetic_program_name'); ?> *</label>
                                    <input type="text" class="form-control" name="program_name" value="<?php echo isset($program) ? $program->program_name : ''; ?>" required />
                                </div>

                                <div class="form-group">
                                    <label for="status"><?php echo _l('dietetic_status'); ?></label>
                                    <select name="status" class="form-control">
                                        <option value="active" <?php echo set_select('status', 'active', (isset($program) && $program->status == 'active') || !isset($program)); ?>>Active</option>
                                        <option value="completed" <?php echo set_select('status', 'completed', isset($program) && $program->status == 'completed'); ?>>Completed</option>
                                        <option value="cancelled" <?php echo set_select('status', 'cancelled', isset($program) && $program->status == 'cancelled'); ?>>Cancelled</option>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="start_date"><?php echo _l('dietetic_start_date'); ?> *</label>
                                            <input type="date" class="form-control" name="start_date" value="<?php echo isset($program) ? $program->start_date : ''; ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="end_date"><?php echo _l('dietetic_end_date'); ?></label>
                                            <input type="date" class="form-control" name="end_date" value="<?php echo isset($program) ? $program->end_date : ''; ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="meal_count"><?php echo _l('dietetic_meal_count'); ?></label>
                                    <input type="number" min="1" max="10" class="form-control" name="meal_count" value="<?php echo isset($program) ? $program->meal_count : '3'; ?>" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5><?php echo _l('dietetic_daily_targets'); ?></h5>

                                <div class="form-group">
                                    <label for="daily_calories"><?php echo _l('dietetic_daily_calories'); ?> (kcal)</label>
                                    <input type="number" class="form-control" id="daily_calories" name="daily_calories" value="<?php echo isset($program) ? $program->daily_calories : ''; ?>" />
                                    <small class="text-muted">Les macronutriments seront calculés automatiquement selon les ratios standards (modifiables)</small>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="daily_protein"><?php echo _l('dietetic_protein'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" id="daily_protein" name="daily_protein" value="<?php echo isset($program) ? $program->daily_protein : ''; ?>" />
                                            <small class="text-muted macro-percentage" id="protein_percentage"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="daily_carbs"><?php echo _l('dietetic_carbs'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" id="daily_carbs" name="daily_carbs" value="<?php echo isset($program) ? $program->daily_carbs : ''; ?>" />
                                            <small class="text-muted macro-percentage" id="carbs_percentage"></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="daily_fats"><?php echo _l('dietetic_fats'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" id="daily_fats" name="daily_fats" value="<?php echo isset($program) ? $program->daily_fats : ''; ?>" />
                                            <small class="text-muted macro-percentage" id="fats_percentage"></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="daily_fiber"><?php echo _l('dietetic_fiber'); ?> (g)</label>
                                    <input type="number" step="0.1" class="form-control" id="daily_fiber" name="daily_fiber" value="<?php echo isset($program) ? $program->daily_fiber : ''; ?>" />
                                    <small class="text-muted">Recommandé: 14g par 1000 kcal</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description"><?php echo _l('dietetic_description'); ?></label>
                                    <textarea class="form-control" name="description" rows="3"><?php echo isset($program) ? $program->description : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="objective"><?php echo _l('dietetic_objective'); ?></label>
                                    <textarea class="form-control" name="objective" rows="3"><?php echo isset($program) ? $program->objective : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="instructions"><?php echo _l('dietetic_instructions'); ?></label>
                                    <textarea class="form-control" name="instructions" rows="4"><?php echo isset($program) ? $program->instructions : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/programs'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
(function() {
    'use strict';

    // Nutritional constants
    const CALORIES_PER_GRAM = {
        protein: 4,
        carbs: 4,
        fats: 9
    };

    // Standard macro ratios (modifiable by user)
    const DEFAULT_RATIOS = {
        carbs: 0.50,    // 50% of calories from carbs
        protein: 0.25,  // 25% of calories from protein
        fats: 0.25      // 25% of calories from fats
    };

    const FIBER_PER_1000_KCAL = 14; // Standard recommendation

    /**
     * Calculate macronutrients from calories
     */
    function calculateMacrosFromCalories(calories) {
        if (!calories || calories <= 0) {
            return null;
        }

        return {
            protein: Math.round((calories * DEFAULT_RATIOS.protein / CALORIES_PER_GRAM.protein) * 10) / 10,
            carbs: Math.round((calories * DEFAULT_RATIOS.carbs / CALORIES_PER_GRAM.carbs) * 10) / 10,
            fats: Math.round((calories * DEFAULT_RATIOS.fats / CALORIES_PER_GRAM.fats) * 10) / 10,
            fiber: Math.round((calories / 1000 * FIBER_PER_1000_KCAL) * 10) / 10
        };
    }

    /**
     * Calculate percentage of calories from macros
     */
    function calculateMacroPercentages(calories, protein, carbs, fats) {
        if (!calories || calories <= 0) {
            return { protein: 0, carbs: 0, fats: 0 };
        }

        const proteinCals = protein * CALORIES_PER_GRAM.protein;
        const carbsCals = carbs * CALORIES_PER_GRAM.carbs;
        const fatsCals = fats * CALORIES_PER_GRAM.fats;

        return {
            protein: Math.round((proteinCals / calories * 100) * 10) / 10,
            carbs: Math.round((carbsCals / calories * 100) * 10) / 10,
            fats: Math.round((fatsCals / calories * 100) * 10) / 10
        };
    }

    /**
     * Update percentage displays
     */
    function updatePercentageDisplays() {
        const calories = parseFloat($('#daily_calories').val()) || 0;
        const protein = parseFloat($('#daily_protein').val()) || 0;
        const carbs = parseFloat($('#daily_carbs').val()) || 0;
        const fats = parseFloat($('#daily_fats').val()) || 0;

        if (calories > 0 && (protein > 0 || carbs > 0 || fats > 0)) {
            const percentages = calculateMacroPercentages(calories, protein, carbs, fats);

            $('#protein_percentage').text(percentages.protein + '% des calories');
            $('#carbs_percentage').text(percentages.carbs + '% des calories');
            $('#fats_percentage').text(percentages.fats + '% des calories');
        } else {
            $('#protein_percentage').text('');
            $('#carbs_percentage').text('');
            $('#fats_percentage').text('');
        }
    }

    /**
     * Auto-fill macros when calories are entered
     */
    function autoFillMacros() {
        const calories = parseFloat($('#daily_calories').val()) || 0;

        if (calories > 0) {
            // Only auto-fill if fields are empty
            const protein = parseFloat($('#daily_protein').val()) || 0;
            const carbs = parseFloat($('#daily_carbs').val()) || 0;
            const fats = parseFloat($('#daily_fats').val()) || 0;
            const fiber = parseFloat($('#daily_fiber').val()) || 0;

            // Calculate suggested values
            const suggested = calculateMacrosFromCalories(calories);

            // Auto-fill empty fields
            if (protein === 0) {
                $('#daily_protein').val(suggested.protein);
            }
            if (carbs === 0) {
                $('#daily_carbs').val(suggested.carbs);
            }
            if (fats === 0) {
                $('#daily_fats').val(suggested.fats);
            }
            if (fiber === 0) {
                $('#daily_fiber').val(suggested.fiber);
            }

            // Update percentages
            updatePercentageDisplays();
        }
    }

    // Initialize on document ready
    $(document).ready(function() {
        // Auto-calculate when calories change
        $('#daily_calories').on('input change', function() {
            autoFillMacros();
        });

        // Update percentages when macros change manually
        $('#daily_protein, #daily_carbs, #daily_fats').on('input change', function() {
            updatePercentageDisplays();
        });

        // Initialize percentages if editing existing program
        <?php if (isset($program) && $program->daily_calories): ?>
        updatePercentageDisplays();
        <?php endif; ?>
    });
})();
</script>
