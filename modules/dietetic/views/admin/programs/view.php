<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h3><?php echo $program->program_name; ?></h3>
                        <p class="text-muted">
                            <?php echo _l('dietetic_patient'); ?>: <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>"><?php echo $patient->client->company; ?></a>
                        </p>
                        <hr />

                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo _l('details'); ?></h5>

                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_status'); ?>:</strong></td>
                                        <td><?php echo dietetic_program_status_badge($program->status); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_dietitian'); ?>:</strong></td>
                                        <td><?php echo $program->dietitian_name; ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_start_date'); ?>:</strong></td>
                                        <td><?php echo _d($program->start_date); ?></td>
                                    </tr>
                                    <?php if ($program->end_date) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_end_date'); ?>:</strong></td>
                                            <td><?php echo _d($program->end_date); ?></td>
                                        </tr>
                                    <?php } ?>
                                    <tr>
                                        <td><strong><?php echo _l('dietetic_meal_count'); ?>:</strong></td>
                                        <td><?php echo $program->meal_count; ?> per day</td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-md-6">
                                <h5><?php echo _l('dietetic_daily_targets'); ?></h5>

                                <table class="table table-borderless">
                                    <?php if ($program->daily_calories) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_calories'); ?>:</strong></td>
                                            <td><?php echo $program->daily_calories; ?> kcal</td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($program->daily_protein) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_protein'); ?>:</strong></td>
                                            <td><?php echo $program->daily_protein; ?>g</td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($program->daily_carbs) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_carbs'); ?>:</strong></td>
                                            <td><?php echo $program->daily_carbs; ?>g</td>
                                        </tr>
                                    <?php } ?>
                                    <?php if ($program->daily_fats) { ?>
                                        <tr>
                                            <td><strong><?php echo _l('dietetic_fats'); ?>:</strong></td>
                                            <td><?php echo $program->daily_fats; ?>g</td>
                                        </tr>
                                    <?php } ?>
                                </table>
                            </div>
                        </div>

                        <?php if ($program->description) { ?>
                            <hr />
                            <h5><?php echo _l('dietetic_description'); ?></h5>
                            <p><?php echo nl2br($program->description); ?></p>
                        <?php } ?>

                        <?php if ($program->objective) { ?>
                            <hr />
                            <h5><?php echo _l('dietetic_objective'); ?></h5>
                            <p><?php echo nl2br($program->objective); ?></p>
                        <?php } ?>

                        <?php if ($program->instructions) { ?>
                            <hr />
                            <h5><?php echo _l('dietetic_instructions'); ?></h5>
                            <p><?php echo nl2br($program->instructions); ?></p>
                        <?php } ?>

                        <hr />
                        <div class="btn-bottom-toolbar">
                            <a href="<?php echo admin_url('dietetic/programs'); ?>" class="btn btn-default"><?php echo _l('back'); ?></a>
                            <?php if (dietetic_has_permission('edit')) { ?>
                                <a href="<?php echo admin_url('dietetic/programs/edit/' . $program->id); ?>" class="btn btn-info">
                                    <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                <!-- Meal Plans -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left"><?php echo _l('dietetic_meal_plans'); ?></h4>
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/programs/create_meal_plan/' . $program->id); ?>" class="btn btn-info pull-right">
                                    <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_meal_plan'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <hr />

                        <?php if (!empty($meal_plans)) { ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><?php echo _l('dietetic_week'); ?></th>
                                        <th><?php echo _l('dietetic_plan_name'); ?></th>
                                        <th><?php echo _l('dietetic_start_date'); ?></th>
                                        <th><?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($meal_plans as $plan) { ?>
                                        <tr>
                                            <td><?php echo $plan->week_number; ?></td>
                                            <td><?php echo $plan->plan_name; ?></td>
                                            <td><?php echo $plan->start_date ? _d($plan->start_date) : '-'; ?></td>
                                            <td>
                                                <a href="<?php echo admin_url('dietetic/programs/meal_plan/' . $plan->id); ?>" class="btn btn-sm btn-default">
                                                    <i class="fa fa-eye"></i> <?php echo _l('view'); ?>
                                                </a>
                                                <?php if (dietetic_has_permission('edit')) { ?>
                                                    <a href="<?php echo admin_url('dietetic/programs/edit_meal_plan/' . $plan->id); ?>" class="btn btn-sm btn-info">
                                                        <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                                    </a>
                                                <?php } ?>
                                                <a href="<?php echo admin_url('dietetic/programs/generate_pdf/' . $plan->id); ?>" class="btn btn-sm btn-success">
                                                    <i class="fa fa-file-pdf-o"></i> PDF
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="text-muted text-center"><?php echo _l('dietetic_no_meal_plans'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
