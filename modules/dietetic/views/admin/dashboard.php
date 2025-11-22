<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter les erreurs CSRF -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Dashboard Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 8px; padding: 30px;">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 style="color: white; margin-top: 0;">
                                    <i class="fa fa-tachometer" style="font-size: 42px; vertical-align: middle; margin-right: 15px;"></i>
                                    <?php echo _l('dietetic_dashboard'); ?>
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin-bottom: 0; font-size: 16px;">
                                    <i class="fa fa-calendar"></i> <?php echo date('l, F j, Y'); ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-right" style="padding-top: 20px;">
                                <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-light btn-lg" style="box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                    <i class="fa fa-user-plus"></i> Créer un Patient
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #2ecc71; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <div style="font-size: 48px; color: #2ecc71; margin-bottom: 15px;">
                            <i class="fa fa-users"></i>
                        </div>
                        <h2 style="margin: 10px 0; color: #2c3e50; font-size: 36px; font-weight: bold;">
                            <?php echo isset($patient_stats->active_patients) ? $patient_stats->active_patients : 0; ?>
                        </h2>
                        <p style="color: #7f8c8d; font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                            <?php echo _l('dietetic_active_patients'); ?>
                        </p>
                        <div style="margin-top: 10px;">
                            <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-success btn-sm">
                                <i class="fa fa-arrow-right"></i> Voir Tous les Patients
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #3498db; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <div style="font-size: 48px; color: #3498db; margin-bottom: 15px;">
                            <i class="fa fa-calendar-check-o"></i>
                        </div>
                        <h2 style="margin: 10px 0; color: #2c3e50; font-size: 36px; font-weight: bold;">
                            <?php echo isset($consultation_stats->scheduled) ? $consultation_stats->scheduled : 0; ?>
                        </h2>
                        <p style="color: #7f8c8d; font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                            Consultations Planifiées
                        </p>
                        <div style="margin-top: 10px;">
                            <a href="<?php echo admin_url('dietetic/consultations'); ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-arrow-right"></i> Voir les Consultations
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #9b59b6; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <div style="font-size: 48px; color: #9b59b6; margin-bottom: 15px;">
                            <i class="fa fa-list-alt"></i>
                        </div>
                        <h2 style="margin: 10px 0; color: #2c3e50; font-size: 36px; font-weight: bold;">
                            <?php echo isset($program_stats->active_programs) ? $program_stats->active_programs : 0; ?>
                        </h2>
                        <p style="color: #7f8c8d; font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                            Programmes Actifs
                        </p>
                        <div style="margin-top: 10px;">
                            <a href="<?php echo admin_url('dietetic/programs'); ?>" class="btn btn-primary btn-sm" style="background: #9b59b6; border-color: #9b59b6;">
                                <i class="fa fa-arrow-right"></i> Voir les Programmes
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #e67e22; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 25px;">
                        <div style="font-size: 48px; color: #e67e22; margin-bottom: 15px;">
                            <i class="fa fa-heartbeat"></i>
                        </div>
                        <h2 style="margin: 10px 0; color: #2c3e50; font-size: 36px; font-weight: bold;">
                            <?php echo isset($consultation_stats->avg_satisfaction) ? number_format($consultation_stats->avg_satisfaction, 1) : 0; ?>/5
                        </h2>
                        <p style="color: #7f8c8d; font-size: 14px; margin: 0; text-transform: uppercase; letter-spacing: 1px;">
                            Satisfaction Client
                        </p>
                        <div style="margin-top: 10px;">
                            <span class="label" style="background: #e67e22; font-size: 11px; padding: 5px 10px;">
                                <?php
                                $stars = isset($consultation_stats->avg_satisfaction) ? round($consultation_stats->avg_satisfaction) : 0;
                                for ($i = 0; $i < $stars; $i++) {
                                    echo '<i class="fa fa-star"></i> ';
                                }
                                for ($i = $stars; $i < 5; $i++) {
                                    echo '<i class="fa fa-star-o"></i> ';
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Upcoming Consultations -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left" style="border-bottom: 3px solid #3498db; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fa fa-calendar-check-o" style="color: #3498db;"></i> <?php echo _l('dietetic_upcoming_consultations'); ?>
                            </h4>
                        </div>
                        <div class="clearfix"></div>

                        <?php if (!empty($upcoming_consultations)) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background: #f8f9fa;">
                                            <th><i class="fa fa-user"></i> <?php echo _l('dietetic_patient'); ?></th>
                                            <th><i class="fa fa-clock-o"></i> <?php echo _l('dietetic_date'); ?></th>
                                            <th><i class="fa fa-tag"></i> <?php echo _l('dietetic_type'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcoming_consultations as $consultation) { ?>
                                            <tr style="cursor: pointer;" onclick="window.location='<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>'">
                                                <td>
                                                    <strong><?php echo isset($consultation->client_name) ? $consultation->client_name : 'N/A'; ?></strong>
                                                </td>
                                                <td>
                                                    <span style="color: #3498db;">
                                                        <i class="fa fa-calendar"></i> <?php echo isset($consultation->consultation_date) ? _dt($consultation->consultation_date) : 'N/A'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="label label-info">
                                                        <?php echo isset($consultation->consultation_type) ? ucfirst(str_replace('_', ' ', $consultation->consultation_type)) : 'N/A'; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center" style="margin-top: 15px;">
                                <a href="<?php echo admin_url('dietetic/consultations'); ?>" class="btn btn-info">
                                    <i class="fa fa-calendar-check-o"></i> Voir Toutes les Consultations
                                </a>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Aucune consultation planifiée pour le moment.
                            </div>
                            <div class="text-center">
                                <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-success">
                                    <i class="fa fa-calendar-plus-o"></i> Planifier une Consultation
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Recent Patients -->
            <div class="col-md-6">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="clearfix">
                            <h4 class="pull-left" style="border-bottom: 3px solid #2ecc71; padding-bottom: 10px; margin-bottom: 20px;">
                                <i class="fa fa-user-plus" style="color: #2ecc71;"></i> <?php echo _l('dietetic_recent_patients'); ?>
                            </h4>
                        </div>
                        <div class="clearfix"></div>

                        <?php if (!empty($recent_patients)) { ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background: #f8f9fa;">
                                            <th><i class="fa fa-user"></i> <?php echo _l('dietetic_client_name'); ?></th>
                                            <th><i class="fa fa-info-circle"></i> <?php echo _l('dietetic_status'); ?></th>
                                            <th class="text-center"><i class="fa fa-cog"></i> Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_patients as $patient) { ?>
                                            <tr style="cursor: pointer;" onclick="window.location='<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>'">
                                                <td>
                                                    <i class="fa fa-user-circle" style="color: #3498db; margin-right: 5px;"></i>
                                                    <strong><?php echo isset($patient->client_name) ? $patient->client_name : 'N/A'; ?></strong>
                                                </td>
                                                <td><?php echo isset($patient->status) ? dietetic_patient_status_badge($patient->status) : 'N/A'; ?></td>
                                                <td class="text-center">
                                                    <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>" class="btn btn-info btn-xs" onclick="event.stopPropagation();">
                                                        <i class="fa fa-folder-open"></i> Ouvrir
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center" style="margin-top: 15px;">
                                <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-success">
                                    <i class="fa fa-users"></i> Voir Tous les Patients
                                </a>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Aucun patient enregistré pour le moment.
                            </div>
                            <div class="text-center">
                                <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-success">
                                    <i class="fa fa-user-plus"></i> Créer un Nouveau Patient
                                </a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 style="border-bottom: 3px solid #f39c12; padding-bottom: 10px; margin-bottom: 20px;">
                            <i class="fa fa-bolt" style="color: #f39c12;"></i> Actions Rapides
                        </h4>

                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-success btn-lg btn-block" style="padding: 20px; margin-bottom: 15px;">
                                    <i class="fa fa-user-plus" style="font-size: 28px; display: block; margin-bottom: 10px;"></i>
                                    <strong>Créer un Patient</strong>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-info btn-lg btn-block" style="padding: 20px; margin-bottom: 15px;">
                                    <i class="fa fa-stethoscope" style="font-size: 28px; display: block; margin-bottom: 10px;"></i>
                                    <strong>Planifier une Consultation</strong>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('dietetic/programs/create'); ?>" class="btn btn-lg btn-block" style="background: #9b59b6; border-color: #9b59b6; color: white; padding: 20px; margin-bottom: 15px;">
                                    <i class="fa fa-clipboard" style="font-size: 28px; display: block; margin-bottom: 10px;"></i>
                                    <strong>Créer un Programme</strong>
                                </a>
                            </div>

                            <div class="col-md-3 col-sm-6">
                                <a href="<?php echo admin_url('dietetic/foods'); ?>" class="btn btn-warning btn-lg btn-block" style="padding: 20px; margin-bottom: 15px;">
                                    <i class="fa fa-database" style="font-size: 28px; display: block; margin-bottom: 10px;"></i>
                                    <strong>Base de Données Alimentaire</strong>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Hover effects for stat cards */
    .panel_s:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Hover effects for tables */
    .table-hover tbody tr:hover {
        background-color: #f8f9fa !important;
        cursor: pointer;
    }

    /* Button hover effects */
    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
</style>

<?php init_tail(); ?>
