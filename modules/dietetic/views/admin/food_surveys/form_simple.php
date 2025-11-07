<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.dietetic-form-header {
    background: linear-gradient(135deg, #01807B 0%, #019B95 100%);
    padding: 25px;
    border-radius: 8px 8px 0 0;
    margin: -15px -15px 25px -15px;
    color: white;
}

.dietetic-form-header h4 {
    margin: 0 0 8px 0;
    font-size: 24px;
    font-weight: 600;
    color: white;
}

.dietetic-form-header p {
    margin: 0;
    opacity: 0.95;
    font-size: 14px;
}

.form-section {
    background: #f9fafb;
    border-left: 4px solid #01807B;
    padding: 20px;
    margin-bottom: 25px;
    border-radius: 4px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #01807B;
    margin: 0 0 15px 0;
    display: flex;
    align-items: center;
}

.form-section-title i {
    margin-right: 10px;
    font-size: 18px;
    color: #F3911D;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.form-group label i {
    margin-right: 8px;
    color: #01807B;
    font-size: 14px;
}

.form-group label .help-icon {
    margin-left: 5px;
    color: #999;
    cursor: help;
    font-size: 14px;
}

.form-control:focus,
.selectpicker:focus {
    border-color: #01807B;
    box-shadow: 0 0 0 0.2rem rgba(1, 128, 123, 0.25);
}

.required-field {
    color: #F3911D;
    margin-left: 3px;
}

.form-actions {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 2px solid #e8e8e8;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-dietetic-primary {
    background: #01807B;
    border-color: #01807B;
    color: white;
    padding: 10px 25px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-dietetic-primary:hover {
    background: #016B68;
    border-color: #016B68;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(1, 128, 123, 0.3);
}

.btn-cancel {
    padding: 10px 25px;
    font-weight: 600;
}

.input-with-icon {
    position: relative;
}

.input-with-icon .form-control {
    padding-left: 35px;
}

.input-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #01807B;
    pointer-events: none;
}

.field-hint {
    font-size: 12px;
    color: #666;
    margin-top: 5px;
    font-style: italic;
}

@media (max-width: 768px) {
    .dietetic-form-header {
        padding: 20px;
    }

    .dietetic-form-header h4 {
        font-size: 20px;
    }

    .form-section {
        padding: 15px;
    }

    .form-actions {
        flex-direction: column-reverse;
        gap: 10px;
    }

    .form-actions button,
    .form-actions a {
        width: 100%;
        text-align: center;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="dietetic-form-header">
                            <h4>
                                <i class="fa fa-clipboard-list"></i>
                                <?php echo isset($survey) ? 'Modifier l\'Enquête Alimentaire' : 'Nouvelle Enquête Alimentaire'; ?>
                            </h4>
                            <p>
                                <?php echo isset($survey) ? 'Modifiez les informations de l\'enquête alimentaire' : 'Créez une enquête pour suivre les habitudes alimentaires de votre patient'; ?>
                            </p>
                        </div>

                        <?php echo form_open(admin_url('dietetic/food_surveys/' . (isset($survey) ? 'edit/' . $survey->id : 'create')), ['id' => 'food-survey-form']); ?>

                        <!-- Section: Informations générales -->
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="fa fa-info-circle"></i>
                                Informations générales
                            </h5>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="survey_name">
                                            <i class="fa fa-tag"></i>
                                            Nom de l'enquête
                                            <span class="required-field">*</span>
                                            <i class="fa fa-question-circle help-icon" data-toggle="tooltip" title="Donnez un nom descriptif à cette enquête"></i>
                                        </label>
                                        <input type="text"
                                               id="survey_name"
                                               name="survey_name"
                                               class="form-control"
                                               value="<?php echo isset($survey) ? htmlspecialchars($survey->survey_name) : ''; ?>"
                                               placeholder="Ex: Enquête Ramadan 2024, Suivi perte de poids..."
                                               required>
                                        <div class="field-hint">Ce nom vous aidera à identifier rapidement l'enquête</div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="objective">
                                            <i class="fa fa-bullseye"></i>
                                            Objectif de l'enquête
                                            <span class="required-field">*</span>
                                        </label>
                                        <textarea id="objective"
                                                  name="objective"
                                                  class="form-control"
                                                  rows="4"
                                                  placeholder="Décrivez l'objectif de cette enquête alimentaire..."
                                                  required><?php echo isset($survey) ? htmlspecialchars($survey->objective) : ''; ?></textarea>
                                        <div class="field-hint">Exemple: Analyser les habitudes alimentaires pendant le Ramadan, Suivre l'évolution nutritionnelle...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Section: Patient et Programme -->
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="fa fa-user-circle"></i>
                                Patient et Programme
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_id">
                                            <i class="fa fa-user"></i>
                                            Patient
                                            <span class="required-field">*</span>
                                        </label>
                                        <select name="patient_id"
                                                id="patient_id"
                                                class="selectpicker"
                                                data-width="100%"
                                                data-live-search="true"
                                                required>
                                            <option value="">Sélectionner un patient</option>
                                            <?php foreach ($patients as $patient) {
                                                $is_selected = false;
                                                if (isset($survey) && $survey->patient_id == $patient->id) {
                                                    $is_selected = true;
                                                } elseif (isset($selected_patient_id) && $selected_patient_id == $patient->id) {
                                                    $is_selected = true;
                                                }
                                            ?>
                                                <option value="<?php echo $patient->id; ?>" <?php echo $is_selected ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($patient->client_name); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="field-hint">Sélectionnez le patient concerné par cette enquête</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="program_id">
                                            <i class="fa fa-heartbeat"></i>
                                            Programme associé
                                            <i class="fa fa-question-circle help-icon" data-toggle="tooltip" title="Optionnel: Liez cette enquête à un programme diététique"></i>
                                        </label>
                                        <select name="program_id"
                                                id="program_id"
                                                class="selectpicker"
                                                data-width="100%"
                                                data-live-search="true">
                                            <option value="">Aucun programme</option>
                                            <?php foreach ($programs as $program) { ?>
                                                <option value="<?php echo $program->id; ?>" <?php echo (isset($survey) && $survey->program_id == $program->id) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($program->program_name); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="field-hint">Associez cette enquête à un programme existant (optionnel)</div>
                                    </div>
                                </div>

                                <?php if (is_admin()) { ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="dietitian_id">
                                            <i class="fa fa-user-md"></i>
                                            Diététicien responsable
                                            <span class="required-field">*</span>
                                        </label>
                                        <select name="dietitian_id"
                                                id="dietitian_id"
                                                class="selectpicker"
                                                data-width="100%"
                                                data-live-search="true"
                                                required>
                                            <option value="">Sélectionner un diététicien</option>
                                            <?php foreach ($staff as $member) { ?>
                                                <option value="<?php echo $member['staffid']; ?>"
                                                    <?php echo (isset($survey) && $survey->dietitian_id == $member['staffid']) ? 'selected' : (get_staff_user_id() == $member['staffid'] ? 'selected' : ''); ?>>
                                                    <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <div class="field-hint">Le diététicien qui supervisera cette enquête</div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Section: Planification -->
                        <div class="form-section">
                            <h5 class="form-section-title">
                                <i class="fa fa-calendar-alt"></i>
                                Planification de l'enquête
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">
                                            <i class="fa fa-calendar-check"></i>
                                            Date de début
                                            <span class="required-field">*</span>
                                        </label>
                                        <?php
                                        $start_date_value = isset($survey) ? $survey->start_date : date('Y-m-d');
                                        echo '<input type="text"
                                                     id="start_date"
                                                     name="start_date"
                                                     class="form-control datepicker"
                                                     value="' . $start_date_value . '"
                                                     autocomplete="off"
                                                     required>';
                                        ?>
                                        <div class="field-hint">Date de début de la période d'observation</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="duration_days">
                                            <i class="fa fa-clock"></i>
                                            Durée (jours)
                                            <span class="required-field">*</span>
                                            <i class="fa fa-question-circle help-icon" data-toggle="tooltip" title="Entre 1 et 90 jours. Recommandé: 7-14 jours"></i>
                                        </label>
                                        <input type="number"
                                               id="duration_days"
                                               name="duration_days"
                                               class="form-control"
                                               value="<?php echo isset($survey) ? $survey->duration_days : '7'; ?>"
                                               min="1"
                                               max="90"
                                               required>
                                        <div class="field-hint">Durée recommandée: 7 à 14 jours pour une analyse complète</div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="alert alert-info" style="border-left: 4px solid #01807B;">
                                        <i class="fa fa-info-circle"></i>
                                        <strong>Période d'enquête:</strong>
                                        Du <span id="display-start-date"><?php echo date('d/m/Y', strtotime($start_date_value)); ?></span>
                                        au <span id="display-end-date"></span>
                                        (<span id="display-duration">7</span> jours)
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <a href="<?php echo admin_url('dietetic/food_surveys'); ?>" class="btn btn-default btn-cancel">
                                <i class="fa fa-times"></i> Annuler
                            </a>

                            <button type="submit" class="btn btn-dietetic-primary">
                                <i class="fa fa-check"></i>
                                <?php echo isset($survey) ? 'Mettre à jour l\'enquête' : 'Créer l\'enquête'; ?>
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
$(function() {
    'use strict';

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Initialize datepicker
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        minDate: '-1y',
        maxDate: '+1y'
    });

    // Calculate and display end date
    function updateEndDate() {
        var startDate = $('#start_date').val();
        var duration = parseInt($('#duration_days').val()) || 7;

        if (startDate) {
            var start = new Date(startDate);
            var end = new Date(start);
            end.setDate(start.getDate() + duration - 1);

            var displayStart = formatDate(start);
            var displayEnd = formatDate(end);

            $('#display-start-date').text(displayStart);
            $('#display-end-date').text(displayEnd);
            $('#display-duration').text(duration);
        }
    }

    function formatDate(date) {
        var day = ('0' + date.getDate()).slice(-2);
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var year = date.getFullYear();
        return day + '/' + month + '/' + year;
    }

    // Update end date on change
    $('#start_date, #duration_days').on('change', updateEndDate);

    // Initialize on page load
    updateEndDate();

    // Form validation
    $('#food-survey-form').on('submit', function(e) {
        var isValid = true;
        var errors = [];

        // Validate survey name
        var surveyName = $('#survey_name').val().trim();
        if (surveyName.length < 3) {
            errors.push('Le nom de l\'enquête doit contenir au moins 3 caractères');
            $('#survey_name').addClass('has-error');
            isValid = false;
        } else {
            $('#survey_name').removeClass('has-error');
        }

        // Validate patient
        if (!$('#patient_id').val()) {
            errors.push('Veuillez sélectionner un patient');
            isValid = false;
        }

        // Validate dietitian (if admin)
        <?php if (is_admin()) { ?>
        if (!$('#dietitian_id').val()) {
            errors.push('Veuillez sélectionner un diététicien');
            isValid = false;
        }
        <?php } ?>

        // Validate duration
        var duration = parseInt($('#duration_days').val());
        if (duration < 1 || duration > 90) {
            errors.push('La durée doit être entre 1 et 90 jours');
            $('#duration_days').addClass('has-error');
            isValid = false;
        } else {
            $('#duration_days').removeClass('has-error');
        }

        // Validate objective
        var objective = $('#objective').val().trim();
        if (objective.length < 10) {
            errors.push('L\'objectif doit contenir au moins 10 caractères');
            $('#objective').addClass('has-error');
            isValid = false;
        } else {
            $('#objective').removeClass('has-error');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez corriger les erreurs suivantes:\n\n' + errors.join('\n'));
        }
    });

    // Add visual feedback for required fields
    $('input[required], select[required], textarea[required]').on('blur', function() {
        if ($(this).val()) {
            $(this).removeClass('has-error');
        } else {
            $(this).addClass('has-error');
        }
    });
});
</script>

<?php init_tail(); ?>
