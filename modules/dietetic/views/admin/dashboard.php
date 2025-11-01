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
                        <h3 class="text-success"><?php echo $patient_stats->active_patients; ?></h3>
                        <p class="text-muted"><?php echo _l('dietetic_active_patients'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-info"><?php echo $consultation_stats->scheduled; ?></h3>
                        <p class="text-muted"><?php echo _l('dietetic_scheduled'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-primary"><?php echo $program_stats->active_programs; ?></h3>
                        <p class="text-muted"><?php echo _l('dietetic_active_programs'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-warning"><?php echo $consultation_stats->avg_satisfaction; ?>/5</h3>
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
                                                <td><a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>"><?php echo $consultation->client_name; ?></a></td>
                                                <td><?php echo _dt($consultation->consultation_date); ?></td>
                                                <td><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></td>
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
                                            <th><?php echo _l('dietetic_dietitian'); ?></th>
                                            <th><?php echo _l('dietetic_status'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_patients as $patient) { ?>
                                            <tr>
                                                <td><a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>"><?php echo $patient->client_name; ?></a></td>
                                                <td><?php echo $patient->dietitian_name; ?></td>
                                                <td><?php echo dietetic_patient_status_badge($patient->status); ?></td>
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

        <!-- Charts -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4><?php echo _l('dietetic_patient_growth'); ?></h4>
                        <canvas id="patientGrowthChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        // Patient Growth Chart
        var ctx = document.getElementById('patientGrowthChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($patient_growth_chart['labels']); ?>,
                datasets: [{
                    label: '<?php echo _l('dietetic_new_patients'); ?>',
                    data: <?php echo json_encode($patient_growth_chart['data']); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.2)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<?php init_tail(); ?>
