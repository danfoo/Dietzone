<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-heartbeat"></i> <?php echo $title; ?>
                        </h4>

                        <hr class="hr-panel-heading">

                        <!-- État des Tables -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4><i class="fa fa-database"></i> Tables de Base de Données</h4>

                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Table</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tables_status as $table => $exists): ?>
                                        <tr>
                                            <td><code><?php echo $table; ?></code></td>
                                            <td class="text-center">
                                                <?php if ($exists): ?>
                                                    <span class="label label-success">
                                                        <i class="fa fa-check"></i> Installée
                                                    </span>
                                                <?php else: ?>
                                                    <span class="label label-danger">
                                                        <i class="fa fa-times"></i> Manquante
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <?php if (in_array(false, $tables_status)): ?>
                                <div class="alert alert-warning">
                                    <i class="fa fa-warning"></i>
                                    Des tables sont manquantes.
                                    <a href="<?php echo admin_url('dietetic/setup/notifications'); ?>" class="alert-link">
                                        Cliquez ici pour les installer
                                    </a>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-success">
                                    <i class="fa fa-check-circle"></i>
                                    Toutes les tables sont installées correctement.
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6">
                                <h4><i class="fa fa-bar-chart"></i> Statistiques</h4>

                                <?php if (!empty($stats)): ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-body text-center" style="padding: 20px;">
                                                <h3 class="bold no-margin" style="color: #01807B;">
                                                    <?php echo $stats['total_patients'] ?? 0; ?>
                                                </h3>
                                                <p class="text-muted no-margin">Patients Total</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-body text-center" style="padding: 20px;">
                                                <h3 class="bold no-margin" style="color: <?php echo ($stats['patients_without_prefs'] ?? 0) > 0 ? '#F3911D' : '#28a745'; ?>;">
                                                    <?php echo $stats['total_patients_with_prefs'] ?? 0; ?>
                                                </h3>
                                                <p class="text-muted no-margin">Avec Préférences</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-body text-center" style="padding: 20px;">
                                                <h3 class="bold no-margin" style="color: #01807B;">
                                                    <?php echo $stats['notifications_today'] ?? 0; ?>
                                                </h3>
                                                <p class="text-muted no-margin">Notifications Aujourd'hui</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="panel_s">
                                            <div class="panel-body text-center" style="padding: 20px;">
                                                <h3 class="bold no-margin" style="color: #01807B;">
                                                    <?php echo $stats['notifications_last_7_days'] ?? 0; ?>
                                                </h3>
                                                <p class="text-muted no-margin">7 Derniers Jours</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    Les statistiques seront disponibles une fois les tables installées.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Configuration Cron -->
                        <div class="row mtop20">
                            <div class="col-md-12">
                                <h4><i class="fa fa-clock-o"></i> Configuration Cron Job</h4>

                                <div class="alert <?php echo $perfex_cron_configured ? 'alert-success' : 'alert-warning'; ?>">
                                    <?php if ($perfex_cron_configured): ?>
                                        <i class="fa fa-check-circle"></i>
                                        <strong>Cron Perfex Configuré</strong><br>
                                        Dernière exécution: <?php echo $last_cron_run ? date('d/m/Y H:i:s', $last_cron_run) : 'N/A'; ?>
                                    <?php else: ?>
                                        <i class="fa fa-warning"></i>
                                        <strong>Cron Perfex Non Détecté</strong><br>
                                        Le cron automatique de Perfex CRM n'a pas encore été exécuté.
                                    <?php endif; ?>
                                </div>

                                <p class="text-muted">
                                    Le système de notifications utilise le hook <code>after_cron_run</code> de Perfex CRM.
                                    Les notifications seront envoyées automatiquement quand le cron Perfex s'exécute.
                                </p>

                                <a href="<?php echo admin_url('dietetic/setup/cron_instructions'); ?>" class="btn btn-info">
                                    <i class="fa fa-book"></i> Voir les Instructions Cron
                                </a>
                            </div>
                        </div>

                        <hr>

                        <!-- Actions -->
                        <div class="text-center mtop20">
                            <?php if (in_array(false, $tables_status)): ?>
                            <a href="<?php echo admin_url('dietetic/setup/notifications'); ?>" class="btn btn-warning btn-lg">
                                <i class="fa fa-download"></i> Installer les Tables Manquantes
                            </a>
                            <?php endif; ?>

                            <?php if (($stats['patients_without_prefs'] ?? 0) > 0): ?>
                            <a href="<?php echo admin_url('dietetic/setup/notifications'); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-users"></i> Créer les Préférences Manquantes (<?php echo $stats['patients_without_prefs']; ?>)
                            </a>
                            <?php endif; ?>

                            <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>" class="btn btn-default">
                                <i class="fa fa-list"></i> Voir les Logs de Notifications
                            </a>

                            <a href="<?php echo admin_url('dietetic/dashboard'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour au Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
