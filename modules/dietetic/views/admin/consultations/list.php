<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; border-radius: 8px; padding: 25px;">
                        <div class="row">
                            <div class="col-md-8">
                                <h2 style="color: white; margin-top: 0;">
                                    <i class="fa fa-calendar-check-o" style="font-size: 42px; vertical-align: middle; margin-right: 15px;"></i>
                                    Gestion des Consultations
                                </h2>
                                <p style="color: rgba(255,255,255,0.9); margin-bottom: 0; font-size: 16px;">
                                    <i class="fa fa-stethoscope"></i> Planifiez et suivez toutes vos consultations diététiques
                                </p>
                            </div>
                            <div class="col-md-4 text-right" style="padding-top: 10px;">
                                <?php if (dietetic_has_permission('create')) { ?>
                                    <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-light btn-lg" style="margin-bottom: 5px;">
                                        <i class="fa fa-plus-circle"></i> Nouvelle Consultation
                                    </a>
                                    <br>
                                    <a href="<?php echo admin_url('dietetic/consultations/calendar'); ?>" class="btn btn-outline" style="border: 2px solid white; color: white; padding: 8px 20px;">
                                        <i class="fa fa-calendar"></i> Voir le Calendrier
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
                <div class="panel_s" style="border-left: 4px solid #3498db; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #3498db; margin-bottom: 10px;">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php echo count($consultations); ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Total Consultations
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #f39c12; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #f39c12; margin-bottom: 10px;">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php
                            $scheduled_count = 0;
                            foreach ($consultations as $c) {
                                if ($c->status === 'scheduled') $scheduled_count++;
                            }
                            echo $scheduled_count;
                            ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Programmées
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #2ecc71; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #2ecc71; margin-bottom: 10px;">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php
                            $completed_count = 0;
                            foreach ($consultations as $c) {
                                if ($c->status === 'completed') $completed_count++;
                            }
                            echo $completed_count;
                            ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Complétées
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s" style="border-left: 4px solid #e74c3c; transition: transform 0.2s;">
                    <div class="panel-body text-center" style="padding: 20px;">
                        <div style="font-size: 40px; color: #e74c3c; margin-bottom: 10px;">
                            <i class="fa fa-calendar-o"></i>
                        </div>
                        <h3 style="margin: 5px 0; color: #2c3e50; font-size: 28px; font-weight: bold;">
                            <?php
                            $today_count = 0;
                            foreach ($consultations as $c) {
                                if (date('Y-m-d', strtotime($c->consultation_date)) === date('Y-m-d')) {
                                    $today_count++;
                                }
                            }
                            echo $today_count;
                            ?>
                        </h3>
                        <p style="color: #7f8c8d; font-size: 13px; margin: 0; text-transform: uppercase;">
                            Aujourd'hui
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Consultations Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div style="border-bottom: 3px solid #3498db; padding-bottom: 10px; margin-bottom: 20px;">
                            <h4 style="margin: 0;">
                                <i class="fa fa-list" style="color: #3498db;"></i> Liste des Consultations
                            </h4>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover dietetic-table" id="consultations-table" style="margin-bottom: 0;">
                                <thead>
                                    <tr style="background: #f8f9fa;">
                                        <th><i class="fa fa-user" style="color: #2ecc71;"></i> <?php echo _l('dietetic_patient'); ?></th>
                                        <th><i class="fa fa-user-md" style="color: #9b59b6;"></i> <?php echo _l('dietetic_dietitian'); ?></th>
                                        <th><i class="fa fa-calendar" style="color: #3498db;"></i> <?php echo _l('dietetic_date'); ?></th>
                                        <th><i class="fa fa-tag" style="color: #f39c12;"></i> <?php echo _l('dietetic_type'); ?></th>
                                        <th><i class="fa fa-info-circle" style="color: #3498db;"></i> <?php echo _l('dietetic_status'); ?></th>
                                        <th><i class="fa fa-clock-o" style="color: #e67e22;"></i> <?php echo _l('dietetic_duration'); ?></th>
                                        <th class="text-center"><i class="fa fa-cog"></i> <?php echo _l('options'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($consultations as $consultation) { ?>
                                        <tr style="cursor: pointer;" onclick="window.location='<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>'">
                                            <td>
                                                <i class="fa fa-user-circle" style="color: #2ecc71; margin-right: 5px; font-size: 16px;"></i>
                                                <strong style="color: #2c3e50;"><?php echo $consultation->client_name; ?></strong>
                                            </td>
                                            <td>
                                                <span style="color: #7f8c8d;">
                                                    <i class="fa fa-stethoscope" style="color: #9b59b6; margin-right: 3px;"></i>
                                                    <?php echo $consultation->dietitian_name; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php
                                                $consult_date = strtotime($consultation->consultation_date);
                                                $today = strtotime(date('Y-m-d'));
                                                $date_class = '';
                                                if (date('Y-m-d', $consult_date) === date('Y-m-d')) {
                                                    $date_class = 'label label-danger';
                                                    $date_text = '<i class="fa fa-exclamation-circle"></i> Aujourd\'hui ' . date('H:i', $consult_date);
                                                } elseif ($consult_date > $today && $consult_date <= strtotime('+7 days')) {
                                                    $date_class = 'label label-warning';
                                                    $date_text = '<i class="fa fa-calendar"></i> ' . _dt($consultation->consultation_date);
                                                } else {
                                                    $date_text = '<span style="color: #7f8c8d;"><i class="fa fa-calendar-o"></i> ' . _dt($consultation->consultation_date) . '</span>';
                                                }
                                                ?>
                                                <?php if ($date_class) { ?>
                                                    <span class="<?php echo $date_class; ?>" style="font-size: 11px; padding: 4px 8px;">
                                                        <?php echo $date_text; ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <?php echo $date_text; ?>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <span class="label" style="background: #f39c12; font-size: 10px; padding: 4px 8px;">
                                                    <?php echo ucfirst(str_replace('_', ' ', $consultation->consultation_type)); ?>
                                                </span>
                                            </td>
                                            <td><?php echo dietetic_consultation_status_badge($consultation->status); ?></td>
                                            <td>
                                                <span class="badge" style="background: #e67e22; font-size: 11px; padding: 4px 8px;">
                                                    <i class="fa fa-hourglass-half"></i> <?php echo $consultation->duration; ?> min
                                                </span>
                                            </td>
                                            <td class="text-center" onclick="event.stopPropagation();">
                                                <div class="btn-group">
                                                    <a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>" class="btn btn-info btn-sm" title="Voir">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <?php if (dietetic_has_permission('edit')) { ?>
                                                        <a href="<?php echo admin_url('dietetic/consultations/edit/' . $consultation->id); ?>" class="btn btn-default btn-sm" title="Modifier">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                    <?php if (dietetic_has_permission('delete')) { ?>
                                                        <a href="#" onclick="dietetic.deleteConfirm('<?php echo admin_url('dietetic/consultations/delete/' . $consultation->id); ?>', function() { location.reload(); }); return false;" class="btn btn-danger btn-sm" title="Supprimer">
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
                    <div class="panel-body" style="background: #ecf0f1; border-left: 4px solid #3498db;">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 style="margin-top: 0; color: #2c3e50;">
                                    <i class="fa fa-lightbulb-o" style="color: #3498db;"></i> Actions Rapides
                                </h5>
                                <div class="btn-group" role="group">
                                    <?php if (dietetic_has_permission('create')) { ?>
                                        <a href="<?php echo admin_url('dietetic/consultations/create'); ?>" class="btn btn-success" style="margin-right: 10px;">
                                            <i class="fa fa-plus-circle"></i> Nouvelle Consultation
                                        </a>
                                        <a href="<?php echo admin_url('dietetic/consultations/calendar'); ?>" class="btn btn-info" style="margin-right: 10px;">
                                            <i class="fa fa-calendar"></i> Calendrier
                                        </a>
                                    <?php } ?>
                                    <a href="<?php echo admin_url('dietetic/patients'); ?>" class="btn btn-primary">
                                        <i class="fa fa-users"></i> Voir les Patients
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 text-right" style="padding-top: 5px;">
                                <p style="margin: 0; color: #7f8c8d;">
                                    <i class="fa fa-info-circle"></i> Cliquez sur une ligne pour voir les détails
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body" style="padding: 15px; background: #fff;">
                        <div class="row">
                            <div class="col-md-12">
                                <h5 style="margin-top: 0; color: #2c3e50;">
                                    <i class="fa fa-info-circle" style="color: #3498db;"></i> Légende des Dates
                                </h5>
                                <div style="display: inline-block; margin-right: 20px;">
                                    <span class="label label-danger" style="font-size: 11px; padding: 5px 10px;">
                                        <i class="fa fa-exclamation-circle"></i> Aujourd'hui
                                    </span>
                                </div>
                                <div style="display: inline-block; margin-right: 20px;">
                                    <span class="label label-warning" style="font-size: 11px; padding: 5px 10px;">
                                        <i class="fa fa-calendar"></i> Cette semaine (7 jours)
                                    </span>
                                </div>
                                <div style="display: inline-block;">
                                    <span style="color: #7f8c8d; font-size: 13px;">
                                        <i class="fa fa-calendar-o"></i> Autres dates
                                    </span>
                                </div>
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
    #consultations-table tbody tr {
        transition: all 0.2s ease;
    }

    #consultations-table tbody tr:hover {
        background-color: #ebf5fb !important;
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

    .btn-outline:hover {
        background: white;
        color: #3498db !important;
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
        border: 2px solid #3498db !important;
        border-radius: 20px;
        padding: 8px 15px !important;
        font-size: 14px;
    }

    .dataTables_filter input:focus {
        border-color: #2980b9 !important;
        box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
    }
</style>

<script>
$(window).on('load', function() {
    // Ensure DataTables is loaded
    if ($.fn.DataTable) {
        $('#consultations-table').DataTable({
            "order": [[2, "desc"]], // Sort by consultation_date desc
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
