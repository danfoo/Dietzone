<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo $title; ?></h4>
                        <p><?php echo $program->program_name; ?></p>
                        <hr />

                        <?php echo form_open($this->uri->uri_string()); ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="plan_name"><?php echo _l('dietetic_plan_name'); ?> *</label>
                                    <input type="text" class="form-control" name="plan_name" value="<?php echo isset($meal_plan) ? $meal_plan->plan_name : 'Week Plan'; ?>" required />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="week_number"><?php echo _l('dietetic_week'); ?> *</label>
                                    <input type="number" min="1" class="form-control" name="week_number" value="<?php echo isset($meal_plan) ? $meal_plan->week_number : '1'; ?>" required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date"><?php echo _l('dietetic_start_date'); ?></label>
                                    <input type="date" class="form-control" name="start_date" value="<?php echo isset($meal_plan) ? $meal_plan->start_date : ''; ?>" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date"><?php echo _l('dietetic_end_date'); ?></label>
                                    <input type="date" class="form-control" name="end_date" value="<?php echo isset($meal_plan) ? $meal_plan->end_date : ''; ?>" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="notes"><?php echo _l('dietetic_notes'); ?></label>
                            <textarea class="form-control" name="notes" rows="4"><?php echo isset($meal_plan) ? $meal_plan->notes : ''; ?></textarea>
                        </div>

                        <div class="btn-bottom-toolbar text-right">
                            <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-default"><?php echo _l('cancel'); ?></a>
                            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                        </div>

                        <?php echo form_close(); ?>

                        <?php if (isset($meal_plan)) { ?>
                            <hr />
                            <div class="alert alert-info">
                                <strong>Note:</strong> After saving the meal plan, you can add meals from the meal plan view page.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
