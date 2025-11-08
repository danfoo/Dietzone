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

.logs-container {
    max-width: 1400px;
    margin: 0 auto;
}

.logs-header {
    margin-bottom: 30px;
}

.logs-header h1 {
    color: #2d3748;
    font-size: 24px;
    margin-bottom: 5px;
}

.logs-header p {
    color: #718096;
    font-size: 14px;
}

.logs-filters {
    background: white;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.logs-filters form {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    align-items: end;
}

.filter-group label {
    display: block;
    margin-bottom: 5px;
    color: #2d3748;
    font-weight: 600;
    font-size: 13px;
}

.filter-group select {
    width: 100%;
    padding: 8px;
    border: 1px solid #cbd5e0;
    border-radius: 5px;
    font-size: 14px;
}

.logs-table {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.logs-table table {
    width: 100%;
    border-collapse: collapse;
}

.logs-table thead {
    background: #f7fafc;
    border-bottom: 2px solid #e2e8f0;
}

.logs-table th {
    padding: 12px 15px;
    text-align: left;
    color: #2d3748;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
}

.logs-table td {
    padding: 12px 15px;
    border-bottom: 1px solid #e2e8f0;
    color: #4a5568;
    font-size: 14px;
}

.logs-table tbody tr:hover {
    background: #f7fafc;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.sent {
    background: #c6f6d5;
    color: #22543d;
}

.status-badge.failed {
    background: #fed7d7;
    color: #742a2a;
}

.status-badge.pending {
    background: #feebc8;
    color: #7c2d12;
}

.channel-badge {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
    margin-right: 5px;
}

.channel-badge.email {
    background: #bee3f8;
    color: #2c5282;
}

.channel-badge.sms {
    background: #c6f6d5;
    color: #22543d;
}

.channel-badge.whatsapp {
    background: #d9f99d;
    color: #3f6212;
}

.notification-type {
    font-size: 12px;
    color: #718096;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
    padding: 20px;
}

.pagination a, .pagination span {
    padding: 8px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 5px;
    color: #4a5568;
    text-decoration: none;
    transition: all 0.3s;
}

.pagination a:hover {
    background: #01807B;
    color: white;
    border-color: #01807B;
}

.pagination .active {
    background: #01807B;
    color: white;
    border-color: #01807B;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #718096;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 20px;
    color: #cbd5e0;
}
</style>

<div id="wrapper">
    <div class="content">
        <!-- Notifications Navigation -->
        <div class="notifications-nav">
            <div class="notifications-nav-container">
                <ul>
                    <li><a href="<?php echo admin_url('dietetic/notifications/settings'); ?>"><i class="fa fa-cog"></i> Configuration</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/templates'); ?>"><i class="fa fa-file-text-o"></i> Modèles</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/logs'); ?>" class="active"><i class="fa fa-list"></i> Historique</a></li>
                    <li><a href="<?php echo admin_url('dietetic/notifications/milestones'); ?>"><i class="fa fa-trophy"></i> Jalons</a></li>
                </ul>
            </div>
        </div>

        <div class="logs-container">
            <div class="logs-header">
                <h1><i class="fa fa-list"></i> Historique des Notifications</h1>
                <p>Consultez toutes les notifications envoyées aux patients</p>
            </div>

            <!-- Filters -->
            <div class="logs-filters">
                <form method="GET" action="<?php echo admin_url('dietetic/notifications/logs'); ?>">
                    <div class="filter-group">
                        <label>Type de notification</label>
                        <select name="type">
                            <option value="">Tous les types</option>
                            <option value="reminder_weight" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'reminder_weight' ? 'selected' : ''; ?>>Rappel Poids</option>
                            <option value="reminder_water" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'reminder_water' ? 'selected' : ''; ?>>Rappel Eau</option>
                            <option value="milestone" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'milestone' ? 'selected' : ''; ?>>Jalon Atteint</option>
                            <option value="consultation_scheduled" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'consultation_scheduled' ? 'selected' : ''; ?>>Consultation Planifiée</option>
                            <option value="consultation_reminder_day" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'consultation_reminder_day' ? 'selected' : ''; ?>>Rappel Consultation (1 jour)</option>
                            <option value="consultation_reminder_hour" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'consultation_reminder_hour' ? 'selected' : ''; ?>>Rappel Consultation (1 heure)</option>
                            <option value="program_assigned" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'program_assigned' ? 'selected' : ''; ?>>Programme Assigné</option>
                            <option value="recommendation_added" <?php echo isset($filters['notification_type']) && $filters['notification_type'] == 'recommendation_added' ? 'selected' : ''; ?>>Nouvelle Recommandation</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Statut</label>
                        <select name="status">
                            <option value="">Tous les statuts</option>
                            <option value="sent" <?php echo isset($filters['status']) && $filters['status'] == 'sent' ? 'selected' : ''; ?>>Envoyé</option>
                            <option value="failed" <?php echo isset($filters['status']) && $filters['status'] == 'failed' ? 'selected' : ''; ?>>Échec</option>
                            <option value="pending" <?php echo isset($filters['status']) && $filters['status'] == 'pending' ? 'selected' : ''; ?>>En attente</option>
                        </select>
                    </div>

                    <div class="filter-group" style="display: flex; align-items: end;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter"></i> Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Logs Table -->
            <?php if (!empty($logs)): ?>
                <div class="logs-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Type</th>
                                <th>Canal</th>
                                <th>Destinataire</th>
                                <th>Statut</th>
                                <th>Détails</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($log->sent_at ?? $log->created_at)); ?></td>
                                    <td>
                                        <?php if ($log->patient_id > 0): ?>
                                            <a href="<?php echo admin_url('dietetic/patients/view/' . $log->patient_id); ?>">
                                                Patient #<?php echo $log->patient_id; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Test</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="notification-type">
                                            <?php
                                            $types = [
                                                'reminder_weight' => '⚖️ Rappel Poids',
                                                'reminder_water' => '💧 Rappel Eau',
                                                'milestone' => '🏆 Jalon',
                                                'consultation_scheduled' => '📅 Consultation',
                                                'consultation_reminder_day' => '⏰ Rappel J-1',
                                                'consultation_reminder_hour' => '⏰ Rappel H-1',
                                                'program_assigned' => '📋 Programme',
                                                'recommendation_added' => '💡 Recommandation',
                                                'test' => '🧪 Test'
                                            ];
                                            echo $types[$log->notification_type] ?? $log->notification_type;
                                            ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="channel-badge <?php echo $log->channel; ?>">
                                            <?php
                                            $icons = [
                                                'email' => '✉️',
                                                'sms' => '📱',
                                                'whatsapp' => '💬'
                                            ];
                                            echo ($icons[$log->channel] ?? '') . ' ' . strtoupper($log->channel);
                                            ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($log->recipient); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $log->status; ?>">
                                            <?php
                                            $status_labels = [
                                                'sent' => '✓ Envoyé',
                                                'failed' => '✗ Échec',
                                                'pending' => '⏳ En attente'
                                            ];
                                            echo $status_labels[$log->status] ?? $log->status;
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($log->error_message): ?>
                                            <span class="text-danger" title="<?php echo htmlspecialchars($log->error_message); ?>">
                                                <i class="fa fa-exclamation-circle"></i> Voir erreur
                                            </span>
                                        <?php elseif ($log->subject): ?>
                                            <span title="<?php echo htmlspecialchars($log->subject); ?>">
                                                <i class="fa fa-info-circle"></i>
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($total > $per_page): ?>
                    <div class="pagination">
                        <?php
                        $total_pages = ceil($total / $per_page);
                        $query_string = http_build_query($filters);

                        if ($page > 1):
                        ?>
                            <a href="<?php echo admin_url('dietetic/notifications/logs?page=' . ($page - 1) . ($query_string ? '&' . $query_string : '')); ?>">
                                <i class="fa fa-chevron-left"></i> Précédent
                            </a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <?php if ($i == $page): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php else: ?>
                                <a href="<?php echo admin_url('dietetic/notifications/logs?page=' . $i . ($query_string ? '&' . $query_string : '')); ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="<?php echo admin_url('dietetic/notifications/logs?page=' . ($page + 1) . ($query_string ? '&' . $query_string : '')); ?>">
                                Suivant <i class="fa fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="logs-table">
                    <div class="empty-state">
                        <i class="fa fa-inbox"></i>
                        <h3>Aucune notification trouvée</h3>
                        <p>Aucune notification n'a encore été envoyée, ou aucune ne correspond à vos filtres.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php init_tail(); ?>
