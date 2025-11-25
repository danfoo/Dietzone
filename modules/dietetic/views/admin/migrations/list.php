<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-database"></i> <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading">

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Les migrations permettent de mettre à jour la structure de la base de données automatiquement.
                            Cliquez sur "Appliquer" pour exécuter une migration non appliquée.
                        </div>

                        <?php if (empty($migrations)): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i>
                                Aucune migration trouvée dans le dossier <code>/modules/dietetic/migrations/</code>
                            </div>
                        <?php else: ?>
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th width="5%">Statut</th>
                                        <th width="40%">Nom de la migration</th>
                                        <th width="20%">Fichier</th>
                                        <th width="20%">Date d'application</th>
                                        <th width="15%" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($migrations as $migration): ?>
                                        <tr id="migration-row-<?php echo $migration['name']; ?>">
                                            <td class="text-center">
                                                <?php if ($migration['applied']): ?>
                                                    <span class="label label-success">
                                                        <i class="fa fa-check"></i> Appliquée
                                                    </span>
                                                <?php else: ?>
                                                    <span class="label label-default">
                                                        <i class="fa fa-clock-o"></i> En attente
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($migration['title']); ?></strong>
                                            </td>
                                            <td>
                                                <code><?php echo htmlspecialchars($migration['file']); ?></code>
                                            </td>
                                            <td>
                                                <?php if ($migration['date_applied']): ?>
                                                    <?php echo date('d/m/Y H:i', strtotime($migration['date_applied'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Non appliquée</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if (!$migration['applied']): ?>
                                                    <button type="button"
                                                            class="btn btn-primary btn-sm apply-migration-btn"
                                                            data-migration-file="<?php echo htmlspecialchars($migration['file']); ?>"
                                                            data-migration-name="<?php echo htmlspecialchars($migration['name']); ?>">
                                                        <i class="fa fa-play"></i> Appliquer
                                                    </button>
                                                <?php else: ?>
                                                    <span class="text-success">
                                                        <i class="fa fa-check-circle"></i> Déjà appliquée
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>

                        <hr>

                        <div class="alert alert-warning">
                            <strong><i class="fa fa-warning"></i> Avertissement :</strong>
                            <ul class="m-t-10">
                                <li>Assurez-vous d'avoir une sauvegarde de votre base de données avant d'appliquer une migration.</li>
                                <li>Les migrations modifient la structure de la base de données et ne peuvent pas être annulées automatiquement.</li>
                                <li>Seuls les administrateurs peuvent appliquer des migrations.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function($) {
    'use strict';

    $(document).ready(function() {
        $('.apply-migration-btn').on('click', function() {
            var btn = $(this);
            var migrationFile = btn.data('migration-file');
            var migrationName = btn.data('migration-name');
            var row = $('#migration-row-' + migrationName);

            if (!confirm('Êtes-vous sûr de vouloir appliquer cette migration ?\n\n' + migrationFile + '\n\nCette action est irréversible.')) {
                return;
            }

            // Disable button and show loading
            btn.prop('disabled', true);
            btn.html('<i class="fa fa-spinner fa-spin"></i> Application...');

            // Send AJAX request
            $.ajax({
                url: '<?php echo admin_url('dietetic/apply_migration'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    migration_file: migrationFile,
                    <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        // Update row to show success
                        row.find('td:first').html('<span class="label label-success"><i class="fa fa-check"></i> Appliquée</span>');
                        row.find('td:nth-child(4)').html('<?php echo date('d/m/Y H:i'); ?>');
                        row.find('td:last').html('<span class="text-success"><i class="fa fa-check-circle"></i> Déjà appliquée</span>');

                        // Show success alert
                        alert_float('success', response.message || 'Migration appliquée avec succès');
                    } else {
                        // Show error and re-enable button
                        alert_float('danger', response.message || 'Erreur lors de l\'application de la migration');
                        btn.prop('disabled', false);
                        btn.html('<i class="fa fa-play"></i> Appliquer');
                    }
                },
                error: function(xhr, status, error) {
                    alert_float('danger', 'Erreur serveur: ' + error);
                    btn.prop('disabled', false);
                    btn.html('<i class="fa fa-play"></i> Appliquer');
                }
            });
        });
    });
})(jQuery);
</script>

<?php init_tail(); ?>
