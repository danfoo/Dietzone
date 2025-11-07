<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo isset($survey) ? 'Modifier l\'Enquête' : 'Nouvelle Enquête Alimentaire'; ?>
                        </h4>
                        <hr class="hr-panel-heading">

                        <?php echo form_open(admin_url('dietetic/food_surveys/' . (isset($survey) ? 'edit/' . $survey->id : 'create'))); ?>

                        <div class="row">
                            <div class="col-md-12">
                                <?php echo render_input('survey_name', 'Nom de l\'enquête', isset($survey) ? $survey->survey_name : '', 'text', ['required' => true]); ?>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="patient_id">Patient *</label>
                                    <select name="patient_id" id="patient_id" class="selectpicker" data-width="100%" required>
                                        <option value="">Sélectionner un patient</option>
                                        <?php foreach ($patients as $patient) { ?>
                                            <option value="<?php echo $patient->id; ?>" <?php echo (isset($survey) && $survey->patient_id == $patient->id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($patient->client_name); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="program_id">Programme (optionnel)</label>
                                    <select name="program_id" id="program_id" class="selectpicker" data-width="100%">
                                        <option value="">Aucun programme</option>
                                        <?php foreach ($programs as $program) { ?>
                                            <option value="<?php echo $program->id; ?>" <?php echo (isset($survey) && $survey->program_id == $program->id) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($program->program_name); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <?php if (is_admin()) { ?>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dietitian_id">Diététicien *</label>
                                    <select name="dietitian_id" id="dietitian_id" class="selectpicker" data-width="100%" required>
                                        <option value="">Sélectionner un diététicien</option>
                                        <?php foreach ($staff as $member) { ?>
                                            <option value="<?php echo $member['staffid']; ?>"
                                                <?php echo (isset($survey) && $survey->dietitian_id == $member['staffid']) ? 'selected' : (get_staff_user_id() == $member['staffid'] ? 'selected' : ''); ?>>
                                                <?php echo htmlspecialchars($member['firstname'] . ' ' . $member['lastname']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <?php } ?>

                            <div class="col-md-6">
                                <?php echo render_date_input('start_date', 'Date de début', isset($survey) ? $survey->start_date : date('Y-m-d'), ['required' => true]); ?>
                            </div>

                            <div class="col-md-6">
                                <?php echo render_input('duration_days', 'Durée (jours)', isset($survey) ? $survey->duration_days : '7', 'number', ['min' => 1, 'max' => 90, 'required' => true]); ?>
                            </div>

                            <div class="col-md-12">
                                <?php echo render_textarea('objective', 'Objectif de l\'enquête', isset($survey) ? $survey->objective : '', ['rows' => 4, 'required' => true]); ?>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right">
                            <i class="fa fa-check"></i>
                            <?php echo isset($survey) ? 'Mettre à jour' : 'Créer l\'enquête'; ?>
                        </button>

                        <a href="<?php echo admin_url('dietetic/food_surveys'); ?>" class="btn btn-default">
                            <i class="fa fa-times"></i> Annuler
                        </a>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
