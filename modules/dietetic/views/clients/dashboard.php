<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <i class="fa fa-heartbeat"></i> My Dietetic Program
        </h4>
    </div>
    <div class="panel-body">
        <?php if (isset($no_patient) && $no_patient) { ?>
            <div class="alert alert-info">
                <i class="fa fa-info-circle"></i>
                You don't have a dietetic program yet. Please contact your dietitian.
            </div>
        <?php } else { ?>

            <!-- Weight Stats -->
            <div class="row">
                <div class="col-md-3">
                    <div class="well text-center">
                        <h4>Current Weight</h4>
                        <h2><?php echo isset($latest_measurement) && $latest_measurement ? $latest_measurement->weight . ' kg' : '-'; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="well text-center">
                        <h4>Target Weight</h4>
                        <h2><?php echo isset($patient->target_weight) ? $patient->target_weight . ' kg' : '-'; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="well text-center">
                        <h4>BMI</h4>
                        <h2><?php echo isset($latest_measurement) && $latest_measurement && $latest_measurement->bmi ? $latest_measurement->bmi : '-'; ?></h2>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="well text-center">
                        <h4>Progress</h4>
                        <h2><?php echo isset($weight_progress->weight_change) && $weight_progress->weight_change !== null ? ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1) . ' kg' : '-'; ?></h2>
                    </div>
                </div>
            </div>

            <!-- Active Program -->
            <?php if (isset($active_program) && $active_program) { ?>
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($active_program->program_name); ?>
                        </h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Start Date:</strong> <?php echo date('d/m/Y', strtotime($active_program->start_date)); ?></p>
                                <?php if ($active_program->end_date) { ?>
                                    <p><strong>End Date:</strong> <?php echo date('d/m/Y', strtotime($active_program->end_date)); ?></p>
                                <?php } ?>
                            </div>
                            <div class="col-md-6">
                                <?php if ($active_program->daily_calories) { ?>
                                    <p><strong>Daily Calories:</strong> <?php echo $active_program->daily_calories; ?> kcal</p>
                                <?php } ?>
                                <?php if ($active_program->daily_protein) { ?>
                                    <p><strong>Protein:</strong> <?php echo $active_program->daily_protein; ?>g</p>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($active_program->objective) { ?>
                            <hr>
                            <p><strong>Objective:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($active_program->objective)); ?></p>
                        <?php } ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    No active program at the moment.
                </div>
            <?php } ?>

            <!-- Upcoming Consultations -->
            <?php if (!empty($upcoming_consultations)) { ?>
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <i class="fa fa-calendar"></i> Upcoming Consultations
                        </h4>
                    </div>
                    <div class="panel-body">
                        <ul class="list-unstyled">
                            <?php foreach ($upcoming_consultations as $consultation) { ?>
                                <li class="mb-2">
                                    <i class="fa fa-calendar-o"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($consultation->consultation_date)); ?>
                                    - <?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            <?php } ?>

        <?php } ?>
    </div>
</div>
