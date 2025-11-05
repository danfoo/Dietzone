<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-balance-scale"></i>
                            <?php echo isset($measurement) ? _l('dietetic_edit_measurement') : _l('dietetic_add_measurement'); ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <?php if (isset($patient)) { ?>
                            <div class="alert alert-info">
                                <strong><?php echo _l('dietetic_patient'); ?>:</strong>
                                <?php echo $patient->client->company; ?>
                                <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>" class="pull-right">
                                    <i class="fa fa-eye"></i> <?php echo _l('view'); ?>
                                </a>
                            </div>
                        <?php } ?>

                        <?php echo form_open(admin_url('dietetic/measurements/' . (isset($measurement) ? 'edit/' . $measurement->id : 'create'))); ?>

                        <input type="hidden" name="patient_id" value="<?php echo $patient->id; ?>" />

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="measurement_date" class="control-label">
                                        <?php echo _l('dietetic_date'); ?> <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                           id="measurement_date"
                                           name="measurement_date"
                                           class="form-control"
                                           value="<?php echo isset($measurement) ? $measurement->measurement_date : date('Y-m-d'); ?>"
                                           required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="weight" class="control-label">
                                        <?php echo _l('dietetic_weight'); ?> (kg) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                           id="weight"
                                           name="weight"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           value="<?php echo isset($measurement) ? $measurement->weight : ''; ?>"
                                           required />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="body_fat" class="control-label">
                                        <?php echo _l('dietetic_body_fat'); ?> (%)
                                        <small class="text-muted"><i class="fa fa-magic"></i> <?php echo _l('dietetic_auto_calculated'); ?></small>
                                    </label>
                                    <input type="number"
                                           id="body_fat"
                                           name="body_fat"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           max="100"
                                           placeholder="<?php echo _l('dietetic_leave_blank_auto'); ?>"
                                           value="<?php echo isset($measurement) && $measurement->body_fat ? $measurement->body_fat : ''; ?>" />
                                    <small class="text-muted"><?php echo _l('dietetic_calculated_from_bmi_age_gender'); ?></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="muscle_mass" class="control-label">
                                        <?php echo _l('dietetic_muscle_mass'); ?> (%)
                                        <small class="text-muted"><i class="fa fa-magic"></i> <?php echo _l('dietetic_auto_calculated'); ?></small>
                                    </label>
                                    <input type="number"
                                           id="muscle_mass"
                                           name="muscle_mass"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           placeholder="<?php echo _l('dietetic_leave_blank_auto'); ?>"
                                           value="<?php echo isset($measurement) && $measurement->muscle_mass ? $measurement->muscle_mass : ''; ?>" />
                                    <small class="text-muted"><?php echo _l('dietetic_calculated_from_weight_body_fat'); ?></small>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="arms" class="control-label">
                                        Arms (cm)
                                    </label>
                                    <input type="number"
                                           id="arms"
                                           name="arms"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           value="<?php echo isset($measurement) && $measurement->arms ? $measurement->arms : ''; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="waist" class="control-label">
                                        <?php echo _l('dietetic_waist'); ?> (cm)
                                    </label>
                                    <input type="number"
                                           id="waist"
                                           name="waist"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           value="<?php echo isset($measurement) && $measurement->waist ? $measurement->waist : ''; ?>" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="hips" class="control-label">
                                        <?php echo _l('dietetic_hips'); ?> (cm)
                                    </label>
                                    <input type="number"
                                           id="hips"
                                           name="hips"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           value="<?php echo isset($measurement) && $measurement->hips ? $measurement->hips : ''; ?>" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="chest" class="control-label">
                                        <?php echo _l('dietetic_chest'); ?> (cm)
                                    </label>
                                    <input type="number"
                                           id="chest"
                                           name="chest"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           value="<?php echo isset($measurement) && $measurement->chest ? $measurement->chest : ''; ?>" />
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="thigh" class="control-label">
                                        <?php echo _l('dietetic_thigh'); ?> (cm)
                                    </label>
                                    <input type="number"
                                           id="thigh"
                                           name="thigh"
                                           class="form-control"
                                           step="0.1"
                                           min="0"
                                           value="<?php echo isset($measurement) && $measurement->thigh ? $measurement->thigh : ''; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes" class="control-label">
                                        <?php echo _l('dietetic_notes'); ?>
                                    </label>
                                    <textarea id="notes"
                                              name="notes"
                                              class="form-control"
                                              rows="4"><?php echo isset($measurement) ? $measurement->notes : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>" class="btn btn-default">
                                <?php echo _l('cancel'); ?>
                            </a>
                            <button type="submit" class="btn btn-info">
                                <?php echo _l('submit'); ?>
                            </button>
                        </div>

                        <?php echo form_close(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
