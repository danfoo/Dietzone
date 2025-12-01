<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <i class="fa fa-clock-o"></i> <?php echo $title; ?>
                        </h4>

                        <hr class="hr-panel-heading">

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            <strong>Le module Dietetic utilise le système de cron intégré de Perfex CRM.</strong><br>
                            Vous n'avez pas besoin de configurer un cron séparé pour les notifications.
                        </div>

                        <h4><i class="fa fa-check-circle text-success"></i> Configuration Automatique (Recommandé)</h4>

                        <p>Les hooks suivants sont déjà enregistrés dans <code>modules/dietetic/dietetic.php</code> :</p>

                        <div class="well">
                            <strong>1. Notifications et Rappels</strong><br>
                            <code>hooks()->add_action('after_cron_run', 'dietetic_send_scheduled_reminders');</code><br>
                            <small class="text-muted">Envoie les rappels de pesée, eau, repas, consultations</small>
                        </div>

                        <div class="well">
                            <strong>2. Paiements Récurrents</strong><br>
                            <code>hooks()->add_action('after_cron_run', 'dietetic_process_recurring_payments');</code><br>
                            <small class="text-muted">Traite les paiements récurrents automatiques</small>
                        </div>

                        <p class="text-success">
                            <i class="fa fa-check"></i>
                            Ces fonctions s'exécutent automatiquement à chaque fois que le cron Perfex s'exécute.
                        </p>

                        <hr>

                        <h4><i class="fa fa-server"></i> Configuration du Cron Perfex CRM</h4>

                        <p>Assurez-vous que le cron de Perfex CRM est configuré sur votre serveur :</p>

                        <h5>Option 1 : Cron Système (Recommandé)</h5>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <p>Ajoutez cette ligne à votre crontab (<code>crontab -e</code>) :</p>
                                <pre style="background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 5px;">*/5 * * * * php <?php echo $project_path; ?>index.php cron/index</pre>
                                <small class="text-muted">Cette commande exécute le cron Perfex toutes les 5 minutes</small>
                            </div>
                        </div>

                        <h5>Option 2 : Auto Cron (Alternative)</h5>
                        <div class="panel panel-default">
                            <div class="panel-body">
                                <p>Activez l'auto-cron dans Perfex :</p>
                                <ol>
                                    <li>Allez dans <strong>Setup > Settings > Cron Job</strong></li>
                                    <li>Activez <strong>"Auto Cron"</strong></li>
                                    <li>Le cron s'exécutera automatiquement à chaque visite du site</li>
                                </ol>
                                <div class="alert alert-warning">
                                    <i class="fa fa-warning"></i>
                                    <strong>Note:</strong> Cette option est moins fiable que le cron système.
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h4><i class="fa fa-list"></i> Tâches Automatiques Dietetic</h4>

                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tâche</th>
                                    <th>Fréquence</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Rappels Pesée</strong></td>
                                    <td>Hebdomadaire</td>
                                    <td>Envoyé le jour et l'heure configurés par patient</td>
                                </tr>
                                <tr>
                                    <td><strong>Rappels Eau</strong></td>
                                    <td>3x/jour</td>
                                    <td>Heures personnalisables (défaut: 10h, 14h, 18h)</td>
                                </tr>
                                <tr>
                                    <td><strong>Rappels Repas</strong></td>
                                    <td>3x/jour</td>
                                    <td>Petit-déjeuner, déjeuner, dîner (heures personnalisables)</td>
                                </tr>
                                <tr>
                                    <td><strong>Rappels Consultation J-1</strong></td>
                                    <td>24h avant</td>
                                    <td>Rappel 1 jour avant la consultation</td>
                                </tr>
                                <tr>
                                    <td><strong>Rappels Consultation H-1</strong></td>
                                    <td>1h avant</td>
                                    <td>Rappel 1 heure avant la consultation</td>
                                </tr>
                                <tr>
                                    <td><strong>Rappel Entrée Repas</strong></td>
                                    <td>18h00</td>
                                    <td>Si patient n'a pas soumis ses repas du jour</td>
                                </tr>
                                <tr>
                                    <td><strong>Paiements Récurrents</strong></td>
                                    <td>Quotidien</td>
                                    <td>Traitement des paiements dus</td>
                                </tr>
                            </tbody>
                        </table>

                        <hr>

                        <h4><i class="fa fa-bug"></i> Vérification et Débogage</h4>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Vérifier que le cron fonctionne</strong>
                            </div>
                            <div class="panel-body">
                                <p>1. Allez dans <strong>Setup > Settings > Cron Job</strong></p>
                                <p>2. Vérifiez la date de dernière exécution</p>
                                <p>3. Consultez les logs dans <strong>Admin > Activity Log</strong></p>
                                <p>4. Recherchez "Dietetic Cron" pour voir les notifications envoyées</p>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <strong>Logs disponibles</strong>
                            </div>
                            <div class="panel-body">
                                <ul>
                                    <li><strong>Activity Log Perfex:</strong> Admin > Activity Log (rechercher "Dietetic Cron")</li>
                                    <li><strong>Logs Notifications:</strong> Admin > Dietetic > Notifications > Logs</li>
                                    <li><strong>Logs Paiements:</strong> Admin > Dietetic > Paiements Récurrents</li>
                                </ul>
                            </div>
                        </div>

                        <hr>

                        <div class="text-center mtop20">
                            <a href="<?php echo admin_url('dietetic/setup/check_notifications'); ?>" class="btn btn-primary">
                                <i class="fa fa-check-circle"></i> Vérifier l'État du Système
                            </a>

                            <a href="<?php echo admin_url('settings?group=cron_job'); ?>" class="btn btn-info" target="_blank">
                                <i class="fa fa-cog"></i> Paramètres Cron Perfex
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
