<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
/* Modern Form Styles */
:root {
    --primary-color: #01807B;
    --secondary-color: #F3911D;
    --tertiary-color: #FFFFFF;
    --text-dark: #2c3e50;
    --text-light: #7f8c8d;
    --background-light: #f8f9fa;
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.08);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.12);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.form-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #016663 100%);
    color: white;
    padding: 24px;
    border-radius: 12px 12px 0 0;
    margin-bottom: 0;
}

.form-header h3 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-container {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: 24px;
}

.form-body {
    padding: 30px;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
}

.form-group label .required {
    color: #e74c3c;
    margin-left: 4px;
}

.form-control {
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    padding: 12px 16px;
    font-size: 14px;
    transition: var(--transition);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(1, 128, 123, 0.1);
    outline: none;
}

.form-control-description {
    font-size: 12px;
    color: var(--text-light);
    margin-top: 6px;
}

.btn-submit {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 14px 32px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-submit:hover {
    background: #016663;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    color: white;
}

.btn-cancel {
    background: #ecf0f1;
    color: var(--text-dark);
    border: none;
    padding: 14px 32px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 15px;
    transition: var(--transition);
    margin-left: 12px;
}

.btn-cancel:hover {
    background: #d5d8dc;
}

.info-box {
    background: linear-gradient(135deg, rgba(1, 128, 123, 0.05) 0%, rgba(243, 145, 29, 0.05) 100%);
    border-left: 4px solid var(--primary-color);
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.info-box h5 {
    margin: 0 0 8px 0;
    color: var(--primary-color);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-box p {
    margin: 0;
    font-size: 14px;
    color: var(--text-light);
}

.select2-container--default .select2-selection--single {
    border: 2px solid #ecf0f1;
    border-radius: 8px;
    height: 46px;
    padding: 8px 16px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px;
}

.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: var(--primary-color);
}

@media (max-width: 768px) {
    .form-body {
        padding: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .btn-submit,
    .btn-cancel {
        width: 100%;
        margin-left: 0;
        margin-top: 10px;
        justify-content: center;
    }
}
</style>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <!-- Info Box -->
                <div class="info-box">
                    <h5>
                        <i class="fa fa-info-circle"></i>
                        À propos des Enquêtes Alimentaires
                    </h5>
                    <p>
                        Créez une enquête pour suivre quotidiennement les habitudes alimentaires de votre patient avec photos.
                        Le patient recevra des notifications pour soumettre ses repas chaque jour.
                    </p>
                </div>

                <!-- Form Container -->
                <div class="form-container">
                    <div class="form-header">
                        <h3>
                            <i class="fa fa-camera"></i>
                            <?php echo isset($survey) ? 'Modifier l\'Enquête Alimentaire' : 'Nouvelle Enquête Alimentaire'; ?>
                        </h3>
                    </div>

                    <div class="form-body">
                        <?php echo form_open($this->uri->uri_string()); ?>

                        <!-- Survey Name -->
                        <div class="form-group">
                            <label for="survey_name">
                                Nom de l'Enquête
                                <span class="required">*</span>
                            </label>
                            <input type="text"
                                   name="survey_name"
                                   id="survey_name"
                                   class="form-control"
                                   placeholder="Ex: Enquête alimentaire - Mars 2025"
                                   value="<?php echo isset($survey) ? $survey->survey_name : ''; ?>"
                                   required>
                            <div class="form-control-description">
                                Donnez un nom descriptif à cette enquête
                            </div>
                        </div>

                        <!-- Patient Selection -->
                        <div class="form-group">
                            <label for="patient_id">
                                Patient
                                <span class="required">*</span>
                            </label>
                            <select name="patient_id"
                                    id="patient_id"
                                    class="form-control select2"
                                    data-none-selected-text="Sélectionner un patient"
                                    required>
                                <option value="">Sélectionner un patient</option>
                                <?php foreach ($patients as $patient) { ?>
                                    <option value="<?php echo $patient->id; ?>"
                                            <?php echo (isset($survey) && $survey->patient_id == $patient->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($patient->client_name); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="form-control-description">
                                Le patient recevra les notifications pour soumettre ses repas quotidiens
                            </div>
                        </div>

                        <!-- Program (Optional) -->
                        <div class="form-group">
                            <label for="program_id">
                                Programme (Optionnel)
                            </label>
                            <select name="program_id"
                                    id="program_id"
                                    class="form-control select2"
                                    data-none-selected-text="Aucun programme">
                                <option value="">Aucun programme</option>
                                <?php foreach ($programs as $program) { ?>
                                    <option value="<?php echo $program->id; ?>"
                                            <?php echo (isset($survey) && $survey->program_id == $program->id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($program->program_name); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div class="form-control-description">
                                Associer cette enquête à un programme existant (optionnel)
                            </div>
                        </div>

                        <!-- Dietitian (if admin can select) -->
                        <?php if (is_admin()) { ?>
                            <div class="form-group">
                                <label for="dietitian_id">
                                    Diététicien
                                    <span class="required">*</span>
                                </label>
                                <select name="dietitian_id"
                                        id="dietitian_id"
                                        class="form-control select2"
                                        data-none-selected-text="Sélectionner un diététicien"
                                        required>
                                    <option value="">Sélectionner un diététicien</option>
                                    <?php foreach ($staff as $member) { ?>
                                        <option value="<?php echo $member['staffid']; ?>"
                                                <?php echo (isset($survey) && $survey->dietitian_id == $member['staffid']) ? 'selected' : (get_staff_user_id() == $member['staffid']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        <?php } ?>

                        <!-- Start Date -->
                        <div class="form-group">
                            <label for="start_date">
                                Date de Début
                                <span class="required">*</span>
                            </label>
                            <input type="date"
                                   name="start_date"
                                   id="start_date"
                                   class="form-control"
                                   value="<?php echo isset($survey) ? $survey->start_date : date('Y-m-d'); ?>"
                                   min="<?php echo date('Y-m-d'); ?>"
                                   required>
                            <div class="form-control-description">
                                Date à partir de laquelle le patient commencera à soumettre ses repas
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="form-group">
                            <label for="duration_days">
                                Durée (en jours)
                                <span class="required">*</span>
                            </label>
                            <input type="number"
                                   name="duration_days"
                                   id="duration_days"
                                   class="form-control"
                                   placeholder="Ex: 7"
                                   value="<?php echo isset($survey) ? $survey->duration_days : '7'; ?>"
                                   min="1"
                                   max="90"
                                   required>
                            <div class="form-control-description">
                                Nombre de jours pendant lesquels le patient devra enregistrer ses repas (recommandé: 7-14 jours)
                            </div>
                        </div>

                        <!-- Objective -->
                        <div class="form-group">
                            <label for="objective">
                                Objectif de l'Enquête
                                <span class="required">*</span>
                            </label>
                            <textarea name="objective"
                                      id="objective"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Décrivez l'objectif de cette enquête alimentaire..."
                                      required><?php echo isset($survey) ? $survey->objective : ''; ?></textarea>
                            <div class="form-control-description">
                                Expliquez pourquoi vous créez cette enquête et ce que vous voulez observer
                            </div>
                        </div>

                        <!-- Status (for edit mode) -->
                        <?php if (isset($survey)) { ?>
                            <div class="form-group">
                                <label for="status">
                                    Statut
                                    <span class="required">*</span>
                                </label>
                                <select name="status" id="status" class="form-control" required>
                                    <option value="active" <?php echo ($survey->status == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="completed" <?php echo ($survey->status == 'completed') ? 'selected' : ''; ?>>Terminée</option>
                                    <option value="cancelled" <?php echo ($survey->status == 'cancelled') ? 'selected' : ''; ?>>Annulée</option>
                                </select>
                            </div>
                        <?php } ?>

                        <!-- Form Actions -->
                        <div class="form-group" style="margin-top: 32px; padding-top: 24px; border-top: 2px solid #ecf0f1;">
                            <button type="submit" class="btn btn-submit">
                                <i class="fa fa-save"></i>
                                <span><?php echo isset($survey) ? 'Mettre à Jour' : 'Créer l\'Enquête'; ?></span>
                            </button>
                            <a href="<?php echo admin_url('dietetic/food_surveys'); ?>" class="btn btn-cancel">
                                <i class="fa fa-times"></i> Annuler
                            </a>
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
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Auto-calculate end date when duration changes
    $('#duration_days, #start_date').on('change', function() {
        var startDate = $('#start_date').val();
        var duration = parseInt($('#duration_days').val());

        if (startDate && duration) {
            var date = new Date(startDate);
            date.setDate(date.getDate() + duration);

            var endDateStr = date.toLocaleDateString('fr-FR', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

            // Show end date info
            var $durationGroup = $('#duration_days').closest('.form-group');
            var $endDateInfo = $durationGroup.find('.end-date-info');

            if ($endDateInfo.length) {
                $endDateInfo.text('Date de fin: ' + endDateStr);
            } else {
                $durationGroup.find('.form-control-description').after(
                    '<div class="end-date-info" style="margin-top: 8px; font-weight: 600; color: var(--primary-color);">' +
                    'Date de fin: ' + endDateStr +
                    '</div>'
                );
            }
        }
    });

    // Trigger on page load
    $('#duration_days').trigger('change');
});
</script>

<?php init_tail(); ?>
