<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body patient-header">
                        <h2><i class="fa fa-user"></i> <?php echo $patient->client->company; ?></h2>
                        <p><?php echo _l('dietetic_patient') . ' #' . $patient->id; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <!-- Patient Information -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_patient_profile'); ?></h4>
                        <hr />

                        <div class="patient-info-grid">
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_dietitian'); ?></label>
                                <div class="value"><?php echo $patient->dietitian->firstname . ' ' . $patient->dietitian->lastname; ?></div>
                            </div>
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_status'); ?></label>
                                <div class="value"><?php echo dietetic_patient_status_badge($patient->status); ?></div>
                            </div>
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_gender'); ?></label>
                                <div class="value"><?php echo ucfirst($patient->gender); ?></div>
                            </div>
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_birth_date'); ?></label>
                                <div class="value"><?php echo _d($patient->birth_date); ?></div>
                            </div>
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_phone'); ?></label>
                                <div class="value"><?php echo $patient->phone; ?></div>
                            </div>
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_email'); ?></label>
                                <div class="value"><?php echo $patient->email; ?></div>
                            </div>
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_activity_level'); ?></label>
                                <div class="value"><?php echo ucfirst(str_replace('_', ' ', $patient->activity_level)); ?></div>
                            </div>
                            <div class="patient-info-item">
                                <label><?php echo _l('dietetic_active_programs'); ?></label>
                                <div class="value"><?php echo $patient->active_programs; ?></div>
                            </div>
                        </div>

                        <hr />

                        <div class="row">
                            <div class="col-md-12">
                                <h5><?php echo _l('dietetic_objective'); ?></h5>
                                <p><?php echo nl2br($patient->objective); ?></p>
                            </div>
                        </div>

                        <?php if ($patient->medical_conditions || $patient->allergies) { ?>
                            <hr />
                            <div class="row">
                                <?php if ($patient->medical_conditions) { ?>
                                    <div class="col-md-6">
                                        <h5><?php echo _l('dietetic_medical_conditions'); ?></h5>
                                        <p><?php echo nl2br($patient->medical_conditions); ?></p>
                                    </div>
                                <?php } ?>
                                <?php if ($patient->allergies) { ?>
                                    <div class="col-md-6">
                                        <h5><?php echo _l('dietetic_allergies'); ?></h5>
                                        <p><?php echo nl2br($patient->allergies); ?></p>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>

                        <div class="clearfix mtop15">
                            <?php if (dietetic_has_permission('edit')) { ?>
                                <a href="<?php echo admin_url('dietetic/patients/edit/' . $patient->id); ?>" class="btn btn-primary">
                                    <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Weight Evolution Chart -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_weight_evolution'); ?></h4>
                        <canvas id="weightChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Consultations -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_consultations'); ?></h4>
                        <?php if (!empty($consultations)) { ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('dietetic_date'); ?></th>
                                        <th><?php echo _l('dietetic_type'); ?></th>
                                        <th><?php echo _l('dietetic_status'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consultations as $consultation) { ?>
                                        <tr>
                                            <td><?php echo _dt($consultation->consultation_date); ?></td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></td>
                                            <td><?php echo dietetic_consultation_status_badge($consultation->status); ?></td>
                                            <td>
                                                <a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>" class="btn btn-sm btn-default">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo _l('dietetic_no_consultations'); ?></p>
                        <?php } ?>
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/consultations/create?patient_id=' . $patient->id); ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_consultation'); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <!-- Programs -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_programs'); ?></h4>
                        <?php if (!empty($programs)) { ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('dietetic_program_name'); ?></th>
                                        <th><?php echo _l('dietetic_start_date'); ?></th>
                                        <th><?php echo _l('dietetic_status'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($programs as $program) { ?>
                                        <tr>
                                            <td><?php echo $program->program_name; ?></td>
                                            <td><?php echo _d($program->start_date); ?></td>
                                            <td><?php echo dietetic_program_status_badge($program->status); ?></td>
                                            <td>
                                                <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-sm btn-default">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo _l('dietetic_no_programs'); ?></p>
                        <?php } ?>
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/programs/create?patient_id=' . $patient->id); ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_program'); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Current Stats -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_weight_progress'); ?></h4>
                        <hr />

                        <div class="text-center">
                            <h5><?php echo _l('dietetic_initial_weight'); ?></h5>
                            <h3><?php echo $patient->initial_weight ? $patient->initial_weight . ' kg' : '-'; ?></h3>
                        </div>

                        <?php if ($patient->latest_measurement) { ?>
                            <div class="text-center mtop15">
                                <h5><?php echo _l('dietetic_current_weight'); ?></h5>
                                <h3 class="text-success"><?php echo $patient->latest_measurement->weight . ' kg'; ?></h3>
                            </div>

                            <?php if ($weight_progress->weight_change !== null) { ?>
                                <div class="text-center mtop15">
                                    <h5><?php echo _l('dietetic_weight_progress'); ?></h5>
                                    <h3 class="<?php echo $weight_progress->weight_change < 0 ? 'text-success' : 'text-warning'; ?>">
                                        <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?> kg
                                        (<?php echo ($weight_progress->percentage_change > 0 ? '+' : '') . number_format($weight_progress->percentage_change, 1); ?>%)
                                    </h3>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <div class="text-center mtop15">
                            <h5><?php echo _l('dietetic_target_weight'); ?></h5>
                            <h3><?php echo $patient->target_weight ? $patient->target_weight . ' kg' : '-'; ?></h3>
                        </div>

                        <div class="text-center mtop15">
                            <h5><?php echo _l('dietetic_height'); ?></h5>
                            <h3><?php echo $patient->height ? $patient->height . ' cm' : '-'; ?></h3>
                        </div>

                        <?php if ($patient->latest_measurement && $patient->latest_measurement->bmi) { ?>
                            <div class="text-center mtop15">
                                <h5><?php echo _l('dietetic_bmi'); ?></h5>
                                <h3><?php echo $patient->latest_measurement->bmi; ?></h3>
                                <p class="text-muted"><?php echo dietetic_get_bmi_category($patient->latest_measurement->bmi); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Measurements -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_measurements'); ?></h4>
                        <hr />

                        <?php if (!empty($measurements)) { ?>
                            <?php foreach (array_slice($measurements, 0, 5) as $measurement) { ?>
                                <div class="mtop10">
                                    <strong><?php echo _d($measurement->measurement_date); ?></strong><br />
                                    Weight: <?php echo $measurement->weight; ?> kg | BMI: <?php echo $measurement->bmi; ?>
                                    <?php if ($measurement->notes) { ?>
                                        <br /><small class="text-muted"><?php echo $measurement->notes; ?></small>
                                    <?php } ?>
                                </div>
                                <hr />
                            <?php } ?>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo _l('dietetic_no_data'); ?></p>
                        <?php } ?>

                        <?php if (dietetic_has_permission('create')) { ?>
                            <button type="button" class="btn btn-info btn-sm btn-block" data-toggle="modal" data-target="#addMeasurementModal">
                                <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_measurement'); ?>
                            </button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Measurement Modal -->
<div class="modal fade" id="addMeasurementModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><?php echo _l('dietetic_add_measurement'); ?></h4>
            </div>
            <div class="modal-body">
                <form id="add_measurement_form">
                    <input type="hidden" name="patient_id" value="<?php echo $patient->id; ?>" />

                    <div class="form-group">
                        <label for="measurement_date"><?php echo _l('dietetic_date'); ?></label>
                        <input type="date" class="form-control" name="measurement_date" value="<?php echo date('Y-m-d'); ?>" required />
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="weight"><?php echo _l('dietetic_weight'); ?> (kg)</label>
                                <input type="number" step="0.1" class="form-control" name="weight" required />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="body_fat"><?php echo _l('dietetic_body_fat'); ?> (%)</label>
                                <input type="number" step="0.1" class="form-control" name="body_fat" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="waist"><?php echo _l('dietetic_waist'); ?> (cm)</label>
                                <input type="number" step="0.1" class="form-control" name="waist" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="hips"><?php echo _l('dietetic_hips'); ?> (cm)</label>
                                <input type="number" step="0.1" class="form-control" name="hips" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes"><?php echo _l('dietetic_notes'); ?></label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-info" onclick="dietetic.addMeasurement()"><?php echo _l('submit'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        dietetic.loadWeightChart(<?php echo $patient->id; ?>, 'weightChart');
    });
</script>

<?php init_tail(); ?>
