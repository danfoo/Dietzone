<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white; border-radius: 8px; padding: 25px;">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 style="color: white; margin-top: 0;">
                                    <i class="fa fa-users" style="font-size: 42px; vertical-align: middle; margin-right: 15px;"></i>
                                    Gestion des Patients
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin-bottom: 0; font-size: 16px;">
                                    <i class="fa fa-heartbeat"></i> Suivez et gérez tous vos patients diététiques
                                </p>
                            </div>
                            <div class="col-md-4 text-right" style="padding-top: 15px;">
                                <?php if (dietetic_has_permission('create')) { ?>
                                    <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-light btn-lg">
                                        <i class="fa fa-user-plus"></i> Nouveau Patient
                                    </a>
                                <?php } ?>
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
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #2ecc71; margin-bottom: 10px;">
                            <i class="fa fa-users"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php echo count($patients); ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Total Patients
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #3498db; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #3498db; margin-bottom: 10px;">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php
                            $active_count = 0;
                            foreach ($patients as $p) {
                                if ($p->status === 'active') $active_count++;
                            }
                            echo $active_count;
                            ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Patients Actifs
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #f39c12; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #f39c12; margin-bottom: 10px;">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php
                            $today_count = 0;
                            foreach ($patients as $p) {
                                if (date('Y-m-d', strtotime($p->created_at)) === date('Y-m-d')) {
                                    $today_count++;
                                }
                            }
                            echo $today_count;
                            ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Nouveaux Aujourd'hui
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #9b59b6; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #9b59b6; margin-bottom: 10px;">
                            <i class="fa fa-user-md"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php
                            $dietitians = array();
                            foreach ($patients as $p) {
                                if (!in_array($p->dietitian_name, $dietitians)) {
                                    $dietitians[] = $p->dietitian_name;
                                }
                            }
                            echo count($dietitians);
                            ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Diététiciens
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Patients Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="border-bottom: 3px solid #2ecc71; padding-bottom: 10px; margin-bottom: 20px;">
                            <h4 style="margin: 0;">
                                <i class="fa fa-list" style="color: #2ecc71;"></i> Liste des Patients
                            </h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover dietetic-table" id="patients-table" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th><i class="fa fa-user" style="color: #2ecc71;"></i> <?php echo _l('dietetic_client_name'); ?></th>
                                        <th><i class="fa fa-user-md" style="color: #9b59b6;"></i> <?php echo _l('dietetic_dietitian'); ?></th>
                                        <th><i class="fa fa-info-circle" style="color: #3498db;"></i> <?php echo _l('dietetic_status'); ?></th>
                                        <th><i class="fa fa-balance-scale" style="color: #e67e22;"></i> <?php echo _l('dietetic_weight'); ?></th>
                                        <th><i class="fa fa-heartbeat" style="color: #e74c3c;"></i> <?php echo _l('dietetic_bmi'); ?></th>
                                        <th><i class="fa fa-running" style="color: #f39c12;"></i> <?php echo _l('dietetic_activity_level'); ?></th>
                                        <th><i class="fa fa-clock-o" style="color: #95a5a6;"></i> <?php echo _l('created_at'); ?></th>
                                        <th class="text-center"><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($patients as $patient) { ?>
                                        <tr style="cursor: pointer;" onclick="window.location='<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>'">
                                            <td>
                                                <i class="fa fa-user-circle" style="color: #2ecc71; margin-right: 5px; font-size: 16px;"></i>
                                                <strong style="color: #2c3e50;"><?php echo $patient->client_name; ?></strong>
                                            </td>
                                            <td>
                                                <span style="color: #7f8c8d;">
                                                    <i class="fa fa-stethoscope" style="color: #9b59b6; margin-right: 3px;"></i>
                                                    <?php echo $patient->dietitian_name; ?>
                                                </span>
                                            </td>
                                            <td><?php echo dietetic_patient_status_badge($patient->status); ?></td>
                                            <td>
                                                <?php if ($patient->initial_weight) { ?>
                                                    <span class="badge" style="background: #e67e22; font-size: 11px; padding: 4px 8px;">
                                                        <?php echo $patient->initial_weight; ?> kg
                                                    </span>
                                                <?php } else { ?>
                                                    <span style="color: #bdc3c7;">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($patient->initial_weight && $patient->height) {
                                                    $bmi = dietetic_calculate_bmi($patient->initial_weight, $patient->height);
                                                    $bmi_color = '#27ae60';
                                                    if ($bmi < 18.5) $bmi_color = '#3498db';
                                                    elseif ($bmi >= 25 && $bmi < 30) $bmi_color = '#f39c12';
                                                    elseif ($bmi >= 30) $bmi_color = '#e74c3c';
                                                ?>
                                                    <span style="color: <?php echo $bmi_color; ?>; font-weight: 600;">
                                                        <?php echo $bmi; ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span style="color: #bdc3c7;">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if ($patient->activity_level) { ?>
                                                    <span class="label" style="background: #f39c12; font-size: 10px; padding: 4px 8px;">
                                                        <?php echo ucfirst(str_replace('_', ' ', $patient->activity_level)); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <span style="color: #bdc3c7;">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <small style="color: #7f8c8d;">
                                                    <?php echo _dt($patient->created_at); ?>
                                                </small>
                                            </td>
                                            <td class="text-center" onclick="event.stopPropagation();">
                                                <div class="btn-group">
                                                    <a href="<?php echo admin_url('dietetic/patients/view/' . $patient->id); ?>" class="btn btn-info btn-sm" title="Voir">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if (dietetic_has_permission('edit')) { ?>
                                                        <a href="<?php echo admin_url('dietetic/patients/edit/' . $patient->id); ?>" class="btn btn-default btn-sm" title="Modifier">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if (dietetic_has_permission('delete')) { ?>
                                                        <a href="#" onclick="dietetic.deleteConfirm('<?php echo admin_url('dietetic/patients/delete/' . $patient->id); ?>', function() { location.reload(); }); return false;" class="btn btn-danger btn-sm" title="Supprimer">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php } ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: #ecf0f1; border-left: 4px solid #2ecc71;">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 style="margin-top: 0; color: #2c3e50;">
                                    <i class="fa fa-lightbulb-o" style="color: #2ecc71;"></i> Actions Rapides
                                </h5>
                                <div class="btn-group" role="group">
                                    <?php if (dietetic_has_permission('create')) { ?>
                                        <a href="<?php echo admin_url('dietetic/patients/create'); ?>" class="btn btn-success" style="margin-right: 10px;">
                                            <i class="fa fa-user-plus"></i> Ajouter un Patient
                                        </a>
                                    <?php } ?>
                                    <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-info" style="margin-right: 10px;">
                                        <i class="fa fa-calendar-plus-o"></i> Nouvelle Consultation
                                    </a>
                                    <a href="<?php echo admin_url('dietetic/programs/create'); ?>" class="btn btn-primary">
                                        <i class="fa fa-file-text-o"></i> Nouveau Programme
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 text-right" style="padding-top: 5px;">
                                <p style="margin: 0; color: #7f8c8d;">
                                    <i class="fa fa-info-circle"></i> Cliquez sur une ligne pour voir les détails du patient
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Enhanced table styles */
    #patients-table tbody tr {
        transition: all 0.2s ease;
    }

    #patients-table tbody tr:hover {
        background-color: #e8f8f5 !important;
        transform: scale(1.005);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    /* Statistics cards hover effect */
    .panel_s:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    /* Button hover effects */
    .btn {
        transition: all 0.2s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    /* Badge and label enhancements */
    .label, .badge {
        transition: all 0.2s ease;
    }

    .label:hover, .badge:hover {
        transform: scale(1.05);
    }

    /* DataTables search box enhancement */
    .dataTables_filter input {
        border: 2px solid #2ecc71 !important;
        border-radius: 20px;
        padding: 8px 15px !important;
        font-size: 14px;
    }

    .dataTables_filter input:focus {
        border-color: #27ae60 !important;
        box-shadow: 0 0 8px rgba(46, 204, 113, 0.3);
    }
</style>

<script>
$(window).on('load', function() {
    // Ensure DataTables is loaded
    if ($.fn.DataTable) {
        $('#patients-table').DataTable({
            "order": [[6, "desc"]], // Sort by created_at desc
            "pageLength": 10,
            "lengthChange": false,
            "searching": true,
            "info": true,
            "paging": true,
            "language": {
                "url": "<?php echo base_url('assets/plugins/jquery-datatables/language/' . perfex_get_datatables_language_file()); ?>"
            }
        });
    } else {
        console.error('DataTables not loaded');
    }
});
</script>

<?php init_tail(); ?>
