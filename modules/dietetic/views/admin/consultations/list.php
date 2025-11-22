<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- FIX JQUERY: Charger jQuery AVANT init_head() pour éviter toutes les erreurs -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">

                        <!-- Header -->
                        <div class="tw-mb-4">
                            <h4 class="tw-font-semibold tw-text-lg">
                                <i class="fa fa-stethoscope tw-mr-2"></i>
                                <?php echo _l('dietetic_consultations'); ?>
                            </h4>
                        </div>

                        <!-- Filter Tabs -->
                        <div class="tw-mb-6">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="<?php echo (!isset($_GET['status']) || $_GET['status'] == 'all') ? 'active' : ''; ?>">
                                    <a href="<?php echo admin_url('dietetic/consultations?status=all'); ?>">
                                        <i class="fa fa-list"></i> Toutes
                                        <span class="badge"><?php echo count($consultations); ?></span>
                                    </a>
                                </li>
                                <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'scheduled') ? 'active' : ''; ?>">
                                    <a href="<?php echo admin_url('dietetic/consultations?status=scheduled'); ?>">
                                        <i class="fa fa-clock-o"></i> Programmées
                                    </a>
                                </li>
                                <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'completed') ? 'active' : ''; ?>">
                                    <a href="<?php echo admin_url('dietetic/consultations?status=completed'); ?>">
                                        <i class="fa fa-check"></i> Complétées
                                    </a>
                                </li>
                                <li class="<?php echo (isset($_GET['status']) && $_GET['status'] == 'cancelled') ? 'active' : ''; ?>">
                                    <a href="<?php echo admin_url('dietetic/consultations?status=cancelled'); ?>">
                                        <i class="fa fa-times"></i> Annulées
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Consultations Table -->
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="consultations-table">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Patient</th>
                                        <th>Date</th>
                                        <th>Heure</th>
                                        <th>Type</th>
                                        <th>Mode</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($consultations)): ?>
                                        <?php foreach ($consultations as $consultation): ?>
                                            <tr>
                                                <td><?php echo $consultation->id; ?></td>
                                                <td>
                                                    <?php if (isset($consultation->patient_name)): ?>
                                                        <a href="<?php echo admin_url('dietetic/patients/view/' . $consultation->patient_id); ?>">
                                                            <?php echo $consultation->patient_name; ?>
                                                        </a>
                                                    <?php else: ?>
                                                        N/A
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($consultation->consultation_date)); ?></td>
                                                <td><?php echo $consultation->consultation_time; ?></td>
                                                <td>
                                                    <span class="label label-default">
                                                        <?php echo dietetic_consultation_type_label($consultation->consultation_type); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($consultation->consultation_mode == 'online'): ?>
                                                        <i class="fa fa-video-camera"></i> En ligne
                                                    <?php else: ?>
                                                        <i class="fa fa-map-marker"></i> Présentiel
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_class = 'default';
                                                    $status_label = ucfirst($consultation->status);

                                                    switch ($consultation->status) {
                                                        case 'scheduled':
                                                            $status_class = 'info';
                                                            $status_label = 'Programmée';
                                                            break;
                                                        case 'completed':
                                                            $status_class = 'success';
                                                            $status_label = 'Complétée';
                                                            break;
                                                        case 'cancelled':
                                                            $status_class = 'danger';
                                                            $status_label = 'Annulée';
                                                            break;
                                                        case 'no_show':
                                                            $status_class = 'warning';
                                                            $status_label = 'Absence';
                                                            break;
                                                    }
                                                    ?>
                                                    <span class="label label-<?php echo $status_class; ?>">
                                                        <?php echo $status_label; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo admin_url('dietetic/consultations/view/' . $consultation->id); ?>"
                                                       class="btn btn-sm btn-default">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('dietetic/consultations/edit/' . $consultation->id); ?>"
                                                       class="btn btn-sm btn-primary">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Aucune consultation trouvée</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables
    if ($.fn.DataTable) {
        $('#consultations-table').DataTable({
            "order": [[2, "desc"]],
            "pageLength": 25,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json"
            }
        });
    }

    // Script simple pour le menu Diététique
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
</script>

<?php init_tail(); ?>
