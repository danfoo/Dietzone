<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="btn btn-info pull-right">
                                <i class="fa fa-cog"></i> Paramètres
                            </a>
                            <a href="<?php echo admin_url('dietetic/notifications/migrations'); ?>" class="btn btn-primary pull-right mright5">
                                <i class="fa fa-database"></i> Migrations
                            </a>
                            <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>" class="btn btn-default pull-right mright5">
                                <i class="fa fa-list"></i> Historique
                            </a>
                        </div>
                        <h4 class="no-margin">
                            <i class="fa fa-bell"></i> Tableau de Bord Notifications
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-success bold" style="margin-top: 0;">
                            <?php echo isset($stats->total_sent) ? $stats->total_sent : 0; ?>
                        </h3>
                        <p class="text-muted">Total envoyées</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-info bold" style="margin-top: 0;">
                            <?php echo isset($stats->sent_today) ? $stats->sent_today : 0; ?>
                        </h3>
                        <p class="text-muted">Envoyées aujourd'hui</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-danger bold" style="margin-top: 0;">
                            <?php echo isset($stats->failed) ? $stats->failed : 0; ?>
                        </h3>
                        <p class="text-muted">Échouées</p>
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="panel_s">
                    <div class="panel-body text-center">
                        <h3 class="text-warning bold" style="margin-top: 0;">
                            <?php echo isset($stats->pending) ? $stats->pending : 0; ?>
                        </h3>
                        <p class="text-muted">En attente</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Actions rapides</h4>
                        <div class="row mtop15">
                            <div class="col-md-3 col-sm-6 text-center">
                                <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="btn btn-primary btn-block">
                                    <i class="fa fa-cog"></i><br>
                                    <span class="mtop5 inline-block">Configuration</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 text-center">
                                <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>" class="btn btn-default btn-block">
                                    <i class="fa fa-history"></i><br>
                                    <span class="mtop5 inline-block">Historique</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 text-center">
                                <a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>" class="btn btn-success btn-block">
                                    <i class="fa fa-trophy"></i><br>
                                    <span class="mtop5 inline-block">Jalons</span>
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 text-center">
                                <a href="<?php echo admin_url('dietetic/notifications/templates'); ?>" class="btn btn-info btn-block">
                                    <i class="fa fa-file-text"></i><br>
                                    <span class="mtop5 inline-block">Modèles</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Notifications -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4>Notifications récentes</h4>
                        <?php if (!empty($recent_notifications)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Canal</th>
                                            <th>Destinataire</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_notifications as $notification) { ?>
                                            <tr>
                                                <td>
                                                    <?php echo date('d/m/Y H:i', strtotime($notification->created_at)); ?>
                                                </td>
                                                <td>
                                                    <span class="label label-default">
                                                        <?php echo ucfirst(str_replace('_', ' ', $notification->notification_type)); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $channel_icons = [
                                                        'email' => 'envelope',
                                                        'sms' => 'mobile',
                                                        'whatsapp' => 'whatsapp',
                                                        'push' => 'bell'
                                                    ];
                                                    $icon = isset($channel_icons[$notification->channel]) ? $channel_icons[$notification->channel] : 'bell';
                                                    ?>
                                                    <i class="fa fa-<?php echo $icon; ?>"></i>
                                                    <?php echo ucfirst($notification->channel); ?>
                                                </td>
                                                <td>
                                                    <?php echo $notification->recipient; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_colors = [
                                                        'sent' => 'success',
                                                        'failed' => 'danger',
                                                        'pending' => 'warning'
                                                    ];
                                                    $color = isset($status_colors[$notification->status]) ? $status_colors[$notification->status] : 'default';
                                                    ?>
                                                    <span class="label label-<?php echo $color; ?>">
                                                        <?php echo ucfirst($notification->status); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mtop15">
                                <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>" class="btn btn-default">
                                    Voir toutes les notifications <i class="fa fa-arrow-right"></i>
                                </a>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle"></i>
                                Aucune notification envoyée pour le moment.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <h4><i class="fa fa-lightbulb-o"></i> À propos du système de notifications</h4>
                    <p>Le système de notifications permet d'envoyer automatiquement des rappels et alertes aux patients via différents canaux :</p>
                    <ul>
                        <li><strong>Email</strong> : Notifications par email</li>
                        <li><strong>SMS</strong> : Via le provider LAM (Sénégal)</li>
                        <li><strong>WhatsApp</strong> : Messages WhatsApp Business</li>
                        <li><strong>Push</strong> : Notifications push via Firebase (web et mobile)</li>
                    </ul>
                    <p class="mtop10">
                        <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="btn btn-sm btn-primary">
                            <i class="fa fa-cog"></i> Configurer maintenant
                        </a>
                        <a href="<?php echo admin_url('dietetic/notifications/migrations'); ?>" class="btn btn-sm btn-info">
                            <i class="fa fa-database"></i> Installer les migrations
                        </a>
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php init_tail(); ?>
