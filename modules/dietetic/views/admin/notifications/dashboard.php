<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<style>
.notifications-nav {
    background: white;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 30px;
}

.notifications-nav-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0;
}

.notifications-nav ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    gap: 0;
}

.notifications-nav li {
    margin: 0;
}

.notifications-nav a {
    display: block;
    padding: 15px 25px;
    color: #718096;
    text-decoration: none;
    font-weight: 600;
    border-bottom: 3px solid transparent;
    transition: all 0.3s;
}

.notifications-nav a:hover {
    color: #01807B;
    background: #f7fafc;
}

.notifications-nav a.active {
    color: #01807B;
    border-bottom-color: #01807B;
}

.notifications-nav i {
    margin-right: 5px;
}

.dashboard-container {
    max-width: 1200px;
    margin: 0 auto;
}

.dashboard-header {
    margin-bottom: 30px;
}

.dashboard-header h1 {
    color: #2d3748;
    font-size: 24px;
    margin-bottom: 5px;
}

.dashboard-header p {
    color: #718096;
    font-size: 14px;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #01807B;
    transition: all 0.3s;
}

.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.stat-card.success {
    border-left-color: #10b981;
}

.stat-card.danger {
    border-left-color: #ef4444;
}

.stat-card.warning {
    border-left-color: #f59e0b;
}

.stat-card.info {
    border-left-color: #3b82f6;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    font-size: 24px;
}

.stat-card.success .stat-icon {
    background: #d1fae5;
    color: #10b981;
}

.stat-card.danger .stat-icon {
    background: #fee2e2;
    color: #ef4444;
}

.stat-card.warning .stat-icon {
    background: #fef3c7;
    color: #f59e0b;
}

.stat-card.info .stat-icon {
    background: #dbeafe;
    color: #3b82f6;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 5px;
}

.stat-label {
    color: #718096;
    font-size: 14px;
    font-weight: 600;
}

/* Quick Actions */
.quick-actions {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.quick-actions h3 {
    color: #2d3748;
    font-size: 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.quick-actions h3 i {
    color: #01807B;
}

.action-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.action-button {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 20px;
    background: #f7fafc;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    text-decoration: none;
    color: #2d3748;
    transition: all 0.3s;
    font-weight: 600;
}

.action-button:hover {
    background: #01807B;
    color: white;
    border-color: #01807B;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(1, 128, 123, 0.2);
}

.action-button i {
    font-size: 20px;
    color: #01807B;
}

.action-button:hover i {
    color: white;
}

/* Recent Notifications */
.recent-notifications {
    background: white;
    border-radius: 8px;
    padding: 25px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.recent-notifications h3 {
    color: #2d3748;
    font-size: 18px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.recent-notifications h3 i {
    color: #01807B;
}

.notifications-table {
    width: 100%;
    border-collapse: collapse;
}

.notifications-table thead {
    background: #f7fafc;
}

.notifications-table th {
    padding: 12px;
    text-align: left;
    color: #718096;
    font-weight: 600;
    font-size: 13px;
    border-bottom: 2px solid #e2e8f0;
}

.notifications-table td {
    padding: 12px;
    color: #2d3748;
    border-bottom: 1px solid #e2e8f0;
    font-size: 14px;
}

.notifications-table tbody tr:hover {
    background: #f7fafc;
}

.badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.badge-success {
    background: #d1fae5;
    color: #10b981;
}

.badge-danger {
    background: #fee2e2;
    color: #ef4444;
}

.badge-warning {
    background: #fef3c7;
    color: #f59e0b;
}

.badge-info {
    background: #dbeafe;
    color: #3b82f6;
}

.channel-badge {
    padding: 3px 8px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
    display: inline-block;
    margin-right: 5px;
}

.channel-email {
    background: #e0e7ff;
    color: #4f46e5;
}

.channel-sms {
    background: #ccfbf1;
    color: #0d9488;
}

.channel-whatsapp {
    background: #d1fae5;
    color: #059669;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 48px;
    color: #cbd5e0;
    margin-bottom: 15px;
}

.empty-state h4 {
    color: #2d3748;
    font-size: 18px;
    margin-bottom: 10px;
}

.empty-state p {
    color: #718096;
    font-size: 14px;
}

.view-all-link {
    text-align: center;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #e2e8f0;
}

.view-all-link a {
    color: #01807B;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.view-all-link a:hover {
    color: #016662;
    gap: 12px;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Navigation -->
        <div class="notifications-nav">
            <div class="notifications-nav-container">
                <ul>
                    <li>
                        <a href="<?php echo admin_url('dietetic/notifications'); ?>" class="active">
                            <i class="fa fa-dashboard"></i> Tableau de Bord
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>">
                            <i class="fa fa-cog"></i> Paramètres
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('dietetic/notifications/templates'); ?>">
                            <i class="fa fa-file-text-o"></i> Modèles
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>">
                            <i class="fa fa-history"></i> Historique
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>">
                            <i class="fa fa-trophy"></i> Jalons
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="dashboard-container">
            <!-- Header -->
            <div class="dashboard-header">
                <h1><i class="fa fa-bell"></i> Notifications & Rappels</h1>
                <p>Gérez et suivez toutes vos notifications automatiques</p>
            </div>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fa fa-bell"></i>
                    </div>
                    <div class="stat-value"><?php echo isset($stats['total']) ? $stats['total'] : 0; ?></div>
                    <div class="stat-label">Total de Notifications</div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fa fa-check"></i>
                    </div>
                    <div class="stat-value"><?php echo isset($stats['sent']) ? $stats['sent'] : 0; ?></div>
                    <div class="stat-label">Envoyées avec Succès</div>
                </div>

                <div class="stat-card danger">
                    <div class="stat-icon">
                        <i class="fa fa-times"></i>
                    </div>
                    <div class="stat-value"><?php echo isset($stats['failed']) ? $stats['failed'] : 0; ?></div>
                    <div class="stat-label">Échecs d'Envoi</div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fa fa-clock-o"></i>
                    </div>
                    <div class="stat-value"><?php echo isset($stats['pending']) ? $stats['pending'] : 0; ?></div>
                    <div class="stat-label">En Attente</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h3>
                    <i class="fa fa-flash"></i>
                    Actions Rapides
                </h3>
                <div class="action-buttons">
                    <a href="<?php echo admin_url('dietetic/notifications/settings'); ?>" class="action-button">
                        <i class="fa fa-cog"></i>
                        <span>Configurer les APIs</span>
                    </a>
                    <a href="<?php echo admin_url('dietetic/notifications/templates'); ?>" class="action-button">
                        <i class="fa fa-file-text-o"></i>
                        <span>Voir les Modèles</span>
                    </a>
                    <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>" class="action-button">
                        <i class="fa fa-history"></i>
                        <span>Consulter l'Historique</span>
                    </a>
                    <a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>" class="action-button">
                        <i class="fa fa-trophy"></i>
                        <span>Jalons Atteints</span>
                    </a>
                </div>
            </div>

            <!-- Recent Notifications -->
            <div class="recent-notifications">
                <h3>
                    <i class="fa fa-clock-o"></i>
                    Notifications Récentes
                </h3>

                <?php if (!empty($recent_notifications)) { ?>
                    <table class="notifications-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Canal</th>
                                <th>Statut</th>
                                <th>Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_notifications as $notification) { ?>
                                <tr>
                                    <td>
                                        <?php echo date('d/m/Y H:i', strtotime($notification->created_at)); ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($notification->patient_id > 0) {
                                            $this->db->select('tblcontacts.firstname, tblcontacts.lastname');
                                            $this->db->from(db_prefix() . 'dietic_patients');
                                            $this->db->join('tblclients', 'tblclients.userid = ' . db_prefix() . 'dietic_patients.client_id');
                                            $this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid AND tblcontacts.is_primary = 1');
                                            $this->db->where(db_prefix() . 'dietic_patients.id', $notification->patient_id);
                                            $patient = $this->db->get()->row();

                                            echo $patient ? $patient->firstname . ' ' . $patient->lastname : 'Patient #' . $notification->patient_id;
                                        } else {
                                            echo '<span class="text-muted">Test</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $type_labels = [
                                            'recommendation' => 'Recommandation',
                                            'consultation' => 'Consultation',
                                            'milestone' => 'Jalon',
                                            'program' => 'Programme',
                                            'reminder_weight' => 'Rappel Pesée',
                                            'reminder_water' => 'Rappel Eau',
                                            'food_survey' => 'Enquête Alimentaire',
                                            'test' => 'Test'
                                        ];
                                        echo isset($type_labels[$notification->notification_type]) ? $type_labels[$notification->notification_type] : $notification->notification_type;
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $channels = json_decode($notification->channels, true);
                                        if (isset($channels['email']) && $channels['email']) {
                                            echo '<span class="channel-badge channel-email"><i class="fa fa-envelope"></i> Email</span>';
                                        }
                                        if (isset($channels['sms']) && $channels['sms']) {
                                            echo '<span class="channel-badge channel-sms"><i class="fa fa-mobile"></i> SMS</span>';
                                        }
                                        if (isset($channels['whatsapp']) && $channels['whatsapp']) {
                                            echo '<span class="channel-badge channel-whatsapp"><i class="fa fa-whatsapp"></i> WhatsApp</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $status_badges = [
                                            'sent' => '<span class="badge badge-success"><i class="fa fa-check"></i> Envoyé</span>',
                                            'failed' => '<span class="badge badge-danger"><i class="fa fa-times"></i> Échec</span>',
                                            'pending' => '<span class="badge badge-warning"><i class="fa fa-clock-o"></i> En attente</span>'
                                        ];
                                        echo isset($status_badges[$notification->status]) ? $status_badges[$notification->status] : $notification->status;
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo character_limiter(strip_tags($notification->message), 50); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <div class="view-all-link">
                        <a href="<?php echo admin_url('dietetic/notifications/logs'); ?>">
                            Voir tout l'historique
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="empty-state">
                        <i class="fa fa-bell-slash-o"></i>
                        <h4>Aucune Notification</h4>
                        <p>Aucune notification n'a encore été envoyée.<br>
                        Les notifications apparaîtront ici une fois que le système sera configuré et actif.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
