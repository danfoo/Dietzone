<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin"><i class="fa fa-heartbeat"></i> <?php echo _l('dietetic_dashboard'); ?></h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-success"><?php echo isset($patient_stats->active_patients) ? $patient_stats->active_patients : 0; ?></h3>
                        <p class="text-muted"><?php echo _l('dietetic_active_patients'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-info"><?php echo isset($consultation_stats->scheduled) ? $consultation_stats->scheduled : 0; ?></h3>
                        <p class="text-muted"><?php echo _l('dietetic_scheduled'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-primary"><?php echo isset($program_stats->active_programs) ? $program_stats->active_programs : 0; ?></h3>
                        <p class="text-muted"><?php echo _l('dietetic_active_programs'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-warning"><?php echo isset($consultation_stats->avg_satisfaction) ? $consultation_stats->avg_satisfaction : 0; ?>/5</h3>
                        <p class="text-muted"><?php echo _l('dietetic_avg_satisfaction'); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Upcoming Consultations -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_upcoming_consultations'); ?></h4>
                        <?php if (!empty($upcoming_consultations)) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('dietetic_patient'); ?></th>
                                            <th><?php echo _l('dietetic_date'); ?></th>
                                            <th><?php echo _l('dietetic_type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcoming_consultations as $consultation) { ?>
                                            <tr>
                                                <td><?php echo isset($consultation->client_name) ? $consultation->client_name : 'N/A'; ?></td>
                                                <td><?php echo isset($consultation->consultation_date) ? _dt($consultation->consultation_date) : 'N/A'; ?></td>
                                                <td><?php echo isset($consultation->consultation_type) ? ucfirst(str_replace('_', ' ', $consultation->consultation_type)) : 'N/A'; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo _l('dietetic_no_consultations'); ?></p>
                        <?php } ?>
                        <a href="<?php echo admin_url('dietetic/consultations'); ?>" class="btn btn-default btn-sm pull-right"><?php echo _l('view_all'); ?></a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>

            <!-- Recent Patients -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_recent_patients'); ?></h4>
                        <?php if (!empty($recent_patients)) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php echo _l('dietetic_client_name'); ?></th>
                                            <th><?php echo _l('dietetic_status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_patients as $patient) { ?>
                                            <tr>
                                                <td><a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>"><?php echo isset($patient->client_name) ? $patient->client_name : 'N/A'; ?></a></td>
                                                <td><?php echo isset($patient->status) ? dietetic_patient_status_badge($patient->status) : 'N/A'; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted"><?php echo _l('dietetic_no_data'); ?></p>
                        <?php } ?>
                        <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-default btn-sm pull-right"><?php echo _l('view_all'); ?></a>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('quick_actions'); ?></h4>
                        <hr />
                        <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-success">
                            <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_patient'); ?>
                        </a>
                        <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-info">
                            <i class="fa fa-calendar"></i> <?php echo _l('dietetic_add_consultation'); ?>
                        </a>
                        <a href="<?php echo admin_url('dietetic/programs/create'); ?>" class="btn btn-primary">
                            <i class="fa fa-list"></i> <?php echo _l('dietetic_add_program'); ?>
                        </a>
                        <a href="<?php echo admin_url('dietetic/foods'); ?>" class="btn btn-default">
                            <i class="fa fa-cutlery"></i> <?php echo _l('dietetic_foods'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
