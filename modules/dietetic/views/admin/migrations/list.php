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
                                                    <form method="post" action="<?php echo admin_url('dietetic/migrations'); ?>" style="display: inline;">
                                                        <?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
                                                        <input type="hidden" name="migration_file" value="<?php echo htmlspecialchars($migration['file']); ?>">
                                                        <button type="submit"
                                                                class="btn btn-primary btn-sm"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir appliquer cette migration ?\n\n<?php echo htmlspecialchars($migration['file']); ?>\n\nCette action est irréversible.');">
                                                            <i class="fa fa-play"></i> Appliquer
                                                        </button>
                                                    </form>
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

<?php init_tail(); ?>
