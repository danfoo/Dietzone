<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-shield"></i> <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Information :</strong> Gérez ici les permissions spécifiques de chaque diététicien.
                            Les administrateurs ont toujours accès à toutes les fonctionnalités.
                        </div>

                        <?php if (empty($staff_members)): ?>
                            <div class="alert alert-warning">
                                Aucun membre du personnel trouvé.
                            </div>
                        <?php else: ?>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Diététicien</th>
                                            <th>Email</th>
                                            <th>Admin</th>
                                            <?php foreach ($available_permissions as $key => $perm): ?>
                                                <th class="text-center" data-toggle="tooltip" title="<?php echo htmlspecialchars($perm['description']); ?>">
                                                    <?php echo htmlspecialchars($perm['label']); ?>
                                                    <?php if (isset($perm['admin_only']) && $perm['admin_only']): ?>
                                                        <span class="badge badge-danger">Admin</span>
                                                    <?php endif; ?>
                                                </th>
                                            <?php endforeach; ?>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($staff_members as $staff): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($staff->firstname . ' ' . $staff->lastname); ?></strong>
                                                    <?php if ($staff->is_not_staff == 0): ?>
                                                        <span class="label label-success">Staff</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo htmlspecialchars($staff->email); ?></td>
                                                <td class="text-center">
                                                    <?php if ($staff->admin == 1): ?>
                                                        <span class="label label-danger"><i class="fa fa-check"></i> Admin</span>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <?php foreach ($available_permissions as $key => $perm): ?>
                                                    <td class="text-center">
                                                        <?php
                                                        $has_permission = isset($staff_permissions[$staff->staffid][$key]) ? $staff_permissions[$staff->staffid][$key] : false;
                                                        $is_admin = $staff->admin == 1;
                                                        ?>

                                                        <?php if ($is_admin): ?>
                                                            <!-- Admins have all permissions automatically -->
                                                            <i class="fa fa-check text-success" data-toggle="tooltip" title="Accès automatique (Admin)"></i>
                                                        <?php else: ?>
                                                            <div class="onoffswitch" data-toggle="tooltip" title="Cliquer pour activer/désactiver">
                                                                <input
                                                                    type="checkbox"
                                                                    class="onoffswitch-checkbox permission-toggle"
                                                                    id="permission_<?php echo $staff->staffid; ?>_<?php echo $key; ?>"
                                                                    data-staff-id="<?php echo $staff->staffid; ?>"
                                                                    data-permission-key="<?php echo $key; ?>"
                                                                    <?php echo $has_permission ? 'checked' : ''; ?>
                                                                >
                                                                <label class="onoffswitch-label" for="permission_<?php echo $staff->staffid; ?>_<?php echo $key; ?>"></label>
                                                            </div>
                                                        <?php endif; ?>
                                                    </td>
                                                <?php endforeach; ?>
                                                <td class="text-center">
                                                    <?php if ($staff->admin != 1): ?>
                                                        <a href="<?php echo admin_url('dietetic/staff_permissions/reset_permissions/' . $staff->staffid); ?>"
                                                           class="btn btn-warning btn-xs"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir réinitialiser toutes les permissions de cet utilisateur?');">
                                                            <i class="fa fa-refresh"></i> Réinitialiser
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Legend -->
                            <div class="row mtop20">
                                <div class="col-md-12">
                                    <h5><i class="fa fa-info-circle"></i> Légende des Permissions</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Permission</th>
                                                    <th>Description</th>
                                                    <th>Type</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($available_permissions as $key => $perm): ?>
                                                    <tr>
                                                        <td><strong><?php echo htmlspecialchars($perm['label']); ?></strong></td>
                                                        <td><?php echo htmlspecialchars($perm['description']); ?></td>
                                                        <td>
                                                            <?php if (isset($perm['admin_only']) && $perm['admin_only']): ?>
                                                                <span class="label label-danger">Réservé aux Admins</span>
                                                            <?php else: ?>
                                                                <span class="label label-success">Configurable</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>

<script>
$(document).ready(function() {
    'use strict';

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Handle permission toggle
    $('.permission-toggle').on('change', function() {
        var $checkbox = $(this);
        var staffId = $checkbox.data('staff-id');
        var permissionKey = $checkbox.data('permission-key');
        var enabled = $checkbox.is(':checked') ? 1 : 0;

        // Disable the checkbox while updating
        $checkbox.prop('disabled', true);

        $.ajax({
            url: admin_url + 'dietetic/staff_permissions/update_permission',
            type: 'POST',
            dataType: 'json',
            data: {
                staff_id: staffId,
                permission_key: permissionKey,
                enabled: enabled
            },
            success: function(response) {
                if (response.success) {
                    alert_float('success', response.message);
                } else {
                    alert_float('danger', response.message || 'Erreur lors de la mise à jour');
                    // Revert checkbox state
                    $checkbox.prop('checked', !enabled);
                }
            },
            error: function(xhr, status, error) {
                alert_float('danger', 'Erreur de communication avec le serveur');
                console.error('AJAX Error:', error);
                // Revert checkbox state
                $checkbox.prop('checked', !enabled);
            },
            complete: function() {
                // Re-enable the checkbox
                $checkbox.prop('disabled', false);
            }
        });
    });
});
</script>

