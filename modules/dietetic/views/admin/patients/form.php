<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><?php echo $title; ?></h4>
                        <hr />

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <?php if (!isset($patient)) { ?>
                                    <div class="form-group">
                                        <label for="client_id"><?php echo _l('dietetic_client_name'); ?> *</label>
                                        <select name="client_id" id="client_id" class="form-control selectpicker" data-live-search="true" required>
                                            <option value="">-- <?php echo _l('select'); ?> --</option>
                                            <?php foreach ($clients as $client) { ?>
                                                <option value="<?php echo $client['userid']; ?>" <?php echo set_select('client_id', $client['userid'], (isset($_GET['client_id']) && $_GET['client_id'] == $client['userid'])); ?>>
                                                    <?php echo $client['company']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } else { ?>
                                    <div class="form-group">
                                        <label><?php echo _l('dietetic_client_name'); ?></label>
                                        <p class="form-control-static"><strong><?php echo $patient->client->company; ?></strong></p>
                                    </div>
                                <?php } ?>

                                <div class="form-group">
                                    <label for="dietitian_id"><?php echo _l('dietetic_dietitian'); ?> *</label>
                                    <select name="dietitian_id" id="dietitian_id" class="form-control selectpicker" required>
                                        <?php foreach ($staff as $member) { ?>
                                            <option value="<?php echo $member['staffid']; ?>" <?php echo set_select('dietitian_id', $member['staffid'], (isset($patient) && $patient->dietitian_id == $member['staffid'])); ?>>
                                                <?php echo $member['firstname'] . ' ' . $member['lastname']; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="status"><?php echo _l('dietetic_status'); ?></label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="active" <?php echo set_select('status', 'active', (isset($patient) && $patient->status == 'active') || !isset($patient)); ?>>Active</option>
                                        <option value="inactive" <?php echo set_select('status', 'inactive', isset($patient) && $patient->status == 'inactive'); ?>>Inactive</option>
                                        <option value="archived" <?php echo set_select('status', 'archived', isset($patient) && $patient->status == 'archived'); ?>>Archived</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="gender"><?php echo _l('dietetic_gender'); ?></label>
                                    <select name="gender" id="gender" class="form-control">
                                        <option value="">-- <?php echo _l('select'); ?> --</option>
                                        <option value="male" <?php echo set_select('gender', 'male', isset($patient) && $patient->gender == 'male'); ?>>Male</option>
                                        <option value="female" <?php echo set_select('gender', 'female', isset($patient) && $patient->gender == 'female'); ?>>Female</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="birth_date"><?php echo _l('dietetic_birth_date'); ?></label>
                                    <input type="date" class="form-control" name="birth_date" value="<?php echo isset($patient) ? $patient->birth_date : ''; ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="phone"><?php echo _l('dietetic_phone'); ?></label>
                                    <input type="text" class="form-control" name="phone" value="<?php echo isset($patient) ? $patient->phone : ''; ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="email"><?php echo _l('dietetic_email'); ?></label>
                                    <input type="email" class="form-control" name="email" value="<?php echo isset($patient) ? $patient->email : ''; ?>" />
                                </div>

                                <div class="form-group">
                                    <label for="activity_level"><?php echo _l('dietetic_activity_level'); ?></label>
                                    <select name="activity_level" id="activity_level" class="form-control">
                                        <option value="">-- <?php echo _l('select'); ?> --</option>
                                        <?php foreach (dietetic_get_activity_levels() as $key => $label) { ?>
                                            <option value="<?php echo $key; ?>" <?php echo set_select('activity_level', $key, isset($patient) && $patient->activity_level == $key); ?>>
                                                <?php echo $label; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="initial_weight"><?php echo _l('dietetic_initial_weight'); ?> (kg)</label>
                                            <input type="number" step="0.1" class="form-control" id="weight" name="initial_weight" value="<?php echo isset($patient) ? $patient->initial_weight : ''; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="target_weight"><?php echo _l('dietetic_target_weight'); ?> (kg)</label>
                                            <input type="number" step="0.1" class="form-control" name="target_weight" value="<?php echo isset($patient) ? $patient->target_weight : ''; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="height"><?php echo _l('dietetic_height'); ?> (cm)</label>
                                            <input type="number" step="0.1" class="form-control" id="height" name="height" value="<?php echo isset($patient) ? $patient->height : ''; ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="objective"><?php echo _l('dietetic_objective'); ?></label>
                                    <textarea class="form-control" name="objective" rows="3"><?php echo isset($patient) ? $patient->objective : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="medical_conditions"><?php echo _l('dietetic_medical_conditions'); ?></label>
                                    <textarea class="form-control" name="medical_conditions" rows="3"><?php echo isset($patient) ? $patient->medical_conditions : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="allergies"><?php echo _l('dietetic_allergies'); ?></label>
                                    <textarea class="form-control" name="allergies" rows="2"><?php echo isset($patient) ? $patient->allergies : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="medications"><?php echo _l('dietetic_medications'); ?></label>
                                    <textarea class="form-control" name="medications" rows="2"><?php echo isset($patient) ? $patient->medications : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="dietary_preferences"><?php echo _l('dietetic_dietary_preferences'); ?></label>
                                    <input type="text" class="form-control" name="dietary_preferences" value="<?php echo isset($patient) ? $patient->dietary_preferences : ''; ?>" placeholder="e.g., vegetarian, vegan, halal" />
                                </div>

                                <div class="form-group">
                                    <label for="lifestyle_notes"><?php echo _l('dietetic_lifestyle_notes'); ?></label>
                                    <textarea class="form-control" name="lifestyle_notes" rows="2"><?php echo isset($patient) ? $patient->lifestyle_notes : ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
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
