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

                                <!-- ===== SECTION FACTURATION ===== -->
                                <div class="panel panel-info" style="border-left: 4px solid #01807B; margin-top: 20px;">
                                    <div class="panel-heading" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                        <h5 style="margin: 0; color: #01807B;">
                                            <i class="fa fa-credit-card"></i> Configuration de Facturation
                                        </h5>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="service_id">Service * <i class="fa fa-info-circle" data-toggle="tooltip" title="Sélectionnez le service/article Perfex à facturer"></i></label>
                                            <select name="service_id" id="service_id" class="form-control selectpicker" data-live-search="true" required>
                                                <option value="">-- Sélectionner un service --</option>
                                                <?php if (isset($services) && !empty($services)) { ?>
                                                    <?php foreach ($services as $service) { ?>
                                                        <option value="<?php echo $service->id; ?>"
                                                                data-rate="<?php echo $service->rate; ?>"
                                                                data-discount-6="<?php echo $service->service_discount_6_months ?? 0; ?>"
                                                                data-discount-12="<?php echo $service->service_discount_12_months ?? 0; ?>"
                                                                <?php echo set_select('service_id', $service->id, isset($program) && $program->service_id == $service->id); ?>>
                                                            <?php echo $service->description; ?> - <?php echo number_format($service->rate, 0, ',', ' '); ?> FCFA/mois
                                                        </option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="duration_months">Durée du programme * <i class="fa fa-info-circle" data-toggle="tooltip" title="Durée en mois"></i></label>
                                                    <select name="duration_months" id="duration_months" class="form-control" required>
                                                        <option value="">-- Sélectionner --</option>
                                                        <option value="1" <?php echo set_select('duration_months', '1', isset($program) && $program->duration_months == 1); ?>>1 mois</option>
                                                        <option value="2" <?php echo set_select('duration_months', '2', isset($program) && $program->duration_months == 2); ?>>2 mois</option>
                                                        <option value="3" <?php echo set_select('duration_months', '3', isset($program) && $program->duration_months == 3); ?>>3 mois</option>
                                                        <option value="6" <?php echo set_select('duration_months', '6', isset($program) && $program->duration_months == 6); ?>>6 mois</option>
                                                        <option value="12" <?php echo set_select('duration_months', '12', isset($program) && $program->duration_months == 12); ?>>1 an (12 mois)</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="payment_mode">Mode de paiement * <i class="fa fa-info-circle" data-toggle="tooltip" title="Choisir entre paiement unique ou mensuel"></i></label>
                                                    <select name="payment_mode" id="payment_mode" class="form-control" required>
                                                        <option value="">-- Sélectionner --</option>
                                                        <option value="one_time" <?php echo set_select('payment_mode', 'one_time', isset($program) && $program->payment_mode == 'one_time'); ?>>
                                                            <i class="fa fa-check-circle"></i> Paiement unique (tout d'un coup)
                                                        </option>
                                                        <option value="recurring" <?php echo set_select('payment_mode', 'recurring', isset($program) && $program->payment_mode == 'recurring'); ?>>
                                                            <i class="fa fa-calendar"></i> Paiement mensuel (facturation récurrente)
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Calculateur de prix dynamique -->
                                        <div id="price-calculator" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 15px;">
                                            <h6 style="color: #01807B; font-weight: 700; margin-bottom: 15px;">
                                                <i class="fa fa-calculator"></i> Calcul du Prix
                                            </h6>

                                            <div style="font-size: 14px; line-height: 1.8;">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Prix mensuel:</strong> <span id="monthly-price">0</span> FCFA
                                                    </div>
                                                    <div class="col-md-6">
                                                        <strong>Durée:</strong> <span id="duration-display">0</span> mois
                                                    </div>
                                                </div>

                                                <div class="row" style="margin-top: 10px;">
                                                    <div class="col-md-6">
                                                        <strong>Sous-total:</strong> <span id="subtotal">0</span> FCFA
                                                    </div>
                                                    <div class="col-md-6" id="discount-row" style="display: none;">
                                                        <strong>Réduction (<span id="discount-percent">0</span>%):</strong>
                                                        <span style="color: #28a745;">-<span id="discount-amount">0</span> FCFA</span>
                                                    </div>
                                                </div>

                                                <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #01807B;">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <h5 style="margin: 0; color: #01807B;">
                                                                <strong>TOTAL À PAYER:</strong>
                                                                <span id="total-price" style="font-size: 24px;">0</span> FCFA
                                                            </h5>
                                                        </div>
                                                    </div>

                                                    <div class="row" style="margin-top: 10px;" id="payment-mode-info">
                                                        <div class="col-md-12">
                                                            <div class="alert alert-info" style="margin: 0; padding: 10px;">
                                                                <i class="fa fa-info-circle"></i> <span id="payment-mode-text"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- ===== FIN SECTION FACTURATION ===== -->

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

                                <?php if (isset($calculated_objectives) && $calculated_objectives): ?>
                                    <!-- Calculated Recommendations from Anamnesis -->
                                    <div class="alert alert-info" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); border-left: 4px solid #01807B; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                                            <h6 style="margin: 0; color: #01807B; font-weight: 700;">
                                                <i class="fa fa-calculator"></i> Valeurs Calculées depuis l'Anamnèse
                                            </h6>
                                            <button type="button" class="btn btn-sm btn-success" id="apply-calculated-values">
                                                <i class="fa fa-check"></i> Appliquer toutes les valeurs
                                            </button>
                                        </div>

                                        <p style="font-size: 12px; color: #666; margin-bottom: 12px;">
                                            <i class="fa fa-info-circle"></i> Basées sur : âge, poids, taille, activité, objectif
                                        </p>

                                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 10px;">
                                            <div style="background: white; padding: 10px; border-radius: 6px; text-align: center;">
                                                <div style="font-size: 11px; color: #999;">MB</div>
                                                <div style="font-size: 18px; font-weight: 700; color: #667eea;">
                                                    <?php echo number_format($calculated_objectives['bmr']['value'], 0); ?>
                                                </div>
                                                <div style="font-size: 10px; color: #999;">kcal/jour</div>
                                            </div>
                                            <div style="background: white; padding: 10px; border-radius: 6px; text-align: center;">
                                                <div style="font-size: 11px; color: #999;">TDEE</div>
                                                <div style="font-size: 18px; font-weight: 700; color: #4facfe;">
                                                    <?php echo number_format($calculated_objectives['tdee']['tdee'], 0); ?>
                                                </div>
                                                <div style="font-size: 10px; color: #999;"><?php echo substr($calculated_objectives['tdee']['activity_description'], 0, 10); ?></div>
                                            </div>
                                            <?php if ($calculated_objectives['calorie_needs']['deficit_surplus'] != 0): ?>
                                                <div style="background: white; padding: 10px; border-radius: 6px; text-align: center;">
                                                    <div style="font-size: 11px; color: #999;">Ajust.</div>
                                                    <div style="font-size: 18px; font-weight: 700; color: #fa709a;">
                                                        <?php echo ($calculated_objectives['calorie_needs']['deficit_surplus'] > 0 ? '+' : '') . $calculated_objectives['calorie_needs']['deficit_surplus']; ?>
                                                    </div>
                                                    <div style="font-size: 10px; color: #999;">kcal/jour</div>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <div style="background: white; padding: 12px; border-radius: 6px;">
                                            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; font-size: 13px;">
                                                <div>
                                                    <strong style="color: #01807B;">Calories:</strong>
                                                    <span id="calc-calories" data-value="<?php echo $calculated_objectives['macros']['calories']; ?>">
                                                        <?php echo number_format($calculated_objectives['macros']['calories'], 0); ?> kcal
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong style="color: #01807B;">Protéines:</strong>
                                                    <span id="calc-protein" data-value="<?php echo $calculated_objectives['macros']['protein']['grams']; ?>">
                                                        <?php echo number_format($calculated_objectives['macros']['protein']['grams'], 0); ?>g
                                                        (<?php echo $calculated_objectives['macros']['protein']['percentage']; ?>%)
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong style="color: #01807B;">Glucides:</strong>
                                                    <span id="calc-carbs" data-value="<?php echo $calculated_objectives['macros']['carbs']['grams']; ?>">
                                                        <?php echo number_format($calculated_objectives['macros']['carbs']['grams'], 0); ?>g
                                                        (<?php echo $calculated_objectives['macros']['carbs']['percentage']; ?>%)
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong style="color: #01807B;">Lipides:</strong>
                                                    <span id="calc-fats" data-value="<?php echo $calculated_objectives['macros']['fats']['grams']; ?>">
                                                        <?php echo number_format($calculated_objectives['macros']['fats']['grams'], 0); ?>g
                                                        (<?php echo $calculated_objectives['macros']['fats']['percentage']; ?>%)
                                                    </span>
                                                </div>
                                                <div>
                                                    <strong style="color: #01807B;">Fibres:</strong>
                                                    <span id="calc-fiber" data-value="<?php echo $calculated_objectives['macros']['fiber']; ?>">
                                                        <?php echo number_format($calculated_objectives['macros']['fiber'], 0); ?>g
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

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

        // Apply calculated values button
        <?php if (isset($calculated_objectives) && $calculated_objectives): ?>
        $('#apply-calculated-values').on('click', function() {
            // Get values from data attributes
            const calories = $('#calc-calories').data('value');
            const protein = $('#calc-protein').data('value');
            const carbs = $('#calc-carbs').data('value');
            const fats = $('#calc-fats').data('value');
            const fiber = $('#calc-fiber').data('value');

            // Apply values to form fields
            $('#daily_calories').val(calories);
            $('#daily_protein').val(protein);
            $('#daily_carbs').val(carbs);
            $('#daily_fats').val(fats);
            $('#daily_fiber').val(fiber);

            // Update percentage displays
            updatePercentageDisplays();

            // Visual feedback
            $(this).html('<i class="fa fa-check"></i> Valeurs appliquées !');
            $(this).removeClass('btn-success').addClass('btn-info');

            setTimeout(function() {
                $('#apply-calculated-values').html('<i class="fa fa-check"></i> Appliquer toutes les valeurs');
                $('#apply-calculated-values').removeClass('btn-info').addClass('btn-success');
            }, 2000);
        });
        <?php endif; ?>

        // ============================================
        // PRICE CALCULATOR FOR BILLING
        // ============================================
        function calculatePrice() {
            const serviceSelect = $('#service_id');
            const durationSelect = $('#duration_months');
            const paymentModeSelect = $('#payment_mode');
            const calculator = $('#price-calculator');

            // Get selected values
            const selectedOption = serviceSelect.find('option:selected');
            const monthlyRate = parseFloat(selectedOption.data('rate')) || 0;
            const discount6 = parseFloat(selectedOption.data('discount-6')) || 0;
            const discount12 = parseFloat(selectedOption.data('discount-12')) || 0;
            const duration = parseInt(durationSelect.val()) || 0;
            const paymentMode = paymentModeSelect.val();

            // Only show calculator if all values are selected
            if (monthlyRate > 0 && duration > 0 && paymentMode) {
                calculator.show();

                // Calculate subtotal
                const subtotal = monthlyRate * duration;

                // Determine discount percentage
                let discountPercent = 0;
                if (duration == 6) {
                    discountPercent = discount6;
                } else if (duration == 12) {
                    discountPercent = discount12;
                }

                // Calculate discount amount
                const discountAmount = (subtotal * discountPercent) / 100;

                // Calculate total
                const total = subtotal - discountAmount;

                // Update display
                $('#monthly-price').text(monthlyRate.toLocaleString('fr-FR'));
                $('#duration-display').text(duration);
                $('#subtotal').text(subtotal.toLocaleString('fr-FR'));

                if (discountPercent > 0) {
                    $('#discount-row').show();
                    $('#discount-percent').text(discountPercent);
                    $('#discount-amount').text(discountAmount.toLocaleString('fr-FR'));
                } else {
                    $('#discount-row').hide();
                }

                $('#total-price').text(total.toLocaleString('fr-FR'));

                // Update payment mode info
                if (paymentMode === 'one_time') {
                    $('#payment-mode-text').html(
                        '<strong>Paiement unique:</strong> Le patient paiera ' + total.toLocaleString('fr-FR') + ' FCFA en une seule fois.'
                    );
                } else if (paymentMode === 'recurring') {
                    $('#payment-mode-text').html(
                        '<strong>Paiement mensuel:</strong> Le patient paiera ' + monthlyRate.toLocaleString('fr-FR') + ' FCFA par mois pendant ' + duration + ' mois. Total: ' + total.toLocaleString('fr-FR') + ' FCFA.'
                    );
                }
            } else {
                calculator.hide();
            }
        }

        // Trigger calculation on any change
        $('#service_id, #duration_months, #payment_mode').on('change', function() {
            calculatePrice();
        });

        // Calculate on page load if editing
        <?php if (isset($program) && $program->service_id): ?>
        calculatePrice();
        <?php endif; ?>

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
})();
</script>
