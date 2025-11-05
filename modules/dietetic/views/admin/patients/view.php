<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Patient Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px;">
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
                        <div class="panel_s" style="border-left: 4px solid #3498db;">
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
                        <div class="panel_s" style="border-left: 4px solid #2ecc71;">
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
                        <div class="panel_s" style="border-left: 4px solid #f39c12;">
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
                        <div class="panel_s" style="border-left: 4px solid #e74c3c;">
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
                        <h4 style="border-bottom: 3px solid #667eea; padding-bottom: 10px; margin-bottom: 20px;">
                            <i class="fa fa-info-circle" style="color: #667eea;"></i> <?php echo _l('dietetic_patient_profile'); ?>
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
                            <h4 class="pull-left" style="border-bottom: 3px solid #9b59b6; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fa fa-list-alt" style="color: #9b59b6;"></i> <?php echo _l('dietetic_programs'); ?>
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
            </div>

            <!-- Right Sidebar -->
            <div class="col-md-4">
                <!-- Quick Actions -->
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 style="border-bottom: 3px solid #f39c12; padding-bottom: 10px; margin-bottom: 15px;">
                            <i class="fa fa-bolt" style="color: #f39c12;"></i> Actions Rapides
                        </h4>
                        <?php if (dietetic_has_permission('create')) { ?>
                            <a href="<?php echo admin_url('dietetic/measurements/create?patient_id=' . $patient->id); ?>" class="btn btn-success btn-block btn-lg" style="margin-bottom: 10px;">
                                <i class="fa fa-plus-circle"></i> Ajouter une Mesure
                            </a>
                            <a href="<?php echo admin_url('dietetic/consultations/create?patient_id=' . $patient->id); ?>" class="btn btn-info btn-block btn-lg" style="margin-bottom: 10px;">
                                <i class="fa fa-calendar-plus-o"></i> Nouvelle Consultation
                            </a>
                            <a href="<?php echo admin_url('dietetic/programs/create?patient_id=' . $patient->id); ?>" class="btn btn-primary btn-block btn-lg">
                                <i class="fa fa-file-text-o"></i> Nouveau Programme
                            </a>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    $(function() {
        dietetic.loadWeightChart(<?php echo $patient->id; ?>, 'weightChart');
    });
</script>

<?php init_tail(); ?>
