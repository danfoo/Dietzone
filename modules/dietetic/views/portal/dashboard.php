<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php $this->load->view('authentication/includes/head'); ?>

<div class="dietetic-portal-header">
    <h1><i class="fa fa-heartbeat"></i> <?php echo _l('dietetic_my_program'); ?></h1>
    <p><?php echo _l('welcome'); ?>, <?php echo $patient->client->company; ?>!</p>
</div>

<div class="portal-stat-grid">
    <div class="portal-stat-card">
        <h3><?php echo $patient->latest_measurement ? $patient->latest_measurement->weight . ' kg' : '-'; ?></h3>
        <p><?php echo _l('dietetic_current_weight'); ?></p>
    </div>

    <div class="portal-stat-card">
        <h3><?php echo $patient->target_weight ? $patient->target_weight . ' kg' : '-'; ?></h3>
        <p><?php echo _l('dietetic_target_weight'); ?></p>
    </div>

    <div class="portal-stat-card">
        <h3><?php echo $patient->latest_measurement && $patient->latest_measurement->bmi ? $patient->latest_measurement->bmi : '-'; ?></h3>
        <p><?php echo _l('dietetic_bmi'); ?></p>
    </div>

    <?php if ($weight_progress->weight_change !== null) { ?>
        <div class="portal-stat-card">
            <h3 class="<?php echo $weight_progress->weight_change < 0 ? 'text-success' : ''; ?>">
                <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?> kg
            </h3>
            <p><?php echo _l('dietetic_weight_progress'); ?></p>
        </div>
    <?php } ?>
</div>

<?php if ($active_program) { ?>
    <div class="portal-program-card">
        <div class="portal-program-header">
            <div class="portal-program-title"><?php echo $active_program->program_name; ?></div>
            <span class="portal-program-status active"><?php echo _l('dietetic_program_active'); ?></span>
        </div>

        <div class="row">
            <div class="col-md-6">
                <p><strong><?php echo _l('dietetic_start_date'); ?>:</strong> <?php echo _d($active_program->start_date); ?></p>
                <?php if ($active_program->end_date) { ?>
                    <p><strong><?php echo _l('dietetic_end_date'); ?>:</strong> <?php echo _d($active_program->end_date); ?></p>
                <?php } ?>
            </div>
            <div class="col-md-6">
                <?php if ($active_program->daily_calories) { ?>
                    <p><strong><?php echo _l('dietetic_daily_calories'); ?>:</strong> <?php echo $active_program->daily_calories; ?> kcal</p>
                <?php } ?>
                <?php if ($active_program->daily_protein) { ?>
                    <p><strong><?php echo _l('dietetic_protein'); ?>:</strong> <?php echo $active_program->daily_protein; ?>g</p>
                <?php } ?>
            </div>
        </div>

        <?php if ($active_program->objective) { ?>
            <p><strong><?php echo _l('dietetic_objective'); ?>:</strong></p>
            <p><?php echo nl2br($active_program->objective); ?></p>
        <?php } ?>

        <?php
        // Get meal plans for this program
        $this->load->model('dietetic/dietetic_meal_plans_model');
        $meal_plans = $this->dietetic_meal_plans_model->get_by_program($active_program->id);
        ?>

        <?php if (!empty($meal_plans)) { ?>
            <h5 class="mtop20"><?php echo _l('dietetic_meal_plans'); ?></h5>
            <?php foreach ($meal_plans as $plan) { ?>
                <div class="portal-meal-plan-card">
                    <div class="portal-meal-plan-header">
                        <?php echo $plan->plan_name; ?> - <?php echo _l('dietetic_week'); ?> <?php echo $plan->week_number; ?>
                    </div>
                    <div class="mtop10">
                        <a href="<?php echo site_url('dietetic/portal/meal_plan/' . $plan->id); ?>" class="btn btn-sm btn-info">
                            <i class="fa fa-eye"></i> <?php echo _l('dietetic_view_details'); ?>
                        </a>
                        <a href="<?php echo site_url('dietetic/portal/download_meal_plan/' . $plan->id); ?>" class="btn btn-sm btn-success">
                            <i class="fa fa-download"></i> <?php echo _l('dietetic_download_pdf'); ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
<?php } else { ?>
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> <?php echo _l('dietetic_no_programs'); ?>
    </div>
<?php } ?>

<div class="row mtop30">
    <div class="col-md-6">
        <div class="portal-weight-chart-container">
            <h4><?php echo _l('dietetic_weight_evolution'); ?></h4>
            <canvas id="portalWeightChart" height="200"></canvas>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><?php echo _l('dietetic_upcoming_consultations'); ?></div>
            <div class="panel-body">
                <?php if (!empty($upcoming_consultations)) { ?>
                    <ul class="portal-consultation-list">
                        <?php foreach ($upcoming_consultations as $consultation) { ?>
                            <li class="portal-consultation-item">
                                <div>
                                    <div class="portal-consultation-date"><?php echo _dt($consultation->consultation_date); ?></div>
                                    <div class="portal-consultation-type"><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></div>
                                </div>
                                <span class="portal-consultation-status badge badge-info"><?php echo _l('dietetic_consultation_scheduled'); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p class="text-muted"><?php echo _l('dietetic_no_consultations'); ?></p>
                <?php } ?>

                <a href="<?php echo site_url('dietetic/portal/consultations'); ?>" class="btn btn-sm btn-default mtop10">
                    <?php echo _l('view_all'); ?>
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (dietetic_get_option('enable_client_measurements', true)) { ?>
    <div class="mtop20">
        <a href="<?php echo site_url('dietetic/portal/measurements'); ?>" class="btn btn-info">
            <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_measurement'); ?>
        </a>
    </div>
<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(function() {
        <?php if (!empty($weight_evolution)) { ?>
            var labels = [<?php foreach ($weight_evolution as $m) { echo '"' . _d($m->measurement_date) . '",'; } ?>];
            var weights = [<?php foreach ($weight_evolution as $m) { echo $m->weight . ','; } ?>];
            var bmis = [<?php foreach ($weight_evolution as $m) { echo $m->bmi . ','; } ?>];

            if (typeof dietetic_portal !== 'undefined') {
                dietetic_portal.loadWeightChart('portalWeightChart', weights, bmis, labels);
            }
        <?php } ?>
    });
</script>

<?php $this->load->view('authentication/includes/footer'); ?>
