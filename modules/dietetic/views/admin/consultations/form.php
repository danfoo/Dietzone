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
                                            <option value="<?php echo $patient->id; ?>" <?php echo set_select('patient_id', $patient->id, (isset($consultation) && $consultation->patient_id == $patient->id) || (isset($_GET['patient_id']) && $_GET['patient_id'] == $patient->id)); ?>>
                                                <?php echo $patient->client_name; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="dietitian_id"><?php echo _l('dietetic_dietitian'); ?> *</label>
                                    <select name="dietitian_id" id="dietitian_id" class="form-control selectpicker" required>
                                        <?php foreach ($staff as $member) { ?>
                                            <option value="<?php echo $member['staffid']; ?>" <?php echo set_select('dietitian_id', $member['staffid'], (isset($consultation) && $consultation->dietitian_id == $member['staffid']) || (!isset($consultation) && $member['staffid'] == get_staff_user_id())); ?>>
                                                <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="consultation_date"><?php echo _l('dietetic_date'); ?> *</label>
                                    <input type="datetime-local" class="form-control" name="consultation_date" value="<?php echo isset($consultation) ? date('Y-m-d\TH:i', strtotime($consultation->consultation_date)) : ''; ?>" required />
                                </div>

                                <div class="form-group">
                                    <label for="consultation_type"><?php echo _l('dietetic_type'); ?></label>
                                    <select name="consultation_type" id="consultation_type" class="form-control">
                                        <option value="initial" <?php echo set_select('consultation_type', 'initial', isset($consultation) && $consultation->consultation_type == 'initial'); ?>>Initial</option>
                                        <option value="follow_up" <?php echo set_select('consultation_type', 'follow_up', (isset($consultation) && $consultation->consultation_type == 'follow_up') || !isset($consultation)); ?>>Follow-up</option>
                                        <option value="emergency" <?php echo set_select('consultation_type', 'emergency', isset($consultation) && $consultation->consultation_type == 'emergency'); ?>>Emergency</option>
                                        <option value="online" <?php echo set_select('consultation_type', 'online', isset($consultation) && $consultation->consultation_type == 'online'); ?>>Online</option>
                                        <option value="in_person" <?php echo set_select('consultation_type', 'in_person', isset($consultation) && $consultation->consultation_type == 'in_person'); ?>>In Person</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status"><?php echo _l('dietetic_status'); ?></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="scheduled" <?php echo set_select('status', 'scheduled', (isset($consultation) && $consultation->status == 'scheduled') || !isset($consultation)); ?>>Scheduled</option>
                                        <option value="completed" <?php echo set_select('status', 'completed', isset($consultation) && $consultation->status == 'completed'); ?>>Completed</option>
                                        <option value="cancelled" <?php echo set_select('status', 'cancelled', isset($consultation) && $consultation->status == 'cancelled'); ?>>Cancelled</option>
                                        <option value="no_show" <?php echo set_select('status', 'no_show', isset($consultation) && $consultation->status == 'no_show'); ?>>No Show</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="duration"><?php echo _l('dietetic_duration'); ?> (minutes)</label>
                                    <input type="number" class="form-control" name="duration" value="<?php echo isset($consultation) ? $consultation->duration : '60'; ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="location"><?php echo _l('dietetic_location'); ?></label>
                                    <input type="text" class="form-control" name="location" value="<?php echo isset($consultation) ? $consultation->location : ''; ?>" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reason"><?php echo _l('dietetic_reason'); ?></label>
                                    <textarea class="form-control" name="reason" rows="3"><?php echo isset($consultation) ? $consultation->reason : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="notes"><?php echo _l('dietetic_notes'); ?></label>
                                    <textarea class="form-control" name="notes" rows="4"><?php echo isset($consultation) ? $consultation->notes : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="observations"><?php echo _l('dietetic_observations'); ?></label>
                                    <textarea class="form-control" name="observations" rows="4"><?php echo isset($consultation) ? $consultation->observations : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="recommendations"><?php echo _l('dietetic_recommendations'); ?></label>
                                    <textarea class="form-control" name="recommendations" rows="4"><?php echo isset($consultation) ? $consultation->recommendations : ''; ?></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="weight_at_visit"><?php echo _l('dietetic_weight'); ?> (kg)</label>
                                            <input type="number" step="0.1" class="form-control" name="weight_at_visit" value="<?php echo isset($consultation) ? $consultation->weight_at_visit : ''; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="satisfaction_score"><?php echo _l('dietetic_satisfaction'); ?> (1-5)</label>
                                            <input type="number" min="1" max="5" class="form-control" name="satisfaction_score" value="<?php echo isset($consultation) ? $consultation->satisfaction_score : ''; ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="next_consultation_date"><?php echo _l('dietetic_next_consultation'); ?></label>
                                    <input type="datetime-local" class="form-control" name="next_consultation_date" value="<?php echo isset($consultation) && $consultation->next_consultation_date ? date('Y-m-d\TH:i', strtotime($consultation->next_consultation_date)) : ''; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/consultations'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
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
