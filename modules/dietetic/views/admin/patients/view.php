<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<style>
/* Modern Patient View Styles */
:root {
    --primary-color: #01807B;
    --primary-dark: #015a57;
    --secondary-color: #F3911D;
    --weight-blue: #3498db;
    --current-green: #2ecc71;
    --target-orange: #F3911D;
    --bmi-red: #e74c3c;
}

/* Enhanced Panel Hover Effects */
.panel_s {
    transition: all 0.3s ease;
    border-radius: 10px;
}

.panel_s:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.12);
}

.panel-body {
    border-radius: 10px;
}

/* Stat Card Enhancements */
.stat-card-enhanced {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.stat-card-enhanced::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100px;
    height: 100px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transition: all 0.3s ease;
}

.stat-card-enhanced:hover::after {
    transform: scale(3);
}

.stat-card-enhanced:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* Table Enhancements */
.table-hover tbody tr {
    transition: all 0.2s ease;
}

.table-hover tbody tr:hover {
    background-color: #f8f9fa !important;
    transform: translateX(3px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

/* Button Enhancements */
.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

/* Info Box Enhancements */
.info-box {
    transition: all 0.3s ease;
    border-radius: 8px !important;
}

.info-box:hover {
    transform: translateX(3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Alert Enhancements */
.alert {
    border-radius: 8px !important;
    animation: slideInDown 0.5s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Chart Container */
#weightChart {
    border-radius: 8px;
}

/* Patient Header Enhancement */
.patient-header-enhanced {
    position: relative;
    overflow: hidden;
    border-radius: 10px !important;
}

.patient-header-enhanced::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

/* Panel Headers */
.panel_s h4 {
    position: relative;
    padding-left: 10px;
}

.panel_s h4::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 20px;
    background: currentColor;
    border-radius: 2px;
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Loading State for Chart */
.chart-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 300px;
    color: #95a5a6;
}

/* Upload Zone for Documents */
.upload-zone-documents {
    border: 3px dashed #4299e1;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: rgba(66, 153, 225, 0.05);
    margin-top: 12px;
}

.upload-zone-documents:hover {
    background: rgba(66, 153, 225, 0.1);
    border-color: #2b6cb0;
    transform: translateY(-2px);
}

.upload-zone-documents i {
    font-size: 48px;
    color: #4299e1;
    margin-bottom: 16px;
    display: block;
}

.upload-zone-documents p {
    margin: 0;
    color: #2d3748;
    font-weight: 600;
    font-size: 16px;
}

.upload-zone-documents small {
    color: #718096;
    display: block;
    margin-top: 8px;
}

.upload-zone-documents.uploading {
    opacity: 0.6;
    pointer-events: none;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Patient Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s patient-header-enhanced">
                    <div class="panel-body" style="background: linear-gradient(135deg, #01807B 0%, #015a57 100%); color: white; border-radius: 10px;">
                        <div class="row">
                            <div class="col-md-9">
                                <h2 style="color: white; margin-top: 10px;">
                                    <i class="fa fa-user-circle" style="font-size: 48px; vertical-align: middle; margin-right: 15px;"></i>
                                    <?php echo $patient->client->company; ?>
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin-bottom: 0;">
                                    <i class="fa fa-id-badge"></i> Patient #<?php echo $patient->id; ?> |
                                    <i class="fa fa-user-md"></i> <?php echo $patient->dietitian->firstname . ' ' . $patient->dietitian->lastname; ?>
                                </p>
                            </div>
                            <div class="col-md-3 text-right">
                                <?php if (dietetic_has_permission('edit')) { ?>
                                    <a href="<?php echo admin_url('dietetic/patients/edit/' . $patient->id); ?>" class="btn btn-light btn-lg" style="margin-top: 15px;">
                                        <i class="fa fa-pencil"></i> <?php echo _l('edit'); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column -->
            <div class="col-md-8">
                <!-- Stats Cards Row -->
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s stat-card-enhanced" style="border-left: 4px solid #3498db; border-radius: 10px;">
                            <div class="panel-body text-center">
                                <div style="font-size: 36px; color: #3498db; margin-bottom: 10px;">
                                    <i class="fa fa-balance-scale"></i>
                                </div>
                                <h4 style="margin: 0; color: #2c3e50;"><?php echo $patient->initial_weight ? $patient->initial_weight . ' kg' : '-'; ?></h4>
                                <p class="text-muted" style="margin: 5px 0 0 0;"><?php echo _l('dietetic_initial_weight'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s stat-card-enhanced" style="border-left: 4px solid #2ecc71; border-radius: 10px;">
                            <div class="panel-body text-center">
                                <div style="font-size: 36px; color: #2ecc71; margin-bottom: 10px;">
                                    <i class="fa fa-line-chart"></i>
                                </div>
                                <h4 style="margin: 0; color: #2c3e50;">
                                    <?php echo $patient->latest_measurement ? $patient->latest_measurement->weight . ' kg' : '-'; ?>
                                </h4>
                                <p class="text-muted" style="margin: 5px 0 0 0;"><?php echo _l('dietetic_current_weight'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s stat-card-enhanced" style="border-left: 4px solid #f39c12; border-radius: 10px;">
                            <div class="panel-body text-center">
                                <div style="font-size: 36px; color: #f39c12; margin-bottom: 10px;">
                                    <i class="fa fa-bullseye"></i>
                                </div>
                                <h4 style="margin: 0; color: #2c3e50;"><?php echo $patient->target_weight ? $patient->target_weight . ' kg' : '-'; ?></h4>
                                <p class="text-muted" style="margin: 5px 0 0 0;"><?php echo _l('dietetic_target_weight'); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6">
                        <div class="panel_s stat-card-enhanced" style="border-left: 4px solid #e74c3c; border-radius: 10px;">
                            <div class="panel-body text-center">
                                <div style="font-size: 36px; color: #e74c3c; margin-bottom: 10px;">
                                    <i class="fa fa-heartbeat"></i>
                                </div>
                                <h4 style="margin: 0; color: #2c3e50;">
                                    <?php echo $patient->latest_measurement && $patient->latest_measurement->bmi ? $patient->latest_measurement->bmi : '-'; ?>
                                </h4>
                                <p class="text-muted" style="margin: 5px 0 0 0;"><?php echo _l('dietetic_bmi'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Alert -->
                <?php if ($patient->latest_measurement && isset($weight_progress->weight_change) && $weight_progress->weight_change !== null) { ?>
                <div class="alert <?php echo $weight_progress->weight_change < 0 ? 'alert-success' : 'alert-warning'; ?>" style="border-left: 4px solid <?php echo $weight_progress->weight_change < 0 ? '#2ecc71' : '#f39c12'; ?>;">
                    <i class="fa fa-<?php echo $weight_progress->weight_change < 0 ? 'check-circle' : 'exclamation-triangle'; ?>" style="font-size: 20px; margin-right: 10px;"></i>
                    <strong>Progression du poids:</strong>
                    <?php echo ($weight_progress->weight_change > 0 ? '+' : '') . number_format($weight_progress->weight_change, 1); ?> kg
                    (<?php echo ($weight_progress->percentage_change > 0 ? '+' : '') . number_format($weight_progress->percentage_change, 1); ?>%)
                </div>
                <?php } ?>

                <!-- Patient Information -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 style="border-bottom: 3px solid #01807B; padding-bottom: 10px; margin-bottom: 20px;">
                            <i class="fa fa-info-circle" style="color: #01807B;"></i> <?php echo _l('dietetic_patient_profile'); ?>
                        </h4>

                        <div class="row">
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-venus-mars"></i> <?php echo _l('dietetic_gender'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;"><?php echo ucfirst($patient->gender); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-calendar"></i> <?php echo _l('dietetic_birth_date'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;"><?php echo _d($patient->birth_date); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-phone"></i> <?php echo _l('dietetic_phone'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;"><?php echo $patient->phone; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-envelope"></i> <?php echo _l('dietetic_email'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;"><?php echo $patient->email; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-running"></i> <?php echo _l('dietetic_activity_level'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;"><?php echo ucfirst(str_replace('_', ' ', $patient->activity_level)); ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-list-alt"></i> <?php echo _l('dietetic_active_programs'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;">
                                        <span class="label label-success" style="font-size: 13px;"><?php echo $patient->active_programs; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-arrows-v"></i> <?php echo _l('dietetic_height'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;"><?php echo $patient->height ? $patient->height . ' cm' : '-'; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-6">
                                <div style="margin-bottom: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                                    <label style="color: #7f8c8d; font-size: 12px; margin-bottom: 5px;">
                                        <i class="fa fa-check-circle"></i> <?php echo _l('dietetic_status'); ?>
                                    </label>
                                    <div class="value" style="font-size: 14px; font-weight: 500;"><?php echo dietetic_patient_status_badge($patient->status); ?></div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                            <h5 style="margin-top: 0; color: #856404;">
                                <i class="fa fa-bullseye"></i> <?php echo _l('dietetic_objective'); ?>
                            </h5>
                            <p style="margin-bottom: 0; color: #856404;"><?php echo nl2br($patient->objective); ?></p>
                        </div>

                        <?php if ($patient->medical_conditions || $patient->allergies) { ?>
                            <div class="row" style="margin-top: 20px;">
                                <?php if ($patient->medical_conditions) { ?>
                                    <div class="col-md-6">
                                        <div style="padding: 15px; background: #f8d7da; border-left: 4px solid #dc3545; border-radius: 5px;">
                                            <h5 style="margin-top: 0; color: #721c24;">
                                                <i class="fa fa-stethoscope"></i> <?php echo _l('dietetic_medical_conditions'); ?>
                                            </h5>
                                            <p style="margin-bottom: 0; color: #721c24;"><?php echo nl2br($patient->medical_conditions); ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($patient->allergies) { ?>
                                    <div class="col-md-6">
                                        <div style="padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 5px;">
                                            <h5 style="margin-top: 0; color: #856404;">
                                                <i class="fa fa-exclamation-triangle"></i> <?php echo _l('dietetic_allergies'); ?>
                                            </h5>
                                            <p style="margin-bottom: 0; color: #856404;"><?php echo nl2br($patient->allergies); ?></p>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Weight Evolution Chart -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 style="border-bottom: 3px solid #2ecc71; padding-bottom: 10px; margin-bottom: 20px;">
                            <i class="fa fa-area-chart" style="color: #2ecc71;"></i> <?php echo _l('dietetic_weight_evolution'); ?>
                        </h4>
                        <canvas id="weightChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Consultations -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left" style="border-bottom: 3px solid #3498db; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fa fa-calendar-check-o" style="color: #3498db;"></i> <?php echo _l('dietetic_consultations'); ?>
                            </h4>
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/consultations/create?patient_id=' . $patient->id); ?>" class="btn btn-success pull-right">
                                    <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_consultation'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (!empty($consultations)) { ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th><i class="fa fa-calendar"></i> <?php echo _l('dietetic_date'); ?></th>
                                        <th><i class="fa fa-tag"></i> <?php echo _l('dietetic_type'); ?></th>
                                        <th><i class="fa fa-info-circle"></i> <?php echo _l('dietetic_status'); ?></th>
                                        <th class="text-center"><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consultations as $consultation) { ?>
                                        <tr>
                                            <td><?php echo _dt($consultation->consultation_date); ?></td>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?></td>
                                            <td><?php echo dietetic_consultation_status_badge($consultation->status); ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>" class="btn btn-info btn-sm">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <?php echo _l('dietetic_no_consultations'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Programs -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left" style="border-bottom: 3px solid #01807B; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fa fa-list-alt" style="color: #01807B;"></i> <?php echo _l('dietetic_programs'); ?>
                            </h4>
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/programs/create?patient_id=' . $patient->id); ?>" class="btn btn-primary pull-right">
                                    <i class="fa fa-plus"></i> <?php echo _l('dietetic_add_program'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>
                        <?php if (!empty($programs)) { ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th><i class="fa fa-file-text"></i> <?php echo _l('dietetic_program_name'); ?></th>
                                        <th><i class="fa fa-calendar"></i> <?php echo _l('dietetic_start_date'); ?></th>
                                        <th><i class="fa fa-info-circle"></i> <?php echo _l('dietetic_status'); ?></th>
                                        <th class="text-center"><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($programs as $program) { ?>
                                        <tr>
                                            <td><strong><?php echo $program->program_name; ?></strong></td>
                                            <td><?php echo _d($program->start_date); ?></td>
                                            <td><?php echo dietetic_program_status_badge($program->status); ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo admin_url('dietetic/programs/view/' . $program->id); ?>" class="btn btn-info btn-sm">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <?php echo _l('dietetic_no_programs'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Food Surveys -->
                <?php if (!empty($food_surveys) || $this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left" style="border-bottom: 3px solid #01807B; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fa fa-clipboard-list" style="color: #01807B;"></i> Enquêtes Alimentaires
                            </h4>
                            <?php if (dietetic_has_permission('create')) { ?>
                                <a href="<?php echo admin_url('dietetic/food_surveys/create?patient_id=' . $patient->id); ?>" class="btn btn-primary pull-right" style="background: #01807B; border-color: #01807B;">
                                    <i class="fa fa-plus"></i> Nouvelle Enquête
                                </a>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>

                        <?php if (!empty($food_surveys)) { ?>
                            <table class="table table-hover">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th><i class="fa fa-tag"></i> Nom de l'enquête</th>
                                        <th><i class="fa fa-calendar"></i> Période</th>
                                        <th><i class="fa fa-chart-line"></i> Progression</th>
                                        <th><i class="fa fa-info-circle"></i> Statut</th>
                                        <th class="text-center"><i class="fa fa-cog"></i> Options</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($food_surveys as $survey) {
                                        $start = new DateTime($survey->start_date);
                                        $end = clone $start;
                                        $end->modify('+' . ($survey->duration_days - 1) . ' days');
                                        $today = new DateTime();

                                        // Calculate status
                                        if ($survey->status == 'completed') {
                                            $status_badge = '<span class="label label-success"><i class="fa fa-check-circle"></i> Terminée</span>';
                                        } elseif ($today > $end) {
                                            $status_badge = '<span class="label label-default"><i class="fa fa-calendar-times"></i> Expirée</span>';
                                        } elseif ($today < $start) {
                                            $status_badge = '<span class="label label-info"><i class="fa fa-clock"></i> À venir</span>';
                                        } else {
                                            $status_badge = '<span class="label label-warning"><i class="fa fa-hourglass-half"></i> En cours</span>';
                                        }

                                        // Calculate progress
                                        $entries_count = isset($survey->entries_count) ? $survey->entries_count : 0;
                                        $progress_percentage = ($survey->duration_days > 0) ? round(($entries_count / $survey->duration_days) * 100) : 0;
                                        $progress_color = $progress_percentage >= 80 ? '#2ecc71' : ($progress_percentage >= 50 ? '#f39c12' : '#e74c3c');
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($survey->survey_name); ?></strong>
                                                <?php if ($survey->program_id) { ?>
                                                    <br><small class="text-muted">
                                                        <i class="fa fa-link"></i> Programme: <?php echo htmlspecialchars($survey->program_name); ?>
                                                    </small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span style="white-space: nowrap;">
                                                    <?php echo _d($survey->start_date); ?>
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fa fa-arrow-right"></i> <?php echo _d($end->format('Y-m-d')); ?>
                                                    <span class="label label-default"><?php echo $survey->duration_days; ?> jours</span>
                                                </small>
                                            </td>
                                            <td>
                                                <div style="margin-bottom: 5px;">
                                                    <strong><?php echo $entries_count; ?>/<?php echo $survey->duration_days; ?></strong>
                                                    <small class="text-muted">entrées</small>
                                                </div>
                                                <div class="progress" style="height: 8px; margin-bottom: 0;">
                                                    <div class="progress-bar" role="progressbar"
                                                         style="width: <?php echo $progress_percentage; ?>%; background-color: <?php echo $progress_color; ?>;"
                                                         aria-valuenow="<?php echo $progress_percentage; ?>"
                                                         aria-valuemin="0"
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="text-muted"><?php echo $progress_percentage; ?>%</small>
                                            </td>
                                            <td><?php echo $status_badge; ?></td>
                                            <td class="text-center">
                                                <a href="<?php echo admin_url('dietetic/food_surveys/view/' . $survey->id); ?>"
                                                   class="btn btn-info btn-sm"
                                                   title="Voir les détails">
                                                    <i class="fa fa-eye"></i> Voir
                                                </a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>

                            <?php if (count($food_surveys) > 0) { ?>
                                <div class="text-right" style="margin-top: 15px;">
                                    <a href="<?php echo admin_url('dietetic/food_surveys?patient_id=' . $patient->id); ?>" class="btn btn-default btn-sm">
                                        <i class="fa fa-list"></i> Voir toutes les enquêtes
                                    </a>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-info" style="border-left: 4px solid #01807B;">
                                <i class="fa fa-info-circle"></i> Aucune enquête alimentaire pour ce patient.
                                <?php if (dietetic_has_permission('create')) { ?>
                                    <br><br>
                                    <a href="<?php echo admin_url('dietetic/food_surveys/create?patient_id=' . $patient->id); ?>"
                                       class="btn btn-sm"
                                       style="background: #01807B; border-color: #01807B; color: white;">
                                        <i class="fa fa-plus"></i> Créer la première enquête
                                    </a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php } ?>

                <!-- Documents Médicaux -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left" style="border-bottom: 3px solid #4299e1; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fa fa-file-text" style="color: #4299e1;"></i> Documents Médicaux
                            </h4>
                        </div>
                        <div class="clearfix"></div>

                        <?php if (dietetic_has_permission('edit')) { ?>
                        <!-- Upload Zone -->
                        <div class="upload-zone-documents" onclick="document.getElementById('medicalDocumentInput').click()">
                            <i class="fa fa-cloud-upload"></i>
                            <p>Cliquez ou glissez pour uploader des documents</p>
                            <small>PDF, images (max 10MB par fichier)</small>
                        </div>
                        <input type="file" id="medicalDocumentInput" accept=".pdf,.jpg,.jpeg,.png" style="display: none;">
                        <?php } ?>

                        <!-- Documents List -->
                        <div id="documentsList" style="margin-top: 20px;">
                            <?php if (!empty($documents)) { ?>
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background: #f8f9fa;">
                                            <th><i class="fa fa-file"></i> Nom du fichier</th>
                                            <th><i class="fa fa-calendar"></i> Date d'upload</th>
                                            <th><i class="fa fa-hdd-o"></i> Taille</th>
                                            <th class="text-center"><i class="fa fa-cog"></i> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($documents as $doc) {
                                            $file_icon = 'fa-file-o';
                                            if ($doc->file_type == 'application/pdf') {
                                                $file_icon = 'fa-file-pdf-o';
                                            } elseif (strpos($doc->file_type, 'image') !== false) {
                                                $file_icon = 'fa-file-image-o';
                                            }

                                            $file_size = $doc->file_size;
                                            $size_formatted = $file_size < 1024 ? $file_size . ' B' :
                                                            ($file_size < 1048576 ? round($file_size / 1024, 2) . ' KB' :
                                                            round($file_size / 1048576, 2) . ' MB');
                                        ?>
                                            <tr data-document-id="<?php echo $doc->id; ?>">
                                                <td>
                                                    <i class="fa <?php echo $file_icon; ?>" style="color: #4299e1; margin-right: 8px;"></i>
                                                    <strong><?php echo htmlspecialchars($doc->original_filename); ?></strong>
                                                </td>
                                                <td><?php echo date('d/m/Y à H:i', strtotime($doc->uploaded_at)); ?></td>
                                                <td><?php echo $size_formatted; ?></td>
                                                <td class="text-center">
                                                    <a href="<?php echo admin_url('dietetic/patients/download_document/' . $doc->id); ?>"
                                                       class="btn btn-info btn-sm"
                                                       title="Télécharger">
                                                        <i class="fa fa-download"></i>
                                                    </a>
                                                    <?php if (dietetic_has_permission('delete')) { ?>
                                                        <button type="button"
                                                                class="btn btn-danger btn-sm delete-document"
                                                                data-document-id="<?php echo $doc->id; ?>"
                                                                title="Supprimer">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            <?php } else { ?>
                                <div class="alert alert-info" style="border-left: 4px solid #4299e1;">
                                    <i class="fa fa-info-circle"></i> Aucun document médical pour ce patient.
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-4">
                <!-- Quick Actions -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 style="border-bottom: 3px solid #F3911D; padding-bottom: 10px; margin-bottom: 15px;">
                            <i class="fa fa-bolt" style="color: #F3911D;"></i> Actions Rapides
                        </h4>
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/measurements/create?patient_id=' . $patient->id); ?>" class="btn btn-success btn-block btn-lg" style="margin-bottom: 10px;">
                                <i class="fa fa-plus-circle"></i> Ajouter une Mesure
                            </a>
                            <a href="<?php echo admin_url('dietetic/consultations/create?patient_id=' . $patient->id); ?>" class="btn btn-info btn-block btn-lg" style="margin-bottom: 10px;">
                                <i class="fa fa-calendar-plus-o"></i> Nouvelle Consultation
                            </a>
                            <a href="<?php echo admin_url('dietetic/programs/create?patient_id=' . $patient->id); ?>" class="btn btn-primary btn-block btn-lg" style="margin-bottom: 10px;">
                                <i class="fa fa-file-text-o"></i> Nouveau Programme
                            </a>
                            <?php if ($this->db->table_exists(db_prefix() . 'dietic_food_surveys')) { ?>
                                <a href="<?php echo admin_url('dietetic/food_surveys/create?patient_id=' . $patient->id); ?>" class="btn btn-block btn-lg" style="background: #01807B; border-color: #01807B; color: white;">
                                    <i class="fa fa-clipboard-list"></i> Nouvelle Enquête Alimentaire
                                </a>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>

                <!-- Assigned Dietitians -->
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left" style="border-bottom: 3px solid #16a085; padding-bottom: 10px; margin-bottom: 15px;">
                                <i class="fa fa-users" style="color: #16a085;"></i> Diététiciens Assignés
                            </h4>
                            <?php if ((is_admin() || dietetic_can_access_patient($patient->id)) && isset($patient->dietitians_count)) { ?>
                                <button class="btn btn-success btn-xs pull-right" onclick="openAssignDietitianModal()" style="margin-top: 8px;">
                                    <i class="fa fa-plus"></i> Assigner
                                </button>
                            <?php } ?>
                        </div>
                        <div class="clearfix"></div>

                        <?php if (isset($patient->dietitians) && !empty($patient->dietitians)) { ?>
                            <?php foreach ($patient->dietitians as $dietitian) { ?>
                                <div style="margin-bottom: 12px; padding: 12px; background: #f8f9fa; border-left: 4px solid <?php echo $dietitian->is_primary ? '#16a085' : '#95a5a6'; ?>; border-radius: 5px;">
                                    <div class="row">
                                        <div class="col-xs-<?php echo (is_admin() || dietetic_can_access_patient($patient->id)) ? '8' : '12'; ?>">
                                            <strong style="color: #2c3e50;">
                                                <i class="fa fa-user-md"></i> <?php echo $dietitian->firstname . ' ' . $dietitian->lastname; ?>
                                                <?php if ($dietitian->is_primary) { ?>
                                                    <span class="label label-success">Principal</span>
                                                <?php } ?>
                                            </strong><br />
                                            <span style="font-size: 12px; color: #7f8c8d;">
                                                <i class="fa fa-envelope"></i> <?php echo $dietitian->email; ?><br />
                                                <?php if ($dietitian->phonenumber) { ?>
                                                    <i class="fa fa-phone"></i> <?php echo $dietitian->phonenumber; ?><br />
                                                <?php } ?>
                                                <i class="fa fa-calendar"></i> Depuis: <?php echo _dt($dietitian->assigned_date); ?>
                                            </span>
                                            <?php if ($dietitian->notes) { ?>
                                                <br /><small class="text-muted"><i class="fa fa-sticky-note-o"></i> <?php echo $dietitian->notes; ?></small>
                                            <?php } ?>
                                        </div>
                                        <?php if (is_admin() || dietetic_can_access_patient($patient->id)) { ?>
                                            <div class="col-xs-4 text-right">
                                                <?php if (!$dietitian->is_primary) { ?>
                                                    <button class="btn btn-info btn-xs" onclick="setPrimaryDietitian(<?php echo $dietitian->dietitian_id; ?>)" title="Définir comme principal" style="margin-bottom: 3px;">
                                                        <i class="fa fa-star"></i>
                                                    </button>
                                                <?php } ?>
                                                <button class="btn btn-danger btn-xs" onclick="removeDietitian(<?php echo $dietitian->dietitian_id; ?>)" title="Retirer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> Aucun diététicien assigné.
                                <?php if (is_admin()) { ?>
                                    <a href="#" onclick="openAssignDietitianModal(); return false;">Assigner un diététicien</a>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Latest Measurements -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 style="border-bottom: 3px solid #e74c3c; padding-bottom: 10px; margin-bottom: 15px;">
                            <i class="fa fa-history" style="color: #e74c3c;"></i> Mesures Récentes
                        </h4>

                        <?php if (!empty($measurements)) { ?>
                            <?php foreach (array_slice($measurements, 0, 5) as $measurement) { ?>
                                <div style="margin-bottom: 15px; padding: 12px; background: #f8f9fa; border-left: 4px solid #e74c3c; border-radius: 5px;">
                                    <div class="row">
                                        <div class="col-xs-8">
                                            <strong style="color: #2c3e50;">
                                                <i class="fa fa-calendar"></i> <?php echo _d($measurement->measurement_date); ?>
                                            </strong><br />
                                            <span style="font-size: 13px; color: #7f8c8d;">
                                                <i class="fa fa-balance-scale"></i> <?php echo $measurement->weight; ?> kg |
                                                <i class="fa fa-heartbeat"></i> IMC: <?php echo $measurement->bmi; ?>
                                            </span>
                                            <?php if ($measurement->notes) { ?>
                                                <br /><small class="text-muted"><i class="fa fa-sticky-note-o"></i> <?php echo substr($measurement->notes, 0, 50) . (strlen($measurement->notes) > 50 ? '...' : ''); ?></small>
                                            <?php } ?>
                                        </div>
                                        <div class="col-xs-4 text-right">
                                            <?php if (dietetic_has_permission('edit')) { ?>
                                                <a href="<?php echo admin_url('dietetic/measurements/edit/' . $measurement->id); ?>" class="btn btn-default btn-xs" style="margin-bottom: 3px;">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            <?php } ?>
                                            <?php if (dietetic_has_permission('delete')) { ?>
                                                <a href="<?php echo admin_url('dietetic/measurements/delete/' . $measurement->id); ?>"
                                                   class="btn btn-danger btn-xs _delete"
                                                   onclick="return confirm('<?php echo _l('confirm_action_prompt'); ?>');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> <?php echo _l('dietetic_no_data'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Assign Dietitian -->
<div class="modal fade" id="assignDietitianModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-user-md"></i> Assigner un Diététicien</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="dietitian_select">Sélectionner un Diététicien</label>
                    <select class="form-control selectpicker" id="dietitian_select" data-live-search="true">
                        <option value="">-- Choisir un diététicien --</option>
                        <?php
                        // Load all staff members with dietetic role
                        $this->load->model('staff_model');
                        $all_staff = $this->staff_model->get('', ['active' => 1]);
                        foreach ($all_staff as $staff_member) {
                            echo '<option value="' . $staff_member['staffid'] . '">' . $staff_member['firstname'] . ' ' . $staff_member['lastname'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="set_as_primary"> Définir comme diététicien principal
                    </label>
                </div>
                <div class="form-group">
                    <label for="assignment_notes">Notes (optionnel)</label>
                    <textarea class="form-control" id="assignment_notes" rows="3" placeholder="Notes sur cette assignation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-success" onclick="assignDietitian()">
                    <i class="fa fa-check"></i> Assigner
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $(function() {
        dietetic.loadWeightChart(<?php echo $patient->id; ?>, 'weightChart');

        // Initialize selectpicker if available
        if ($.fn.selectpicker) {
            $('.selectpicker').selectpicker();
        }

        // Fix menu toggle
        setTimeout(function() {
            $('#side-menu a[href*="dietetic"]').first().parent().find('> a').on('click', function(e) {
                var $submenu = $(this).next('ul');
                if ($submenu.length > 0) {
                    e.preventDefault();
                    $submenu.toggleClass('in');
                    $(this).parent().toggleClass('active');
                }
            });
        }, 500);
    });

    // Open assign dietitian modal
    function openAssignDietitianModal() {
        $('#assignDietitianModal').modal('show');
    }

    // Assign dietitian to patient
    function assignDietitian() {
        var dietitian_id = $('#dietitian_select').val();
        var is_primary = $('#set_as_primary').is(':checked') ? 1 : 0;
        var notes = $('#assignment_notes').val();

        if (!dietitian_id) {
            alert('Veuillez sélectionner un diététicien');
            return;
        }

        $.post('<?php echo admin_url('dietetic/patients/assign_dietitian/' . $patient->id); ?>', {
            dietitian_id: dietitian_id,
            is_primary: is_primary,
            notes: notes
        }, function(response) {
            if (response.success) {
                alert_float('success', response.message);
                location.reload();
            } else {
                alert_float('danger', response.message);
            }
        }, 'json');
    }

    // Set dietitian as primary
    function setPrimaryDietitian(dietitian_id) {
        if (confirm('Définir ce diététicien comme principal ?')) {
            $.post('<?php echo admin_url('dietetic/patients/set_primary_dietitian/' . $patient->id); ?>', {
                dietitian_id: dietitian_id
            }, function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
    }

    // Remove dietitian from patient
    function removeDietitian(dietitian_id) {
        if (confirm('Retirer ce diététicien du patient ?')) {
            $.post('<?php echo admin_url('dietetic/patients/remove_dietitian/' . $patient->id); ?>', {
                dietitian_id: dietitian_id
            }, function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                }
            }, 'json');
        }
    }

    // Document Upload Handler
    $('#medicalDocumentInput').on('change', function(e) {
        var file = e.target.files[0];

        if (!file) return;

        // Validate file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            alert_float('danger', 'Le fichier dépasse 10MB');
            this.value = '';
            return;
        }

        // Validate file type
        var allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        if (allowedTypes.indexOf(file.type) === -1) {
            alert_float('danger', 'Type de fichier non autorisé (PDF ou images uniquement)');
            this.value = '';
            return;
        }

        // Show uploading state
        $('.upload-zone-documents').addClass('uploading');
        $('.upload-zone-documents p').text('Upload en cours...');

        // Upload file
        var formData = new FormData();
        formData.append('document', file);

        $.ajax({
            url: '<?php echo admin_url('dietetic/patients/upload_document/' . $patient->id); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                    location.reload();
                } else {
                    alert_float('danger', response.message);
                    $('.upload-zone-documents').removeClass('uploading');
                    $('.upload-zone-documents p').text('Cliquez ou glissez pour uploader des documents');
                }
            },
            error: function() {
                alert_float('danger', 'Erreur lors de l\'upload du document');
                $('.upload-zone-documents').removeClass('uploading');
                $('.upload-zone-documents p').text('Cliquez ou glissez pour uploader des documents');
            }
        });

        // Reset input
        this.value = '';
    });

    // Drag and drop support
    $('.upload-zone-documents').on('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('background', 'rgba(66, 153, 225, 0.15)');
    });

    $('.upload-zone-documents').on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('background', 'rgba(66, 153, 225, 0.05)');
    });

    $('.upload-zone-documents').on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).css('background', 'rgba(66, 153, 225, 0.05)');

        var files = e.originalEvent.dataTransfer.files;
        if (files.length > 0) {
            $('#medicalDocumentInput')[0].files = files;
            $('#medicalDocumentInput').trigger('change');
        }
    });

    // Delete document handler
    $(document).on('click', '.delete-document', function() {
        var documentId = $(this).data('document-id');
        var $row = $(this).closest('tr');

        if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
            $.ajax({
                url: '<?php echo admin_url('dietetic/patients/delete_document/'); ?>' + documentId,
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert_float('success', response.message);
                        $row.fadeOut(300, function() {
                            $(this).remove();

                            // Check if table is empty
                            if ($('#documentsList tbody tr').length === 0) {
                                location.reload();
                            }
                        });
                    } else {
                        alert_float('danger', response.message);
                    }
                },
                error: function() {
                    alert_float('danger', 'Erreur lors de la suppression du document');
                }
            });
        }
    });
</script>

<?php init_tail(); ?>
