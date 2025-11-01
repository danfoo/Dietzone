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
                                    <input type="number" class="form-control" name="daily_calories" value="<?php echo isset($program) ? $program->daily_calories : ''; ?>" />
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="daily_protein"><?php echo _l('dietetic_protein'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="daily_protein" value="<?php echo isset($program) ? $program->daily_protein : ''; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="daily_carbs"><?php echo _l('dietetic_carbs'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="daily_carbs" value="<?php echo isset($program) ? $program->daily_carbs : ''; ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="daily_fats"><?php echo _l('dietetic_fats'); ?> (g)</label>
                                            <input type="number" step="0.1" class="form-control" name="daily_fats" value="<?php echo isset($program) ? $program->daily_fats : ''; ?>" />
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="daily_fiber"><?php echo _l('dietetic_fiber'); ?> (g)</label>
                                    <input type="number" step="0.1" class="form-control" name="daily_fiber" value="<?php echo isset($program) ? $program->daily_fiber : ''; ?>" />
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
